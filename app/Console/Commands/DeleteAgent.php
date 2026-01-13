<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeleteAgent extends Command
{
    protected $signature = 'agent:delete 
                           {identifier : Email ou matricule de l\'agent}
                           {--force : Forcer la suppression sans confirmation}';

    protected $description = 'Supprimer un agent interne';

    public function handle()
    {
        $identifier = $this->argument('identifier');
        
        // Chercher l'agent
        $agent = User::where('email', $identifier)
            ->orWhere('matricule', $identifier)
            ->whereIn('type_user', ['agent_comptoir', 'commercial'])
            ->first();

        if (!$agent) {
            $this->error('Agent non trouvé avec l\'identifiant : ' . $identifier);
            return 1;
        }

        // Protection contre la suppression de l'admin
        if ($agent->type_user === 'admin' || $agent->email === 'admin@psiafrica.ci') {
            $this->error('Impossible de supprimer le compte administrateur.');
            return 1;
        }

        $this->info("Agent trouvé : {$agent->name} ({$agent->email})");

        // Demander confirmation si --force n'est pas utilisé
        if (!$this->option('force')) {
            if (!$this->confirm('Êtes-vous sûr de vouloir supprimer cet agent ? Cette action est irréversible.')) {
                $this->info('Suppression annulée.');
                return 0;
            }
        }

        try {
            // Supprimer la photo si elle existe
            if ($agent->photo_user && $agent->photo_user != 'NULL') {
                $photoPath = public_path('/upload/users/' . $agent->photo_user);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }

            $agentName = $agent->name;
            $agent->delete();

            $this->info("✅ Agent '{$agentName}' supprimé avec succès.");

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la suppression : ' . $e->getMessage());
            return 1;
        }
    }
}