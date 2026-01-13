<?php
/**
 * @Author: ManestEtoo
 * @Date:   2023-09-28 08:53:22
 * @Last Modified by:   CORRECTION ERREUR PERMISSIONS
 * @Last Modified time: 2025-10-01 - FIX contains() error
 */
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    // use SoftDeletes;

    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'matricule',
        'password',
        'id_grade',
        'ent1d',
        'contact',
        'id_categorie',
        'type_user',
        'photo_user',
        'etat',
        'update_user',
        'user1d',
        'date_embauche',
        'salaire',
        'adresse',
        'statut_emploi',
        'email_verified_at',
        'crm_permissions',
        'caisse_permissions', // Permissions pour la caisse
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * ✅ CORRECTION : Retrait du cast 'permissions' en array
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_embauche' => 'date',
        'salaire' => 'decimal:2',
        'etat' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'crm_permissions' => 'array', // Cast automatique en array
        'caisse_permissions' => 'array', // Permissions pour la caisse
        // ❌ 'permissions' => 'array', // RETIRÉ - Cause l'erreur contains()
        // ✅ Utilisez Spatie Permission pour gérer les permissions
    ];

    /**
     * Les attributs qui doivent être ajoutés aux arrays.
     *
     * @var array
     */
    protected $appends = [
        'type_user_label',
        'statut_emploi_label',
        'full_photo_url',
        'years_of_service'
    ];

    /**
     * Les valeurs par défaut pour les attributs.
     *
     * @var array
     */
    protected $attributes = [
        'ent1d' => 1,
        'etat' => 1,
        'statut_emploi' => 'actif',
        'type_user' => 'public',
        'photo_user' => 'NULL'
    ];

    /**
     * Boot du modèle - VERSION CORRIGÉE
     */
    protected static function boot()
    {
        parent::boot();
        
        // Événement avant sauvegarde
        static::saving(function ($user) {
            try {
                // Auto-générer un matricule si vide pour les agents internes
                if (empty($user->matricule) && in_array($user->type_user, ['agent_comptoir', 'commercial', 'admin'])) {
                    $user->matricule = $user->generateMatricule();
                }

                // S'assurer que les valeurs par défaut sont correctes
                if (is_null($user->ent1d)) {
                    $user->ent1d = 1;
                }

                if (is_null($user->etat)) {
                    $user->etat = 1;
                }

                if (empty($user->statut_emploi)) {
                    $user->statut_emploi = 'actif';
                }

                if (empty($user->photo_user) || $user->photo_user === 'null') {
                    $user->photo_user = 'NULL';
                }

                // S'assurer que l'email est en minuscules
                if (!empty($user->email)) {
                    $user->email = strtolower(trim($user->email));
                }

                // Normaliser le nom
                if (!empty($user->name)) {
                    $user->name = ucwords(strtolower(trim($user->name)));
                }

            } catch (\Exception $e) {
                Log::error('Erreur dans User::boot saving:', [
                    'error' => $e->getMessage(),
                    'user_data' => $user->toArray()
                ]);
            }
        });

        // Événement après création
        static::created(function ($user) {
            try {
                Log::info('Nouvel utilisateur créé:', [
                    'id' => $user->id,
                    'name' => $user->name,
                    'type_user' => $user->type_user,
                    'matricule' => $user->matricule,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur dans User::boot created:', ['error' => $e->getMessage()]);
            }
        });
    }

    /**
     * Mutateur pour le mot de passe - VERSION CORRIGÉE
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            // Vérifier si le mot de passe est déjà haché
            if (strlen($value) === 60 && substr($value, 0, 4) === '$2y$') {
                // Le mot de passe est déjà haché
                $this->attributes['password'] = $value;
            } else {
                // Hacher le mot de passe
                $this->attributes['password'] = Hash::make($value);
            }
        }
    }

    /**
     * Mutateur pour l'email (normalisation)
     */
    public function setEmailAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['email'] = strtolower(trim($value));
        }
    }

    /**
     * Mutateur pour le nom (normalisation)
     */
    public function setNameAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['name'] = ucwords(strtolower(trim($value)));
        }
    }

    /**
     * Mutateur pour le matricule
     */
    public function setMatriculeAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['matricule'] = strtoupper(trim($value));
        }
    }

    /**
     * Mutateur pour le type_user
     */
    public function setTypeUserAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['type_user'] = strtolower(trim($value));
        }
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Vérifier si l'utilisateur a une permission spécifique
     * Cette méthode remplace l'utilisation de contains() sur un array
     */
    public function hasPermission($permission): bool
    {
        try {
            // Utiliser Spatie Permission pour vérifier
            return $this->can($permission);
        } catch (\Exception $e) {
            Log::error('Erreur hasPermission:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id,
                'permission' => $permission
            ]);
            return false;
        }
    }

    /**
     * ✅ MÉTHODE AJOUTÉE : Obtenir toutes les permissions sous forme de Collection
     */
    public function getPermissionsCollection()
    {
        try {
            // Retourne une Collection Laravel, pas un array
            return collect($this->getAllPermissions()->pluck('name')->toArray());
        } catch (\Exception $e) {
            Log::error('Erreur getPermissionsCollection:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return collect([]);
        }
    }

    /**
     * ✅ MÉTHODE AJOUTÉE : Vérifier si l'utilisateur a des permissions spécifiques
     */
    public function hasAnyPermission(array $permissions): bool
    {
        try {
            return $this->hasAnyPermission($permissions);
        } catch (\Exception $e) {
            Log::error('Erreur hasAnyPermission:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return false;
        }
    }

    /**
     * Obtenir les rôles formatés (méthode legacy)
     */
    public function get_roles()
    {
        try {
            $roles = [];
            foreach ($this->getRoleNames() as $key => $role) {
                $roles[$key] = $role;
            }
            return $roles;
        } catch (\Exception $e) {
            Log::error('Erreur dans get_roles:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return [];
        }
    }

    // ==================== RELATIONS ====================

    /**
     * Relation avec la catégorie
     */
    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categories::class, 'id_categorie')->withDefault([
            'libelle' => 'Non défini'
        ]);
    }

    /**
     * Relation avec le grade
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grades::class, 'id_grade')->withDefault([
            'libelle' => 'Non défini'
        ]);
    }

    /**
     * Relation avec l'entreprise
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprises::class, 'ent1d')->withDefault([
            'nom' => 'PSI Africa'
        ]);
    }

    /**
     * Relation avec l'utilisateur qui a fait la dernière mise à jour
     */
    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_user')->withDefault([
            'name' => 'Système'
        ]);
    }

    /**
     * Relation avec l'utilisateur qui a créé cet enregistrement
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1d')->withDefault([
            'name' => 'Système'
        ]);
    }

    /**
     * Relation avec les profils visa créés par cet utilisateur
     */
    public function profilsVisa(): HasMany
    {
        return $this->hasMany(ProfilVisa::class, 'user1d');
    }

    /**
     * Relation avec les profils visa mis à jour par cet utilisateur
     */
    public function profilsVisaUpdated(): HasMany
    {
        return $this->hasMany(ProfilVisa::class, 'update_user');
    }

    /**
     * Relation avec les messages profil visa
     */
    public function messagesProfilVisa(): HasMany
    {
        return $this->hasMany(AddMessageProfilVisa::class, 'user1d');
    }

    /**
     * Relation CRM Clients
     */
    public function crmClients()
    {
        return $this->hasMany(CRMClient::class, 'user_id');
    }

    /**
     * Relation CRM Invoices
     */
    public function crmInvoices()
    {
        return $this->hasMany(CRMInvoice::class, 'user_id');
    }

    // ==================== SCOPES ====================

    /**
     * Scope pour les agents internes uniquement
     */
    public function scopeAgentsInternes($query)
    {
        return $query->whereIn('type_user', ['admin', 'agent_comptoir', 'commercial']);
    }

    /**
     * Scope pour les utilisateurs publics uniquement
     */
    public function scopeUtilisateursPublics($query)
    {
        return $query->where(function($q) {
            $q->where('type_user', 'public')
              ->orWhereNull('type_user')
              ->orWhere('type_user', '');
        });
    }

    /**
     * Scope pour les agents actifs
     */
    public function scopeActifs($query)
    {
        return $query->where('etat', 1)->where(function($q) {
            $q->where('statut_emploi', 'actif')
              ->orWhereNull('statut_emploi');
        });
    }

    /**
     * Scope pour les agents comptoir
     */
    public function scopeAgentsComptoir($query)
    {
        return $query->where('type_user', 'agent_comptoir');
    }

    /**
     * Scope pour les commerciaux
     */
    public function scopeCommerciaux($query)
    {
        return $query->where('type_user', 'commercial');
    }

    /**
     * Scope pour les administrateurs
     */
    public function scopeAdmins($query)
    {
        return $query->where('type_user', 'admin');
    }

    /**
     * Scope pour une entreprise donnée
     */
    public function scopeForEntreprise($query, $entrepriseId = 1)
    {
        return $query->where('ent1d', $entrepriseId);
    }

    /**
     * Scope pour recherche par nom, email ou matricule
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('matricule', 'like', "%{$search}%");
        });
    }

    /**
     * Scope pour filtrer par rôle
     */
    public function scopeWithRole($query, $roleName)
    {
        return $query->whereHas('roles', function($q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Obtenir le libellé du type d'utilisateur
     */
    public function getTypeUserLabelAttribute(): string
    {
        $labels = [
            'public' => 'Utilisateur Public',
            'admin' => 'Administrateur',
            'agent_comptoir' => 'Agent Comptoir',
            'commercial' => 'Commercial'
        ];

        return $labels[$this->type_user] ?? 'Non défini';
    }

    /**
     * Obtenir le libellé du statut d'emploi
     */
    public function getStatutEmploiLabelAttribute(): string
    {
        $labels = [
            'actif' => 'Actif',
            'suspendu' => 'Suspendu',
            'conge' => 'En congé',
            'demission' => 'Démission'
        ];

        return $labels[$this->statut_emploi ?? 'actif'] ?? 'Non défini';
    }

    /**
     * Obtenir l'URL complète de la photo
     */
    public function getFullPhotoUrlAttribute(): ?string
    {
        if ($this->photo_user && $this->photo_user != 'NULL' && $this->photo_user != 'null') {
            $photoPath = public_path('upload/users/' . $this->photo_user);
            if (file_exists($photoPath)) {
                return asset('upload/users/' . $this->photo_user);
            }
        }
        return null;
    }

    /**
     * Calculer les années de service
     */
    public function getYearsOfServiceAttribute(): float
    {
        if (!$this->date_embauche) {
            return 0;
        }

        try {
            return $this->date_embauche->diffInYears(now());
        } catch (\Exception $e) {
            Log::error('Erreur calcul années de service:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return 0;
        }
    }

    /**
     * Obtenir l'initiales pour l'avatar
     */
    public function getInitialsAttribute(): string
    {
        if (empty($this->name)) {
            return 'NA';
        }

        $nameParts = explode(' ', trim($this->name));
        if (count($nameParts) >= 2) {
            return strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
        }
        
        return strtoupper(substr($this->name, 0, 2));
    }

    // ==================== MÉTHODES MÉTIER ====================

    /**
     * Vérifier si l'utilisateur est un agent interne
     */
    public function isAgentInterne(): bool
    {
        return in_array($this->type_user, ['admin', 'agent_comptoir', 'commercial']);
    }

    /**
     * Vérifier si l'utilisateur est un utilisateur public
     */
    public function isUtilisateurPublic(): bool
    {
        return $this->type_user === 'public' || empty($this->type_user);
    }

    /**
     * Vérifier si l'agent est actif
     */
    public function isActif(): bool
    {
        return $this->etat == 1 && ($this->statut_emploi === 'actif' || empty($this->statut_emploi));
    }

    /**
     * Vérifier si l'utilisateur est protégé
     */
    public function isProtected(): bool
    {
        $protectedEmails = ['admin@psiafrica.ci', 'superadmin@psiafrica.ci'];
        $protectedNames = ['Super Admin', 'Administrateur Principal'];
        
        return in_array($this->email, $protectedEmails) || 
               in_array($this->name, $protectedNames) ||
               $this->hasRole('Super Admin');
    }

    /**
     * Obtenir le tableau de bord approprié selon le type d'utilisateur
     */
    public function getDashboardRoute(): string
    {
        switch ($this->type_user) {
            case 'admin':
                return route('admin.dashboard');
            case 'agent_comptoir':
                return '/comptoir/dashboard';
            case 'commercial':
                return '/commercial/dashboard';
            case 'public':
                return route('mes.demandes');
            default:
                return route('dashboard');
        }
    }

    /**
     * Obtenir les permissions selon le type d'utilisateur
     * ✅ RETOURNE MAINTENANT UN ARRAY, PAS UNE COLLECTION
     */
    public function getDefaultPermissions(): array
    {
        $permissions = [
            'admin' => ['*'], // Toutes les permissions
            'agent_comptoir' => [
                'manage_profil_visa',
                'view_profil_visa',
                'edit_profil_visa_status',
                'add_message_profil_visa',
                'view_dashboard_comptoir',
                'manage_rendez_vous',
                'view_statistiques_comptoir',
            ],
            'commercial' => [
                'manage_clients',
                'view_clients',
                'manage_forfaits',
                'view_forfaits',
                'manage_souscrire_forfaits',
                'view_dashboard_commercial',
                'manage_partenaires',
                'view_statistiques_commercial',
                'manage_temoignages',
            ],
            'public' => [
                'manage_own_profile',
                'view_own_profil_visa',
            ]
        ];

        return $permissions[$this->type_user] ?? [];
    }

    /**
     * Générer un matricule automatique
     */
    public function generateMatricule(): string
    {
        $prefix = match($this->type_user) {
            'admin' => 'ADM',
            'agent_comptoir' => 'CPT',
            'commercial' => 'COM',
            default => 'USR'
        };

        try {
            // Trouver le prochain numéro disponible
            $lastMatricule = self::where('matricule', 'like', $prefix . '%')
                ->orderBy('matricule', 'desc')
                ->first();

            if ($lastMatricule) {
                $lastNumber = (int) substr($lastMatricule->matricule, 3);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            Log::error('Erreur génération matricule:', [
                'error' => $e->getMessage(),
                'type_user' => $this->type_user
            ]);
            return $prefix . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Actions de gestion du statut
     */
    public function suspend(): bool
    {
        try {
            $this->statut_emploi = 'suspendu';
            $this->etat = 0;
            return $this->save();
        } catch (\Exception $e) {
            Log::error('Erreur suspension utilisateur:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return false;
        }
    }

    public function activate(): bool
    {
        try {
            $this->statut_emploi = 'actif';
            $this->etat = 1;
            return $this->save();
        } catch (\Exception $e) {
            Log::error('Erreur activation utilisateur:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return false;
        }
    }

    public function resign(): bool
    {
        try {
            $this->statut_emploi = 'demission';
            $this->etat = 0;
            return $this->save();
        } catch (\Exception $e) {
            Log::error('Erreur démission utilisateur:', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return false;
        }
    }

    // ==================== MÉTHODES STATIQUES ====================

    /**
     * Obtenir les statistiques globales des agents
     */
    public static function getAgentsStatistics(): array
    {
        try {
            return [
                'total_agents' => self::agentsInternes()->count(),
                'agents_actifs' => self::agentsInternes()->actifs()->count(),
                'agents_comptoir' => self::agentsComptoir()->count(),
                'commerciaux' => self::commerciaux()->count(),
                'admins' => self::admins()->count(),
                'agents_suspendus' => self::agentsInternes()->where('statut_emploi', 'suspendu')->count(),
                'nouveaux_ce_mois' => self::agentsInternes()
                    ->whereMonth('created_at', now()->month)->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Erreur statistiques agents:', ['error' => $e->getMessage()]);
            return [
                'total_agents' => 0,
                'agents_actifs' => 0,
                'agents_comptoir' => 0,
                'commerciaux' => 0,
                'admins' => 0,
                'agents_suspendus' => 0,
                'nouveaux_ce_mois' => 0,
            ];
        }
    }

    /**
     * Créer un utilisateur avec validation
     */
    public static function createSafely(array $data): ?self
    {
        try {
            // Validation des données obligatoires
            if (empty($data['name']) || empty($data['email'])) {
                throw new \InvalidArgumentException('Nom et email sont obligatoires');
            }

            // Vérifier l'unicité de l'email
            if (self::where('email', strtolower(trim($data['email'])))->exists()) {
                throw new \InvalidArgumentException('Cet email est déjà utilisé');
            }

            // Vérifier l'unicité du matricule s'il est fourni
            if (!empty($data['matricule']) && self::where('matricule', strtoupper(trim($data['matricule'])))->exists()) {
                throw new \InvalidArgumentException('Ce matricule est déjà utilisé');
            }

            // Créer l'utilisateur
            $user = new self();
            foreach ($data as $key => $value) {
                if (in_array($key, $user->getFillable())) {
                    $user->$key = $value;
                }
            }

            // Définir les valeurs par défaut
            $user->ent1d = $data['ent1d'] ?? 1;
            $user->etat = $data['etat'] ?? 1;
            $user->statut_emploi = $data['statut_emploi'] ?? 'actif';
            $user->email_verified_at = $data['email_verified_at'] ?? now();

            $saved = $user->save();
            
            if ($saved) {
                Log::info('Utilisateur créé avec createSafely:', [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type_user' => $user->type_user
                ]);
                return $user;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Erreur création utilisateur safe:', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return null;
        }
    }

    /**
     * Obtenir le badge de couleur selon le statut
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->statut_emploi ?? 'actif') {
            'actif' => 'badge-success',
            'suspendu' => 'badge-warning',
            'conge' => 'badge-info',
            'demission' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Obtenir le badge de couleur selon le type d'utilisateur
     */
    public function getTypeBadgeClass(): string
    {
        return match($this->type_user) {
            'admin' => 'badge-danger',
            'agent_comptoir' => 'badge-info',
            'commercial' => 'badge-success',
            'public' => 'badge-primary',
            default => 'badge-secondary'
        };
    }

    /**
     * Nettoyer les anciennes données
     */
    public static function cleanupOldData($days = 365)
    {
        try {
            // Supprimer les utilisateurs publics inactifs depuis X jours
            $deletedCount = self::utilisateursPublics()
                ->where('etat', 0)
                ->where('updated_at', '<', now()->subDays($days))
                ->whereNull('email_verified_at')
                ->delete();

            Log::info("Nettoyage effectué: {$deletedCount} utilisateurs supprimés");
            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Erreur nettoyage données:', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    // ==================== MÉTHODES CRM PERMISSIONS ====================

    /**
     * Obtenir les permissions CRM de l'utilisateur
     * @return array
     */
    public function getCrmPermissions(): array
    {
        try {
            // Si crm_permissions est null ou vide
            if (empty($this->crm_permissions)) {
                Log::info('getCrmPermissions: Aucune permission définie', [
                    'user_id' => $this->id,
                    'user_name' => $this->name,
                    'crm_permissions_value' => $this->crm_permissions
                ]);
                return [];
            }

            // Si c'est déjà un array (grâce au cast)
            if (is_array($this->crm_permissions)) {
                Log::info('getCrmPermissions: Permissions trouvées (array)', [
                    'user_id' => $this->id,
                    'permissions' => $this->crm_permissions
                ]);
                return $this->crm_permissions;
            }

            // Si c'est une string JSON
            if (is_string($this->crm_permissions)) {
                $decoded = json_decode($this->crm_permissions, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    Log::info('getCrmPermissions: Permissions décodées depuis JSON', [
                        'user_id' => $this->id,
                        'permissions' => $decoded
                    ]);
                    return $decoded;
                }
                
                Log::warning('getCrmPermissions: Erreur décodage JSON', [
                    'user_id' => $this->id,
                    'json_error' => json_last_error_msg(),
                    'raw_value' => $this->crm_permissions
                ]);
            }

            return [];

        } catch (\Exception $e) {
            Log::error('getCrmPermissions: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return [];
        }
    }

    /**
     * Vérifier si l'utilisateur a une permission CRM spécifique
     * @param string $permission
     * @return bool
     */
    public function hasCrmPermission(string $permission): bool
    {
        try {
            // Super Admin a toutes les permissions
            if ($this->hasRole('Super Admin')) {
                return true;
            }

            $permissions = $this->getCrmPermissions();
            
            $hasPermission = in_array($permission, $permissions);
            
            Log::info('hasCrmPermission: Vérification', [
                'user_id' => $this->id,
                'permission' => $permission,
                'result' => $hasPermission,
                'user_permissions' => $permissions
            ]);
            
            return $hasPermission;

        } catch (\Exception $e) {
            Log::error('hasCrmPermission: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id,
                'permission' => $permission
            ]);
            return false;
        }
    }

    /**
     * Mettre à jour les permissions CRM de l'utilisateur
     * @param array $permissions
     * @return bool
     */
    public function updateCrmPermissions(array $permissions): bool
    {
        try {
            Log::info('updateCrmPermissions: Début mise à jour', [
                'user_id' => $this->id,
                'user_name' => $this->name,
                'new_permissions' => $permissions
            ]);

            // Enregistrer les permissions
            $this->crm_permissions = $permissions;
            $saved = $this->save();

            Log::info('updateCrmPermissions: Sauvegarde', [
                'user_id' => $this->id,
                'saved' => $saved,
                'permissions_after_save' => $this->crm_permissions
            ]);

            return $saved;

        } catch (\Exception $e) {
            Log::error('updateCrmPermissions: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id,
                'permissions' => $permissions
            ]);
            return false;
        }
    }

    /**
     * Vérifier si l'utilisateur a au moins une des permissions CRM
     * @param array $permissions
     * @return bool
     */
    public function hasAnyCrmPermission(array $permissions): bool
    {
        try {
            // Super Admin a toutes les permissions
            if ($this->hasRole('Super Admin')) {
                return true;
            }

            $userPermissions = $this->getCrmPermissions();
            
            foreach ($permissions as $permission) {
                if (in_array($permission, $userPermissions)) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('hasAnyCrmPermission: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return false;
        }
    }

    /**
     * Vérifier si l'utilisateur a toutes les permissions CRM spécifiées
     * @param array $permissions
     * @return bool
     */
    public function hasAllCrmPermissions(array $permissions): bool
    {
        try {
            // Super Admin a toutes les permissions
            if ($this->hasRole('Super Admin')) {
                return true;
            }

            $userPermissions = $this->getCrmPermissions();
            
            foreach ($permissions as $permission) {
                if (!in_array($permission, $userPermissions)) {
                    return false;
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error('hasAllCrmPermissions: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return false;
        }
    }

    /**
     * Obtenir les permissions Caisse de l'utilisateur
     * @return array
     */
    public function getCaissePermissions(): array
    {
        try {
            // Super Admin a toutes les permissions
            if ($this->hasRole('Super Admin') || $this->type_user === 'admin') {
                return ['modifier_entrees', 'supprimer_entrees', 'modifier_sorties', 'supprimer_sorties'];
            }

            // Si caisse_permissions est null ou vide
            if (empty($this->caisse_permissions)) {
                return [];
            }

            // Si c'est déjà un array (grâce au cast)
            if (is_array($this->caisse_permissions)) {
                return $this->caisse_permissions;
            }

            // Si c'est une string JSON
            if (is_string($this->caisse_permissions)) {
                $decoded = json_decode($this->caisse_permissions, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }

            return [];

        } catch (\Exception $e) {
            Log::error('getCaissePermissions: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id
            ]);
            return [];
        }
    }

    /**
     * Vérifier si l'utilisateur a une permission Caisse spécifique
     * @param string $permission
     * @return bool
     */
    public function hasCaissePermission(string $permission): bool
    {
        try {
            // Super Admin et Admin ont toutes les permissions
            if ($this->hasRole('Super Admin') || $this->type_user === 'admin') {
                return true;
            }

            $permissions = $this->getCaissePermissions();
            return in_array($permission, $permissions);

        } catch (\Exception $e) {
            Log::error('hasCaissePermission: Erreur', [
                'error' => $e->getMessage(),
                'user_id' => $this->id,
                'permission' => $permission
            ]);
            return false;
        }
    }
}