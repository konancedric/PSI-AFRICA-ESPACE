<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilVisa;
use App\Models\StatutsEtat;
use App\Models\InformationsPersonnelles;
use App\Models\CoordonneesPersonnelles;
use App\Models\PiecesIdentites;
use App\Models\SituationProfessionnelle;
use App\Models\QuestionnairesDocuments;
use App\Models\InformationsParents;
use App\Models\InformationsMineurs;
use App\Models\AddMessageProfilVisa;
use App\Models\Entreprises;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use DataTables;

class ProfilVisaController extends Controller
{
    /**
     * ✅ MIDDLEWARE CORRIGÉ - Sécurité renforcée
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        
        // Middleware pour l'accès au module
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            
            if (!$user) {
                return redirect()->route('login')->with('error', 'Connexion requise');
            }
            
            return $next($request);
        });
        
        // Middleware pour suppression multiple - ADMIN UNIQUEMENT
        $this->middleware(function ($request, $next) {
            $action = $request->route()->getActionMethod();
            
            if (in_array($action, ['massDelete', 'massDeleteConfirm'])) {
                $user = auth()->user();
                
                if (!$this->canMassDeleteProfils($user)) {
                    Log::warning("TENTATIVE SUPPRESSION MULTIPLE NON AUTORISÉE par {$user->name}");
                    
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'ACCÈS REFUSÉ: Seuls les Administrateurs peuvent effectuer des suppressions multiples.'
                        ], 403);
                    }
                    
                    return redirect()->back()->with('error', 'ACCÈS REFUSÉ: Seuls les Administrateurs peuvent effectuer des suppressions multiples.');
                }
            }
            
            return $next($request);
        })->only(['massDelete', 'massDeleteConfirm']);
    }

    /**
     * ✅ MÉTHODE INDEX CORRIGÉE - Affichage sécurisé avec filtrage par utilisateur
     */
    public function index(Request $request): mixed
    {
        try {
            $linkEditor = "https://ed.psiafrica.ci";
            $link_ext = "https://psiafrica.ci";
            $user1d = Auth::user()->id;
            $user = Auth::user();
            $dataEntreprise = Entreprises::where('user1d', $user1d)->first();
            $ent1d = 1;
            
            $dataStatutsEtat = StatutsEtat::where('etat', 1)->orderBy('libelle', 'asc')->get();
            $dataConseillerClients = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('users.etat', '=', 1)
                ->where('model_has_roles.role_id', '=', 7)
                ->select('users.*', 'model_has_roles.role_id')
                ->get();

            // ✅ CORRECTION CRITIQUE : Filtrage sécurisé par type d'utilisateur
            $baseQuery = ProfilVisa::where('ent1d', 1);

            // ✅ SÉCURITÉ : Les utilisateurs publics ne voient QUE leurs propres profils
            if (!$this->isAgent($user)) {
                $baseQuery->where('user1d', $user->id);

                // ✅ NOUVEAU : Masquer les profils auto-créés non complétés
                // Ne pas afficher les profils qui ont encore le message par défaut de l'inscription
                $baseQuery->where(function($query) {
                    $query->where('message', '!=', 'Profil créé automatiquement lors de l\'inscription')
                          ->orWhereNull('message');
                });

                Log::info("Restriction appliquée: utilisateur public {$user->name} voit seulement ses profils complétés");
            } else {
                Log::info("Accès agent: {$user->name} voit tous les profils");
            }

            // Gestion des filtres de recherche
            if(isset($request->btnSend) AND $request->btnSend != "") {
                if($request->btnSend == "search_by_date") {
                    if(isset($request->date_debut) AND $request->date_debut != "") {
                        $date_debut = $request->date_debut;
                    } else {
                        $date_debut = Carbon::now()->toDateString();
                    }
                    if(isset($request->date_fin) AND $request->date_fin != "") {
                        $date_fin = $request->date_fin;
                    } else {
                        $date_fin = Carbon::now()->toDateString();
                    }
                    $dataProfilVisa = (clone $baseQuery)->whereBetween('profil_visa.created_at', [$date_debut, $date_fin])->orderBy('id', 'desc')->paginate(100);
                }
                elseif($request->btnSend == "search_by_name") {
                    if(isset($request->id_user1d) AND $request->id_user1d != "") {
                        $id_user1d = $request->id_user1d;
                    } else {
                        $id_user1d = 0;
                    }
                    $dataProfilVisa = (clone $baseQuery)->where('profil_visa.user1d', $id_user1d)->orderBy('id', 'desc')->paginate(100);
                }
                elseif($request->btnSend == "search_by_id_profil_visa") {
                    if(isset($request->id_profil_visa) AND $request->id_profil_visa != "") {
                        $id_profil_visa = $request->id_profil_visa;
                    } else {
                        $id_profil_visa = 0;
                    }
                    $dataProfilVisa = (clone $baseQuery)->where('profil_visa.id', $id_profil_visa)->orderBy('id', 'desc')->paginate(100);
                }
                elseif($request->btnSend == "search_by_type_profil") {
                    if(isset($request->type_profil_visa) AND $request->type_profil_visa != "") {
                        $type_profil_visa = $request->type_profil_visa;
                    } else {
                        $type_profil_visa = 0;
                    }
                    $dataProfilVisa = (clone $baseQuery)->where('profil_visa.type_profil_visa', $type_profil_visa)->orderBy('id', 'desc')->paginate(100);
                }
                else {
                    $dataProfilVisa = (clone $baseQuery)->orderBy('id', 'desc')->paginate(100);
                }
            }
            else {
                $dataProfilVisa = (clone $baseQuery)->orderBy('id', 'desc')->paginate(100);
            }
            
            $dataAllProfilVisa = (clone $baseQuery)->orderBy('id', 'desc')->get();
            
            Log::info("Index Profil Visa chargé pour {$user->name} - {$dataProfilVisa->count()} profils affichés");
            
            return view('admin.profil-visa.profil-visa', compact('linkEditor', 'user1d', 'dataProfilVisa', 'dataEntreprise', 'ent1d', 'dataConseillerClients', 'dataStatutsEtat', 'link_ext', 'dataAllProfilVisa'));

        } catch (\Exception $e) {
            Log::error('Erreur index ProfilVisa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors du chargement des profils visa: ' . $e->getMessage());
        }
    }

