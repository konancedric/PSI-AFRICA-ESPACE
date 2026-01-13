<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CaisseEntree;
use App\Models\CaisseSortie;
use App\Models\CaisseCloture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClotureMensuelle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caisse:cloture-mensuelle {--force : Forcer la clôture même si le mois n\'est pas terminé}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Effectue la clôture mensuelle de la caisse et archive les données du mois précédent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');

        // Vérifier si nous sommes le 1er du mois ou si on force
        $aujourdhui = Carbon::now();
        if (!$force && $aujourdhui->day != 1) {
            $this->error('La clôture mensuelle ne peut être effectuée que le 1er du mois.');
            $this->info('Utilisez --force pour forcer la clôture.');
            return 1;
        }

        // Le mois à clôturer est le mois précédent
        $moisACloture = $force ? $aujourdhui : $aujourdhui->copy()->subMonth();
        $moisLabel = $moisACloture->locale('fr')->isoFormat('MMMM YYYY');

        $this->info("Démarrage de la clôture mensuelle pour : {$moisLabel}");

        try {
            DB::beginTransaction();

            // Vérifier s'il y a des données non clôturées
            $entreesNonCloturees = CaisseEntree::moisActif()->count();
            $sortiesNonCloturees = CaisseSortie::moisActif()->count();

            if ($entreesNonCloturees === 0 && $sortiesNonCloturees === 0) {
                $this->warn('Aucune donnée à clôturer pour ce mois.');
                DB::rollBack();
                return 0;
            }

            $this->info("Données trouvées : {$entreesNonCloturees} entrées, {$sortiesNonCloturees} sorties");

            // Calculer les totaux
            $totalEntrees = CaisseEntree::moisActif()->sum('montant');
            $totalSorties = CaisseSortie::moisActif()->sum('montant');
            $solde = $totalEntrees - $totalSorties;

            // Calculer les marges par catégorie
            $margeCabinet = CaisseEntree::moisActif()
                ->where('categorie', 'Frais de Cabinet')
                ->sum('montant');

            $totalDocs = CaisseEntree::moisActif()
                ->where('categorie', 'Documents de Voyage')
                ->sum('montant');

            $verseTiers = CaisseEntree::moisActif()
                ->where('categorie', 'Documents de Voyage')
                ->sum('montant_verse_tiers');

            $margeDocs = $totalDocs - $verseTiers;

            // Calculer la dîme (10% du solde positif)
            $dime = $solde > 0 ? $solde * 0.10 : 0;

            // Créer l'enregistrement de clôture
            $cloture = CaisseCloture::create([
                'uuid' => (string) Str::uuid(),
                'mois' => $moisACloture->format('Y-m'),
                'date' => Carbon::now(),
                'total_entrees' => $totalEntrees,
                'total_sorties' => $totalSorties,
                'solde' => $solde,
                'marge_cabinet' => $margeCabinet,
                'total_cabinet' => $margeCabinet,
                'marge_docs' => $margeDocs,
                'total_docs' => $totalDocs,
                'verse_tiers' => $verseTiers,
                'dime' => $dime,
                'cloture' => true,
                'nb_entrees' => $entreesNonCloturees,
                'nb_sorties' => $sortiesNonCloturees,
                'remarques' => "Clôture automatique du {$aujourdhui->format('d/m/Y à H:i')}",
                'created_by_user_id' => 1, // ID de l'admin système
                'created_by_username' => 'SYSTEM'
            ]);

            $this->info("Clôture créée avec ID : {$cloture->id}");

            // Marquer toutes les entrées et sorties avec le cloture_id
            $entreesUpdated = CaisseEntree::moisActif()->update(['cloture_id' => $cloture->id]);
            $sortiesUpdated = CaisseSortie::moisActif()->update(['cloture_id' => $cloture->id]);

            $this->info("Entrées clôturées : {$entreesUpdated}");
            $this->info("Sorties clôturées : {$sortiesUpdated}");

            DB::commit();

            // Afficher le résumé
            $this->newLine();
            $this->info('╔═══════════════════════════════════════════════════╗');
            $this->info('║     CLÔTURE MENSUELLE EFFECTUÉE AVEC SUCCÈS      ║');
            $this->info('╠═══════════════════════════════════════════════════╣');
            $this->line("║ Mois                : {$moisLabel}");
            $this->line("║ Total entrées       : " . number_format($totalEntrees, 0, ',', ' ') . " FCFA");
            $this->line("║ Total sorties       : " . number_format($totalSorties, 0, ',', ' ') . " FCFA");
            $this->line("║ Solde               : " . number_format($solde, 0, ',', ' ') . " FCFA");
            $this->line("║ Marge cabinet       : " . number_format($margeCabinet, 0, ',', ' ') . " FCFA");
            $this->line("║ Marge documents     : " . number_format($margeDocs, 0, ',', ' ') . " FCFA");
            $this->line("║ Dîme (10%)          : " . number_format($dime, 0, ',', ' ') . " FCFA");
            $this->info('╚═══════════════════════════════════════════════════╝');
            $this->newLine();

            $this->info('Les données du nouveau mois peuvent maintenant être saisies.');

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Erreur lors de la clôture mensuelle :');
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
