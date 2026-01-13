<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UpdateAgent extends Command
{
    protected $signature = 'agent:update 
                           {identifier : Email ou matricule de l\'agent}
                           {--name= : Nouveau nom}
                           {--email= : Nouvel email}
                           {--contact= : Nouveau contact}
                           {--status= : Nouveau statut (actif|suspendu|conge|demission)}
                           {--salaire= : Nouveau salaire}
                           {--password= : Nouveau mot de passe}';

    protected $description = 'Modifier les informations d\'un agent';

    public function handle()
    {
        $identifier = $this->argument('identifier');
        
        // Chercher l'agent par email ou matricule
        $agent = User::where('email', $identifier)
            ->orWhere('matricule', $identifier)
            ->whereIn('type_user', ['agent_comptoir', 'commercial', 'admin'])
            ->first();

        if (!$agent) {
            $this->error('Agent non trouvé avec l\'identifiant : ' . $identifier);
            return 1;
        }

        $this->info("Agent trouvé : {$agent->name} ({$agent->email})");

        $updated = false;
        $changes = [];

        // Mettre à jour les champs spécifiés
        if ($name = $this->option('name')) {
            $agent->name = $name;
            $changes[] = "Nom: {$name}";
            $updated = true;
        }

        if ($email = $this->option('email')) {
            if (User::where('email', $email)->where('id', '!=', $agent->id)->exists()) {
                $this->error('Cet email est déjà utilisé par un autre utilisateur.');
                return 1;
            }
            $agent->email = $email;
            $changes[] = "Email: {$email}";
            $updated = true;
        }

        if ($contact = $this->option('contact')) {
            $agent->contact = $contact;
            $changes[] = "Contact: {$contact}";
            $updated = true;
        }

        if ($status = $this->option('status')) {
            if (!in_array($status, ['actif', 'suspendu', 'conge', 'demission'])) {
                $this->error('Statut invalide. Utilisez : actif, suspendu, conge, ou demission');
                return 1;
            }
            $agent->statut_emploi = $status;
            $agent->etat = ($status === 'actif') ? 1 : 0;
            $changes[] = "Statut: {$status}";
            $updated = true;
        }

        if ($salaire = $this->option('salaire')) {
            $agent->salaire = $salaire;
            $changes[] = "Salaire: " . number_format($salaire) . " FCFA";
            $updated = true;
        }

        if ($password = $this->option('password')) {
            $agent->password = Hash::make($password);
            $changes[] = "Mot de passe: [MODIFIÉ]";
            $updated = true;
        }

        if (!$updated) {
            $this->warn('Aucune modification spécifiée.');
            return 0;
        }

        try {
            $agent->save();
            
            $this->info('✅ Agent mis à jour avec succès !');
            $this->info('Modifications apportées :');
            foreach ($changes as $change) {
                $this->line('  - ' . $change);
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('Erreur lors de la mise à jour : ' . $e->getMessage());
            return 1;
        }
    }
}
