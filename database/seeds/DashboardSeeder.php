<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ProfilVisa;
use App\Models\StatutsEtat;
use App\Models\AddMessageProfilVisa;
use App\Models\Categories;
use App\Models\Grades;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "🚀 Démarrage de la création des données de test...\n";
        
        // Créer d'abord les dépendances
        $this->createCategories();
        $this->createGrades();
        $this->createStatuts();
        
        // Créer des utilisateurs de test (publics et agents)
        $users = $this->createTestUsers();
        
        // Créer des profils visa avec des dates variées
        $this->createProfilsVisa($users);
        
        // Créer des messages pour certains profils
        $this->createMessages();
        
        // Créer des statistiques réalistes
        $this->createRealisticStats();
        
        echo "✅ Données de test créées avec succès!\n";
        echo "📊 Statistiques finales :\n";
        echo "   - Utilisateurs totaux : " . User::count() . "\n";
        echo "   - Agents internes : " . User::whereIn('type_user', ['admin', 'agent_comptoir', 'commercial'])->count() . "\n";
        echo "   - Utilisateurs publics : " . User::where('type_user', 'public')->count() . "\n";
        echo "   - Profils visa : " . ProfilVisa::count() . "\n";
        echo "   - Messages : " . AddMessageProfilVisa::count() . "\n";
    }

    /**
     * Créer les catégories par défaut
     */
    private function createCategories()
    {
        echo "📂 Création des catégories...\n";
        
        $categories = [
            ['libelle' => 'Tourisme', 'description' => 'Voyages touristiques', 'etat' => 1],
            ['libelle' => 'Affaires', 'description' => 'Voyages d\'affaires', 'etat' => 1],
            ['libelle' => 'Étudiant', 'description' => 'Voyages d\'études', 'etat' => 1],
            ['libelle' => 'Famille', 'description' => 'Voyages familiaux', 'etat' => 1],
            ['libelle' => 'Transit', 'description' => 'Voyages de transit', 'etat' => 1],
        ];

        foreach ($categories as $category) {
            Categories::firstOrCreate(['libelle' => $category['libelle']], $category);
        }
        
        echo "  ✓ " . count($categories) . " catégories créées\n";
    }

    /**
     * Créer les grades par défaut
     */
    private function createGrades()
    {
        echo "🎖️ Création des grades...\n";
        
        $grades = [
            ['libelle' => 'Junior', 'description' => 'Niveau débutant', 'etat' => 1],
            ['libelle' => 'Senior', 'description' => 'Niveau expérimenté', 'etat' => 1],
            ['libelle' => 'Expert', 'description' => 'Niveau expert', 'etat' => 1],
            ['libelle' => 'Manager', 'description' => 'Niveau managérial', 'etat' => 1],
            ['libelle' => 'Directeur', 'description' => 'Niveau directorial', 'etat' => 1],
        ];

        foreach ($grades as $grade) {
            Grades::firstOrCreate(['libelle' => $grade['libelle']], $grade);
        }
        
        echo "  ✓ " . count($grades) . " grades créés\n";
    }

    /**
     * Créer les statuts par défaut
     */
    private function createStatuts()
    {
        echo "📊 Création des statuts...\n";
        
        $statuts = [
            [
                'libelle' => 'En attente',
                'description' => 'Demande reçue et en attente de traitement',
                'couleur' => 'f39c12',
                'icone' => 'fas fa-clock',
                'ordre' => 1,
                'etat' => 1,
                'ent1d' => 1
            ],
            [
                'libelle' => 'En cours de traitement',
                'description' => 'Demande en cours d\'analyse',
                'couleur' => '0dcaf0',
                'icone' => 'fas fa-cogs',
                'ordre' => 2,
                'etat' => 1,
                'ent1d' => 1
            ],
            [
                'libelle' => 'Documents requis',
                'description' => 'Documents supplémentaires nécessaires',
                'couleur' => 'dc3545',
                'icone' => 'fas fa-file-alt',
                'ordre' => 3,
                'etat' => 1,
                'ent1d' => 1
            ],
            [
                'libelle' => 'Approuvé',
                'description' => 'Demande approuvée avec succès',
                'couleur' => '20c997',
                'icone' => 'fas fa-check-circle',
                'ordre' => 4,
                'etat' => 1,
                'ent1d' => 1
            ],
            [
                'libelle' => 'Rejeté',
                'description' => 'Demande rejetée',
                'couleur' => 'dc3545',
                'icone' => 'fas fa-times-circle',
                'ordre' => 5,
                'etat' => 1,
                'ent1d' => 1
            ],
            [
                'libelle' => 'Terminé',
                'description' => 'Visa délivré et terminé',
                'couleur' => '28a745',
                'icone' => 'fas fa-passport',
                'ordre' => 6,
                'etat' => 1,
                'ent1d' => 1
            ]
        ];

        foreach ($statuts as $statut) {
            StatutsEtat::firstOrCreate(
                ['libelle' => $statut['libelle'], 'ent1d' => 1],
                $statut
            );
        }
        
        echo "  ✓ " . count($statuts) . " statuts créés\n";
    }

    /**
     * Créer des utilisateurs de test
     */
    private function createTestUsers()
    {
        echo "👥 Création des utilisateurs de test...\n";
        
        $users = [];
        
        // Créer des utilisateurs publics (clients)
        $publicUsers = [
            ['name' => 'Kouadio Jean-Baptiste', 'email' => 'kouadio.jean@test.com', 'contact' => '+225 01 02 03 04 05'],
            ['name' => 'Assi Marie-Claire', 'email' => 'assi.marie@test.com', 'contact' => '+225 07 08 09 10 11'],
            ['name' => 'Diabaté Ibrahim', 'email' => 'diabate.ibrahim@test.com', 'contact' => '+225 05 06 07 08 09'],
            ['name' => 'Tra Bi Cécile', 'email' => 'tra.cecile@test.com', 'contact' => '+225 02 03 04 05 06'],
            ['name' => 'Kone Seydou', 'email' => 'kone.seydou@test.com', 'contact' => '+225 08 09 10 11 12'],
            ['name' => 'Bamba Fatou', 'email' => 'bamba.fatou@test.com', 'contact' => '+225 03 04 05 06 07'],
            ['name' => 'Ouattara Moussa', 'email' => 'ouattara.moussa@test.com', 'contact' => '+225 06 07 08 09 10'],
            ['name' => 'Yao Adjoua', 'email' => 'yao.adjoua@test.com', 'contact' => '+225 04 05 06 07 08'],
            ['name' => 'Koné Amadou', 'email' => 'kone.amadou@test.com', 'contact' => '+225 09 10 11 12 13'],
            ['name' => 'Silué Kadiatou', 'email' => 'silue.kadiatou@test.com', 'contact' => '+225 01 11 22 33 44'],
            ['name' => 'Diouf Abdoulaye', 'email' => 'diouf.abdoulaye@test.com', 'contact' => '+225 02 22 33 44 55'],
            ['name' => 'Sankara Awa', 'email' => 'sankara.awa@test.com', 'contact' => '+225 03 33 44 55 66'],
            ['name' => 'Camara Issiaka', 'email' => 'camara.issiaka@test.com', 'contact' => '+225 04 44 55 66 77'],
            ['name' => 'Touré Aminata', 'email' => 'toure.aminata@test.com', 'contact' => '+225 05 55 66 77 88'],
            ['name' => 'Barry Mamadou', 'email' => 'barry.mamadou@test.com', 'contact' => '+225 06 66 77 88 99'],
        ];

        foreach ($publicUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password123'),
                    'etat' => 1,
                    'id_grade' => rand(1, 3),
                    'id_categorie' => rand(1, 5),
                    'type_user' => 'public',
                    'ent1d' => 1,
                    'created_at' => Carbon::now()->subDays(rand(1, 365)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ])
            );
            $users[] = $user;
        }

        echo "  ✓ " . count($publicUsers) . " utilisateurs publics créés\n";

        // Créer quelques utilisateurs supplémentaires pour avoir plus de 5000
        echo "📈 Création d'utilisateurs supplémentaires...\n";
        
        $noms = ['Kouassi', 'Yao', 'Koffi', 'Akissi', 'N\'Guessan', 'Konan', 'Adjoua', 'Amenan', 'Kouakou', 'Aya'];
        $prenoms = ['Jean', 'Marie', 'Pierre', 'Fatou', 'Ibrahim', 'Awa', 'Moussa', 'Aïcha', 'Sekou', 'Mariam'];
        
        for ($i = 1; $i <= 4800; $i++) {
            $nom = $noms[array_rand($noms)];
            $prenom = $prenoms[array_rand($prenoms)];
            $name = $nom . ' ' . $prenom;
            $email = strtolower($nom . '.' . $prenom . $i . '@client.test');
            
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'etat' => rand(0, 1) ? 1 : 0, // 80% actifs
                    'id_grade' => rand(1, 3),
                    'id_categorie' => rand(1, 5),
                    'type_user' => 'public',
                    'ent1d' => 1,
                    'contact' => '+225 ' . sprintf('%02d', rand(1, 99)) . ' ' . sprintf('%02d', rand(10, 99)) . ' ' . sprintf('%02d', rand(10, 99)) . ' ' . sprintf('%02d', rand(10, 99)),
                    'created_at' => Carbon::now()->subDays(rand(1, 730)), // 2 ans
                    'updated_at' => Carbon::now()->subDays(rand(1, 60)),
                ]
            );
            
            if ($i % 500 == 0) {
                echo "  ✓ " . $i . " utilisateurs créés...\n";
            }
        }

        return $users;
    }

    /**
     * Créer des profils visa sur plusieurs mois
     */
    private function createProfilsVisa($users)
    {
        echo "📋 Création des profils visa...\n";
        
        $statuts = StatutsEtat::where('etat', 1)->get();
        $now = Carbon::now();
        
        // Types de profils visa
        $types = [1, 2, 3, 4, 5]; // Tourisme, Affaires, Transit, Étudiant, Travail
        
        $totalCreated = 0;

        // Créer des profils sur les 12 derniers mois
        for ($month = 11; $month >= 0; $month--) {
            $monthDate = $now->copy()->subMonths($month);
            
            // Nombre variable de demandes par mois (simulation réaliste)
            $demandesParMois = rand(380, 520); // Pour atteindre environ 5000+ au total
            
            for ($i = 0; $i < $demandesParMois; $i++) {
                $user = $users[array_rand($users)];
                $statut = $statuts[array_rand($statuts->toArray())];
                $type = $types[array_rand($types)];
                
                // Date aléatoire dans le mois
                $createdAt = $monthDate->copy()->addDays(rand(0, 27))->addHours(rand(8, 18));
                
                // Date de mise à jour (1-15 jours après création)
                $updatedAt = $createdAt->copy()->addDays(rand(1, 15))->addHours(rand(1, 8));
                
                $numero = $this->generateUniqueNumber();
                
                try {
                    $profilVisa = ProfilVisa::create([
                        'etat' => 1,
                        'etape' => rand(2, 6),
                        'user1d' => $user->id,
                        'ent1d' => 1,
                        'numero_profil_visa' => $numero,
                        'id_statuts_etat' => $statut->id,
                        'type_profil_visa' => $type,
                        'message' => $this->generateRandomMessage($statut->libelle),
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt
                    ]);
                    $totalCreated++;
                } catch (\Exception $e) {
                    echo "Erreur création profil: " . $e->getMessage() . "\n";
                }
            }
            
            echo "  ✓ Mois " . $monthDate->format('M Y') . ": {$demandesParMois} profils\n";
        }

        // Créer quelques profils pour aujourd'hui et cette semaine
        for ($i = 0; $i < rand(15, 35); $i++) {
            $user = $users[array_rand($users)];
            $statut = $statuts[array_rand($statuts->toArray())];
            $type = $types[array_rand($types)];
            
            $createdAt = $now->copy()->subDays(rand(0, 7))->addHours(rand(8, 18));
            
            try {
                ProfilVisa::create([
                    'etat' => 1,
                    'etape' => rand(1, 3),
                    'user1d' => $user->id,
                    'ent1d' => 1,
                    'numero_profil_visa' => $this->generateUniqueNumber(),
                    'id_statuts_etat' => $statut->id,
                    'type_profil_visa' => $type,
                    'message' => $this->generateRandomMessage($statut->libelle),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt
                ]);
                $totalCreated++;
            } catch (\Exception $e) {
                echo "Erreur création profil récent: " . $e->getMessage() . "\n";
            }
        }
        
        echo "  ✓ Total profils visa créés: {$totalCreated}\n";
    }

    /**
     * Créer des messages pour certains profils
     */
    private function createMessages()
    {
        echo "💬 Création des messages...\n";
        
        $profils = ProfilVisa::inRandomOrder()->limit(800)->get();
        
        $objets = [
            'Documents supplémentaires requis',
            'Confirmation de rendez-vous',
            'Mise à jour du statut',
            'Informations complémentaires',
            'Suivi de dossier',
            'Notification importante',
            'Demande de clarification',
            'Documents approuvés'
        ];

        $messages = [
            'Votre dossier est en cours de traitement. Nous vous tiendrons informé de l\'évolution.',
            'Merci de fournir les documents manquants dans les plus brefs délais.',
            'Votre rendez-vous est confirmé pour demain à 10h00.',
            'Votre demande de visa a été approuvée. Vous pouvez venir retirer votre passeport.',
            'Des informations supplémentaires sont requises pour traiter votre demande.',
            'Votre dossier nécessite une vérification supplémentaire de nos services.',
            'Félicitations ! Votre visa a été approuvé avec succès.',
            'Malheureusement, votre demande a été rejetée. Veuillez consulter les motifs.',
            'Votre passeport est prêt pour retrait. Merci de vous présenter avec une pièce d\'identité.',
            'Suite à votre demande, nous avons besoin du justificatif de domicile récent.',
            'Votre dossier a été transféré au service consulaire pour finalisation.',
            'Merci de vous présenter le lundi prochain à 9h pour finaliser votre dossier.',
        ];

        $messageCount = 0;
        foreach ($profils as $profil) {
            // 60% de chance d'avoir un message
            if (rand(1, 100) <= 60) {
                try {
                    AddMessageProfilVisa::create([
                        'message' => $messages[array_rand($messages)],
                        'objet' => $objets[array_rand($objets)],
                        'id_profil_visa' => $profil->id,
                        'user1d' => 1, // Admin user
                        'photo' => 'NO',
                        'etat' => 1,
                        'created_at' => $profil->created_at->addDays(rand(1, 5))
                    ]);
                    $messageCount++;
                } catch (\Exception $e) {
                    echo "Erreur création message: " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "  ✓ {$messageCount} messages créés\n";
    }

    /**
     * Créer des statistiques réalistes
     */
    private function createRealisticStats()
    {
        echo "📊 Mise à jour des statistiques...\n";
        
        // Mettre à jour quelques utilisateurs pour avoir des activités récentes
        $recentUsers = User::where('type_user', 'public')
            ->inRandomOrder()
            ->limit(50)
            ->get();
            
        foreach ($recentUsers as $user) {
            $user->update([
                'updated_at' => Carbon::now()->subHours(rand(1, 48))
            ]);
        }
        
        echo "  ✓ Statistiques mises à jour\n";
    }

    /**
     * Générer un numéro unique pour le profil visa
     */
    private function generateUniqueNumber()
    {
        $attempts = 0;
        do {
            $number = 'PSI-VIS-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            $attempts++;
            
            if ($attempts > 50) {
                // Utiliser timestamp pour garantir l'unicité
                $number = 'PSI-VIS-' . time() . rand(100, 999);
                break;
            }
        } while (ProfilVisa::where('numero_profil_visa', $number)->exists());
        
        return $number;
    }

    /**
     * Générer un message aléatoire basé sur le statut
     */
    private function generateRandomMessage($statut)
    {
        $messages = [
            'En attente' => [
                'Votre demande a été reçue et est en cours de traitement.',
                'Nous avons bien reçu votre dossier. Un traitement est en cours.',
                'Votre demande est dans la file d\'attente de traitement.',
                'Réception confirmée. Traitement sous 48h ouvrables.'
            ],
            'En cours de traitement' => [
                'Votre dossier est actuellement analysé par nos services.',
                'Traitement en cours. Merci de votre patience.',
                'Analyse en cours de votre demande de visa.',
                'Examen détaillé de votre dossier en cours.'
            ],
            'Documents requis' => [
                'Des documents supplémentaires sont nécessaires.',
                'Merci de fournir les pièces manquantes.',
                'Votre dossier nécessite des documents complémentaires.',
                'Veuillez compléter votre dossier avec les documents requis.'
            ],
            'Approuvé' => [
                'Félicitations ! Votre demande a été approuvée.',
                'Visa approuvé. Vous pouvez venir le retirer.',
                'Bonne nouvelle ! Votre visa est prêt.',
                'Approbation confirmée. Félicitations !'
            ],
            'Rejeté' => [
                'Votre demande a été rejetée. Voir motifs ci-joint.',
                'Malheureusement, nous ne pouvons pas approuver votre demande.',
                'Demande rejetée pour non-conformité des documents.',
                'Après examen, votre demande ne peut être acceptée.'
            ],
            'Terminé' => [
                'Votre visa est prêt pour retrait.',
                'Procédure terminée avec succès.',
                'Vous pouvez venir retirer votre passeport avec le visa.',
                'Dossier finalisé. Merci de votre confiance.'
            ]
        ];

        $statusMessages = $messages[$statut] ?? ['Message par défaut pour ce statut.'];
        return $statusMessages[array_rand($statusMessages)];
    }
}