<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilVisa;
use App\Models\StatutsEtat;
use App\Models\ConfigWeb;
use App\Models\Logs;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AuthHomeController extends Controller
{
    /**
     * ✅ CORRECTION PRINCIPALE : Rediriger vers profil-visa au lieu de mes-demandes
     */
    public function mesDemandes(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Vous devez être connecté pour voir vos demandes.');
            }

            // Log de l'activité
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = $user->id;
                $priorite = 1;
                $log_detail = "Consultation mes demandes | Success | Utilisateur : " . $user->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
            }

            Log::info('Mes demandes - Redirection vers profil-visa pour: ' . $user->name . ' (ID: ' . $user->id . ')');

            // ✅ REDIRECTION VERS PROFIL-VISA
            return redirect()->route('profil.visa.index')->with('info', 'Consultation de vos profils visa');

        } catch (\Exception $e) {
            Log::error('Erreur Mes Demandes - Redirection: ' . $e->getMessage());
            
            // En cas d'erreur, rediriger quand même vers profil-visa
            return redirect()->route('profil.visa.index')->with('error', 'Erreur lors du chargement. Redirection vers vos profils visa.');
        }
    }

    /**
     * ✅ CORRECTION : Dashboard général avec redirection intelligente
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // ✅ CORRECTION : Redirection automatique selon le type d'utilisateur ET les rôles
        try {
            // Vérifier les rôles d'abord
            if (method_exists($user, 'hasRole')) {
                if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
                    return redirect('/admin/dashboard');
                } elseif ($user->hasRole('Commercial')) {
                    return redirect('/commercial/dashboard');
                } elseif ($user->hasRole('Agent Comptoir')) {
                    return redirect('/comptoir/dashboard');
                }
            }
            
            // Fallback sur type_user
            switch ($user->type_user) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'agent_comptoir':
                    return redirect('/comptoir/dashboard');
                case 'commercial':
                    return redirect('/commercial/dashboard');
                case 'public':
                default:
                    // ✅ CORRECTION : Pour les utilisateurs publics, rediriger vers PROFIL-VISA
                    Log::info('Redirection utilisateur public vers profil-visa: ' . $user->name);
                    return redirect()->route('profil.visa.index');
            }
        } catch (\Exception $e) {
            Log::error('Erreur redirection dashboard index: ' . $e->getMessage());
            return redirect()->route('profil.visa.index');
        }
    }

    /**
     * ✅ Page de profil utilisateur
     */
    public function profile(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Log de l'activité
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = $user->id;
                $priorite = 1;
                $log_detail = "Consultation Profile | Success | Utilisateur : " . $user->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
            }

            $dataUserInfo = User::where('id', $user->id)->get();
            
            return view('pages.profile', compact('dataUserInfo'));
            
        } catch (\Exception $e) {
            Log::error('Erreur profile: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Erreur lors du chargement du profil');
        }
    }

    /**
     * ✅ CORRECTION MAJEURE : Configuration système (admin seulement) - Basé sur l'ancien code
     */
    public function Configuration(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasAnyRole(['Admin', 'Super Admin']) && $user->type_user !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
            }
            
            // Log de l'activité (comme dans l'ancien code)
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = $user->id;
                $priorite = 1;
                $log_detail = "Consultation espace configuration | Success | Utilisateur : " . $user->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
            }
            
            // ✅ RÉCUPÉRER LES DONNÉES DE CONFIGURATION (comme dans l'ancien code)
            $dataConfigWeb = ConfigWeb::first();
            
            // Si pas de données, créer des données par défaut
            if (!$dataConfigWeb) {
                $dataConfigWeb = $this->getDefaultConfigData();
            }
            
            Log::info('Configuration chargée pour: ' . $user->name);
            
            // ✅ CORRECTION : Retourner la vue vers pages.configuration
            return view('pages.configuration', compact('dataConfigWeb'));

        } catch (\Exception $e) {
            Log::error('Erreur Configuration: ' . $e->getMessage());
            
            // En cas d'erreur, créer des données par défaut
            $dataConfigWeb = $this->getDefaultConfigData();
            return view('pages.configuration', compact('dataConfigWeb'));
        }
    }

    /**
     * ✅ CORRECTION MAJEURE : Mise à jour configuration - Basé sur l'ancien code mais corrigé
     */
    public function updateConfiguration(Request $request)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        
        // ✅ Validation basée sur l'ancien code mais mise à jour
        $validator = Validator::make($request->all(), [
            'denomination' => 'required',
            'contact' => 'required',
            'user1d' => 'required',
            'id' => 'required',
            'email' => 'required|email',
            'adresse' => 'required',
        ]);

        if ($validator->fails()) {
            // Log d'erreur
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 2;
                $log_detail = "Mise à jour espace configuration web | Error | Utilisateur : " . Auth::user()->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
            }
            
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            // ✅ GESTION DES FICHIERS - Basé sur l'ancien code
            $logoEnt = $request->last_logo_ent ?? '';
            $imgPub = $request->last_img_pub ?? '';

            // Gestion du logo
            if ($request->hasFile('logo_ent')) {
                $this->validate($request, [
                    'logo_ent' => 'required|image|mimes:jpeg,png,jpg,gif,JPEG,PNG,JPG,GIF|max:2048',
                ]);

                $image = $request->file('logo_ent');
                $logoEnt = time() . $request->user1d . '_logo_ent.' . $image->getClientOriginalExtension();
                
                // Créer le dossier si nécessaire
                $uploadPath = public_path('/upload/config_web/');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $image->move($uploadPath, $logoEnt);
            }

            // Gestion de l'image pub
            if ($request->hasFile('img_pub')) {
                $this->validate($request, [
                    'img_pub' => 'required|image|mimes:jpeg,png,jpg,gif,JPEG,PNG,JPG,GIF|max:2048',
                ]);

                $image = $request->file('img_pub');
                $imgPub = time() . $request->user1d . '_img_pub.' . $image->getClientOriginalExtension();
                
                // Créer le dossier si nécessaire
                $uploadPath = public_path('/upload/config_web/');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                
                $image->move($uploadPath, $imgPub);
            }

            // ✅ MISE À JOUR - Basé sur l'ancien code mais corrigé pour éviter l'erreur de colonne
            $ConfigWeb = ConfigWeb::find($request->id);
            
            if ($ConfigWeb) {
                // ✅ PAYLOAD CORRIGÉ - Ne pas utiliser 'update_user' si la colonne n'existe pas
                $payload = [
                    'img_pub' => $imgPub,
                    'logo_ent' => $logoEnt,
                    'denomination' => $request->denomination,
                    'contact' => $request->contact,
                    'description' => $request->description,
                    'updated_at' => $currentDateTime,
                    'email' => $request->email,
                    'adresse' => $request->adresse,
                    'link_facebook' => $request->link_facebook,
                    'link_linkedin' => $request->link_linkedin,
                    'link_instagram' => $request->link_instagram,
                    'link_twitter' => $request->link_twitter,
                    'link_video' => $request->link_video,
                    'num_whatsapp' => $request->num_whatsapp,
                ];

                // ✅ VÉRIFIER SI LA COLONNE EXISTS AVANT DE L'AJOUTER
                if (Schema::hasColumn('config_web', 'update_user')) {
                    $payload['update_user'] = $request->user1d;
                }

                $update = $ConfigWeb->update($payload);

                // Log de succès
                if (class_exists('App\Models\Logs')) {
                    $log_ip = $request->ip();
                    $user1d = Auth::user()->id;
                    $priorite = 1;
                    $log_detail = "Mise à jour espace configuration web | Success | Utilisateur : " . Auth::user()->name;
                    Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
                }

                return redirect()->back()->with('success', 'Configuration Web mise à jour avec succès !');
            }

            // Log d'erreur si pas trouvé
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 2;
                $log_detail = "Mise à jour espace configuration web | Error | Utilisateur : " . Auth::user()->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
            }

            return redirect()->back()->with('error', 'Configuration non trouvée.');

        } catch (\Exception $e) {
            // Log d'erreur
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 2;
                $log_detail = "Mise à jour espace configuration web | Error | Utilisateur : " . Auth::user()->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);
            }

            Log::error('Erreur updateConfiguration: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Logs système
     */
    public function log_stat(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasAnyRole(['Admin', 'Super Admin']) && $user->type_user !== 'admin') {
                return redirect()->route('dashboard')->with('error', 'Accès non autorisé');
            }

            // Log de l'activité
            if (class_exists('App\Models\Logs')) {
                $log_ip = $request->ip();
                $user1d = $user->id;
                $priorite = 1;
                $log_detail = "Consultation Log | Success | Utilisateur : " . $user->name;
                Logs::saveLogs($log_detail, $user1d, 1, $log_ip, $priorite);

                $dataLogs = Logs::orderBy('created_at', 'desc')->get();
            } else {
                $dataLogs = collect();
            }
                
            return view('admin.logs.logs', compact('dataLogs'));
            
        } catch (\Exception $e) {
            Log::error('Erreur logs: ' . $e->getMessage());
            return view('admin.logs.logs', ['dataLogs' => collect()]);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Données de configuration par défaut
     */
    private function getDefaultConfigData()
    {
        return (object) [
            'id' => 1,
            'denomination' => 'PSI Africa',
            'email' => 'contact@psiafrica.ci',
            'contact' => '+225 XX XX XX XX',
            'adresse' => 'Abidjan, Côte d\'Ivoire',
            'description' => 'Votre partenaire pour vos démarches de visa',
            'link_video' => '',
            'link_facebook' => '',
            'link_linkedin' => '',
            'link_twitter' => '',
            'link_instagram' => '',
            'num_whatsapp' => '',
            'logo_ent' => '',
            'img_pub' => '',
            'created_at' => now(),
            'updated_at' => now()
        ];
    }

    /**
     * Créer une nouvelle demande (redirection)
     */
    public function createDemande()
    {
        return redirect()->route('profil.visa.create')->with('info', 'Créez votre demande de visa');
    }

    /**
     * ✅ AMÉLIORATION : Voir les détails d'une demande
     */
    public function viewDemande($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Connexion requise');
            }
            
            if (!Schema::hasTable('profil_visa') || !class_exists('App\Models\ProfilVisa')) {
                return redirect()->route('profil.visa.index')->with('error', 'Module profil visa non disponible');
            }
            
            $profilVisa = ProfilVisa::where('id', $id)
                ->where('user1d', $user->id)
                ->where('ent1d', 1)
                ->first();
                
            if (!$profilVisa) {
                return redirect()->route('profil.visa.index')->with('error', 'Demande non trouvée ou accès non autorisé');
            }
            
            return view('pages.demande-details', compact('profilVisa'));
            
        } catch (\Exception $e) {
            Log::error('Erreur viewDemande: ' . $e->getMessage());
            return redirect()->route('profil.visa.index')->with('error', 'Erreur lors du chargement des détails');
        }
    }

    /**
     * ✅ API pour obtenir les statistiques en temps réel
     */
    public function getRealtimeUserStats()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Utilisateur non connecté'
                ], 401);
            }
            
            $stats = [
                'total_demandes' => 0,
                'demandes_ce_mois' => 0,
                'demandes_en_cours' => 0,
                'demandes_approuvees' => 0
            ];
            
            // Calculer les vraies statistiques si possible
            if (Schema::hasTable('profil_visa') && class_exists('App\Models\ProfilVisa')) {
                $stats['total_demandes'] = ProfilVisa::where('user1d', $user->id)->where('ent1d', 1)->count();
                $stats['demandes_ce_mois'] = ProfilVisa::where('user1d', $user->id)
                    ->where('ent1d', 1)
                    ->whereMonth('created_at', now()->month)
                    ->count();
            }
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'user_info' => [
                    'name' => $user->name,
                    'email' => $user->email
                ],
                'last_update' => now()->format('d/m/Y H:i:s')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur getRealtimeUserStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Vérifier la santé du système pour l'utilisateur
     */
    public function checkSystemHealth()
    {
        try {
            $health = [
                'database' => Schema::hasTable('users'),
                'profil_visa_module' => Schema::hasTable('profil_visa'),
                'permissions' => Schema::hasTable('permissions'),
                'config_web' => Schema::hasTable('config_web')
            ];
            
            $overallHealth = array_reduce($health, function($carry, $item) {
                return $carry && $item;
            }, true);
            
            return response()->json([
                'success' => true,
                'health' => $health,
                'overall_healthy' => $overallHealth,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur checkSystemHealth: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}