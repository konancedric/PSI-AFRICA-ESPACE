<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetAgentPassword extends Command
{
    protected $signature = 'agent:reset-password 
                           {identifier : Email ou matricule de l\'agent}
                           {--password= : Nouveau mot de passe (généré automatiquement si non fourni)}';

    protected $description = 'Réinitialiser le mot de passe d\'un agent';

    public function handle()
    {
        $identifier = $this->argument('identifier');
        $password = $this->option('password') ?: $this->generatePassword();
        
        // Chercher l'agent
        $agent = User::where('email', $identifier)
            ->orWhere('matricule', $identifier)
            ->whereIn('type_user', ['agent_comptoir', 'commercial', 'admin'])
            ->first();

        if (!$agent) {
            $this->error('Agent non trouvé avec l\'identifiant : ' . $identifier);
            return 1;
        }

        try {
            $agent->password = Hash::make($password);
            $agent->save();

            $this->info('✅ Mot de passe réinitialisé avec succès !');
            $this->info("Agent : {$agent->name} ({$agent->email})");
            $this->info("Nouveau mot de passe : {$password}");
            $this->warn('⚠️  Communiquez ce mot de passe de manière sécurisée.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la réinitialisation : ' . $e->getMessage());
            return 1;
        }
    }

    private function generatePassword(): string
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 12);
    }
}