<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grades;
use App\Models\Categories;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    /**
     * ✅ API DataTables COMPLÈTEMENT CORRIGÉE - AUCUN DOUBLON DE RÔLES
     */
    public function getUserList(Request $request)
    {
        try {
            Log::info('🚀 getUserList: Début requête DataTables - CORRECTION DOUBLONS RÔLES');

            // Vérifications de base
            $user = Auth::user();
            if (!$user || !$this->hasAccess($user)) {
                return $this->errorResponse('Accès refusé', $request);
            }

            // ✅ Query optimisée avec DISTINCT pour éviter les doublons
            $query = User::select([
                'id', 'name', 'email', 'type_user', 'created_at', 'updated_at',
                'matricule', 'contact', 'photo_user', 'etat', 'statut_emploi',
                'id_categorie', 'id_grade'
            ])
            ->whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])
            ->distinct(); // ✅ AJOUT DISTINCT AU NIVEAU PRINCIPAL

            // Ajouter condition ent1d si la colonne existe
            if (Schema::hasColumn('users', 'ent1d')) {
                $query->where('ent1d', 1);
            }

            // Appliquer les filtres
            if ($request->filled('type_filter')) {
                $query->where('type_user', $request->type_filter);
            }

            if ($request->filled('status_filter') && Schema::hasColumn('users', 'etat')) {
                $query->where('etat', $request->status_filter);
            }

            if ($request->filled('role_filter')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('name', $request->role_filter);
                });
            }

            $data = $query->orderBy('created_at', 'desc')->get();
            
            Log::info("✅ getUserList: {$data->count()} agents récupérés SANS DOUBLONS");

            return Datatables::of($data)
                ->addColumn('agent_info', function ($agent) {
                    return $this->formatAgentInfo($agent);
                })
                ->addColumn('contact_info', function ($agent) {
                    return $this->formatContactInfo($agent);
                })
                ->addColumn('type_user_badge', function ($agent) {
                    return $this->formatTypeUserBadge($agent);
                })
                ->addColumn('status', function ($agent) {
                    return $this->formatStatus($agent);
                })
                ->addColumn('roles', function ($agent) {
                    return $this->formatRolesNoDuplicates($agent); // ✅ NOUVELLE MÉTHODE SANS DOUBLONS
                })
                ->addColumn('grade_categorie', function ($agent) {
                    return $this->formatGradeCategorie($agent);
                })
                ->addColumn('permissions', function ($agent) {
                    return $this->formatPermissionsNoDuplicates($agent); // ✅ NOUVELLE MÉTHODE SANS DOUBLONS
                })
                ->addColumn('created_info', function ($agent) {
                    return $agent->created_at ? Carbon::parse($agent->created_at)->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('action', function ($agent) {
                    return $this->formatActions($agent);
                })
                ->rawColumns(['agent_info', 'contact_info', 'type_user_badge', 'status', 'roles', 'grade_categorie', 'permissions', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('❌ getUserList error: ' . $e->getMessage());
            return $this->errorResponse('Erreur serveur: ' . $e->getMessage(), $request);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Formater les rôles SANS AUCUN DOUBLON
     */
    private function formatRolesNoDuplicates($agent): string
    {
        try {
            Log::info("🔍 Formatage rôles pour agent {$agent->id} - {$agent->name}");

            // ✅ MÉTHODE 1 : Récupération directe via query unique
            $uniqueRoles = $this->getUserRolesUnique($agent->id);
            
            if (empty($uniqueRoles)) {
                // ✅ FALLBACK : Utiliser le type d'utilisateur
                $fallbackRole = $this->getTypeUserLabel($agent->type_user);
                $color = $this->getRoleColorByType($agent->type_user);
                Log::info("  → Fallback: {$fallbackRole}");
                return "<span class='badge bg-{$color} fallback-role' title='Rôle basé sur le type utilisateur'>{$fallbackRole}</span>";
            }
            
            // ✅ AFFICHAGE DES RÔLES UNIQUES
            $badges = '';
            foreach ($uniqueRoles as $roleName) {
                $color = $this->getRoleColor($roleName);
                $badges .= "<span class='badge bg-{$color} m-1' title='Rôle assigné'>" . e($roleName) . "</span>";
                Log::info("  → Rôle affiché: {$roleName}");
            }
            
            return $badges;
            
        } catch (\Exception $e) {
            Log::error("❌ Erreur formatage rôles pour agent {$agent->id}: " . $e->getMessage());
            
            // En cas d'erreur, afficher le type d'utilisateur
            $fallbackRole = $this->getTypeUserLabel($agent->type_user);
            $color = $this->getRoleColorByType($agent->type_user);
            return "<span class='badge bg-{$color} error-fallback' title='Erreur - rôle basé sur le type'>{$fallbackRole}</span>";
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Récupérer les rôles uniques d'un utilisateur
     */
    private function getUserRolesUnique($userId): array
    {
        try {
            if (!Schema::hasTable('model_has_roles') || !Schema::hasTable('roles')) {
                Log::warning("Tables rôles manquantes pour utilisateur {$userId}");
                return [];
            }

            // ✅ QUERY AVEC DISTINCT POUR ÉLIMINER TOTALEMENT LES DOUBLONS
            $roles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $userId)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->distinct()
                ->pluck('roles.name')
                ->toArray();

            // ✅ DOUBLE SÉCURITÉ : array_unique pour être absolument sûr
            $uniqueRoles = array_values(array_unique($roles));
            
            Log::info("Rôles uniques pour utilisateur {$userId}: " . implode(', ', $uniqueRoles));
            
            return $uniqueRoles;

        } catch (\Exception $e) {
            Log::error("Erreur récupération rôles uniques pour utilisateur {$userId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Formater les permissions SANS DOUBLONS
     */
    private function formatPermissionsNoDuplicates($agent): string
    {
        try {
            // ✅ RÉCUPÉRATION DIRECTE DES PERMISSIONS UNIQUES
            $uniquePermissions = $this->getUserPermissionsUnique($agent->id);
            
            if (empty($uniquePermissions)) {
                return '<span class="badge bg-warning small">Aucune</span>';
            }
            
            $badges = '';
            $count = 0;
            $maxDisplay = 3; // Limiter l'affichage
            
            foreach ($uniquePermissions as $permissionName) {
                if ($count < $maxDisplay) {
                    $badges .= "<span class='badge bg-dark m-1 small'>" . e($permissionName) . "</span>";
                    $count++;
                }
            }
            
            if (count($uniquePermissions) > $maxDisplay) {
                $remaining = count($uniquePermissions) - $maxDisplay;
                $badges .= "<span class='badge bg-secondary m-1 small'>+{$remaining}</span>";
            }
            
            return $badges;
            
        } catch (\Exception $e) {
            Log::error("Erreur formatage permissions pour agent {$agent->id}: " . $e->getMessage());
            return '<span class="badge bg-secondary small">Non définies</span>';
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Récupérer les permissions uniques d'un utilisateur
     */
    private function getUserPermissionsUnique($userId): array
    {
        try {
            if (!Schema::hasTable('model_has_permissions') && !Schema::hasTable('role_has_permissions')) {
                return [];
            }

            $permissions = [];

            // ✅ PERMISSIONS DIRECTES (si elles existent)
            if (Schema::hasTable('model_has_permissions')) {
                $directPermissions = DB::table('model_has_permissions')
                    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('model_has_permissions.model_id', $userId)
                    ->where('model_has_permissions.model_type', 'App\\Models\\User')
                    ->distinct()
                    ->pluck('permissions.name')
                    ->toArray();
                
                $permissions = array_merge($permissions, $directPermissions);
            }

            // ✅ PERMISSIONS VIA RÔLES
            if (Schema::hasTable('role_has_permissions')) {
                $rolePermissions = DB::table('model_has_roles')
                    ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
                    ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
                    ->where('model_has_roles.model_id', $userId)
                    ->where('model_has_roles.model_type', 'App\\Models\\User')
                    ->distinct()
                    ->pluck('permissions.name')
                    ->toArray();
                
                $permissions = array_merge($permissions, $rolePermissions);
            }

            // ✅ ÉLIMINER TOUS LES DOUBLONS
            return array_values(array_unique($permissions));

        } catch (\Exception $e) {
            Log::error("Erreur récupération permissions uniques pour utilisateur {$userId}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ✅ CORRECTION : Stocker un agent SANS CRÉER DE DOUBLONS DE RÔLES
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            Log::info('UserController store - Début création agent SANS DOUBLONS:', 
                $request->except(['password', 'password_confirmation']));

            // Validation
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'type_user' => 'required|in:admin,agent_comptoir,commercial',
                'password' => 'required|min:6|confirmed',
                'role' => 'required|exists:roles,id',
            ];

            if (Schema::hasColumn('users', 'contact')) {
                $rules['contact'] = 'required|string';
            }
            if (Schema::hasColumn('users', 'matricule')) {
                $rules['matricule'] = 'nullable|string|unique:users,matricule';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Erreur de validation: ' . $validator->errors()->first());
            }

            DB::beginTransaction();

            // Préparation des données
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'type_user' => $request->type_user,
                'password' => Hash::make($request->password),
                'user1d' => Auth::id(),
                'email_verified_at' => now(),
            ];

            // Ajouter les colonnes conditionnelles
            if (Schema::hasColumn('users', 'contact')) {
                $userData['contact'] = $request->contact;
            }
            if (Schema::hasColumn('users', 'matricule')) {
                $userData['matricule'] = $request->matricule ?: $this->generateMatricule($request->type_user);
            }
            if (Schema::hasColumn('users', 'ent1d')) {
                $userData['ent1d'] = 1;
            }
            if (Schema::hasColumn('users', 'etat')) {
                $userData['etat'] = 1;
            }
            if (Schema::hasColumn('users', 'statut_emploi')) {
                $userData['statut_emploi'] = 'actif';
            }

            // Créer l'utilisateur
            $user = User::create($userData);
            Log::info("Utilisateur créé avec ID: {$user->id}");

            // ✅ ASSIGNATION DU RÔLE SANS DOUBLONS - MÉTHODE CORRIGÉE
            if ($request->filled('role')) {
                $this->assignRoleWithoutDuplicates($user, $request->role);
            }

            // Gestion de la photo
            if ($request->hasFile('photo_user')) {
                try {
                    $this->handlePhotoUpload($request, $user);
                } catch (\Exception $e) {
                    Log::error('Erreur upload photo: ' . $e->getMessage());
                }
            }

            DB::commit();

            Log::info("✅ Agent créé avec succès SANS DOUBLONS: {$user->name} (ID: {$user->id})");

            return redirect()->route('users.index')
                ->with('success', "Agent {$user->name} créé avec succès!");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ Erreur création agent: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Assigner un rôle sans créer de doublons
     */
    private function assignRoleWithoutDuplicates($user, $roleId): void
    {
        try {
            // ✅ NETTOYER D'ABORD TOUS LES RÔLES EXISTANTS POUR CET UTILISATEUR
            DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            Log::info("Anciens rôles supprimés pour utilisateur {$user->id}");

            // ✅ VÉRIFIER QUE LE RÔLE EXISTE
            $role = Role::find($roleId);
            if (!$role) {
                Log::warning("Rôle avec ID {$roleId} non trouvé");
                return;
            }

            // ✅ ASSIGNER LE RÔLE UNIQUE
            DB::table('model_has_roles')->insert([
                'role_id' => $role->id,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id
            ]);

            Log::info("Rôle {$role->name} assigné SANS DOUBLON à l'utilisateur {$user->id}");

        } catch (\Exception $e) {
            Log::error("Erreur assignation rôle sans doublons: " . $e->getMessage());
        }
    }

    /**
     * ✅ CORRECTION : Mettre à jour un agent SANS CRÉER DE DOUBLONS DE RÔLES
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            Log::info('UserController update - Début mise à jour SANS DOUBLONS:', 
                $request->except(['password', 'password_confirmation']));

            $rules = [
                'id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->id,
                'password' => 'nullable|min:6|confirmed',
            ];

            if (Schema::hasColumn('users', 'contact')) {
                $rules['contact'] = 'required|string';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Erreur de validation: ' . $validator->errors()->first());
            }

            $user = User::find($request->id);
            if (!$user) {
                return redirect()->back()->with('error', 'Agent non trouvé');
            }

            // Protéger certains utilisateurs
            if (in_array($user->email, ['admin@psiafrica.ci', 'superadmin@psiafrica.ci'])) {
                return redirect()->back()->with('error', 'Utilisateur protégé - modification interdite');
            }

            DB::beginTransaction();

            // Données à mettre à jour
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'update_user' => Auth::id(),
            ];

            if (Schema::hasColumn('users', 'contact')) {
                $updateData['contact'] = $request->contact;
            }
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // ✅ METTRE À JOUR LE RÔLE SANS CRÉER DE DOUBLONS
            if ($request->filled('role')) {
                $this->assignRoleWithoutDuplicates($user, $request->role);
            }

            // Gérer la photo
            if ($request->hasFile('photo_user')) {
                try {
                    $this->handlePhotoUpload($request, $user);
                } catch (\Exception $e) {
                    Log::error('Erreur upload photo: ' . $e->getMessage());
                }
            }

            DB::commit();

            Log::info("✅ Agent mis à jour avec succès SANS DOUBLONS: {$user->name}");

            return redirect()->back()->with('success', "Agent {$user->name} mis à jour avec succès!");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ Erreur mise à jour agent: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    // ==================== MÉTHODES EXISTANTES CONSERVÉES ====================

    /**
     * Page principale des agents internes
     */
    public function index(): View
    {
        try {
            $user = Auth::user();
            
            if (!$user || (!$user->hasAnyRole(['Admin', 'Super Admin']) && $user->type_user !== 'admin')) {
                return redirect('/dashboard')->with('error', 'Accès non autorisé');
            }

            $data = [
                'user1d' => $user->id,
                'roles' => $this->getSafeRoles(),
                'dataCategories' => $this->getSafeCategories(),
                'dataGrades' => $this->getSafeGrades(),
            ];
            
            return view('admin.users.users', $data);
            
        } catch (\Exception $e) {
            Log::error('UserController index error: ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Erreur de chargement: ' . $e->getMessage());
        }
    }

    /**
     * Créer un agent - PAGE
     */
    public function create(): View
    {
        try {
            $data = [
                'roles' => $this->getSafeRoles(),
                'dataCategories' => $this->getSafeCategories(),
                'dataGrades' => $this->getSafeGrades(),
            ];
            
            return view('admin.users.create-user', $data);
            
        } catch (\Exception $e) {
            Log::error('Create view error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur de chargement: ' . $e->getMessage());
        }
    }

    /**
     * Modifier un agent - PAGE
     */
    public function edit($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->route('users.index')->with('error', 'Agent non trouvé');
            }

            if (!in_array($user->type_user, ['admin', 'agent_comptoir', 'commercial'])) {
                return redirect()->route('users.index')->with('error', 'Type d\'utilisateur non valide');
            }

            // ✅ RÉCUPÉRER LE PREMIER RÔLE SANS DOUBLONS
            $userRoles = $this->getUserRolesUnique($user->id);
            $user_role = null;
            
            if (!empty($userRoles)) {
                $user_role = Role::where('name', $userRoles[0])->first();
            }

            $data = [
                'user' => $user,
                'user_role' => $user_role,
                'roles' => $this->getSafeRoles(),
                'dataCategories' => $this->getSafeCategories(),
                'dataGrades' => $this->getSafeGrades(),
            ];

            return view('admin.users.user-edit', $data);

        } catch (\Exception $e) {
            Log::error('Edit error: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Erreur de chargement: ' . $e->getMessage());
        }
    }

    // ==================== MÉTHODES PRIVÉES UTILITAIRES ====================

    /**
     * Vérifier l'accès utilisateur
     */
    private function hasAccess($user): bool
    {
        try {
            return $user->hasAnyRole(['Admin', 'Super Admin']) || $user->type_user === 'admin';
        } catch (\Exception $e) {
            return $user->type_user === 'admin';
        }
    }

    /**
     * Réponse d'erreur pour DataTables
     */
    private function errorResponse(string $message, Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $message
        ]);
    }

    /**
     * ✅ CORRECTION : Obtenir les rôles de manière robuste SANS DOUBLONS
     */
    private function getSafeRoles()
    {
        try {
            if (!Schema::hasTable('roles')) {
                return $this->createDefaultRoles();
            }

            // ✅ RÉCUPÉRER UNIQUEMENT LES RÔLES DISTINCTS
            $roles = Role::whereIn('name', ['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial'])
                ->select('id', 'name')
                ->distinct()
                ->orderBy('name', 'asc')
                ->get();

            if ($roles->isEmpty()) {
                return $this->createDefaultRoles();
            }

            return $roles;

        } catch (\Exception $e) {
            Log::error('Erreur getSafeRoles: ' . $e->getMessage());
            return $this->createDefaultRoles();
        }
    }

    /**
     * Créer des rôles par défaut
     */
    private function createDefaultRoles()
    {
        try {
            if (!Schema::hasTable('roles')) {
                return collect();
            }

            $defaultRoles = ['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'];
            $roles = collect();

            foreach ($defaultRoles as $roleName) {
                try {
                    $role = Role::firstOrCreate(['name' => $roleName], ['guard_name' => 'web']);
                    $roles->push($role);
                } catch (\Exception $e) {
                    Log::error("Erreur création rôle {$roleName}: " . $e->getMessage());
                }
            }

            return $roles;

        } catch (\Exception $e) {
            Log::error('Erreur createDefaultRoles: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Obtenir les catégories de manière sécurisée
     */
    private function getSafeCategories()
    {
        try {
            if (Schema::hasTable('categories')) {
                return Categories::where('etat', 1)->orderBy('libelle')->get();
            }
        } catch (\Exception $e) {
            Log::warning('Erreur catégories: ' . $e->getMessage());
        }
        return collect();
    }

    /**
     * Obtenir les grades de manière sécurisée
     */
    private function getSafeGrades()
    {
        try {
            if (Schema::hasTable('grades')) {
                return Grades::where('etat', 1)->orderBy('libelle')->get();
            }
        } catch (\Exception $e) {
            Log::warning('Erreur grades: ' . $e->getMessage());
        }
        return collect();
    }

    /**
     * Générer un matricule
     */
    private function generateMatricule(string $typeUser): string
    {
        $prefix = match($typeUser) {
            'admin' => 'ADM',
            'agent_comptoir' => 'CPT',
            'commercial' => 'COM',
            default => 'USR'
        };

        try {
            $lastMatricule = User::where('matricule', 'like', $prefix . '%')
                ->orderBy('matricule', 'desc')
                ->first();

            $nextNumber = $lastMatricule ? 
                ((int) substr($lastMatricule->matricule, 3)) + 1 : 1;

            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        } catch (\Exception $e) {
            return $prefix . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Gérer l'upload de photo
     */
    private function handlePhotoUpload(Request $request, User $user): void
    {
        try {
            if (!$request->hasFile('photo_user') || !Schema::hasColumn('users', 'photo_user')) {
                return;
            }

            $image = $request->file('photo_user');
            
            if (!$image->isValid()) {
                return;
            }

            $matricule = $user->matricule ?? $user->id;
            $filename = time() . '_' . $matricule . '_photo.' . $image->getClientOriginalExtension();
            
            $uploadPath = public_path('upload/users');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Supprimer l'ancienne photo
            if ($user->photo_user && $user->photo_user != 'NULL') {
                $oldPhotoPath = public_path('upload/users/' . $user->photo_user);
                if (file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            }
            
            $image->move($uploadPath, $filename);
            $user->update(['photo_user' => $filename]);
            
        } catch (\Exception $e) {
            Log::error('Photo upload error: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir le libellé du type d'utilisateur
     */
    private function getTypeUserLabel(string $typeUser): string
    {
        return match($typeUser) {
            'admin' => 'Administrateur',
            'agent_comptoir' => 'Agent Comptoir',
            'commercial' => 'Commercial',
            default => 'Non défini'
        };
    }

    /**
     * Obtenir la couleur du rôle
     */
    private function getRoleColor(string $roleName): string
    {
        return match($roleName) {
            'Super Admin' => 'dark',
            'Admin' => 'danger',
            'Agent Comptoir' => 'info',
            'Commercial' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Obtenir la couleur par type d'utilisateur
     */
    private function getRoleColorByType(string $typeUser): string
    {
        return match($typeUser) {
            'admin' => 'danger',
            'agent_comptoir' => 'info',
            'commercial' => 'success',
            default => 'secondary'
        };
    }

    // ==================== MÉTHODES DE FORMATAGE DATATABLE ====================

    private function formatAgentInfo($agent): string
    {
        $avatar = $this->getAvatar($agent);
        
        return "
            <div class='d-flex align-items-center'>
                {$avatar}
                <div>
                    <div class='fw-bold'>" . e($agent->name) . "</div>
                    <small class='text-muted'>" . e($agent->email) . "</small>
                </div>
            </div>
        ";
    }

    private function getAvatar($agent): string
    {
        if (isset($agent->photo_user) && $agent->photo_user && $agent->photo_user != 'NULL') {
            $photoUrl = asset('upload/users/' . $agent->photo_user);
            return "<img src='{$photoUrl}' class='rounded-circle me-2' style='width: 32px; height: 32px; object-fit: cover;' alt='Photo'>";
        }
        
        $initials = strtoupper(substr($agent->name, 0, 2));
        return "<div class='rounded-circle bg-primary text-white text-center me-2 d-inline-flex align-items-center justify-content-center' style='width: 32px; height: 32px; font-size: 0.8rem; font-weight: bold;'>{$initials}</div>";
    }

    private function formatContactInfo($agent): string
    {
        $info = '';
        if (isset($agent->contact) && $agent->contact) {
            $info .= "<small class='text-muted d-block'><i class='fas fa-phone'></i> " . e($agent->contact) . "</small>";
        }
        if (isset($agent->matricule) && $agent->matricule) {
            $info .= "<small class='text-muted d-block'><i class='fas fa-id-card'></i> " . e($agent->matricule) . "</small>";
        }
        return $info ?: '<small class="text-muted">Non défini</small>';
    }

    private function formatTypeUserBadge($agent): string
    {
        $colors = [
            'admin' => 'danger',
            'agent_comptoir' => 'info', 
            'commercial' => 'success'
        ];
        $labels = [
            'admin' => 'Administrateur',
            'agent_comptoir' => 'Agent Comptoir',
            'commercial' => 'Commercial'
        ];
        
        $color = $colors[$agent->type_user] ?? 'secondary';
        $label = $labels[$agent->type_user] ?? ucfirst($agent->type_user);
        
        return "<span class='badge bg-{$color}'>" . e($label) . "</span>";
    }

    private function formatStatus($agent): string
    {
        $etat = $agent->etat ?? 1;
        $color = $etat == 1 ? 'success' : 'danger';
        $label = $etat == 1 ? 'Actif' : 'Inactif';
        
        return "<span class='badge bg-{$color}'>" . e($label) . "</span>";
    }

    private function formatGradeCategorie($agent): string
    {
        $info = '';
        
        try {
            if (isset($agent->id_grade) && $agent->id_grade) {
                $grade = Grades::find($agent->id_grade);
                if ($grade) {
                    $info .= "<small class='text-primary d-block'><i class='fas fa-award'></i> " . e($grade->libelle) . "</small>";
                }
            }
            
            if (isset($agent->id_categorie) && $agent->id_categorie) {
                $categorie = Categories::find($agent->id_categorie);
                if ($categorie) {
                    $info .= "<small class='text-info d-block'><i class='fas fa-tag'></i> " . e($categorie->libelle) . "</small>";
                }
            }
        } catch (\Exception $e) {
            // Ignorer l'erreur
        }
        
        return $info ?: '<small class="text-muted">Non défini</small>';
    }

    private function formatActions($agent): string
    {
        // Protéger certains utilisateurs
        if (in_array($agent->email, ['admin@psiafrica.ci', 'superadmin@psiafrica.ci'])) {
            return '<span class="text-muted small"><i class="fas fa-shield-alt"></i> Protégé</span>';
        }

        $etat = $agent->etat ?? 1;
        
        return "
            <div class='btn-group' role='group'>
                <button class='btn btn-sm btn-outline-primary' onclick='editAgent({$agent->id})' title='Modifier'>
                    <i class='fas fa-edit'></i>
                </button>
                <button class='btn btn-sm btn-outline-info' onclick='viewAgent({$agent->id})' title='Voir détails'>
                    <i class='fas fa-eye'></i>
                </button>
                <button class='btn btn-sm btn-outline-warning' onclick='resetPassword({$agent->id})' title='Réinitialiser mot de passe'>
                    <i class='fas fa-key'></i>
                </button>
                <button class='btn btn-sm btn-outline-" . ($etat == 1 ? 'warning' : 'success') . "' onclick='toggleStatus({$agent->id}, " . ($etat == 1 ? '0' : '1') . ")' title='" . ($etat == 1 ? 'Désactiver' : 'Activer') . "'>
                    <i class='fas fa-" . ($etat == 1 ? 'pause' : 'play') . "'></i>
                </button>
                <button class='btn btn-sm btn-outline-danger' onclick='deleteAgent({$agent->id})' title='Supprimer'>
                    <i class='fas fa-trash'></i>
                </button>
            </div>
        ";
    }

    // ==================== MÉTHODES PUBLIQUES CONSERVÉES ====================

    /**
     * Supprimer un agent
     */
    public function delete($id): RedirectResponse
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return redirect()->route('users.index')->with('error', 'Agent non trouvé');
            }

            // Protéger certains utilisateurs
            if (in_array($user->email, ['admin@psiafrica.ci', 'superadmin@psiafrica.ci'])) {
                return redirect()->route('users.index')->with('error', 'Utilisateur protégé - suppression interdite');
            }

            if (!in_array($user->type_user, ['admin', 'agent_comptoir', 'commercial'])) {
                return redirect()->route('users.index')->with('error', 'Action non autorisée');
            }

            // ✅ NETTOYER TOUS LES RÔLES AVANT SUPPRESSION
            DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\\Models\\User')
                ->delete();

            // Supprimer la photo
            if (Schema::hasColumn('users', 'photo_user') && $user->photo_user && $user->photo_user != 'NULL') {
                $photoPath = public_path('upload/users/' . $user->photo_user);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $userName = $user->name;
            $user->delete();

            return redirect()->route('users.index')->with('success', "Agent {$userName} supprimé avec succès!");

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression agent: ' . $e->getMessage());
            return redirect()->route('users.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut (AJAX)
     */
    public function editetat(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'etat' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
            }

            $user = User::find($request->id);
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Agent non trouvé']);
            }

            // Protéger certains utilisateurs
            if (in_array($user->email, ['admin@psiafrica.ci', 'superadmin@psiafrica.ci'])) {
                return response()->json(['success' => false, 'error' => 'Utilisateur protégé']);
            }

            $updateData = ['etat' => $request->etat, 'update_user' => Auth::id()];
            
            if (Schema::hasColumn('users', 'statut_emploi')) {
                $updateData['statut_emploi'] = $request->etat == 1 ? 'actif' : 'suspendu';
            }

            $user->update($updateData);

            $action = $request->etat == 1 ? 'activé' : 'désactivé';
            
            return response()->json([
                'success' => true,
                'message' => "Agent {$action} avec succès"
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Statistiques des agents
     */
    public function getStatistics()
    {
        try {
            $baseQuery = User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial']);
            
            if (Schema::hasColumn('users', 'ent1d')) {
                $baseQuery->where('ent1d', 1);
            }

            $stats = [
                'total_agents' => (clone $baseQuery)->count(),
                'admins' => User::where('type_user', 'admin')
                    ->when(Schema::hasColumn('users', 'ent1d'), function($q) {
                        return $q->where('ent1d', 1);
                    })->count(),
                'agents_comptoir' => User::where('type_user', 'agent_comptoir')
                    ->when(Schema::hasColumn('users', 'ent1d'), function($q) {
                        return $q->where('ent1d', 1);
                    })->count(),
                'commerciaux' => User::where('type_user', 'commercial')
                    ->when(Schema::hasColumn('users', 'ent1d'), function($q) {
                        return $q->where('ent1d', 1);
                    })->count(),
            ];

            if (Schema::hasColumn('users', 'etat')) {
                $agentsActifsQuery = clone $baseQuery;
                $agentsActifsQuery->where('etat', 1);
                $stats['agents_actifs'] = $agentsActifsQuery->count();
                
                $agentsInactifsQuery = clone $baseQuery;
                $agentsInactifsQuery->where('etat', 0);
                $stats['agents_inactifs'] = $agentsInactifsQuery->count();
            } else {
                $stats['agents_actifs'] = $stats['total_agents'];
                $stats['agents_inactifs'] = 0;
            }

            $stats['nouveaux_ce_mois'] = (clone $baseQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'total_agents' => 0,
                'admins' => 0,
                'agents_comptoir' => 0,
                'commerciaux' => 0,
                'agents_actifs' => 0,
                'agents_inactifs' => 0,
                'nouveaux_ce_mois' => 0,
                'error' => true,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Détails d'un agent (AJAX)
     */
    public function getUserDetails($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Agent non trouvé']);
            }

            if (!in_array($user->type_user, ['admin', 'agent_comptoir', 'commercial'])) {
                return response()->json(['success' => false, 'error' => 'Action non autorisée']);
            }

            // ✅ RÉCUPÉRER LES RÔLES SANS DOUBLONS
            $uniqueRoles = $this->getUserRolesUnique($user->id);

            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type_user' => $user->type_user,
                'type_user_label' => $this->getTypeUserLabel($user->type_user),
                'matricule' => $user->matricule ?? 'N/A',
                'contact' => $user->contact ?? 'N/A',
                'etat' => $user->etat ?? 1,
                'statut_emploi' => $user->statut_emploi ?? 'actif',
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'roles' => $uniqueRoles, // ✅ RÔLES SANS DOUBLONS
            ];

            // ✅ AJOUTER LES PERMISSIONS SANS DOUBLONS
            try {
                $userData['permissions'] = $this->getUserPermissionsUnique($user->id);
            } catch (\Exception $e) {
                $userData['permissions'] = [];
            }

            return response()->json([
                'success' => true,
                'user' => $userData
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    /**
     * Réinitialiser mot de passe (AJAX)
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:users,id',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
            }

            $user = User::find($request->id);
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Agent non trouvé']);
            }

            // Protéger certains utilisateurs
            if (in_array($user->email, ['admin@psiafrica.ci', 'superadmin@psiafrica.ci'])) {
                return response()->json(['success' => false, 'error' => 'Utilisateur protégé']);
            }

            $user->update([
                'password' => Hash::make($request->password),
                'update_user' => Auth::id()
            ]);

            return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Erreur: ' . $e->getMessage()]);
        }
    }
}