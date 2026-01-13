<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CaisseEntree;
use App\Models\CaisseSortie;
use App\Models\CaisseDepense;
use App\Models\CaisseBudget;
use App\Models\CaisseCloture;
use App\Models\CaisseActivity;
use App\Models\CRMClient;
use App\Models\CRMInvoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CaisseController extends Controller
{
    /**
     * Afficher la page de gestion de caisse
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Vérifier si l'utilisateur connecté a accès à la caisse
        $currentUser = Auth::user();

        if (!$currentUser) {
            return redirect('/login')->with('error', 'Vous devez être connecté pour accéder à la caisse');
        }

        // Vérifier si l'utilisateur est bloqué
        if ($currentUser->caisse_blocked) {
            Log::warning('Tentative d\'accès à la caisse par un utilisateur bloqué', [
                'user_id' => $currentUser->id,
                'user_name' => $currentUser->name,
                'ip' => request()->ip()
            ]);

            return redirect('/home')->with('error', 'Votre accès à la caisse a été bloqué. Contactez l\'administrateur.');
        }

        // Vérifier si l'utilisateur a le droit d'accéder à la caisse (agents internes)
        if (!in_array($currentUser->type_user, ['admin', 'commercial', 'agent_comptoir'])) {
            Log::warning('Tentative d\'accès à la caisse par un utilisateur non autorisé', [
                'user_id' => $currentUser->id,
                'user_name' => $currentUser->name,
                'type_user' => $currentUser->type_user
            ]);

            return redirect('/home')->with('error', 'Vous n\'avez pas accès à la caisse.');
        }

        try {
            // Récupérer tous les agents internes (admin, agent_comptoir, commercial)
            // qui ont accès au CRM
            $users = User::agentsInternes()
                ->actifs()
                ->with(['roles', 'permissions'])
                ->select('id', 'name', 'email', 'matricule', 'type_user', 'etat', 'photo_user', 'crm_permissions', 'caisse_blocked')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'username' => $user->matricule ?? $user->email,
                        'name' => $user->name,
                        'email' => $user->email,
                        'matricule' => $user->matricule,
                        'role' => $user->type_user === 'admin' ? 'admin' : 'agent',
                        'type_user' => $user->type_user,
                        'permissions' => $this->getUserPermissions($user),
                        'active' => $user->etat == 1 && !$user->caisse_blocked,
                        'caisse_blocked' => $user->caisse_blocked,
                        'photo' => $user->full_photo_url,
                        'crm_permissions' => $user->getCrmPermissions(),
                        'caisse_permissions' => $user->getCaissePermissions()
                    ];
                });

            return view('caisse.caisse', compact('users'));

        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la caisse:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // En cas d'erreur, retourner la vue avec un tableau vide
            $users = collect([]);
            return view('caisse.caisse', compact('users'));
        }
    }

    /**
     * Obtenir les permissions d'un utilisateur pour la caisse
     *
     * @param User $user
     * @return array
     */
    private function getUserPermissions($user)
    {
        $permissions = [];

        // Permissions de base pour tous
        $permissions[] = 'dashboard';

        // Permissions selon le type d'utilisateur
        if ($user->type_user === 'admin' || $user->hasRole('Super Admin') || $user->hasRole('Admin')) {
            $permissions = ['dashboard', 'entries', 'exits', 'settings', 'depenses', 'reports'];
        } elseif ($user->type_user === 'commercial') {
            $permissions = ['dashboard', 'entries', 'exits', 'reports'];
        } elseif ($user->type_user === 'agent_comptoir') {
            $permissions = ['dashboard', 'entries', 'exits'];
        }

        // Ajouter les permissions CRM si l'utilisateur en a
        $crmPermissions = $user->getCrmPermissions();
        if (!empty($crmPermissions)) {
            if (in_array('dashboard', $crmPermissions)) {
                $permissions[] = 'crm_dashboard';
            }
            if (in_array('clients', $crmPermissions)) {
                $permissions[] = 'crm_clients';
            }
            if (in_array('invoicing', $crmPermissions)) {
                $permissions[] = 'crm_invoicing';
            }
        }

        return array_unique($permissions);
    }

    /**
     * API pour récupérer les utilisateurs (pour AJAX)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        try {
            $users = User::agentsInternes()
                ->actifs()
                ->select('id', 'name', 'email', 'matricule', 'type_user', 'etat', 'photo_user', 'crm_permissions', 'caisse_blocked')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'username' => $user->matricule ?? $user->email,
                        'name' => $user->name,
                        'email' => $user->email,
                        'matricule' => $user->matricule,
                        'role' => $user->type_user === 'admin' ? 'admin' : 'agent',
                        'type_user' => $user->type_user,
                        'permissions' => $this->getUserPermissions($user),
                        'active' => $user->etat == 1 && !$user->caisse_blocked,
                        'caisse_blocked' => $user->caisse_blocked,
                        'photo' => $user->full_photo_url,
                        'crm_permissions' => $user->getCrmPermissions(),
                        'caisse_permissions' => $user->getCaissePermissions()
                    ];
                });

            return response()->json([
                'success' => true,
                'users' => $users
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des utilisateurs:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'users' => []
            ], 500);
        }
    }

    /**
     * Récupérer la liste des mois disponibles avec des données
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMoisDisponibles()
    {
        try {
            // Récupérer les mois distincts depuis les entrées et sorties
            $moisEntrees = CaisseEntree::selectRaw('YEAR(date) as annee, MONTH(date) as mois')
                ->groupBy('annee', 'mois')
                ->get();

            $moisSorties = CaisseSortie::selectRaw('YEAR(date) as annee, MONTH(date) as mois')
                ->groupBy('annee', 'mois')
                ->get();

            // Fusionner et dédupliquer
            $moisCollection = $moisEntrees->concat($moisSorties)->unique(function ($item) {
                return $item->annee . '-' . $item->mois;
            });

            // Formatter les données
            $moisDisponibles = $moisCollection->map(function ($item) {
                $date = \Carbon\Carbon::create($item->annee, $item->mois, 1);
                return [
                    'mois' => (int) $item->mois,
                    'annee' => (int) $item->annee,
                    'label' => $date->locale('fr')->isoFormat('MMMM YYYY'),
                    'value' => sprintf('%04d-%02d', $item->annee, $item->mois),
                    'est_mois_actuel' => $item->mois == now()->month && $item->annee == now()->year
                ];
            })->sortByDesc('value')->values();

            return response()->json([
                'success' => true,
                'data' => $moisDisponibles
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des mois disponibles:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des mois disponibles',
                'data' => []
            ], 500);
        }
    }

    // ==================== ENTRÉES ====================

    public function getEntrees(Request $request)
    {
        try {
            // Récupérer le mois et l'année depuis la requête, ou utiliser le mois en cours
            $mois = $request->input('mois', now()->month);
            $annee = $request->input('annee', now()->year);

            // Récupérer les entrées du mois spécifié
            $entrees = CaisseEntree::with('creator:id,name,matricule')
                ->whereYear('date', $annee)
                ->whereMonth('date', $mois)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $entrees,
                'mois' => $mois,
                'annee' => $annee,
                'periode' => sprintf('%02d/%d', $mois, $annee)
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération entrées:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function storeEntree(Request $request)
    {
        try {
            $validated = $request->validate([
                'uuid' => 'nullable|string|unique:caisse_entrees,uuid',
                'date' => 'required|date',
                'ref' => 'nullable|string|unique:caisse_entrees,ref',
                'nom' => 'required|string',
                'prenoms' => 'required|string',
                'categorie' => 'required|string',
                'nature' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'mode_paiement' => 'required|string',
                'detail_prestations' => 'nullable|array',
                'tiers_nom' => 'nullable|string',
                'montant_verse_tiers' => 'nullable|numeric|min:0',
                'created_by_username' => 'nullable|string',
                'type_payeur' => 'nullable|string|in:lui_meme,autre_personne',
                'payeur_nom_prenom' => 'nullable|string',
                'payeur_telephone' => 'nullable|string',
                'payeur_relation' => 'nullable|string',
                'payeur_reference_dossier' => 'nullable|string'
            ]);

            // Ajouter l'utilisateur créateur
            $validated['created_by_user_id'] = Auth::id();
            $validated['created_by_username'] = $validated['created_by_username'] ?? Auth::user()->name;

            // Créer l'entrée (uuid et ref seront générés automatiquement si non fournis)
            $entree = CaisseEntree::create($validated);

            // Recharger pour avoir les valeurs générées
            $entree->load('creator');

            // Enregistrer l'activité
            CaisseActivity::log(
                'Entrée Créée',
                "Nouvelle entrée de {$entree->montant} FCFA créée pour {$entree->nom} {$entree->prenoms} (Réf: {$entree->ref})",
                'entree',
                $entree->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Entrée enregistrée avec succès',
                'data' => $entree
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création entrée:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateEntree(Request $request, $uuid)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user->hasCaissePermission('modifier_entrees')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de modifier les entrées'
                ], 403);
            }

            $entree = CaisseEntree::where('uuid', $uuid)->firstOrFail();

            $validated = $request->validate([
                'date' => 'required|date',
                'nom' => 'required|string',
                'prenoms' => 'required|string',
                'categorie' => 'required|string',
                'nature' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'mode_paiement' => 'required|string',
                'detail_prestations' => 'nullable|array',
                'tiers_nom' => 'nullable|string',
                'montant_verse_tiers' => 'nullable|numeric|min:0',
                'type_payeur' => 'nullable|string|in:lui_meme,autre_personne',
                'payeur_nom_prenom' => 'nullable|string',
                'payeur_telephone' => 'nullable|string',
                'payeur_relation' => 'nullable|string',
                'payeur_reference_dossier' => 'nullable|string'
            ]);

            $entree->update($validated);

            // Enregistrer l'activité
            CaisseActivity::log(
                'Entrée Modifiée',
                "Entrée {$entree->ref} modifiée pour {$entree->nom} {$entree->prenoms} - Montant: {$entree->montant} FCFA",
                'entree',
                $entree->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Entrée modifiée avec succès',
                'data' => $entree
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur modification entrée:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function deleteEntree($uuid)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user->hasCaissePermission('supprimer_entrees')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de supprimer les entrées'
                ], 403);
            }

            $entree = CaisseEntree::where('uuid', $uuid)->firstOrFail();

            // Enregistrer l'activité AVANT de supprimer
            $details = "Entrée {$entree->ref} supprimée - {$entree->nom} {$entree->prenoms} - Montant: {$entree->montant} FCFA";
            CaisseActivity::log(
                'Entrée Supprimée',
                $details,
                'entree',
                $entree->id
            );

            // Suppression définitive (hard delete) au lieu du soft delete
            $entree->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Entrée supprimée définitivement avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur suppression entrée:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function getEntree($uuid)
    {
        try {
            $entree = CaisseEntree::where('uuid', $uuid)->with('creator:id,name,matricule')->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $entree
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération entrée:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Entrée non trouvée'], 404);
        }
    }

    public function getEntreeByRef($ref)
    {
        try {
            $entree = CaisseEntree::where('ref', $ref)->with('creator:id,name,matricule')->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $entree
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération entrée par ref:', ['ref' => $ref, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Entrée non trouvée'], 404);
        }
    }

    // ==================== SORTIES ====================

    public function getSorties(Request $request)
    {
        try {
            // Récupérer le mois et l'année depuis la requête, ou utiliser le mois en cours
            $mois = $request->input('mois', now()->month);
            $annee = $request->input('annee', now()->year);

            // Récupérer les sorties du mois spécifié
            $sorties = CaisseSortie::with('creator:id,name,matricule')
                ->whereYear('date', $annee)
                ->whereMonth('date', $mois)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sorties,
                'mois' => $mois,
                'annee' => $annee,
                'periode' => sprintf('%02d/%d', $mois, $annee)
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération sorties:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function storeSortie(Request $request)
    {
        try {
            $validated = $request->validate([
                'uuid' => 'nullable|string|unique:caisse_sorties,uuid',
                'date' => 'required|date',
                'ref' => 'nullable|string|unique:caisse_sorties,ref',
                'beneficiaire' => 'required|string',
                'motif' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'mode_paiement' => 'required|string',
                'remarques' => 'nullable|string',
                'created_by_username' => 'nullable|string'
            ]);

            // Ajouter l'utilisateur créateur
            $validated['created_by_user_id'] = Auth::id();
            $validated['created_by_username'] = $validated['created_by_username'] ?? Auth::user()->name;

            // Créer la sortie (uuid et ref seront générés automatiquement si non fournis)
            $sortie = CaisseSortie::create($validated);

            // Recharger pour avoir les valeurs générées
            $sortie->load('creator');

            // Enregistrer l'activité
            CaisseActivity::log(
                'Sortie Créée',
                "Nouvelle sortie de {$sortie->montant} FCFA pour {$sortie->beneficiaire} - Motif: {$sortie->motif} (Réf: {$sortie->ref})",
                'sortie',
                $sortie->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Sortie enregistrée avec succès',
                'data' => $sortie
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création sortie:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateSortie(Request $request, $uuid)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user->hasCaissePermission('modifier_sorties')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de modifier les sorties'
                ], 403);
            }

            $sortie = CaisseSortie::where('uuid', $uuid)->firstOrFail();
            $validated = $request->validate([
                'date' => 'required|date',
                'beneficiaire' => 'required|string',
                'motif' => 'required|string',
                'montant' => 'required|numeric|min:0',
                'mode_paiement' => 'required|string',
                'remarques' => 'nullable|string',
            ]);

            $sortie->update($validated);

            // Enregistrer l'activité
            CaisseActivity::log(
                'Sortie Modifiée',
                "Sortie {$sortie->ref} modifiée - Bénéficiaire: {$sortie->beneficiaire} - Montant: {$sortie->montant} FCFA",
                'sortie',
                $sortie->id
            );

            return response()->json(['success' => true, 'message' => 'Sortie modifiée', 'data' => $sortie]);
        } catch (\Exception $e) {
            Log::error('Erreur modification sortie:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    public function deleteSortie($uuid)
    {
        try {
            // Vérifier les permissions
            $user = Auth::user();
            if (!$user->hasCaissePermission('supprimer_sorties')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez pas la permission de supprimer les sorties'
                ], 403);
            }

            $sortie = CaisseSortie::where('uuid', $uuid)->firstOrFail();

            // Enregistrer l'activité AVANT de supprimer
            $details = "Sortie {$sortie->ref} supprimée - Bénéficiaire: {$sortie->beneficiaire} - Montant: {$sortie->montant} FCFA";
            CaisseActivity::log(
                'Sortie Supprimée',
                $details,
                'sortie',
                $sortie->id
            );

            // Suppression définitive (hard delete) au lieu du soft delete
            $sortie->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Sortie supprimée définitivement avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur suppression sortie:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    // ==================== STATISTIQUES ====================

    public function getStats(Request $request)
    {
        try {
            // Récupérer le mois et l'année depuis les paramètres, sinon utiliser le mois en cours
            $mois = $request->input('mois', now()->month);
            $annee = $request->input('annee', now()->year);

            // Calculer les entrées
            $totalEntrees = CaisseEntree::whereYear('date', $annee)
                ->whereMonth('date', $mois)
                ->sum('montant');

            // Calculer les sorties HORS BANQUE (les sorties vers la banque ne sont pas considérées comme des sorties réelles)
            $totalSorties = CaisseSortie::whereYear('date', $annee)
                ->whereMonth('date', $mois)
                ->where('beneficiaire', '!=', 'Banque')
                ->sum('montant');

            // Calculer la dîme : 10% sur les entrées totales
            $dime = $totalEntrees * 0.10;

            // Calculer le solde net : Total entrée - Total sortie (hors banque) - Dîme
            $soldeNet = $totalEntrees - $totalSorties - $dime;

            $stats = [
                'total_entrees' => $totalEntrees,
                'total_sorties' => $totalSorties,
                'dime' => $dime,
                'solde' => $soldeNet, // Solde net = Entrées - Sorties (hors banque) - Dîme
                'nb_entrees' => CaisseEntree::whereYear('date', $annee)
                    ->whereMonth('date', $mois)
                    ->count(),
                'nb_sorties' => CaisseSortie::whereYear('date', $annee)
                    ->whereMonth('date', $mois)
                    ->where('beneficiaire', '!=', 'Banque')
                    ->count(),
                'mois' => $mois,
                'annee' => $annee
            ];

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            Log::error('Erreur stats:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    // ==================== PERMISSIONS ====================

    /**
     * Récupérer les permissions caisse de l'utilisateur connecté
     */
    public function getMyPermissions()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
            }

            $permissions = $user->caisse_permissions ?? $this->getUserPermissions($user);
            $caissePermissions = $user->getCaissePermissions();

            return response()->json([
                'success' => true,
                'permissions' => $permissions,
                'caisse_permissions' => $caissePermissions,
                'user_id' => $user->id,
                'timestamp' => time()
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération permissions:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Sauvegarder les permissions caisse d'un utilisateur
     */
    public function updateUserPermissions(Request $request, $userId)
    {
        try {
            $validated = $request->validate([
                'permissions' => 'nullable|array',
                'permissions.*' => 'string',
                'caisse_permissions' => 'nullable|array',
                'caisse_permissions.*' => 'string'
            ]);

            $user = User::findOrFail($userId);

            // Sauvegarder les permissions caisse spécifiques si fournies
            if (isset($validated['caisse_permissions'])) {
                $user->caisse_permissions = $validated['caisse_permissions'];
            }

            // Note: Les permissions générales sont gérées par le système getUserPermissions()
            // basé sur le type_user, mais on peut les stocker si nécessaire

            $user->save();

            Log::info('Permissions caisse mises à jour', [
                'user_id' => $userId,
                'permissions' => $validated['permissions'] ?? [],
                'caisse_permissions' => $validated['caisse_permissions'] ?? [],
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permissions mises à jour',
                'permissions' => $validated['permissions'] ?? [],
                'caisse_permissions' => $validated['caisse_permissions'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour permissions:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Bloquer/Débloquer un utilisateur de la caisse
     */
    public function toggleUserBlock($userId)
    {
        try {
            $user = User::findOrFail($userId);

            // Vérifier que ce n'est pas l'utilisateur connecté lui-même
            if ($user->id === Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas modifier votre propre statut'
                ], 403);
            }

            // Inverser le statut de blocage
            $user->caisse_blocked = !$user->caisse_blocked;
            $user->save();

            $action = $user->caisse_blocked ? 'bloqué' : 'débloqué';

            Log::info("Utilisateur {$action} de la caisse", [
                'user_id' => $userId,
                'user_name' => $user->name,
                'caisse_blocked' => $user->caisse_blocked,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Utilisateur {$action} avec succès",
                'caisse_blocked' => $user->caisse_blocked
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur blocage/déblocage utilisateur:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur'
            ], 500);
        }
    }

    /**
     * Récupérer tous les clients CRM avec leurs factures pour la caisse
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClients()
    {
        try {
            $clients = CRMClient::with(['invoices' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->whereHas('invoices') // Seulement les clients qui ont au moins une facture
            ->select('id', 'uid', 'nom', 'prenoms', 'email', 'contact', 'statut', 'created_at')
            ->get()
            ->map(function($client) {
                $totalFactures = $client->invoices->sum('amount');
                $totalPaye = $client->invoices->sum('paid_amount');
                $restant = $totalFactures - $totalPaye;

                return [
                    'id' => $client->id,
                    'uid' => $client->uid,
                    'nom' => $client->nom,
                    'prenoms' => $client->prenoms,
                    'nom_complet' => trim($client->nom . ' ' . ($client->prenoms ?? '')),
                    'email' => $client->email,
                    'contact' => $client->contact,
                    'statut' => $client->statut,
                    'created_at' => $client->created_at,
                    'total_factures' => $totalFactures,
                    'total_paye' => $totalPaye,
                    'restant' => $restant,
                    'nombre_factures' => $client->invoices->count(),
                    'invoices' => $client->invoices->map(function($invoice) {
                        return [
                            'id' => $invoice->id,
                            'number' => $invoice->number,
                            'service' => $invoice->service,
                            'amount' => $invoice->amount,
                            'paid_amount' => $invoice->paid_amount,
                            'remaining' => $invoice->remaining,
                            'status' => $invoice->status,
                            'due_date' => $invoice->due_date,
                            'issue_date' => $invoice->issue_date,
                            'agent' => $invoice->agent,
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'clients' => $clients
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération clients CRM pour caisse:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des clients'
            ], 500);
        }
    }

    /**
     * Enregistrer un paiement CRM depuis la caisse
     *
     * @param Request $request
     * @param int $invoiceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordCRMPayment(Request $request, $invoiceId)
    {
        try {
            $user = Auth::user();

            $validator = \Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'notes' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Utiliser une transaction pour garantir l'intégrité des données
            DB::beginTransaction();

            $invoice = CRMInvoice::findOrFail($invoiceId);

            Log::info('Avant paiement CRM', [
                'invoice_id' => $invoiceId,
                'invoice_number' => $invoice->number,
                'paid_amount_before' => $invoice->paid_amount,
                'amount_to_pay' => $request->amount,
                'status_before' => $invoice->status
            ]);

            // Créer le paiement CRM
            $payment = \App\Models\CRMPayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'user_id' => $user->id,
            ]);

            // Mettre à jour le montant payé de la facture
            $invoice->paid_amount = (float)$invoice->paid_amount + (float)$request->amount;

            // Mettre à jour le statut
            if ($invoice->paid_amount >= $invoice->amount) {
                $invoice->status = 'paid';
            } else if ($invoice->paid_amount > 0) {
                $invoice->status = 'partial';
            }

            $invoice->save();

            // Créer une activité CRM pour le paiement
            \App\Models\CRMActivity::create([
                'action' => 'Paiement Caisse',
                'details' => "Paiement de {$request->amount} FCFA pour facture {$invoice->number} via Caisse (Réf: {$request->notes})",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            DB::commit();

            // Recharger la facture avec toutes les relations
            $invoice = $invoice->fresh();

            Log::info('Après paiement CRM', [
                'invoice_id' => $invoiceId,
                'invoice_number' => $invoice->number,
                'paid_amount_after' => $invoice->paid_amount,
                'remaining' => $invoice->amount - $invoice->paid_amount,
                'status_after' => $invoice->status,
                'payment_id' => $payment->id,
                'invoice_validated' => !is_null($invoice->client_validated_at),
                'receipt_signature_accessible' => !is_null($invoice->client_validated_at) && $invoice->paid_amount > 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paiement CRM enregistré avec succès',
                'invoice' => $invoice,
                'payment' => $payment
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur enregistrement paiement CRM depuis caisse:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'invoice_id' => $invoiceId,
                'amount' => $request->amount ?? null
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== CLÔTURE MENSUELLE ====================

    /**
     * Vérifier si un mois est clôturé
     */
    public function checkCloture($mois)
    {
        try {
            $cloture = CaisseCloture::where('mois', $mois)->where('cloture', true)->first();

            return response()->json([
                'success' => true,
                'cloture' => !is_null($cloture),
                'data' => $cloture
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur vérification clôture:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Clôturer un mois
     */
    public function cloturerMois(Request $request)
    {
        try {
            $validated = $request->validate([
                'mois' => 'required|string|regex:/^\d{4}-\d{2}$/',
                'remarques' => 'nullable|string'
            ]);

            $mois = $validated['mois'];
            $user = Auth::user();

            // Vérifier si le mois est déjà clôturé
            $existingCloture = CaisseCloture::where('mois', $mois)->where('cloture', true)->first();
            if ($existingCloture) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce mois est déjà clôturé'
                ], 400);
            }

            // Calculer les dates de début et fin du mois
            list($annee, $moisNum) = explode('-', $mois);
            $dateDebut = date('Y-m-d', strtotime("$annee-$moisNum-01"));
            $dateFin = date('Y-m-t', strtotime("$annee-$moisNum-01"));

            // Récupérer les entrées et sorties du mois
            $entrees = CaisseEntree::whereBetween('date', [$dateDebut, $dateFin])->get();
            $sortiesAll = CaisseSortie::whereBetween('date', [$dateDebut, $dateFin])->get();

            // Filtrer les sorties pour exclure la banque (les versements à la banque ne sont pas des sorties réelles)
            $sorties = $sortiesAll->filter(function($sortie) {
                return $sortie->beneficiaire !== 'Banque';
            });

            // Calculer les statistiques
            $totalEntrees = $entrees->sum('montant');
            $totalSorties = $sorties->sum('montant');
            $nbEntrees = $entrees->count();
            $nbSorties = $sorties->count();

            // Calculer les marges
            $margeCabinet = 0;
            $totalCabinet = 0;
            $margeDocs = 0;
            $totalDocs = 0;
            $verseTiers = 0;

            foreach ($entrees as $entree) {
                if ($entree->categorie === 'Frais de Cabinet') {
                    $margeCabinet += floatval($entree->montant);
                    $totalCabinet += floatval($entree->montant);
                } else {
                    $montant = floatval($entree->montant);
                    $montantVerseTiers = floatval($entree->montant_verse_tiers ?? 0);
                    $margeDocs += ($montant - $montantVerseTiers);
                    $totalDocs += $montant;
                    $verseTiers += $montantVerseTiers;
                }
            }

            // Calculer la dîme : 10% sur les entrées totales (et non sur la marge)
            $dime = $totalEntrees * 0.10;

            // Calculer le solde net : Total entrées - Total sorties (hors banque) - Dîme
            $solde = $totalEntrees - $totalSorties - $dime;

            // Calculer aussi la marge totale pour information
            $margeTotale = $margeCabinet + $margeDocs;

            // Créer ou mettre à jour la clôture
            $cloture = CaisseCloture::updateOrCreate(
                ['mois' => $mois],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'date' => now(),
                    'total_entrees' => $totalEntrees,
                    'total_sorties' => $totalSorties,
                    'solde' => $solde, // Solde net = Entrées - Sorties (hors banque) - Dîme
                    'marge_cabinet' => $margeCabinet,
                    'total_cabinet' => $totalCabinet,
                    'marge_docs' => $margeDocs,
                    'total_docs' => $totalDocs,
                    'verse_tiers' => $verseTiers,
                    'dime' => $dime,
                    'cloture' => true,
                    'nb_entrees' => $nbEntrees,
                    'nb_sorties' => $nbSorties,
                    'remarques' => $validated['remarques'] ?? null,
                    'created_by_user_id' => $user->id,
                    'created_by_username' => $user->name
                ]
            );

            Log::info('Mois clôturé', [
                'mois' => $mois,
                'user_id' => $user->id,
                'total_entrees' => $totalEntrees,
                'total_sorties' => $totalSorties,
                'solde' => $solde
            ]);

            // Enregistrer l'activité
            CaisseActivity::log(
                'Clôture Mensuelle',
                "Mois {$mois} clôturé - Entrées: {$totalEntrees} FCFA, Sorties: {$totalSorties} FCFA, Solde: {$solde} FCFA",
                'cloture',
                $cloture->id
            );

            return response()->json([
                'success' => true,
                'message' => "Mois $mois clôturé avec succès",
                'data' => $cloture
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur clôture mois:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la clôture: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les données d'un mois clôturé
     */
    public function getDonneesCloturees($mois)
    {
        try {
            $cloture = CaisseCloture::where('mois', $mois)->where('cloture', true)->first();

            if (!$cloture) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune clôture trouvée pour ce mois'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cloture
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération données clôturées:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }

    // ==================== ACTIVITÉS ====================

    /**
     * Récupérer les activités de la caisse avec filtres et pagination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivities(Request $request)
    {
        try {
            $user = Auth::user();

            // Vérifier l'accès à la caisse
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Paramètres de pagination et filtrage
            $perPage = $request->input('per_page', 50);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $actionFilter = $request->input('action', '');
            $userFilter = $request->input('user_id', '');
            $dateFrom = $request->input('date_from', '');
            $dateTo = $request->input('date_to', '');

            // Construire la requête
            $query = CaisseActivity::with('user:id,name,matricule,email')
                ->orderBy('created_at', 'desc');

            // Filtres
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('action', 'LIKE', "%{$search}%")
                      ->orWhere('details', 'LIKE', "%{$search}%")
                      ->orWhere('user_name', 'LIKE', "%{$search}%");
                });
            }

            if (!empty($actionFilter)) {
                $query->where('action', $actionFilter);
            }

            if (!empty($userFilter)) {
                $query->where('user_id', $userFilter);
            }

            if (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }

            if (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', $dateTo);
            }

            // Récupérer les activités avec pagination
            $activities = $query->paginate($perPage);

            // Récupérer les types d'actions uniques pour les filtres
            $actionTypes = CaisseActivity::select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action');

            // Récupérer les utilisateurs qui ont des activités
            $users = CaisseActivity::select('user_id', 'user_name')
                ->distinct()
                ->whereNotNull('user_id')
                ->with('user:id,name,matricule')
                ->get()
                ->map(function($activity) {
                    return [
                        'id' => $activity->user_id,
                        'name' => $activity->user ? $activity->user->name : $activity->user_name,
                        'matricule' => $activity->user ? $activity->user->matricule : null
                    ];
                })
                ->unique('id')
                ->values();

            return response()->json([
                'success' => true,
                'activities' => $activities->items(),
                'pagination' => [
                    'total' => $activities->total(),
                    'per_page' => $activities->perPage(),
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'from' => $activities->firstItem(),
                    'to' => $activities->lastItem()
                ],
                'filters' => [
                    'action_types' => $actionTypes,
                    'users' => $users
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des activités Caisse:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des activités'
            ], 500);
        }
    }
}
