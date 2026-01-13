<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ListAgents extends Command
{
    protected $signature = 'agent:list 
                           {--type= : Filtrer par type (agent_comptoir|commercial)}
                           {--status= : Filtrer par statut (actif|suspendu|conge|demission)}
                           {--format=table : Format de sortie (table|json)}';

    protected $description = 'Lister tous les agents internes';

    public function handle()
    {
        $type = $this->option('type');
        $status = $this->option('status');
        $format = $this->option('format');

        // Construire la requête
        $query = User::whereIn('type_user', ['agent_comptoir', 'commercial', 'admin'])
            ->with('roles');

        if ($type) {
            $query->where('type_user', $type);
        }

        if ($status) {
            $query->where('statut_emploi', $status);
        }

        $agents = $query->orderBy('name')->get();

        if ($agents->isEmpty()) {
            $this->warn('Aucun agent trouvé avec les critères spécifiés.');
            return 0;
        }

        if ($format === 'json') {
            $this->line(json_encode($agents->toArray(), JSON_PRETTY_PRINT));
            return 0;
        }

        // Affichage en tableau
        $this->info("📋 Liste des agents ({$agents->count()} trouvé(s))");
        
        $tableData = $agents->map(function ($agent) {
            return [
                $agent->matricule,
                $agent->name,
                $agent->email,
                $agent->type_user_label,
                $agent->getRoleNames()->first() ?? 'Aucun',
                $agent->statut_emploi_label,
                $agent->created_at->format('d/m/Y'),
            ];
        })->toArray();

        $this->table(
            ['Matricule', 'Nom', 'Email', 'Type', 'Rôle', 'Statut', 'Créé le'],
            $tableData
        );

        return 0;
    }
}