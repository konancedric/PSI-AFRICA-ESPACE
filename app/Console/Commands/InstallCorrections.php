<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class InstallCorrections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psi:install-corrections {--force : Forcer l\'installation même si déjà installé}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installer toutes les corrections PSI Africa pour les rôles et permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Installation des corrections PSI Africa');
        $this->info('=========================================');
        
        $force = $this->option('force');
        
        try {
            // 1. Vérifier les prérequis
            $this->checkPrerequisites();
            
            // 2. Sauvegarder la base de données
            if ($this->confirm('Voulez-vous sauvegarder la base de données avant les modifications ?')) {
                $this->backupDatabase();
            }
            
            // 3. Installer les middlewares
            $this->installMiddlewares();
            
            // 4. Mettre à jour les routes
            $this->updateRoutes();
            
            // 5. Installer les permissions
            $this->installPermissions($force);
            
            // 6. Mettre à jour les contrôleurs
            $this->updateControllers();
            
            // 7. Diagnostiquer et corriger
            $this->runDiagnostic(true);
            
            // 8. Vider les caches
            $this->clearCaches();
            
            // 9. Vérification finale
            $this->finalVerification();
            
            $this->info('✅ Installation terminée avec succès !');
            $this->showNextSteps();
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'installation: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Vérifier les prérequis
     */
    private function checkPrerequisites()
    {
        $this->info('🔍 Vérification des prérequis...');
        
        // Vérifier Laravel
        $laravelVersion = app()->version();
        $this->line("  ✅ Laravel version: {$laravelVersion}");
        
        // Vérifier Spatie Permission
        try {
            $spatieVersion = \Composer\InstalledVersions::getVersion('spatie/laravel-permission');
            $this->line("  ✅ Spatie Permission: {$spatieVersion}");
        } catch (\Exception $e) {
            $this->warn("  ⚠️  Spatie Permission: Non détecté - {$e->getMessage()}");
        }
        
        // Vérifier la base de données
        try {
            DB::connection()->getPdo();
            $this->line("  ✅ Connexion base de données: OK");
        } catch (\Exception $e) {
            throw new \Exception("Connexion base de données échouée: {$e->getMessage()}");
        }
        
        // Vérifier les tables requises
        $requiredTables = ['users', 'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions'];
        foreach ($requiredTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $this->line("  ✅ Table {$table}: Présente");
            } else {
                throw new \Exception("Table manquante: {$table}");
            }
        }
    }

    /**
     * Sauvegarder la base de données
     */
    private function backupDatabase()
    {
        $this->info('💾 Sauvegarde de la base de données...');
        
        try {
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }
            
            $filename = "psi_africa_backup_" . date('Y-m-d_H-i-s') . ".sql";
            $fullPath = $backupPath . '/' . $filename;
            
            $command = "mysqldump -h{$host} -u{$username} -p{$password} {$database} > {$fullPath}";
            exec($command, $output, $return);
            
            if ($return === 0) {
                $this->info("  ✅ Sauvegarde créée: {$filename}");
            } else {
                $this->warn("  ⚠️  Erreur lors de la sauvegarde - Continuons quand même");
            }
            
        } catch (\Exception $e) {
            $this->warn("  ⚠️  Erreur sauvegarde: {$e->getMessage()} - Continuons quand même");
        }
    }

    /**
     * Installer les middlewares
     */
    private function installMiddlewares()
    {
        $this->info('🛡️  Installation des middlewares...');
        
        $middlewares = [
            'CommercialAccessMiddleware',
            'ComptoirAccessMiddleware', 
            'UserTypeMiddleware',
            'BypassPermissionCheck'
        ];
        
        foreach ($middlewares as $middleware) {
            $path = app_path("Http/Middleware/{$middleware}.php");
            if (File::exists($path)) {
                $this->line("  ✅ {$middleware}: Déjà présent");
            } else {
                $this->warn("  ⚠️  {$middleware}: MANQUANT - Veuillez le créer manuellement");
            }
        }
        
        // Vérifier le fichier Kernel.php
        $kernelPath = app_path('Http/Kernel.php');
        if (File::exists($kernelPath)) {
            $kernelContent = File::get($kernelPath);
            if (strpos($kernelContent, 'commercial.access') !== false) {
                $this->line("  ✅ Kernel.php: Middlewares enregistrés");
            } else {
                $this->warn("  ⚠️  Kernel.php: Middlewares NON enregistrés - Mise à jour nécessaire");
            }
        }
    }

    /**
     * Mettre à jour les routes
     */
    private function updateRoutes()
    {
        $this->info('🛣️  Vérification des routes...');
        
        $routesPath = base_path('routes/web.php');
        if (File::exists($routesPath)) {
            $routesContent = File::get($routesPath);
            
            // Vérifier les routes commerciales
            if (strpos($routesContent, 'commercial.access') !== false) {
                $this->line("  ✅ Routes commerciales: Corrigées");
            } else {
                $this->warn("  ⚠️  Routes commerciales: NON corrigées");
            }
            
            // Vérifier les routes comptoir
            if (strpos($routesContent, 'comptoir.access') !== false) {
                $this->line("  ✅ Routes comptoir: Corrigées");
            } else {
                $this->warn("  ⚠️  Routes comptoir: NON corrigées");
            }
        }
    }

    /**
     * Installer les permissions
     */
    private function installPermissions($force = false)
    {
        $this->info('🔑 Installation des permissions...');
        
        try {
            // Exécuter le seeder des permissions
            $this->call('db:seed', ['--class' => 'PermissionsSeeder', '--force' => $force]);
            $this->line("  ✅ Permissions installées via seeder");
            
        } catch (\Exception $e) {
            $this->warn("  ⚠️  Erreur seeder: {$e->getMessage()}");
            
            // Essayer une installation manuelle basique
            $this->installBasicPermissions();
        }
    }

    /**
     * Installation basique des permissions en cas d'échec du seeder
     */
    private function installBasicPermissions()
    {
        $this->info('🔧 Installation manuelle des permissions...');
        
        try {
            $basicPermissions = [
                'manage_clients',
                'view_clients',
                'manage_forfaits', 
                'view_forfaits',
                'view_dashboard_commercial',
                'manage_profil_visa',
                'view_profil_visa',
                'view_dashboard_comptoir'
            ];
            
            foreach ($basicPermissions as $permission) {
                DB::table('permissions')->insertOrIgnore([
                    'name' => $permission,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            $this->line("  ✅ Permissions de base installées manuellement");
            
        } catch (\Exception $e) {
            $this->error("  ❌ Échec installation manuelle: {$e->getMessage()}");
        }
    }

    /**
     * Mettre à jour les contrôleurs
     */
    private function updateControllers()
    {
        $this->info('🎮 Vérification des contrôleurs...');
        
        $controllers = [
            'CommercialDashboardController',
            'ComptoirDashboardController',
            'DashboardController'
        ];
        
        foreach ($controllers as $controller) {
            $path = app_path("Http/Controllers/{$controller}.php");
            if (File::exists($path)) {
                $this->line("  ✅ {$controller}: Présent");
            } else {
                $this->warn("  ⚠️  {$controller}: MANQUANT");
            }
        }
    }

    /**
     * Exécuter le diagnostic avec correction automatique
     */
    private function runDiagnostic($fix = true)
    {
        $this->info('🔍 Diagnostic et correction...');
        
        try {
            $this->call('psi:diagnose-permissions', $fix ? ['--fix' => true] : []);
        } catch (\Exception $e) {
            $this->warn("  ⚠️  Erreur diagnostic: {$e->getMessage()}");
        }
    }

    /**
     * Vider les caches
     */
    private function clearCaches()
    {
        $this->info('🧹 Nettoyage des caches...');
        
        try {
            Artisan::call('cache:clear');
            $this->line("  ✅ Cache application vidé");
            
            Artisan::call('config:clear');
            $this->line("  ✅ Cache configuration vidé");
            
            Artisan::call('route:clear');
            $this->line("  ✅ Cache routes vidé");
            
            Artisan::call('view:clear');
            $this->line("  ✅ Cache vues vidé");
            
            // Vider le cache des permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->line("  ✅ Cache permissions vidé");
            
        } catch (\Exception $e) {
            $this->warn("  ⚠️  Erreur vidage cache: {$e->getMessage()}");
        }
    }

    /**
     * Vérification finale
     */
    private function finalVerification()
    {
        $this->info('✅ Vérification finale...');
        
        // Compter les éléments installés
        $rolesCount = DB::table('roles')->count();
        $permissionsCount = DB::table('permissions')->count();
        $usersCount = DB::table('users')->count();
        $commercialUsers = DB::table('users')->where('type_user', 'commercial')->count();
        
        $this->table(['Élément', 'Total'], [
            ['Rôles', $rolesCount],
            ['Permissions', $permissionsCount],
            ['Utilisateurs', $usersCount],
            ['Commerciaux', $commercialUsers],
        ]);
        
        // Vérifier un utilisateur commercial
        $testCommercial = DB::table('users')->where('type_user', 'commercial')->first();
        if ($testCommercial) {
            $hasRole = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $testCommercial->id)
                ->where('roles.name', 'Commercial')
                ->exists();
                
            if ($hasRole) {
                $this->line("  ✅ Test commercial '{$testCommercial->name}': Rôle assigné");
            } else {
                $this->warn("  ⚠️  Test commercial '{$testCommercial->name}': Rôle NON assigné");
            }
        }
    }

    /**
     * Afficher les étapes suivantes
     */
    private function showNextSteps()
    {
        $this->info('📋 Étapes suivantes recommandées:');
        $this->line('');
        $this->line('1. 🔐 Connectez-vous avec un compte commercial');
        $this->line('2. 🧪 Testez l\'accès aux nouvelles fonctionnalités:');
        $this->line('   - Dashboard Commercial: /commercial/dashboard');
        $this->line('   - Gestion Clients: /commercial/clients');
        $this->line('   - Forfaits: /forfaits');
        $this->line('   - Services: /services');
        $this->line('   - Partenaires: /partenaires');
        $this->line('   - Témoignages: /temoignages');
        $this->line('');
        $this->line('3. 🔧 Si des problèmes persistent:');
        $this->line('   php artisan psi:diagnose-permissions --fix');
        $this->line('');
        $this->line('4. 📚 Consultez les logs en cas d\'erreur:');
        $this->line('   storage/logs/laravel.log');
        
        $this->warn('⚠️  N\'oubliez pas de tester en conditions réelles !');
    }
}