    /**
     * ✅ MÉTHODE PRINCIPALE CORRIGÉE - Basée sur l'ancien code fonctionnel
     * Ajouter un message à un profil visa
     */
    public function getAddMessageProfilVisa(Request $request)
    {
        try {
            Log::info('Tentative ajout message profil visa', [
                'user_id' => Auth::user()->id,
                'profil_id' => $request->id ?? 'non fourni',
                'objet' => $request->objet ?? 'non fourni'
            ]);

            // ✅ VALIDATION CORRIGÉE - Message n'est plus requis
            $validator = Validator::make($request->all(), [
                'objet' => 'required|string|max:255',
                'user1d' => 'required|integer',
                'id' => 'required|integer|exists:profil_visa,id',
            ], [
                'objet.required' => 'L\'objet du message est obligatoire',
                'user1d.required' => 'L\'utilisateur est requis',
                'id.required' => 'L\'ID du profil visa est requis',
                'id.exists' => 'Ce profil visa n\'existe pas'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation échoué ajout message', [
                    'errors' => $validator->errors()->toArray(),
                    'user_id' => Auth::user()->id
                ]);
                return redirect()->back()->withInput()->with('error', $validator->messages()->first());
            }

            // ✅ VÉRIFICATION DE SÉCURITÉ : L'utilisateur peut-il modifier ce profil ?
            $profilVisa = ProfilVisa::find($request->id);
            if (!$profilVisa) {
                return redirect()->back()->with('error', 'Profil visa non trouvé');
            }

            $user = Auth::user();
            if (!$this->canEditProfil($user, $profilVisa)) {
                Log::warning('Tentative d\'accès non autorisée au profil', [
                    'user_id' => $user->id,
                    'profil_id' => $request->id
                ]);
                return redirect()->back()->with('error', 'Vous n\'avez pas les droits pour modifier ce profil');
            }

            // ✅ CRÉATION DU MESSAGE
            $addMessage = new AddMessageProfilVisa();
            $addMessage->user1d = $request->user1d;
            $addMessage->ent1d = 1;
            $addMessage->id_profil_visa = $request->id;
            $addMessage->objet = $request->objet;
            $addMessage->message = $request->message ?? ''; // Message optionnel
            $addMessage->etat = 1;

            if ($addMessage->save()) {
                Log::info('Message ajouté avec succès', [
                    'message_id' => $addMessage->id,
                    'profil_id' => $request->id,
                    'user_id' => $request->user1d
                ]);
                return redirect()->back()->with('success', 'Message ajouté avec succès!');
            } else {
                return redirect()->back()->with('error', 'Échec de l\'ajout du message');
            }

        } catch (\Exception $e) {
            Log::error('Erreur ajout message profil visa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Une erreur s\'est produite: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire de création d'un nouveau profil visa
     */
    public function createForm()
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur est authentifié
        if (!$user) {
            return redirect()->route('login')
                ->with('info', 'Veuillez vous connecter ou créer un compte pour soumettre un profil visa.');
        }

        // Types de profils visa disponibles
        $typesProfilVisa = [
            1 => 'Tourisme',
            2 => 'Affaires',
            3 => 'Transit',
            4 => 'Étudiant',
            5 => 'Travail',
            6 => 'Famille',
            7 => 'Autre'
        ];

        return view('admin.profil-visa.form-new-profil-visa', compact('typesProfilVisa'));
    }

    /**
     * ✅ CRÉER OU METTRE À JOUR UN PROFIL VISA
     */
    public function create(Request $request): mixed
    {
        try {
            Log::info('Création/Mise à jour profil visa démarrée', ['user_id' => Auth::user()->id]);

            $validator = Validator::make($request->all(), [
                'type_profil_visa' => 'required|string|max:255',
                'motif_voyage' => 'required|string|max:255',
            ], [
                'type_profil_visa.required' => 'Le type de profil visa est obligatoire',
                'motif_voyage.required' => 'Le motif du voyage est obligatoire',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error', $validator->messages()->first());
            }

            $user1d = Auth::user()->id;
            $ent1d = 1;

            // ✅ NOUVEAU : Vérifier s'il existe un profil auto-créé
            $existingProfil = ProfilVisa::where('user1d', $user1d)
                ->where('ent1d', $ent1d)
                ->where('message', 'Profil créé automatiquement lors de l\'inscription')
                ->first();

            if ($existingProfil) {
                // Mettre à jour le profil auto-créé existant
                $existingProfil->type_profil_visa = $request->type_profil_visa;
                $existingProfil->motif_voyage = $request->motif_voyage;
                $existingProfil->message = 'Profil complété par le client le ' . now()->format('d/m/Y à H:i');
                $existingProfil->updated_at = now();

                if ($existingProfil->save()) {
                    Log::info('Profil visa auto-créé mis à jour', [
                        'profil_id' => $existingProfil->id,
                        'user_id' => $user1d
                    ]);
                    return redirect('profil-visa')->with('success', 'Votre profil visa a été complété avec succès!');
                } else {
                    return redirect('profil-visa')->with('error', 'Échec de la mise à jour du profil visa! Réessayez.');
                }
            } else {
                // Créer un nouveau profil
                $profilVisa = new ProfilVisa();
                $profilVisa->user1d = $user1d;
                $profilVisa->ent1d = $ent1d;
                $profilVisa->type_profil_visa = $request->type_profil_visa;
                $profilVisa->motif_voyage = $request->motif_voyage;
                $profilVisa->numero_profil_visa = 'PSI'.date('Y').'-'.str_pad(ProfilVisa::count() + 1, 6, '0', STR_PAD_LEFT);
                $profilVisa->message = 'Profil créé par le client le ' . now()->format('d/m/Y à H:i');
                $profilVisa->etat = 1;

                if ($profilVisa->save()) {
                    Log::info('Nouveau profil visa créé', [
                        'profil_id' => $profilVisa->id,
                        'user_id' => $user1d
                    ]);
                    return redirect('profil-visa')->with('success', 'Profil visa créé avec succès!');
                } else {
                    return redirect('profil-visa')->with('error', 'Échec de création du profil visa! Réessayez.');
                }
            }

        } catch (\Exception $e) {
            Log::error('Erreur create: ' . $e->getMessage());
            return redirect('profil-visa')->with('error', 'Une erreur s\'est produite: ' . $e->getMessage());
        }
    }

    /**
     * ✅ SUPPRIMER UN PROFIL VISA INDIVIDUEL (Correction majeure)
     */
    public function delete(Request $request): mixed
    {
        try {
            $user = Auth::user();
            
            // ✅ CORRECTION MAJEURE : Vérifier la permission ET le rôle
            if (!$this->canDeleteProfil($user)) {
                Log::warning("Tentative suppression non autorisée", [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'roles' => $user->getRoleNames(),
                    'has_permission' => $user->can('delete_profil_visa')
                ]);
                return redirect('profil-visa')->with('error', 'Vous n\'avez pas les droits pour supprimer des profils');
            }

            $profilVisa = ProfilVisa::find($request->id);
            
            if (!$profilVisa) {
                return redirect('profil-visa')->with('error', 'Profil visa non trouvé');
            }

            // Supprimer l'utilisateur associé si c'est un profil public
            $checkProfilVisa = ProfilVisa::where('id', $request->id)->first();
            if ($checkProfilVisa && $User = User::find($checkProfilVisa->user1d)) {
                $User->delete();
            }

            $profilVisa->delete();
            
            Log::info('Profil visa supprimé', [
                'profil_id' => $request->id,
                'deleted_by' => $user->id,
                'deleted_by_name' => $user->name
            ]);

            return redirect('profil-visa')->with('success', 'Profil visa supprimé avec succès!');
            
        } catch (\Exception $e) {
            Log::error('Erreur delete: ' . $e->getMessage());
            return redirect('profil-visa')->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Suppression avec confirmation GET
     */
    public function deleteOKOK($id): mixed
    {
        try {
            $user = Auth::user();
            
            if (!$this->canDeleteProfil($user)) {
                Log::warning("Tentative suppression GET non autorisée", [
                    'user_id' => $user->id,
                    'profil_id' => $id
                ]);
                return redirect('profil-visa')->with('error', 'Vous n\'avez pas les droits pour supprimer des profils');
            }

            $profilVisa = ProfilVisa::find($id);
            
            if (!$profilVisa) {
                return redirect('profil-visa')->with('error', 'Profil visa non trouvé');
            }

            // Supprimer l'utilisateur associé si c'est un profil public
            if ($User = User::find($profilVisa->user1d)) {
                $User->delete();
            }

            $profilVisa->delete();
            
            Log::info('Profil visa supprimé (GET)', [
                'profil_id' => $id,
                'deleted_by' => $user->id,
                'deleted_by_name' => $user->name
            ]);

            return redirect('profil-visa')->with('success', 'Profil visa supprimé avec succès!');
            
        } catch (\Exception $e) {
            Log::error('Erreur deleteOKOK: ' . $e->getMessage());
            return redirect('profil-visa')->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * ✅ MÉTHODE CORRIGÉE : Suppression multiple (ADMIN UNIQUEMENT)
     * Accepte les formats: profil_ids=[1,2,3] OU selected_ids="1,2,3"
     */
    public function massDelete(Request $request)
    {
        try {
            $user = Auth::user();
            
            // ✅ Vérification stricte pour suppression multiple
            if (!$this->canMassDeleteProfils($user)) {
                Log::warning("ACCÈS REFUSÉ - Suppression multiple", [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'roles' => $user->getRoleNames()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'ACCÈS REFUSÉ: Seuls les Administrateurs avec la permission de suppression peuvent effectuer cette action.'
                ], 403);
            }

            // ✅ CORRECTION CRITIQUE : Gérer les deux formats d'envoi
            $profilIds = [];
            
            // Format 1 : tableau profil_ids (attendu initialement)
            if ($request->has('profil_ids') && is_array($request->profil_ids)) {
                $profilIds = $request->profil_ids;
            }
            // Format 2 : chaîne selected_ids (envoyé par le JavaScript)
            elseif ($request->has('selected_ids')) {
                $idsString = $request->selected_ids;
                // Convertir la chaîne "1,2,3" en tableau [1,2,3]
                $profilIds = array_filter(array_map('intval', explode(',', $idsString)));
            }
            // Format 3 : tableau selected_ids
            elseif ($request->has('selected_ids') && is_array($request->selected_ids)) {
                $profilIds = $request->selected_ids;
            }
            
            // Validation
            if (empty($profilIds)) {
                Log::warning('Aucun profil sélectionné', [
                    'request_data' => $request->all(),
                    'user_id' => $user->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun profil sélectionné pour la suppression'
                ], 422);
            }

            // Vérifier que tous les IDs sont valides
            $invalidIds = [];
            foreach ($profilIds as $id) {
                if (!is_numeric($id) || $id <= 0) {
                    $invalidIds[] = $id;
                }
            }
            
            if (!empty($invalidIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'IDs invalides détectés: ' . implode(', ', $invalidIds)
                ], 422);
            }

            $deletedCount = 0;
            $deletedProfils = [];
            $errors = [];
            
            // Récupérer la raison de suppression si fournie
            $deletionReason = $request->input('mass_deletion_reason', 'Non spécifiée');

            DB::beginTransaction();

            foreach ($profilIds as $profilId) {
                try {
                    $profilVisa = ProfilVisa::find($profilId);
                    
                    if ($profilVisa) {
                        // Récupérer le numéro avant suppression
                        $numeroProfilVisa = $profilVisa->numero_profil_visa ?? "PSI-{$profilId}";
                        
                        // Supprimer l'utilisateur associé si existe
                        if ($profilVisa->user1d) {
                            $associatedUser = User::find($profilVisa->user1d);
                            if ($associatedUser) {
                                $associatedUser->delete();
                            }
                        }
                        
                        // Supprimer le profil
                        $profilVisa->delete();
                        
                        $deletedCount++;
                        $deletedProfils[] = $numeroProfilVisa;
                        
                        Log::info('Profil supprimé (masse)', [
                            'profil_id' => $profilId,
                            'numero' => $numeroProfilVisa,
                            'raison' => $deletionReason,
                            'deleted_by' => $user->id,
                            'deleted_by_name' => $user->name
                        ]);
                    } else {
                        $errors[] = "Profil #$profilId non trouvé";
                        Log::warning("Profil non trouvé lors suppression multiple", [
                            'profil_id' => $profilId
                        ]);
                    }
                } catch (\Exception $e) {
                    $errors[] = "Erreur profil #$profilId: " . $e->getMessage();
                    Log::error("Erreur suppression profil $profilId", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            DB::commit();

            Log::info('Suppression multiple terminée', [
                'deleted_count' => $deletedCount,
                'total_requested' => count($profilIds),
                'raison' => $deletionReason,
                'deleted_by' => $user->id,
                'deleted_by_name' => $user->name,
                'errors_count' => count($errors)
            ]);

            // Message de succès adaptatif
            $message = $deletedCount > 0 
                ? "$deletedCount profil(s) supprimé(s) avec succès"
                : "Aucun profil n'a pu être supprimé";
            
            if (!empty($errors) && $deletedCount > 0) {
                $message .= " (" . count($errors) . " erreur(s))";
            }

            return response()->json([
                'success' => $deletedCount > 0,
                'message' => $message,
                'deleted_count' => $deletedCount,
                'deleted_profils' => $deletedProfils,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur suppression multiple', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NOUVELLE MÉTHODE : Confirmation suppression multiple
     */
    public function massDeleteConfirm(Request $request)
    {
        // Cette méthode peut retourner une vue de confirmation
        // ou simplement appeler massDelete si déjà confirmé
        return $this->massDelete($request);
    }

    /**
     * ✅ STATISTIQUES PROFIL VISA
     */
    public function getStatistics()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Non connecté'], 401);
            }

            $baseQuery = ProfilVisa::where('ent1d', 1);

            // Filtrer pour les utilisateurs publics
            if (!$this->isAgent($user)) {
                $baseQuery->where('user1d', $user->id);

                // Masquer les profils auto-créés non complétés
                $baseQuery->where(function($query) {
                    $query->where('message', '!=', 'Profil créé automatiquement lors de l\'inscription')
                          ->orWhereNull('message');
                });
            }
            
            $stats = [
                'total_demandes' => (clone $baseQuery)->count(),
                'demandes_en_attente' => (clone $baseQuery)->whereNull('id_statuts_etat')->count(),
                'demandes_ce_mois' => (clone $baseQuery)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'demandes_aujourd_hui' => (clone $baseQuery)
                    ->whereDate('created_at', Carbon::now()->toDateString())
                    ->count(),
            ];

            // Statistiques par statut (pour les agents)
            if ($this->isAgent($user)) {
                $statutsStats = [];
                $statuts = StatutsEtat::where('etat', 1)->get();
                
                foreach ($statuts as $statut) {
                    $statutsStats[$statut->libelle] = (clone $baseQuery)
                        ->where('id_statuts_etat', $statut->id)
                        ->count();
                }
                
                $stats['statuts'] = $statutsStats;
            }
            
            return response()->json([
                'success' => true,
                'statistics' => $stats,
                'user_info' => [
                    'name' => $user->name,
                    'type' => $user->type_user ?? 'public',
                    'is_agent' => $this->isAgent($user)
                ],
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur API statistiques: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    // ==================== MÉTHODES UTILITAIRES SÉCURISÉES ====================

    /**
     * ✅ Vérifier si l'utilisateur est un agent
     */
    private function isAgent($user): bool
    {
        try {
            $hasAgentRole = false;
            if (method_exists($user, 'hasAnyRole')) {
                $hasAgentRole = $user->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir', 'Commercial']);
            }
                          
            $hasAgentType = in_array($user->type_user ?? 'public', ['admin', 'agent_comptoir', 'commercial']);
            
            return $hasAgentRole || $hasAgentType;
        } catch (\Exception $e) {
            return in_array($user->type_user ?? 'public', ['admin', 'agent_comptoir', 'commercial']);
        }
    }

    /**
     * ✅ Vérifier si l'utilisateur peut éditer un profil
     */
    private function canEditProfil($user, $profilVisa): bool
    {
        try {
            // Les agents peuvent modifier tous les profils
            if ($this->isAgent($user)) {
                return true;
            }
            
            // Les utilisateurs publics ne peuvent modifier que leurs propres profils
            return $profilVisa->user1d == $user->id;
            
        } catch (\Exception $e) {
            Log::error('Erreur canEditProfil: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ CORRECTION MAJEURE : Vérifier si l'utilisateur peut supprimer des profils
     * Maintenant vérifie AUSSI la permission delete_profil_visa
     */
    private function canDeleteProfil($user): bool
    {
        try {
            $hasAdminRole = false;
            $hasDeletePermission = false;
            
            // Vérifier les rôles Admin et Super Admin
            if (method_exists($user, 'hasAnyRole')) {
                $hasAdminRole = $user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial']);
            }
            
            // Vérifier le type d'utilisateur
            $hasAdminType = in_array($user->type_user ?? '', ['admin', 'agent_comptoir', 'commercial']);
            
            // ✅ CORRECTION CRITIQUE : Vérifier la permission delete_profil_visa
            if (method_exists($user, 'can')) {
                $hasDeletePermission = $user->can('delete_profil_visa');
            }
            
            // L'utilisateur doit avoir SOIT un rôle d'agent SOIT la permission
            return ($hasAdminRole || $hasAdminType) && $hasDeletePermission;
            
        } catch (\Exception $e) {
            Log::error('Erreur canDeleteProfil: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ Vérifier si l'utilisateur peut effectuer des suppressions multiples
     * STRICTEMENT LIMITÉ AUX ADMIN ET SUPER ADMIN
     */
    private function canMassDeleteProfils($user): bool
    {
        try {
            $hasAdminRole = false;
            $hasAdminType = false;
            $hasDeletePermission = false;
            
            // Vérifier UNIQUEMENT les rôles Admin et Super Admin (pas Agent Comptoir ni Commercial)
            if (method_exists($user, 'hasRole')) {
                $hasAdminRole = $user->hasRole(['Super Admin', 'Admin']);
            }
            
            // Vérifier le type admin uniquement
            $hasAdminType = ($user->type_user === 'admin');
            
            // Vérifier la permission de suppression
            if (method_exists($user, 'can')) {
                $hasDeletePermission = $user->can('delete_profil_visa');
            }
            
            // L'utilisateur doit être Admin/Super Admin ET avoir la permission
            $canMassDelete = ($hasAdminRole || $hasAdminType) && $hasDeletePermission;
            
            Log::info('Vérification suppression multiple', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'has_admin_role' => $hasAdminRole,
                'has_admin_type' => $hasAdminType,
                'has_delete_permission' => $hasDeletePermission,
                'can_mass_delete' => $canMassDelete
            ]);
            
            return $canMassDelete;
            
        } catch (\Exception $e) {
            Log::error('Erreur canMassDeleteProfils: ' . $e->getMessage());
            return false;
        }
    }
}