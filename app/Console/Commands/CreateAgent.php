<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAgent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:create 
                           {name : Nom complet de l\'agent}
                           {email : Adresse email de l\'agent}
                           {type : Type d\'agent (agent_comptoir|commercial)}
                           {--password= : Mot de passe (généré automatiquement si non fourni)}
                           {--contact= : Numéro de téléphone}
                           {--matricule= : Matricule personnalisé (généré automatiquement si non fourni)}
                           {--date-embauche= : Date d\'embauche (format Y-m-d)}
                           {--salaire= : Salaire en FCFA}
                           {--adresse= : Adresse de l\'agent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer un nouvel agent interne (comptoir ou commercial)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Récupérer les arguments et options
        $name = $this->argument('name');
        $email = $this->argument('email');
        $type = $this->argument('type');
        $password = $this->option('password') ?: $this->generatePassword();
        $contact = $this->option('contact');
        $matricule = $this->option('matricule');
        $dateEmbauche = $this->option('date-embauche') ?: now()->toDateString();
        $salaire = $this->option('salaire');
        $adresse = $this->option('adresse');

        // Validation des entrées
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'type' => $type,
            'password' => $password,
            'date_embauche' => $dateEmbauche,
            'salaire' => $salaire,
        ], [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'type' => 'required|in:agent_comptoir,commercial',
            'password' => 'required|string|min:6',
            'date_embauche' => 'required|date',
            'salaire' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            $this->error('Erreurs de validation :');
            foreach ($validator->errors()->all() as $error) {
                $this->line('  - ' . $error);
            }
            return 1;
        }

        // Générer le matricule si non fourni
        if (!$matricule) {
            $matricule = $this->generateMatricule($type);
        } else {
            // Vérifier que le matricule n'existe pas déjà
            if (User::where('matricule', $matricule)->exists()) {
                $this->error('Ce matricule existe déjà. Utilisez un autre matricule ou laissez vide pour génération automatique.');
                return 1;
            }
        }

        $this->info('Création de l\'agent en cours...');

        try {
            // Créer l'agent
            $agent = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'matricule' => $matricule,
                'contact' => $contact,
                'type_user' => $type,
                'date_embauche' => $dateEmbauche,
                'salaire' => $salaire,
                'adresse' => $adresse,
                'etat' => 1,
                'statut_emploi' => 'actif',
                'ent1d' => 1,
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle approprié
            $roleName = $type === 'agent_comptoir' ? 'Agent Comptoir' : 'Commercial';
            $role = Role::where('name', $roleName)->first();
            
            if ($role) {
                $agent->assignRole($role);
                $this->info("Rôle '{$roleName}' assigné avec succès.");
            } else {
                $this->warn("Attention: Le rôle '{$roleName}' n'existe pas. Créez-le avec php artisan db:seed --class=RolesPermissionsAgentsSeeder");
            }

            // Afficher les informations de l'agent créé
            $this->displayAgentInfo($agent, $password);

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la création de l\'agent : ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Générer un matricule automatique
     */
    private function generateMatricule($type): string
    {
        $prefix = $type === 'agent_comptoir' ? 'CPT' : 'COM';
        
        $lastAgent = User::where('matricule', 'like', $prefix . '%')
            ->orderBy('matricule', 'desc')
            ->first();

        if ($lastAgent) {
            $lastNumber = (int) substr($lastAgent->matricule, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Générer un mot de passe sécurisé
     */
    private function generatePassword(): string
    {
        $length = 12;
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Afficher les informations de l'agent créé
     */
    private function displayAgentInfo($agent, $password): void
    {
        $this->info('');
        $this->info('✅ Agent créé avec succès !');
        $this->info('════════════════════════════════════════');
        
        $this->table(
            ['Propriété', 'Valeur'],
            [
                ['Nom', $agent->name],
                ['Email', $agent->email],
                ['Matricule', $agent->matricule],
                ['Type', $agent->type_user_label],
                ['Contact', $agent->contact ?: 'Non renseigné'],
                ['Date d\'embauche', $agent->date_embauche->format('d/m/Y')],
                ['Salaire', $agent->salaire ? number_format($agent->salaire) . ' FCFA' : 'Non renseigné'],
                ['Statut', $agent->statut_emploi_label],
                ['Mot de passe', $password],
            ]
        );

        $this->info('');
        $this->info('🔗 Liens utiles :');
        
        if ($agent->type_user === 'agent_comptoir') {
            $this->line('  - Tableau de bord : ' . url('/comptoir/dashboard'));
            $this->line('  - Gestion profils visa : ' . url('/profil-visa'));
        } else {
            $this->line('  - Tableau de bord : ' . url('/commercial/dashboard'));
            $this->line('  - Gestion clients : ' . url('/commercial/clients'));
            $this->line('  - Gestion forfaits : ' . url('/forfaits'));
        }

        $this->info('');
        $this->warn('⚠️  Important : Communiquez ces informations de connexion à l\'agent de manière sécurisée.');
        $this->warn('   Demandez-lui de changer son mot de passe lors de la première connexion.');
    }
}