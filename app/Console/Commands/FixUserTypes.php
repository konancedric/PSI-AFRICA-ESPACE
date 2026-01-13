<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class FixUserTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger les types d\'utilisateurs - Séparer agents internes et utilisateurs publics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔧 Correction des types d\'utilisateurs...');
        
        try {
            // 1. Corriger les utilisateurs sans type_user ou avec type_user vide
            $usersWithoutType = User::where(function($query) {
                $query->whereNull('type_user')
                      ->orWhere('type_user', '')
                      ->orWhere('type_user', 'NULL');
            })->get();

            $this->info("📝 Trouvé {$usersWithoutType->count()} utilisateur(s) sans type défini");

            foreach ($usersWithoutType as $user) {
                // Vérifier si c'est un agent interne par les rôles
                $roles = $user->getRoleNames()->toArray();
                $isInternalAgent = in_array('Admin', $roles) || 
                                  in_array('Agent Comptoir', $roles) || 
                                  in_array('Commercial', $roles);

                if ($isInternalAgent) {
                    // Déterminer le type selon le rôle
                    if (in_array('Admin', $roles)) {
                        $user->type_user = 'admin';
                    } elseif (in_array('Agent Comptoir', $roles)) {
                        $user->type_user = 'agent_comptoir';
                    } elseif (in_array('Commercial', $roles)) {
                        $user->type_user = 'commercial';
                    }
                    
                    $this->line("🔧 Agent interne identifié: {$user->name} -> {$user->type_user}");
                } else {
                    // C'est un utilisateur public
                    $user->type_user = 'public';
                    $this->line("👤 Utilisateur public: {$user->name} -> public");
                }

                $user->save();
            }

            // 2. Corriger les utilisateurs avec des types incorrects
            $usersWithWrongType = User::whereNotIn('type_user', ['admin', 'agent_comptoir', 'commercial', 'public'])->get();
            
            $this->info("🔍 Trouvé {$usersWithWrongType->count()} utilisateur(s) avec type incorrect");

            foreach ($usersWithWrongType as $user) {
                $roles = $user->getRoleNames()->toArray();
                $isInternalAgent = in_array('Admin', $roles) || 
                                  in_array('Agent Comptoir', $roles) || 
                                  in_array('Commercial', $roles);

                $oldType = $user->type_user;

                if ($isInternalAgent) {
                    // Déterminer le type selon le rôle
                    if (in_array('Admin', $roles)) {
                        $user->type_user = 'admin';
                    } elseif (in_array('Agent Comptoir', $roles)) {
                        $user->type_user = 'agent_comptoir';
                    } elseif (in_array('Commercial', $roles)) {
                        $user->type_user = 'commercial';
                    }
                } else {
                    $user->type_user = 'public';
                }

                $this->line("🔄 Correction: {$user->name} | {$oldType} -> {$user->type_user}");
                $user->save();
            }

            // 3. Statistiques finales
            $this->info("\n📊 Statistiques après correction:");
            
            $stats = [
                'Administrateurs' => User::where('type_user', 'admin')->count(),
                'Agents Comptoir' => User::where('type_user', 'agent_comptoir')->count(),
                'Commerciaux' => User::where('type_user', 'commercial')->count(),
                'Utilisateurs Publics' => User::where('type_user', 'public')->count(),
                'Total' => User::count(),
            ];

            foreach ($stats as $type => $count) {
                $this->line("  - {$type}: {$count}");
            }

            // 4. Vérifier les incohérences
            $this->info("\n🔍 Vérification des incohérences...");
            
            $adminsWithoutRole = User::where('type_user', 'admin')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'Admin');
                })->count();

            $agentsComptoirWithoutRole = User::where('type_user', 'agent_comptoir')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'Agent Comptoir');
                })->count();

            $commerciauxWithoutRole = User::where('type_user', 'commercial')
                ->whereDoesntHave('roles', function($query) {
                    $query->where('name', 'Commercial');
                })->count();

            if ($adminsWithoutRole > 0) {
                $this->warn("⚠️  {$adminsWithoutRole} admin(s) sans rôle Admin");
            }
            if ($agentsComptoirWithoutRole > 0) {
                $this->warn("⚠️  {$agentsComptoirWithoutRole} agent(s) comptoir sans rôle Agent Comptoir");
            }
            if ($commerciauxWithoutRole > 0) {
                $this->warn("⚠️  {$commerciauxWithoutRole} commercial/commerciaux sans rôle Commercial");
            }

            $this->info("\n✅ Correction terminée avec succès !");
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la correction: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}