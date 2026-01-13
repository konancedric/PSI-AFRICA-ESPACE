<?php

namespace App\Http\Controllers;

use App\Models\CRMClient;
use App\Models\CRMInvoice;
use App\Models\CRMPayment;
use App\Models\CRMActivity;
use App\Models\User;
use App\Models\CRMRelance;
use App\Models\CRMClientCommentaire;
use App\Models\CRMContract;
use App\Models\CaisseEntree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CRMController extends Controller
{
    public function __construct()
    {
        // Appliquer le middleware 'auth' à toutes les méthodes SAUF celles qui doivent être publiques
        $this->middleware('auth')->except(['showContract', 'downloadContractPDF']);
    }

public function index(Request $request)
{
    $user = Auth::user();
    
    Log::info('CRM index: Tentative d\'accès', [
        'user' => $user->name,
        'user_id' => $user->id,
        'role' => $user->getRoleNames()->first()
    ]);
    
    // Super Admin et Admin ont TOUJOURS accès
    if ($user->hasRole('Super Admin') || $user->hasRole('Admin')) {
        Log::info('CRM index: Accès accordé - Super Admin/Admin');
        return view('crm.index');
    }
    
    // Pour les autres utilisateurs, vérifier les permissions CRM
    $permissions = $user->getCrmPermissions();
    
    Log::info('CRM index: Permissions utilisateur', [
        'permissions' => $permissions,
        'nb_permissions' => count($permissions)
    ]);
    
    // Si pas de permissions définies, donner accès selon le rôle par défaut
    if (empty($permissions)) {
        if ($user->hasAnyRole(['Manager', 'Commercial', 'Agent Comptoir'])) {
            Log::info('CRM index: Accès accordé - Permissions par défaut selon rôle');
            return view('crm.index');
        }
    } else {
        // ✅ CORRECTION : Vérifier si l'utilisateur a AU MOINS UNE permission CRM valide
        $validPermissions = ['dashboard', 'clients', 'invoicing', 'recovery', 'performance', 'analytics', 'admin'];
        
        // Vérifier s'il y a une intersection entre les permissions de l'utilisateur et les permissions valides
        $hasAnyPermission = count(array_intersect($permissions, $validPermissions)) > 0;
        
        if ($hasAnyPermission) {
            Log::info('CRM index: Accès accordé - Utilisateur a au moins une permission CRM', [
                'permissions_actives' => array_intersect($permissions, $validPermissions)
            ]);
            return view('crm.index');
        }
    }
    
    // Accès refusé UNIQUEMENT si l'utilisateur n'a AUCUNE permission CRM
    Log::warning('CRM index: Accès refusé - Aucune permission CRM', [
        'user' => $user->name,
        'permissions' => $permissions
    ]);
    
    return view('errors.403', [
        'message' => 'Vous n\'avez aucune permission pour accéder au système CRM. Contactez votre administrateur.'
    ]);
}

    public function getStats()
{
    try {
        $user = Auth::user();

        // Statistiques clients
        $totalClients = CRMClient::count();
        $clientsConvertis = CRMClient::where('statut', 'Converti')->count();

        // ✅ AJOUT : Statistiques par statut pour les graphiques
        $clientsParStatut = CRMClient::select('statut', \DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get()
            ->pluck('total', 'statut')
            ->toArray();

        // Statistiques factures
        $totalInvoices = CRMInvoice::count();
        $paidInvoices = CRMInvoice::where('status', 'paid')->count();
        $overdueInvoices = CRMInvoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        // Revenus
        $totalInvoiced = CRMInvoice::sum('amount');
        $totalPaid = CRMInvoice::sum('paid_amount');

        // ✅ AJOUT : Revenus par mois pour le graphique
        $revenusParMois = CRMInvoice::select(
                \DB::raw('MONTH(created_at) as mois'),
                \DB::raw('SUM(paid_amount) as total')
            )
            ->whereYear('created_at', now()->year)
            ->groupBy('mois')
            ->orderBy('mois')
            ->get()
            ->pluck('total', 'mois')
            ->toArray();

        // Activités récentes
        $activities = CRMActivity::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => [
                'clients' => [
                    'total' => $totalClients,
                    'convertis' => $clientsConvertis,
                    'taux_conversion' => $totalClients > 0 ?
                        round(($clientsConvertis / $totalClients) * 100, 1) : 0,
                    // ✅ Données pour le graphique des statuts
                    'par_statut' => $clientsParStatut
                ],
                'invoices' => [
                    'total' => $totalInvoices,
                    'paid' => $paidInvoices,
                    'overdue' => $overdueInvoices
                ],
                'revenue' => [
                    'total_invoiced' => $totalInvoiced,
                    'total_paid' => $totalPaid,
                    // ✅ Données pour le graphique d'évolution
                    'par_mois' => $revenusParMois
                ],
                'activities' => $activities
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur getStats: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function getClients(Request $request)
{
    try {
        $user = Auth::user();
        
        Log::info('📋 getClients appelé', [
            'user' => $user->name,
            'user_id' => $user->id
        ]);
        
        // ✅ REQUÊTE SIMPLE ET SÉCURISÉE
        $query = CRMClient::query();
        
        // Filtrer selon le rôle
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Commercial', 'Agent Comptoir'])) {
            $query->where('user_id', $user->id);
        }

        // Filtres
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }

        if ($request->has('prestation') && $request->prestation) {
            $query->where('prestation', $request->prestation);
        }

        // Filtre relances
        if ($request->has('relance_filter') && $request->relance_filter) {
            $filter = $request->relance_filter;
            
            if ($filter === 'non_relance') {
                $query->whereDoesntHave('relances');
            } elseif ($filter === 'urgent') {
                $query->whereHas('relances', function($q) {
                    $q->where('date_relance', '<', now()->subDays(14))
                      ->where('statut', 'En cours');
                });
            } elseif ($filter === 'bientot') {
                $query->whereHas('relances', function($q) {
                    $q->whereBetween('date_relance', [now()->subDays(14), now()->subDays(7)])
                      ->where('statut', 'En cours');
                });
            } elseif ($filter === 'recent') {
                $query->whereHas('relances', function($q) {
                    $q->where('date_relance', '>=', now()->subDays(7))
                      ->where('statut', 'En cours');
                });
            }
        }

        // ✅ PAGINATION SANS EAGER LOADING - Augmentée à 200
        $clients = $query->orderBy('created_at', 'desc')->paginate(200);

        Log::info('✅ Clients paginés', ['count' => $clients->count()]);

        // ✅ CHARGER LES RELATIONS EN POST-TRAITEMENT
        $clients->getCollection()->transform(function ($client) {
            // Charger les relances avec gestion d'erreur
            try {
                $relances = DB::table('crm_relances')
                    ->where('client_id', $client->id)
                    ->orderBy('date_relance', 'desc')
                    ->limit(10)
                    ->get();
                
                $client->relances = $relances;
            } catch (\Exception $e) {
                Log::warning('Erreur chargement relances', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
                $client->relances = collect([]);
            }

            // Charger les commentaires avec gestion d'erreur
            try {
                $commentaires = DB::table('crm_client_commentaires')
                    ->where('client_id', $client->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
                
                $client->commentaires = $commentaires;
            } catch (\Exception $e) {
                Log::warning('Erreur chargement commentaires', [
                    'client_id' => $client->id,
                    'error' => $e->getMessage()
                ]);
                $client->commentaires = collect([]);
            }

            return $client;
        });

        Log::info('✅ getClients SUCCESS', ['total' => $clients->total()]);

        return response()->json([
            'success' => true, 
            'clients' => $clients
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Erreur getClients', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => 'Erreur serveur',
            'details' => config('app.debug') ? [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ] : null
        ], 500);
    }
}

    public function storeClient(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'contact' => 'required|string|max:255',
            'prestation' => 'required|string|max:255',
            'statut' => 'required|in:Lead,Prospect,Opportunité,Négociation,Converti,Perdu,En attente de paiement des frais de profil visa et d\'inscription,En attente de paiement des frais de cabinet,Profil visa payé,Frais d\'assistance payés,En attente de documents,Documents validés,Rendez-vous au bureau PSI,Rendez-vous d\'urgence,Prise de RDV ambassade confirmée,En attente de décision visa,Visa accepté,Visa refusé,Visa validé,Billet d\'avion payé,Départ confirmé,En suivi post-départ,Message d\'urgence,Opportunité',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $client = CRMClient::create([
            'nom' => $request->nom,
            'prenoms' => $request->prenoms,
            'contact' => $request->contact,
            'email' => $request->email,
            'media' => $request->media ?? 'Facebook',
            'prestation' => $request->prestation,
            'budget' => $request->budget ?? 0,
            'statut' => $request->statut,
            'agent' => $user->name,
            'commentaire' => $request->commentaire,
            'user_id' => $user->id,
        ]);

        // ✅ CRÉER AUTOMATIQUEMENT UNE RELANCE PROGRAMMÉE DANS 7 JOURS
        CRMRelance::create([
            'client_id' => $client->id,
            'agent_name' => $user->name,
            'user_id' => $user->id,
            'statut' => 'En cours',
            'commentaire' => 'Relance automatique - Premier contact programmé suite à la création du client',
            'date_relance' => now(),
            'prochaine_relance' => now()->addDays(7) // 7 jours plus tard
        ]);

        // Enregistrer l'activité
        CRMActivity::create([
            'action' => 'Nouveau Client',
            'details' => "Client {$client->nom} {$client->prenoms} ajouté - Relance programmée dans 7 jours",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Client créé avec succès. Relance automatique programmée dans 7 jours.',
            'client' => $client
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur storeClient: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function deleteClient($id)
{
    try {
        $user = Auth::user();
        $client = CRMClient::findOrFail($id);

        // ✅ VÉRIFICATION DES PERMISSIONS
        $permissions = $user->getCrmPermissions();
        $canDelete = $user->hasAnyRole(['Super Admin', 'Admin']) 
                     || in_array('delete_clients', $permissions);

        if (!$canDelete) {
            Log::warning('CRM deleteClient: Accès refusé', [
                'user' => $user->name,
                'user_id' => $user->id,
                'client_id' => $id,
                'permissions' => $permissions
            ]);
            return response()->json([
                'success' => false, 
                'error' => 'Vous n\'avez pas la permission de supprimer des clients'
            ], 403);
        }

        $clientName = $client->nom;
        $client->delete();

        CRMActivity::create([
            'action' => 'Suppression Client',
            'details' => "Client {$clientName} supprimé par {$user->name}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Client supprimé']);

    } catch (\Exception $e) {
        Log::error('Erreur deleteClient: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function getInvoices(Request $request)
    {
        try {
            $user = Auth::user();
            $query = CRMInvoice::with('client');
            
            if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'])) {
                $query->where('user_id', $user->id);
            }
            
            // ✅ AJOUT DE LA RECHERCHE
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('number', 'like', "%{$search}%")
                      ->orWhere('client_name', 'like', "%{$search}%")
                      ->orWhere('service', 'like', "%{$search}%")
                      ->orWhere('agent', 'like', "%{$search}%");
                });
            }

            // ✅ FILTRE PAR STATUT
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // ✅ FILTRE PAR DATE
            if ($request->has('date_from') && $request->date_from) {
                $query->where('due_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->where('due_date', '<=', $request->date_to);
            }
            
            // ✅ PAGINATION AUGMENTÉE À 200
            $invoices = $query->orderBy('created_at', 'desc')->paginate(200);

            return response()->json(['success' => true, 'invoices' => $invoices]);

        } catch (\Exception $e) {
            Log::error('Erreur getInvoices: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function storeInvoice(Request $request)
{
    try {
        // ✅ VALIDATION AVEC MESSAGES CLAIRS
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:crm_clients,id',
            'service' => 'required|string|max:2000',  // ✅ Augmenté pour texte long
            'amount' => 'required|numeric|min:1',  // ✅ Min 1 FCFA
            'due_date' => 'required|date|after_or_equal:today',  // ✅ Date future
        ], [
            'client_id.required' => 'Veuillez sélectionner un client',
            'client_id.exists' => 'Client introuvable',
            'service.required' => 'Veuillez décrire le service',
            'amount.required' => 'Veuillez indiquer le montant',
            'amount.min' => 'Le montant doit être supérieur à 0',
            'due_date.required' => 'Veuillez indiquer la date d\'échéance',
            'due_date.after_or_equal' => 'La date d\'échéance ne peut pas être dans le passé',
        ]);

        if ($validator->fails()) {
            Log::error('❌ Validation échouée', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false, 
                'error' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $client = CRMClient::findOrFail($request->client_id);

        Log::info('✅ Tentative création facture', [
            'client_id' => $client->id,
            'client_name' => $client->nom,
            'amount' => $request->amount,
            'user' => $user->name
        ]);

        // ✅ CRÉER LA FACTURE
        $invoice = new CRMInvoice();
        $invoice->client_id = $client->id;
        $invoice->client_name = trim($client->nom . ' ' . ($client->prenoms ?? ''));
        $invoice->service = $request->service;
        $invoice->amount = $request->amount;
        $invoice->paid_amount = 0;
        $invoice->status = 'pending';
        $invoice->issue_date = now();
        $invoice->due_date = $request->due_date;
        $invoice->agent = $user->name;
        $invoice->notes = $request->notes;
        $invoice->reminders_count = 0;
        $invoice->user_id = $user->id;
        
        // ✅ SAUVEGARDER AVEC GESTION D'ERREUR
        $saved = $invoice->save();

        if (!$saved) {
            throw new \Exception('Échec de la sauvegarde de la facture');
        }

        Log::info('✅ Facture sauvegardée', [
            'invoice_id' => $invoice->id,
            'number' => $invoice->number
        ]);

        // ✅ ENREGISTRER L'ACTIVITÉ
        CRMActivity::create([
            'action' => 'Nouvelle Facture',
            'details' => "Facture {$invoice->number} créée pour {$client->nom} - Montant: " . number_format($invoice->amount, 0, ',', ' ') . " FCFA",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        // ✅ RECHARGER AVEC RELATIONS
        $invoice->load('client');

        return response()->json([
            'success' => true,
            'message' => 'Facture créée avec succès',
            'invoice' => $invoice
        ], 201);

    } catch (\Exception $e) {
        Log::error('❌ ERREUR CRÉATION FACTURE', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => 'Erreur serveur : ' . $e->getMessage()
        ], 500);
    }
}

    public function viewInvoice($id)
    {
        try {
            $user = Auth::user();
            $invoice = CRMInvoice::with('client', 'payments')->findOrFail($id);
            
            if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial']) && $invoice->user_id != $user->id) {
                return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
            }

            return response()->json(['success' => true, 'invoice' => $invoice]);

        } catch (\Exception $e) {
            Log::error('Erreur viewInvoice: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateInvoice(Request $request, $id)
{
    try {
        $user = Auth::user();
        $invoice = CRMInvoice::findOrFail($id);

        // ✅ VÉRIFICATION DES PERMISSIONS
        $permissions = $user->getCrmPermissions();
        $canEdit = $user->hasAnyRole(['Super Admin', 'Admin']) 
                   || in_array('edit_invoices', $permissions);

        if (!$canEdit) {
            return response()->json([
                'success' => false, 
                'error' => 'Vous n\'avez pas la permission de modifier les factures'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'service' => 'required|string|max:2000',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $invoice->update($request->only(['service', 'amount', 'due_date', 'notes']));

        CRMActivity::create([
            'action' => 'Modification Facture',
            'details' => "Facture {$invoice->number} modifiée par {$user->name}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Facture mise à jour', 'invoice' => $invoice]);

    } catch (\Exception $e) {
        Log::error('Erreur updateInvoice: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
    

    public function printInvoice($id)
    {
        try {
            $user = Auth::user();
            $invoice = CRMInvoice::with('client')->findOrFail($id);
            
            if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial']) && $invoice->user_id != $user->id) {
                return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
            }

            CRMActivity::create([
                'action' => 'Impression Facture',
                'details' => "Facture {$invoice->number} imprimée",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json(['success' => true, 'invoice' => $invoice]);

        } catch (\Exception $e) {
            Log::error('Erreur printInvoice: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function recordPayment(Request $request, $id)
{
    try {
        $user = Auth::user();
        $invoice = CRMInvoice::with('client')->findOrFail($id);
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial', 'Manager'])) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:Espèces,Virement bancaire,Mobile Money,Carte bancaire,Chèque,Autres'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $amount = $request->input('amount');
        $remaining = $invoice->amount - $invoice->paid_amount;
        
        if ($amount <= 0 || $amount > $remaining) {
            return response()->json([
                'success' => false, 
                'error' => 'Montant invalide. Le montant doit être entre 0 et ' . number_format($remaining, 0, ',', ' ') . ' FCFA'
            ], 400);
        }

        // Enregistrer le paiement avec méthode
        CRMPayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $request->input('payment_method', 'Espèces'),
            'user_id' => $user->id,
            'notes' => $request->input('notes', null)
        ]);

        // Mettre à jour la facture
        $invoice->paid_amount += $amount;
        
        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'paid';
            
            // Mettre à jour le statut du client
            $client = $invoice->client;
            if ($client && $client->statut !== 'Converti') {
                $client->statut = 'Converti';
                $client->save();
                
                CRMActivity::create([
                    'action' => 'Client Converti',
                    'details' => "Le client {$client->nom} a été converti suite au paiement de la facture {$invoice->number}",
                    'user_name' => $user->name,
                    'user_id' => $user->id,
                ]);
            }
        } else {
            $invoice->status = 'partial';
        }
        
        $invoice->save();

        CRMActivity::create([
            'action' => 'Paiement Reçu',
            'details' => "Paiement de " . number_format($amount, 0, ',', ' ') . " FCFA via {$request->input('payment_method')} par {$user->name} pour la facture {$invoice->number}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré avec succès',
            'invoice' => [
                'id' => $invoice->id,
                'paid_amount' => $invoice->paid_amount,
                'remaining' => $invoice->amount - $invoice->paid_amount,
                'status' => $invoice->status
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur recordPayment: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'error' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage()
        ], 500);
    }
}


    public function deleteInvoice($id)
{
    try {
        $user = Auth::user();

        // ✅ VÉRIFICATION DES PERMISSIONS
        $permissions = $user->getCrmPermissions();
        $canDelete = $user->hasAnyRole(['Super Admin', 'Admin']) 
                     || in_array('delete_invoices', $permissions);

        if (!$canDelete) {
            return response()->json([
                'success' => false, 
                'error' => 'Vous n\'avez pas la permission de supprimer des factures'
            ], 403);
        }

        $invoice = CRMInvoice::findOrFail($id);
        $invoiceNumber = $invoice->number;
        $invoice->delete();

        CRMActivity::create([
            'action' => 'Suppression Facture',
            'details' => "Facture {$invoiceNumber} supprimée par {$user->name}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Facture supprimée']);

    } catch (\Exception $e) {
        Log::error('Erreur deleteInvoice: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
    public function getRecoveryData()
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'])) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }

        // Factures en retard
        $overdueInvoices = CRMInvoice::with('client')
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->get();

        $totalOverdue = $overdueInvoices->sum(function($inv) {
            return $inv->amount - $inv->paid_amount;
        });

        // Montant récupéré ce mois (paiements reçus ce mois)
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $recoveredThisMonth = CRMPayment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Factures avec informations client pour le tableau
        $invoicesWithClientInfo = $overdueInvoices->map(function($invoice) {
            return [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'amount' => $invoice->amount,
                'paid_amount' => $invoice->paid_amount,
                'due_date' => $invoice->due_date,
                'status' => $invoice->status,
                'service' => $invoice->service,
                'client_name' => $invoice->client->nom . ' ' . ($invoice->client->prenoms ?? ''),
                'client_email' => $invoice->client->email,
                'client_contact' => $invoice->client->contact,
                'reminders_count' => $invoice->reminders_count ?? 0,
                'last_reminder_at' => $invoice->last_reminder_at,
            ];
        });

        return response()->json([
            'success' => true,
            'total_overdue' => $totalOverdue,
            'count' => $overdueInvoices->count(),
            'total_recovered' => $recoveredThisMonth,
            'overdue_invoices' => $invoicesWithClientInfo
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur getRecoveryData: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function getPerformanceData()
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Commercial'])) {
                return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
            }

            $agentPerformance = User::leftJoin('crm_clients', 'users.id', '=', 'crm_clients.user_id')
                ->leftJoin('crm_invoices', 'users.id', '=', 'crm_invoices.user_id')
                ->select(
                    'users.name',
                    DB::raw('COUNT(DISTINCT crm_clients.id) as total_clients'),
                    DB::raw('COUNT(DISTINCT crm_invoices.id) as total_invoices'),
                    DB::raw('COALESCE(SUM(crm_invoices.paid_amount), 0) as revenue')
                )
                ->where('users.id', '!=', 1)
                ->groupBy('users.id', 'users.name')
                ->get();

            return response()->json(['success' => true, 'agent_performance' => $agentPerformance]);

        } catch (\Exception $e) {
            Log::error('Erreur getPerformanceData: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getAnalyticsData()
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        // Statistiques clients
        $totalCustomers = CRMClient::count();
        $activeCustomers = CRMClient::whereIn('statut', ['Opportunité', 'Négociation', 'Converti'])
            ->count();
        
        // Clients perdus pour le taux d'attrition
        $lostCustomers = CRMClient::where('statut', 'Perdu')->count();
        $churnRate = $totalCustomers > 0 ? round(($lostCustomers / $totalCustomers) * 100, 1) : 0;
        
        // Valeur vie client (Customer Lifetime Value)
        $totalRevenue = CRMInvoice::sum('paid_amount');
        $customerLifetimeValue = $totalCustomers > 0 ? round($totalRevenue / $totalCustomers, 0) : 0;

        // Évolution mensuelle (Nouveaux clients et Conversions)
        $monthlyTrends = collect();
        for ($month = 1; $month <= 12; $month++) {
            $newClients = CRMClient::whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->count();
            
            $conversions = CRMClient::whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->where('statut', 'Converti')
                ->count();
            
            $monthlyTrends->push([
                'month' => $month,
                'nouveaux_clients' => $newClients,
                'conversions' => $conversions
            ]);
        }

        // Segmentation par prestation
        $segmentation = CRMClient::select('prestation', DB::raw('COUNT(*) as count'))
            ->groupBy('prestation')
            ->get()
            ->map(function($item) {
                return [
                    'prestation' => $item->prestation,
                    'count' => $item->count
                ];
            });

        return response()->json([
            'success' => true,
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'customer_lifetime_value' => $customerLifetimeValue,
            'churn_rate' => $churnRate,
            'monthly_trends' => $monthlyTrends,
            'segmentation' => $segmentation
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur getAnalyticsData: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function getAdminData()
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $users = User::with('roles')
            ->withCount(['crmClients', 'crmInvoices'])
            ->whereHas('roles', function($query) {
                $query->whereIn('name', [
                    'Super Admin', 
                    'Admin', 
                    'Manager', 
                    'Commercial', 
                    'Agent Comptoir'
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($u) {
                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'etat' => $u->etat,
                    'roles' => $u->getRoleNames()->toArray(),
                    'permissions' => $u->permissions ?? $this->getDefaultPermissionsByRole($u->getRoleNames()->first()),
                    'crm_clients_count' => $u->crm_clients_count,
                    'crm_invoices_count' => $u->crm_invoices_count,
                ];
            });

        $systemStats = [
            'total_users' => User::whereHas('roles', function($query) {
                $query->whereIn('name', [
                    'Super Admin', 
                    'Admin', 
                    'Manager', 
                    'Commercial', 
                    'Agent Comptoir'
                ]);
            })->count(),
            'total_clients' => CRMClient::count(),
            'total_invoices' => CRMInvoice::count(),
            'total_revenue' => CRMInvoice::sum('paid_amount'),
        ];

        return response()->json([
            'success' => true, 
            'users' => $users, 
            'system_stats' => $systemStats
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur getAdminData: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
public function performance(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Commercial'])) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }
        
        // === CALCUL DIRECT DES STATISTIQUES ===
        
        // 1. CA Total (utiliser la somme des montants payés des factures)
        $totalRevenue = DB::table('crm_invoices')->sum('paid_amount');
        
        // 2. Clients et conversion
        $totalClients = DB::table('crm_clients')->count();
        $convertedClients = DB::table('crm_clients')->where('statut', 'Converti')->count();
        $conversionRate = $totalClients > 0 ? round(($convertedClients / $totalClients) * 100, 1) : 0;
        
        // 3. Taille moyenne affaire
        $totalInvoices = DB::table('crm_invoices')->count();
        $avgDealSize = $totalInvoices > 0 ? round($totalRevenue / $totalInvoices, 0) : 0;
        
        // 4. Cycle de vente
        $avgSalesCycle = 15;
        
        // === PERFORMANCE PAR AGENT (UNIQUEMENT CEUX AVEC DONNÉES) ===
        $agentPerformance = DB::table('users')
            ->leftJoin('crm_clients', 'users.id', '=', 'crm_clients.user_id')
            ->leftJoin('crm_invoices', 'users.id', '=', 'crm_invoices.user_id')
            ->select(
                'users.name',
                DB::raw('COUNT(DISTINCT crm_clients.id) as total_clients'),
                DB::raw('COUNT(DISTINCT CASE WHEN crm_clients.statut = "Converti" THEN crm_clients.id END) as converted_clients'),
                DB::raw('COUNT(DISTINCT crm_invoices.id) as total_invoices'),
                DB::raw('COALESCE(SUM(crm_invoices.paid_amount), 0) as revenue')
            )
            ->where('users.etat', 1)
            ->groupBy('users.id', 'users.name')
            ->having('total_clients', '>', 0) // FILTRER : uniquement les agents avec des clients
            ->orderByDesc('revenue')
            ->get()
            ->map(function($agent) {
                $convRate = $agent->total_clients > 0 
                    ? round(($agent->converted_clients / $agent->total_clients) * 100, 1) 
                    : 0;
                
                return [
                    'name' => $agent->name,
                    'total_clients' => (int)$agent->total_clients,
                    'converted_clients' => (int)$agent->converted_clients,
                    'total_invoices' => (int)$agent->total_invoices,
                    'revenue' => (float)$agent->revenue,
                    'conversion_rate' => (float)$convRate
                ];
            });
        
        // === ÉVOLUTION MENSUELLE ===
        $monthlyRevenue = [];
        for ($month = 1; $month <= 12; $month++) {
            $revenue = DB::table('crm_invoices')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->sum('paid_amount');
            
            $monthlyRevenue[] = [
                'month' => $month,
                'revenue' => (float)$revenue
            ];
        }
        
        return response()->json([
            'success' => true,
            'agent_performance' => $agentPerformance,
            'monthly_revenue' => $monthlyRevenue,
            'stats' => [
                'total_revenue' => (float)$totalRevenue,
                'conversion_rate' => (float)$conversionRate,
                'avg_deal_size' => (float)$avgDealSize,
                'avg_sales_cycle' => (int)$avgSalesCycle,
                'revenue_growth' => 25,
                'conversion_growth' => 5,
                'deal_growth' => 12,
                'cycle_improvement' => 3
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('❌ Erreur performance: ' . $e->getMessage());
        
        return response()->json([
            'success' => false, 
            'error' => 'Erreur lors du chargement',
            'details' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

private function getDefaultPermissionsByRole($role)
{
    $permissions = ['dashboard', 'clients', 'invoicing'];
    
    if (in_array($role, ['Super Admin', 'Admin'])) {
        $permissions = [
            'dashboard', 'clients', 'invoicing', 'recovery', 'performance', 
            'analytics', 'admin', 'edit_clients', 'delete_clients', 
            'edit_invoices', 'delete_invoices', 'edit_payments', 'delete_payments'
        ];
    } elseif (in_array($role, ['Manager', 'Commercial'])) {
        $permissions = [
            'dashboard', 'clients', 'invoicing', 'recovery', 'performance',
            'edit_clients', 'edit_invoices', 'edit_payments'
        ];
    } elseif ($role === 'Agent Comptoir') {
        $permissions = [
            'dashboard', 'clients', 'invoicing', 'recovery',
            'edit_clients', 'edit_payments'
        ];
    }
    
    return $permissions;
}

public function getUserDetails($id)
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $targetUser = User::with('roles')->findOrFail($id);
        
        // Permissions CRM actuelles de l'utilisateur
        $currentPermissions = $targetUser->getCrmPermissions();
        
        // Si aucune permission définie, utiliser les permissions par défaut
        if (empty($currentPermissions)) {
            $currentPermissions = $this->getDefaultPermissionsByRole($targetUser->getRoleNames()->first());
        }
        
        Log::info('getUserDetails: Détails utilisateur récupérés', [
            'user_id' => $targetUser->id,
            'name' => $targetUser->name,
            'permissions' => $currentPermissions
        ]);
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $targetUser->id,
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'etat' => $targetUser->etat,
                'roles' => $targetUser->getRoleNames()->toArray(),
                'permissions' => $currentPermissions,
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('getUserDetails: Erreur', [
            'error' => $e->getMessage(),
            'user_id' => $id
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => $e->getMessage()
        ], 500);
    }
}
public function updateUserPermissions(Request $request, $id)
{
    try {
        $admin = Auth::user();
        
        if (!$admin->hasAnyRole(['Super Admin', 'Admin'])) {
            Log::warning('updateUserPermissions: Accès refusé', [
                'admin' => $admin->name,
                'admin_role' => $admin->getRoleNames()->first()
            ]);
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $user = User::findOrFail($id);
        $permissions = $request->input('permissions', []);
        
        Log::info('updateUserPermissions: Début mise à jour', [
            'admin' => $admin->name,
            'user_cible' => $user->name,
            'user_id' => $user->id,
            'permissions_recues' => $permissions,
            'type_permissions' => gettype($permissions)
        ]);

        // S'assurer que permissions est un array
        if (!is_array($permissions)) {
            $permissions = [];
        }

        // Enregistrer dans crm_permissions
        $user->crm_permissions = $permissions;
        $saved = $user->save();
        
        Log::info('updateUserPermissions: Sauvegarde effectuée', [
            'saved' => $saved,
            'user_id' => $user->id,
            'permissions_enregistrees' => $user->crm_permissions,
            'db_value' => DB::table('users')->where('id', $user->id)->value('crm_permissions')
        ]);
        
        // Vérifier que les permissions ont bien été enregistrées
        $user->refresh();
        $verifiedPermissions = $user->getCrmPermissions();
        
        Log::info('updateUserPermissions: Vérification post-sauvegarde', [
            'user_id' => $user->id,
            'permissions_verifiees' => $verifiedPermissions
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permissions CRM mises à jour avec succès',
            'permissions' => $verifiedPermissions,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->getRoleNames()->first()
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('updateUserPermissions: Erreur', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'user_id' => $id ?? 'inconnu'
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
        ], 500);
    }
}

public function toggleUserStatus(Request $request, $id)
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $targetUser = User::findOrFail($id);
        
        if ($targetUser->id == 1) {
            return response()->json(['success' => false, 'error' => 'Impossible de modifier le Super Admin'], 403);
        }

        $targetUser->etat = $request->status;
        $targetUser->save();

        $action = $request->status == 1 ? 'activé' : 'bloqué';

        CRMActivity::create([
            'action' => 'Modification Statut Utilisateur',
            'details' => "Utilisateur {$targetUser->name} {$action}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour']);

    } catch (\Exception $e) {
        Log::error('Erreur toggleUserStatus: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function resetUserPassword($id)
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $targetUser = User::findOrFail($id);
        
        if ($targetUser->id == 1) {
            return response()->json(['success' => false, 'error' => 'Impossible de modifier le Super Admin'], 403);
        }

        $targetUser->password = bcrypt('password123');
        $targetUser->save();

        CRMActivity::create([
            'action' => 'Reset Mot de Passe',
            'details' => "Mot de passe de {$targetUser->name} réinitialisé",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Mot de passe réinitialisé']);

    } catch (\Exception $e) {
        Log::error('Erreur resetUserPassword: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function deleteUser($id)
{
    try {
        $user = Auth::user();
        
        if (!$user->hasRole('Super Admin')) {
            return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
        }

        $targetUser = User::findOrFail($id);
        
        if ($targetUser->id == 1) {
            return response()->json(['success' => false, 'error' => 'Impossible de supprimer le Super Admin'], 403);
        }

        $userName = $targetUser->name;
        $targetUser->delete();

        CRMActivity::create([
            'action' => 'Suppression Utilisateur',
            'details' => "Utilisateur {$userName} supprimé",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Utilisateur supprimé']);

    } catch (\Exception $e) {
        Log::error('Erreur deleteUser: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
public function getUserPermissions()
{
    try {
        $user = Auth::user();
        
        Log::info('getUserPermissions: Récupération permissions', [
            'user' => $user->name,
            'user_id' => $user->id,
            'role' => $user->getRoleNames()->first()
        ]);
        
        // Super Admin a toutes les permissions
        if ($user->hasRole('Super Admin')) {
            $permissions = ['dashboard', 'clients', 'invoicing', 'recovery', 'performance', 'analytics', 'admin'];
            
            Log::info('getUserPermissions: Super Admin - Toutes permissions', [
                'permissions' => $permissions
            ]);
        } else {
            // Récupérer les permissions personnalisées
            $permissions = $user->getCrmPermissions();
            
            // Si aucune permission définie, utiliser les permissions par défaut
            if (empty($permissions)) {
                $permissions = $this->getDefaultPermissionsByRole($user->getRoleNames()->first());
                
                Log::info('getUserPermissions: Permissions par défaut utilisées', [
                    'role' => $user->getRoleNames()->first(),
                    'permissions' => $permissions
                ]);
            } else {
                Log::info('getUserPermissions: Permissions personnalisées trouvées', [
                    'permissions' => $permissions
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'permissions' => $permissions,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->getRoleNames()->first()
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('getUserPermissions: Erreur', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => $e->getMessage()
        ], 500);
    }
}

public function updateClient(Request $request, $id)
{
    try {
        $user = Auth::user();
        $client = CRMClient::findOrFail($id);

        // ✅ VÉRIFICATION DES PERMISSIONS
        $permissions = $user->getCrmPermissions();
        $canEdit = $user->hasAnyRole(['Super Admin', 'Admin']) 
                   || in_array('edit_clients', $permissions)
                   || $client->user_id == $user->id; // Peut éditer ses propres clients

        if (!$canEdit) {
            Log::warning('CRM updateClient: Accès refusé', [
                'user' => $user->name,
                'user_id' => $user->id,
                'client_id' => $id,
                'permissions' => $permissions
            ]);
            return response()->json([
                'success' => false, 
                'error' => 'Vous n\'avez pas la permission de modifier ce client'
            ], 403);
        }

        Log::info('📝 Mise à jour client', [
            'client_id' => $id,
            'ancien_statut' => $client->statut,
            'nouveau_statut' => $request->statut
        ]);

        // Mise à jour des champs
        if ($request->has('nom')) $client->nom = $request->nom;
        if ($request->has('prenoms')) $client->prenoms = $request->prenoms;
        if ($request->has('contact')) $client->contact = $request->contact;
        if ($request->has('email')) $client->email = $request->email;
        if ($request->has('statut')) $client->statut = $request->statut;
        if ($request->has('prestation')) $client->prestation = $request->prestation;
        if ($request->has('budget')) $client->budget = $request->budget;
        if ($request->has('commentaire')) $client->commentaire = $request->commentaire;
        if ($request->has('media')) $client->media = $request->media;

        $client->save();

        // ✅ CORRECTION : Si le client passe au statut "Visa validé", clôturer automatiquement toutes ses relances en cours
        if ($request->has('statut') && $request->statut === 'Visa validé') {
            $relancesEnCours = CRMRelance::where('client_id', $client->id)
                ->where('statut', 'En cours')
                ->get();

            foreach ($relancesEnCours as $relance) {
                $relance->statut = 'Clôturé';
                $relance->commentaire .= "\n\n✅ Relance clôturée automatiquement : Visa validé obtenu le " . now()->format('d/m/Y');
                $relance->save();
            }

            if ($relancesEnCours->count() > 0) {
                Log::info("✅ {$relancesEnCours->count()} relance(s) clôturée(s) automatiquement pour le client {$client->nom} (Visa validé)");
            }
        }

        CRMActivity::create([
            'action' => 'Modification Client',
            'details' => "Client {$client->nom} modifié par {$user->name}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        $client->load(['relances' => function($q) {
            $q->orderBy('date_relance', 'desc')->limit(10);
        }]);

        Log::info('✅ Client mis à jour avec succès', [
            'client_id' => $client->id,
            'nouveau_statut' => $client->statut
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Client mis à jour avec succès',
            'client' => $client
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Erreur updateClient', [
            'client_id' => $id,
            'error' => $e->getMessage(),
            'line' => $e->getLine()
        ]);
        
        return response()->json([
            'success' => false, 
            'error' => 'Erreur de mise à jour: ' . $e->getMessage()
        ], 500);
    }
}
public function getClientInvoices($clientId)
{
    try {
        $user = Auth::user();
        
        // Vérifier que le client existe
        $client = CRMClient::findOrFail($clientId);
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial']) 
            && $client->user_id != $user->id) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        // Récupérer toutes les factures du client
        $invoices = CRMInvoice::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'invoices' => $invoices,
            'client' => $client
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getClientInvoices: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

// ==================== RELANCES ====================

public function getRelances(Request $request)
{
    try {
        $user = Auth::user();
        $query = CRMRelance::with(['client', 'user']);

        // ✅ MODIFICATION : Tous les agents peuvent voir toutes les relances (avec le nom de l'agent qui a fait la relance)
        // Les relances sont maintenant visibles par tous pour améliorer la transparence et la coordination
        // Le nom de l'agent qui a fait chaque relance est affiché dans la colonne AGENT

        // Filtres
        if ($request->has('statut') && $request->statut) {
            $query->where('statut', $request->statut);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('client', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%");
            });
        }
        
        // Clients à relancer cette semaine
        // ✅ CORRECTION : Exclure les clients avec "Visa validé" car ils n'ont plus besoin de relances
        // ✅ CORRECTION : Un client relancé il y a moins de 7 jours (urgence OK ✅) ne doit PAS apparaître ici
        //                 Il apparaît uniquement dans l'historique des relances
        //                 Seuls les clients dont la DERNIÈRE relance est il y a plus de 7 jours apparaissent
        // ✅ FIX : Utiliser une sous-requête pour vérifier seulement la DERNIÈRE relance, pas toutes
        $clientsARelancer = CRMClient::where('statut', '!=', 'Visa validé')
            ->with(['relances' => function($q) {
                $q->orderBy('date_relance', 'desc')->limit(1);
            }])
            ->get()
            ->filter(function($client) {
                // Si le client n'a aucune relance, il doit apparaître (URGENT)
                if ($client->relances->isEmpty()) {
                    return true;
                }
                // Si la DERNIÈRE relance est il y a plus de 7 jours, il doit apparaître
                $derniereRelance = $client->relances->first();
                $joursDepuis = now()->diffInDays($derniereRelance->date_relance);
                return $joursDepuis > 7;
            })
            ->values();
        
        $relances = $query->orderBy('date_relance', 'desc')->paginate(50);
        
        return response()->json([
            'success' => true,
            'relances' => $relances,
            'clients_a_relancer' => $clientsARelancer
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getRelances: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

// Removed duplicate storeRelance method to fix redeclaration error.

public function updateRelance(Request $request, $id)
{
    try {
        $user = Auth::user();
        $relance = CRMRelance::findOrFail($id);
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager']) 
            && $relance->user_id != $user->id) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:En cours,Clôturé',
            'commentaire' => 'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Mettre à jour la relance
        $relance->statut = $request->statut;
        
        if ($request->has('commentaire')) {
            $relance->commentaire = $request->commentaire;
        }
        
        // Recalculer la prochaine relance
        if ($request->statut === 'En cours') {
            $relance->prochaine_relance = now()->addDays(7);
        } else {
            $relance->prochaine_relance = null;
        }
        
        $relance->save();
        
        CRMActivity::create([
            'action' => 'Modification Relance',
            'details' => "Relance modifiée - Nouveau statut: {$relance->statut}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Relance mise à jour',
            'relance' => $relance
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur updateRelance: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function getClientRelances($clientId)
{
    try {
        $client = CRMClient::findOrFail($clientId);
        
        $relances = CRMRelance::where('client_id', $clientId)
            ->with('user')
            ->orderBy('date_relance', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'client' => $client,
            'relances' => $relances
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getClientRelances: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function getRelancesStats()
{
    try {
        // ✅ CORRECTION : Les statistiques de relances sont maintenant partagées entre tous les agents
        // Cela permet à chaque agent de voir le nombre total de relances effectuées par l'équipe
        // Quand un agent fait une relance, le compteur diminue pour tout le monde

        $stats = [
            'total' => CRMRelance::count(),
            'en_cours' => CRMRelance::where('statut', 'En cours')->count(),
            'clotures' => CRMRelance::where('statut', 'Clôturé')->count(),
            'cette_semaine' => CRMRelance::whereBetween('date_relance', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            // ✅ CORRECTION : Exclure les clients avec "Visa validé" du comptage des relances à faire
            'a_relancer_aujourd_hui' => CRMRelance::where('prochaine_relance', '<=', now())
                ->where('statut', 'En cours')
                ->whereHas('client', function($q) {
                    $q->where('statut', '!=', 'Visa validé');
                })
                ->count(),
            // ✅ AJOUT : Clients jamais relancés
            'jamais_relances' => CRMClient::whereDoesntHave('relances')
                ->where('statut', '!=', 'Visa validé')
                ->count()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur getRelancesStats: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}




private function getRelanceTemplate($statut, $canal = 'whatsapp')
{
    $templates = [
        
        // ==================== PHASE 1 - DÉCOUVERTE ====================
        'Lead' => [
            'sms' => "Bonjour, ici PSI AFRICA. Nos conseils sont 100% gratuits pour vous aider à voyager légalement. Souhaitez-vous en bénéficier ?",
            'whatsapp' => "Bonjour et bienvenue chez PSI AFRICA. Nos conseils gratuits vous aident à trouver la voie légale la plus sûre pour voyager. Souhaitez-vous une évaluation de votre profil ?",
            'email' => [
                'subject' => "Bienvenue chez PSI AFRICA - Profitez de vos conseils gratuits",
                'body' => "Bonjour,\n\nVous pouvez bénéficier gratuitement de nos conseils pour identifier le visa qui correspond à votre profil. Cliquez ici pour démarrer.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Prospect' => [
            'sms' => "Bonjour, avez-vous avancé dans votre projet de voyage ? Nos conseillers sont disponibles gratuitement pour vous orienter.",
            'whatsapp' => "Bonjour, votre projet de voyage avance-t-il ? Chez PSI AFRICA, nous vous conseillons gratuitement sur toutes les procédures légales et sûres.",
            'email' => [
                'subject' => "Ne laissez pas votre projet s'arrêter ici",
                'body' => "Bonjour,\n\nProfitez de nos conseils gratuits pour avancer sur une procédure légale, simple et sécurisée. Cliquez ici pour réserver votre entretien.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'À convertir' => [
            'sms' => "Bonjour, votre dossier est presque prêt. Il ne reste qu'à valider votre paiement pour démarrer la procédure.",
            'whatsapp' => "Bonjour, votre dossier est complet à 90 %. Dès validation de votre paiement, notre équipe lance votre procédure et le suivi personnalisé.",
            'email' => [
                'subject' => "Finalisez votre dossier dès aujourd'hui",
                'body' => "Bonjour,\n\nVotre projet de voyage est prêt à démarrer. Finalisez votre paiement pour bénéficier d'un accompagnement complet et sécurisé avec PSI AFRICA. Finaliser maintenant.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Perdu' => [
            'sms' => "Bonjour, nous n'avons pas eu de retour de votre part. Souhaitez-vous que nous reprenions contact pour finaliser votre projet ?",
            'whatsapp' => "Bonjour, cela fait un moment sans nouvelles de vous. Votre projet de voyage est toujours réalisable, souhaitez-vous qu'on en discute ?",
            'email' => [
                'subject' => "Votre projet est-il toujours d'actualité ?",
                'body' => "Bonjour,\n\nNous restons à votre disposition pour reprendre ensemble votre projet de voyage légal et sûr.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        // ==================== PHASE 2 - ENGAGEMENT ====================
        'Profil visa payé' => [
            'sms' => "Bonjour, nous confirmons la réception de votre paiement. Votre profil visa sera étudié dans un délai maximum de 72 heures ouvrées.",
            'whatsapp' => "Bonjour, votre paiement pour l'étude de profil est bien reçu. Vous recevrez votre résultat par mail dans un délai de 72 heures ouvrées.",
            'email' => [
                'subject' => "Confirmation de paiement - Étude de votre profil visa",
                'body' => "Bonjour,\n\nNous confirmons la réception de votre paiement. Votre profil visa est en cours d'analyse et vous recevrez un retour sous 72 heures.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],

        'En attente de paiement des frais de profil visa et d\'inscription' => [
            'sms' => "Bonjour {{client_prenoms}}, votre dossier PSI Africa est prêt à démarrer. Il ne reste plus qu'à régler vos frais de profil visa et d'inscription (115.000 F CFA) pour lancer la procédure. Contactez-nous vite pour finaliser. -- PSI Africa, immigration légale et transparente.",
            'whatsapp' => "Bonjour {{client_prenoms}}\nNous espérons que vous allez bien.\nVotre dossier est **en attente du paiement des frais de profil visa et d'inscription (115.000 F CFA)**.\nCette étape valide votre **éligibilité et votre accompagnement personnalisé**.\n\n✅ Paiement possible au bureau ou par Mobile Money.\n📞 Contactez-nous dès aujourd'hui pour réserver votre place.\n\nPSI Africa -- Conseil 100 % transparent & accompagnement professionnel.",
            'email' => [
                'subject' => "Finalisez votre inscription PSI Africa dès aujourd'hui",
                'body' => "Bonjour {{client_prenoms}},\n\nVotre dossier est **en attente du règlement de vos frais de profil visa et d'inscription (115.000 F CFA)**.\nUne fois validés, nous lancerons immédiatement l'analyse de votre profil et la préparation de vos documents de voyage.\n\nVous pouvez régler en agence ou via Mobile Money.\n\nPSI Africa -- l'immigration légale et transparente à votre service."
            ]
        ],
        
        // ✅ NOUVEAU STATUT 2
        'En attente de paiement des frais de cabinet' => [
            'sms' => "Bonjour {{client_prenoms}}, votre dossier PSI Africa est en attente du paiement des frais de cabinet (500.000 F CFA ou 250.000 F CFA à la signature + solde sous 45 jours). Finalisez pour activer votre accompagnement complet. -- PSI Africa.",
            'whatsapp' => "Bonjour {{client_prenoms}}\nVotre dossier PSI Africa est à l'étape des **frais de cabinet**.\nVous pouvez régler **500.000 F CFA au comptant** ou **250.000 F CFA maintenant** et le reste sous **45 jours**.\n\nCe paiement valide votre **prise en charge totale par nos experts** (visa, documents, suivi et assistance complète).\n\nContactez-nous pour planifier votre règlement dès aujourd'hui.\nPSI Africa -- Transparence, sécurité, réussite.",
            'email' => [
                'subject' => "Validez votre accompagnement complet PSI Africa",
                'body' => "Bonjour {{client_prenoms}},\n\nNous vous rappelons que votre dossier est en attente du **règlement des frais de cabinet**.\nVous avez la possibilité de :\n-- payer **500.000 F CFA comptant**, ou\n-- régler **250.000 F CFA dès maintenant**, puis le solde sous **45 jours**.\n\nCe paiement confirme votre accompagnement officiel pour toutes les démarches liées à votre visa et votre voyage.\n\nContactez-nous pour finaliser votre inscription.\n\nPSI Africa -- votre partenaire de confiance pour une immigration réussie."
            ]
        ],
        
        'Frais d\'assistance payés' => [
            'sms' => "Bonjour, nous avons bien reçu vos frais d'assistance. Votre procédure est désormais active.",
            'whatsapp' => "Bonjour, vos frais d'assistance sont validés. Notre équipe a démarré la procédure selon votre profil.",
            'email' => [
                'subject' => "Votre procédure est désormais active",
                'body' => "Bonjour,\n\nMerci pour votre confiance. Vos frais d'assistance ont été reçus. Votre dossier est en cours de traitement par notre équipe.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'En attente de documents' => [
            'sms' => "Bonjour, votre dossier est en attente de certains documents. Merci de les transmettre dès que possible pour ne pas retarder la procédure.",
            'whatsapp' => "Bonjour, nous attendons encore quelques documents pour avancer votre dossier. Pouvez-vous les envoyer aujourd'hui ?",
            'email' => [
                'subject' => "Documents manquants pour la suite de votre procédure",
                'body' => "Bonjour,\n\nIl manque encore certains documents à votre dossier. Merci de les transmettre afin que nous puissions poursuivre la procédure sans délai.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Documents validés' => [
            'sms' => "Bonjour, vos documents ont été vérifiés et validés. Nous préparons la prochaine étape de votre dossier.",
            'whatsapp' => "Bonjour, vos documents sont désormais validés. Notre équipe vous contactera pour la suite.",
            'email' => [
                'subject' => "Vos documents sont validés - prochaine étape",
                'body' => "Bonjour,\n\nVotre dossier est complet. Notre équipe prépare la suite de votre accompagnement.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Rendez-vous au bureau PSI' => [
            'sms' => "Bonjour, votre rendez-vous au bureau PSI AFRICA est confirmé. Merci d'être ponctuel et de venir avec vos pièces justificatives.",
            'whatsapp' => "Bonjour, nous confirmons votre rendez-vous au bureau PSI AFRICA pour finaliser vos documents. Merci d'apporter vos pièces le jour du rendez-vous.",
            'email' => [
                'subject' => "Confirmation de votre rendez-vous PSI AFRICA",
                'body' => "Bonjour,\n\nVotre rendez-vous au bureau PSI AFRICA est confirmé. Merci d'arriver à l'heure convenue avec les pièces demandées.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Rendez-vous d\'urgence' => [
            'sms' => "Bonjour, merci de vous rendre d'urgence au bureau PSI AFRICA pour régulariser votre dossier.",
            'whatsapp' => "Bonjour, nous vous demandons de venir d'urgence au bureau PSI AFRICA afin de finaliser votre procédure.",
            'email' => [
                'subject' => "Rendez-vous d'urgence - régularisation de votre dossier",
                'body' => "Bonjour,\n\nVeuillez-vous rendre rapidement dans nos bureaux afin de régulariser votre dossier et éviter tout retard.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        // ==================== PHASE 3 - VISA ====================
        'Prise de RDV ambassade confirmée' => [
            'sms' => "Bonjour, votre rendez-vous à l'ambassade est confirmé. Merci de vérifier vos documents avant le jour du dépôt.",
            'whatsapp' => "Bonjour, votre rendez-vous à l'ambassade est confirmé. Notre équipe reste disponible pour vérifier vos documents avant le dépôt.",
            'email' => [
                'subject' => "Confirmation de votre rendez-vous à l'ambassade",
                'body' => "Bonjour,\n\nVotre rendez-vous à l'ambassade est désormais fixé. Assurez-vous que tous vos documents sont prêts.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'En attente de décision visa' => [
            'sms' => "Bonjour, votre dossier est actuellement en traitement à l'ambassade. Nous vous informerons dès réception de la décision.",
            'whatsapp' => "Bonjour, votre demande de visa est en cours d'examen. Nous suivons la situation et vous tiendrons informé dès qu'il y aura une mise à jour.",
            'email' => [
                'subject' => "Suivi de votre dossier visa",
                'body' => "Bonjour,\n\nVotre dossier est en cours d'étude à l'ambassade. Nous vous informerons dès qu'une décision sera prise.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Visa accepté' => [
            'sms' => "Félicitations, votre visa a été accepté. Notre équipe vous contactera pour les étapes suivantes.",
            'whatsapp' => "Bonne nouvelle, votre visa est accepté. Contactez-nous pour organiser votre voyage.",
            'email' => [
                'subject' => "Félicitations - votre visa est accepté",
                'body' => "Bonjour,\n\nVotre visa a été approuvé. Contactez notre équipe pour la suite de votre projet.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Visa refusé' => [
            'sms' => "Bonjour, votre visa a malheureusement été refusé. Contactez-nous pour étudier les solutions possibles.",
            'whatsapp' => "Bonjour, nous avons reçu la décision de refus de votre visa. Nous pouvons vous conseiller sur les démarches à suivre.",
            'email' => [
                'subject' => "Retour sur votre dossier visa",
                'body' => "Bonjour,\n\nNous avons pris connaissance du refus de votre visa. Nos conseillers peuvent vous aider à préparer une nouvelle demande solide.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Visa validé' => [
            'sms' => "Bonjour, votre visa est validé. Vous pouvez désormais organiser votre voyage.",
            'whatsapp' => "Félicitations ! Votre visa est validé. Nous restons disponibles pour la suite.",
            'email' => [
                'subject' => "Visa validé - Félicitations",
                'body' => "Bonjour,\n\nVotre visa est désormais validé. Nous restons à votre disposition pour la suite de votre projet.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        // ==================== PHASE 4 - VOYAGE ====================
        'Billet d\'avion payé' => [
            'sms' => "Bonjour, nous confirmons la réception de votre paiement pour le billet d'avion.",
            'whatsapp' => "Bonjour, votre billet d'avion est confirmé. Vous recevrez vos documents de voyage sous peu.",
            'email' => [
                'subject' => "Confirmation de votre billet d'avion",
                'body' => "Bonjour,\n\nVotre paiement pour le billet d'avion est bien reçu. Votre confirmation de vol vous sera transmise prochainement.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Départ confirmé' => [
            'sms' => "Bonjour, votre départ est confirmé. Nous vous souhaitons un excellent voyage.",
            'whatsapp' => "Bonjour, tout est prêt pour votre départ. PSI AFRICA vous remercie pour votre confiance.",
            'email' => [
                'subject' => "Bon voyage avec PSI AFRICA",
                'body' => "Bonjour,\n\nVotre départ est confirmé. Nous vous souhaitons un excellent séjour et restons à votre disposition.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'En suivi post-départ' => [
            'sms' => "Bonjour, nous espérons que votre séjour se passe bien. PSI AFRICA reste disponible si besoin.",
            'whatsapp' => "Bonjour, comment se passe votre séjour ? N'hésitez pas à nous contacter si besoin d'assistance.",
            'email' => [
                'subject' => "Suivi de votre séjour",
                'body' => "Bonjour,\n\nNous espérons que tout se passe bien pour vous. PSI AFRICA reste à votre écoute pour toute assistance.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        // ==================== PHASE 5 - RELANCE / GESTION SPÉCIALE ====================
        'Négociation' => [
            'sms' => "Bonjour, nous sommes prêts à démarrer votre dossier. Pouvons-nous discuter des modalités finales ?",
            'whatsapp' => "Bonjour, votre dossier est validé. Notre équipe est disponible pour finaliser les derniers détails avec vous.",
            'email' => [
                'subject' => "Finalisation de votre dossier PSI AFRICA",
                'body' => "Bonjour,\n\nNous sommes prêts à démarrer votre accompagnement. Contactez-nous pour finaliser les derniers détails.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Message d\'urgence' => [
            'sms' => "Bonjour, merci de nous recontacter d'urgence afin de régulariser votre dossier.",
            'whatsapp' => "Bonjour, veuillez nous recontacter rapidement pour finaliser votre procédure.",
            'email' => [
                'subject' => "Urgent - votre dossier nécessite une action immédiate",
                'body' => "Bonjour,\n\nNous vous prions de nous contacter rapidement afin d'éviter le blocage de votre dossier.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        'Opportunité' => [
            'sms' => "Bonjour, avez-vous un nouveau projet de voyage ? PSI AFRICA peut à nouveau vous accompagner.",
            'whatsapp' => "Bonjour, nous espérons que vous allez bien. Avez-vous un nouveau projet ? Nos conseillers sont disponibles pour vous aider.",
            'email' => [
                'subject' => "Et si nous réalisions un nouveau projet ensemble ?",
                'body' => "Bonjour,\n\nPSI AFRICA reste à votre service pour vos nouveaux projets de voyage ou de formation à l'étranger.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],
        
        // Alias pour compatibilité avec les anciens statuts
        'Converti' => [
            'sms' => "Bonjour, nous confirmons la réception de votre paiement. Votre profil visa sera étudié dans un délai maximum de 72 heures ouvrées.",
            'whatsapp' => "Bonjour, votre paiement pour l'étude de profil est bien reçu. Vous recevrez votre résultat par mail dans un délai de 72 heures ouvrées.",
            'email' => [
                'subject' => "Confirmation de paiement - Étude de votre profil visa",
                'body' => "Bonjour,\n\nNous confirmons la réception de votre paiement. Votre profil visa est en cours d'analyse et vous recevrez un retour sous 72 heures.\n\nCordialement,\nL'équipe PSI AFRICA"
            ]
        ],

        
    ];
    
    return $templates[$statut][$canal] ?? null;
}

// ==================== FONCTION CORRIGÉE : storeRelance ====================
public function storeRelance(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:crm_clients,id',
            'commentaire' => 'required|string',
            'statut' => 'required|in:En cours,Clôturé',
            'canal' => 'nullable|in:sms,whatsapp,email',
            'message_type' => 'nullable|string', // Template automatique ou personnalisé
        ], [
            'client_id.required' => 'Veuillez sélectionner un client',
            'commentaire.required' => 'Veuillez ajouter un commentaire',
            'statut.required' => 'Veuillez sélectionner un statut',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        $client = CRMClient::findOrFail($request->client_id);

        // ✅ VÉRIFICATION : Empêcher la création de relances pour les clients avec "Visa validé"
        if ($client->statut === 'Visa validé') {
            return response()->json([
                'success' => false,
                'error' => 'Ce client a obtenu son visa validé. Les relances ne sont plus nécessaires.'
            ], 400);
        }

        // ✅ OBTENIR LE TEMPLATE AUTOMATIQUE SI DEMANDÉ
        $commentaire = $request->commentaire;
        $canal = $request->canal ?? 'whatsapp';
        
        if ($request->message_type === 'auto') {
            $template = $this->getRelanceTemplate($client->statut, $canal);
            
            if ($template) {
                if (is_array($template)) {
                    // Email : ajouter sujet + corps
                    $commentaire = "📧 Email envoyé\nObjet: {$template['subject']}\n\n{$template['body']}";
                } else {
                    // SMS/WhatsApp
                    $icon = $canal === 'sms' ? '📱' : '💬';
                    $commentaire = "{$icon} {$canal}: {$template}";
                }
            }
        }
        
        // ✅ CALCULER LA PROCHAINE RELANCE
        $prochaineRelance = $request->statut === 'En cours' ? 
            now()->addDays(7) : null;
        
        $relance = CRMRelance::create([
            'client_id' => $client->id,
            'agent_name' => $user->name,
            'user_id' => $user->id,
            'statut' => $request->statut,
            'commentaire' => $commentaire,
            'date_relance' => now(),
            'prochaine_relance' => $prochaineRelance,
            'canal' => $canal, // ✅ NOUVEAU CHAMP (ajouter à la migration)
        ]);
        
        // ✅ ENREGISTRER L'ACTIVITÉ
        CRMActivity::create([
            'action' => 'Nouvelle Relance',
            'details' => "Relance {$canal} effectuée pour {$client->nom} - Statut: {$relance->statut}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);
        
        $relance->load('client');
        
        return response()->json([
            'success' => true,
            'message' => 'Relance enregistrée avec succès' . ($prochaineRelance ? ' - Prochaine relance programmée dans 7 jours' : ''),
            'relance' => $relance
        ], 201);
        
    } catch (\Exception $e) {
        Log::error('Erreur storeRelance: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// ==================== NOUVELLE FONCTION : Obtenir les templates disponibles ====================
public function getRelanceTemplates(Request $request)
{
    try {
        $statut = $request->input('statut', 'Lead');
        $canal = $request->input('canal', 'whatsapp');
        
        $template = $this->getRelanceTemplate($statut, $canal);
        
        if (!$template) {
            return response()->json([
                'success' => false,
                'error' => 'Aucun template disponible pour ce statut et ce canal'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'template' => $template,
            'statut' => $statut,
            'canal' => $canal
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getRelanceTemplates: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// ==================== FONCTION CORRIGÉE : getRelancesAujourdhui ====================
public function getRelancesAujourdhui()
{
    try {
        $user = Auth::user();
        
        // ✅ Relances à faire aujourd'hui + URGENTES (en retard)
        // ✅ CORRECTION : Exclure les clients avec "Visa validé" des alertes
        // ✅ CORRECTION : Les alertes sont partagées entre tous les agents pour une meilleure coordination
        $relancesAujourdhui = CRMRelance::with('client')
            ->where('statut', 'En cours')
            ->where(function($query) {
                $query->whereDate('prochaine_relance', '<=', now())
                      ->orWhereNull('prochaine_relance');
            })
            ->whereHas('client', function($q) {
                $q->where('statut', '!=', 'Visa validé');
            })
            ->orderBy('prochaine_relance', 'asc')
            ->get();
        
        // ✅ CALCULER L'URGENCE
        $relancesAujourdhui = $relancesAujourdhui->map(function($relance) {
            if ($relance->prochaine_relance) {
                $joursRetard = now()->diffInDays($relance->prochaine_relance, false);
                $relance->jours_retard = abs((int)$joursRetard);
                $relance->is_urgent = $joursRetard < -3; // Plus de 3 jours de retard = URGENT
            } else {
                $relance->jours_retard = 0;
                $relance->is_urgent = false;
            }
            return $relance;
        });
        
        return response()->json([
            'success' => true,
            'relances' => $relancesAujourdhui,
            'count' => $relancesAujourdhui->count(),
            'count_urgent' => $relancesAujourdhui->where('is_urgent', true)->count()
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getRelancesAujourdhui: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function addClientCommentaire(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'commentaire' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $client = CRMClient::findOrFail($id);

        $commentaire = CRMClientCommentaire::create([
            'client_id' => $client->id,
            'user_id' => $user->id,
            'agent_name' => $user->name,
            'commentaire' => $request->commentaire,
        ]);

        CRMActivity::create([
            'action' => 'Commentaire Client',
            'details' => "Commentaire ajouté pour {$client->nom} par {$user->name}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Commentaire ajouté avec succès',
            'commentaire' => $commentaire
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur addClientCommentaire: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function getClientCommentaires($id)
{
    try {
        $client = CRMClient::findOrFail($id);
        
        $commentaires = CRMClientCommentaire::where('client_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'commentaires' => $commentaires,
            'client' => $client
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur getClientCommentaires: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

/**
 * ✅ NOUVELLE FONCTION : Charger UNIQUEMENT les clients pour le select de relances
 * Route optimisée sans pagination pour éviter les erreurs
 */
public function getClientsForRelanceSelect()
{
    try {
        $user = Auth::user();
        
        // ✅ Charger TOUS les clients actifs (sans pagination)
        // ✅ CORRECTION : Exclure les clients avec "Visa validé" car ils ne nécessitent plus de relances
        $query = CRMClient::select('id', 'nom', 'prenoms', 'contact', 'statut')
                          ->where('statut', '!=', 'Visa validé');

        // Filtrer selon le rôle
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager'])) {
            $query->where('user_id', $user->id);
        }

        // ✅ Obtenir TOUS les clients (limité à 500 pour la performance)
        $clients = $query->orderBy('nom', 'asc')
                        ->limit(500)
                        ->get();
        
        Log::info('✅ getClientsForRelanceSelect SUCCESS', [
            'count' => $clients->count()
        ]);
        
        return response()->json([
            'success' => true,
            'clients' => $clients
        ]);
        
    } catch (\Exception $e) {
        Log::error('❌ Erreur getClientsForRelanceSelect', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

// Après la méthode updateClient existante, ajoutez :

public function getClient($id)
{
    try {
        $user = Auth::user();
        $client = CRMClient::findOrFail($id);
        
        // Vérifier les permissions
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial']) 
            && $client->user_id != $user->id) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        return response()->json([
            'success' => true,
            'client' => $client
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getClient: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function getPayment($id)
{
    try {
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial', 'Manager'])) {
            return response()->json(['success' => false, 'error' => 'Accès non autorisé'], 403);
        }
        
        $payment = CRMPayment::with('invoice')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'payment' => $payment
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur getPayment: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function updatePayment(Request $request, $id)
{
    try {
        $user = Auth::user();
        
        // ✅ VÉRIFICATION DES PERMISSIONS
        $permissions = $user->getCrmPermissions();
        $canEdit = $user->hasAnyRole(['Super Admin', 'Admin']) 
                   || in_array('edit_payments', $permissions);
        
        if (!$canEdit) {
            return response()->json([
                'success' => false, 
                'error' => 'Vous n\'avez pas la permission de modifier les paiements'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'payment_date' => 'nullable|date'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $payment = CRMPayment::findOrFail($id);
        $invoice = $payment->invoice;
        
        $oldAmount = $payment->amount;
        $newAmount = $request->amount;
        
        $payment->amount = $newAmount;
        $payment->payment_method = $request->payment_method;
        if ($request->payment_date) {
            $payment->payment_date = $request->payment_date;
        }
        if ($request->notes) {
            $payment->notes = $request->notes;
        }
        $payment->save();
        
        $invoice->paid_amount = $invoice->paid_amount - $oldAmount + $newAmount;
        
        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'paid';
        } else if ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'pending';
        }
        
        $invoice->save();
        
        CRMActivity::create([
            'action' => 'Modification Paiement',
            'details' => "Paiement modifié par {$user->name} pour la facture {$invoice->number} - Nouveau montant: " . number_format($newAmount, 0, ',', ' ') . " FCFA",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Paiement mis à jour avec succès',
            'payment' => $payment,
            'invoice' => $invoice
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur updatePayment: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function deletePayment($id)
{
    try {
        $user = Auth::user();
        
        // ✅ VÉRIFICATION DES PERMISSIONS
        $permissions = $user->getCrmPermissions();
        $canDelete = $user->hasRole('Super Admin') 
                     || in_array('delete_payments', $permissions);
        
        if (!$canDelete) {
            return response()->json([
                'success' => false, 
                'error' => 'Vous n\'avez pas la permission de supprimer des paiements'
            ], 403);
        }
        
        $payment = CRMPayment::findOrFail($id);
        $invoice = $payment->invoice;
        $amount = $payment->amount;
        
        $invoice->paid_amount -= $amount;
        
        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'paid';
        } else if ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        } else {
            $invoice->status = 'pending';
        }
        
        $invoice->save();

        // Supprimer l'entrée de caisse correspondante si elle existe
        if ($payment->notes && preg_match('/Ref:\s*([A-Z]+-\d{8}-\d{4})/', $payment->notes, $matches)) {
            $caisseRef = $matches[1];
            $caisseEntree = CaisseEntree::where('ref', $caisseRef)->first();

            if ($caisseEntree) {
                // Vérifier si l'entrée n'est pas clôturée avant de la supprimer
                if (!$caisseEntree->isCloturee()) {
                    $caisseEntree->forceDelete();
                    Log::info("Entrée de caisse {$caisseRef} supprimée suite à la suppression du paiement CRM #{$id}");
                } else {
                    Log::warning("Impossible de supprimer l'entrée de caisse {$caisseRef} car elle est clôturée");
                }
            }
        }

        $payment->delete();

        CRMActivity::create([
            'action' => 'Suppression Paiement',
            'details' => "Paiement de " . number_format($amount, 0, ',', ' ') . " FCFA supprimé par {$user->name} pour la facture {$invoice->number}",
            'user_name' => $user->name,
            'user_id' => $user->id,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Paiement supprimé',
            'invoice' => $invoice
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur deletePayment: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

// ✅ NOUVELLE MÉTHODE : Vérifier les permissions de l'utilisateur
public function checkUserPermissions()
{
    try {
        $user = Auth::user();
        $permissions = $user->getCrmPermissions();
        
        return response()->json([
            'success' => true,
            'permissions' => [
                'edit_clients' => $user->hasAnyRole(['Super Admin', 'Admin']) || in_array('edit_clients', $permissions),
                'delete_clients' => $user->hasAnyRole(['Super Admin', 'Admin']) || in_array('delete_clients', $permissions),
                'edit_invoices' => $user->hasAnyRole(['Super Admin', 'Admin']) || in_array('edit_invoices', $permissions),
                'delete_invoices' => $user->hasAnyRole(['Super Admin', 'Admin']) || in_array('delete_invoices', $permissions),
                'edit_payments' => $user->hasAnyRole(['Super Admin', 'Admin']) || in_array('edit_payments', $permissions),
                'delete_payments' => $user->hasRole('Super Admin') || in_array('delete_payments', $permissions),
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error('Erreur checkUserPermissions: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

/**
     * Obtenir tous les contrats
     */
    public function getContracts()
    {
        try {
            $contracts = CRMContract::with(['creator', 'updater'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'contracts' => $contracts
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getContracts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des contrats'
            ], 500);
        }
    }

    /**
     * Obtenir un contrat spécifique
     */
    public function getContract($id)
    {
        try {
            $contract = CRMContract::with(['creator', 'updater'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'contract' => $contract
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getContract: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Contrat non trouvé'
            ], 404);
        }
    }

    /**
     * Créer un nouveau contrat
     */
    public function storeContract(Request $request)
    {
        try {
            Log::info('🆕 storeContract - Données reçues', [
                'data' => $request->all(),
                'user' => Auth::user()->name
            ]);

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'date_naissance' => 'required|date',
                'nationalite' => 'required|string|max:255',
                'sexe' => 'required|in:Masculin,Féminin',
                'etat_civil' => 'required|string|max:255',
                'profession' => 'required|string|max:255',
                'adresse' => 'required|string',
                'ville' => 'required|string|max:255',
                'telephone_mobile' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'type_visa' => 'required|string|max:255',
                'pays_destination' => 'required|string|max:255',
                'montant_contrat' => 'required|numeric|min:0',
                'montant_lettres' => 'required|string',
                'date_contrat' => 'required|date',
            ]);

            if ($validator->fails()) {
                Log::error('❌ storeContract - Erreur de validation', [
                    'errors' => $validator->errors()->toArray(),
                    'data' => $request->all()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Préparer les données avec le conseiller automatique
            $contractData = $request->all();

            // Définir automatiquement le conseiller comme l'utilisateur connecté si non fourni
            if (empty($contractData['conseiller'])) {
                $contractData['conseiller'] = Auth::user()->name;
            }

            $contract = CRMContract::create($contractData);

            // Générer automatiquement le token de signature (valable 72h)
            $signatureToken = $contract->generateSignatureToken(72);

            // Générer l'URL complète de signature
            $signatureUrl = url('/signature/' . $signatureToken);

            Log::info('✅ Contrat créé avec succès', [
                'contract_id' => $contract->id,
                'numero' => $contract->numero_contrat,
                'user' => Auth::user()->name,
                'signature_token' => $signatureToken,
                'signature_url' => $signatureUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contrat créé avec succès',
                'contract' => $contract->fresh(), // Recharger pour avoir le token
                'signature_url' => $signatureUrl,
                'token_expires_at' => $contract->token_expires_at->format('d/m/Y H:i')
            ], 201);
        } catch (\Exception $e) {
            Log::error('❌ Erreur storeContract: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du contrat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un contrat
     */
    public function updateContract(Request $request, $id)
    {
        try {
            $contract = CRMContract::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255',
                'prenom' => 'sometimes|required|string|max:255',
                'date_naissance' => 'sometimes|required|date',
                'nationalite' => 'sometimes|required|string|max:255',
                'sexe' => 'sometimes|required|in:Masculin,Féminin',
                'etat_civil' => 'sometimes|required|string|max:255',
                'profession' => 'sometimes|required|string|max:255',
                'telephone_mobile' => 'sometimes|required|string|max:20',
                'email' => 'sometimes|required|email|max:255',
                'type_visa' => 'sometimes|required|string|max:255',
                'pays_destination' => 'sometimes|required|string|max:255',
                'montant_contrat' => 'sometimes|required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contract->update($request->all());

            Log::info('Contrat mis à jour', [
                'contract_id' => $contract->id,
                'numero' => $contract->numero_contrat,
                'user' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contrat mis à jour avec succès',
                'contract' => $contract
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updateContract: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du contrat'
            ], 500);
        }
    }

    /**
     * Supprimer un contrat
     */
    public function deleteContract($id)
    {
        try {
            $contract = CRMContract::findOrFail($id);
            $numeroContrat = $contract->numero_contrat;

            // Suppression permanente (forceDelete) au lieu de soft delete
            $contract->forceDelete();

            Log::info('Contrat supprimé définitivement de la base de données', [
                'contract_id' => $id,
                'numero' => $numeroContrat,
                'user' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contrat supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur deleteContract: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du contrat'
            ], 500);
        }
    }

    /**
     * Signer un contrat
     */
    public function signContract(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'signature' => 'required|string',
                'nom_signataire' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $contract = CRMContract::findOrFail($id);
            
            $contract->signer(
                $request->signature,
                $request->nom_signataire
            );

            Log::info('Contrat signé', [
                'contract_id' => $contract->id,
                'numero' => $contract->numero_contrat,
                'signataire' => $request->nom_signataire,
                'user' => Auth::user()->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contrat signé avec succès',
                'contract' => $contract
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur signContract: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la signature du contrat'
            ], 500);
        }
    }

    /**
     * Régénérer le token de signature pour un contrat
     */
    public function regenerateToken($id)
    {
        try {
            $contract = CRMContract::findOrFail($id);

            // Vérifier que le contrat n'est pas déjà signé
            if ($contract->statut === 'Signé') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce contrat est déjà signé et ne peut pas avoir un nouveau lien de signature.'
                ], 400);
            }

            // Générer un nouveau token (valable 72h)
            $signatureToken = $contract->generateSignatureToken(72);

            // Générer l'URL complète de signature
            $signatureUrl = url('/signature/' . $signatureToken);

            Log::info('Token de signature régénéré', [
                'contract_id' => $contract->id,
                'numero' => $contract->numero_contrat,
                'user' => Auth::user()->name,
                'expires_at' => $contract->token_expires_at->format('d/m/Y H:i')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nouveau lien de signature généré avec succès',
                'signature_url' => $signatureUrl,
                'token_expires_at' => $contract->token_expires_at->format('d/m/Y H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur regenerateToken: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la régénération du lien de signature'
            ], 500);
        }
    }

    /**
     * Statistiques des contrats
     */
    public function getContractsStats()
    {
        try {
            $totalContracts = CRMContract::count();
            $signedContracts = CRMContract::signed()->count();
            $pendingContracts = CRMContract::pending()->count();
            $monthContracts = CRMContract::thisMonth()->count();
            $yearContracts = CRMContract::thisYear()->count();

            // Montant total des contrats signés
            $totalAmount = CRMContract::signed()->sum('montant_contrat');

            // Contrats récents
            $recentContracts = CRMContract::with('creator')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Statistiques par mois (12 derniers mois)
            $monthlyStats = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $count = CRMContract::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                
                $monthlyStats[] = [
                    'month' => $date->format('M Y'),
                    'count' => $count
                ];
            }

            return response()->json([
                'success' => true,
                'stats' => [
                    'total' => $totalContracts,
                    'signed' => $signedContracts,
                    'pending' => $pendingContracts,
                    'this_month' => $monthContracts,
                    'this_year' => $yearContracts,
                    'total_amount' => $totalAmount,
                    'recent_contracts' => $recentContracts,
                    'monthly_stats' => $monthlyStats,
                    'signature_rate' => $totalContracts > 0 
                        ? round(($signedContracts / $totalContracts) * 100, 2)
                        : 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getContractsStats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }

    /**
     * Générer le lien de consultation du contrat pour le copier
     */
    public function sendContractLink($id)
    {
        try {
            $contract = CRMContract::findOrFail($id);

            // Générer le lien de consultation (ou utiliser l'existant)
            $viewLink = $contract->getViewLink();

            Log::info('Lien de consultation du contrat généré', [
                'contract_id' => $contract->id,
                'numero_contrat' => $contract->numero_contrat
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lien généré avec succès',
                'view_link' => $viewLink,
                'contract' => [
                    'numero_contrat' => $contract->numero_contrat,
                    'nom_complet' => $contract->nom_complet,
                    'email' => $contract->email
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur sendContractLink: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du lien'
            ], 500);
        }
    }

    /**
     * Afficher le contrat au client via un lien unique (route publique)
     */
    public function showContract($token)
    {
        try {
            // Rechercher le contrat par son view_token
            $contract = CRMContract::findByViewToken($token);

            if (!$contract) {
                return view('contracts.view-error', [
                    'error' => 'Lien invalide',
                    'message' => 'Ce lien de consultation n\'existe pas ou a expiré.'
                ]);
            }

            // Afficher le contrat
            return view('contracts.view-contract', [
                'contract' => $contract
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage du contrat', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return view('contracts.view-error', [
                'error' => 'Erreur',
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.'
            ]);
        }
    }

    /**
     * Télécharger le contrat en PDF (route publique)
     */
    public function downloadContractPDF($token)
    {
        try {
            // Rechercher le contrat par son view_token
            $contract = CRMContract::findByViewToken($token);

            if (!$contract) {
                abort(404, 'Contrat introuvable');
            }

            // Générer le PDF
            $pdf = \PDF::loadView('contracts.pdf-contract', [
                'contract' => $contract
            ]);

            // Télécharger le PDF
            return $pdf->download('Contrat_' . $contract->numero_contrat . '.pdf');

        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement du contrat PDF', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Erreur lors du téléchargement du PDF');
        }
    }

    /**
     * Récupérer toutes les activités CRM
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivities(Request $request)
    {
        try {
            $user = Auth::user();

            // Vérifier les permissions - Utiliser EXACTEMENT la même logique que l'index
            // Super Admin et Admin ont TOUJOURS accès
            if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin')) {
                // Pour les autres utilisateurs, vérifier les permissions CRM
                $permissions = $user->getCrmPermissions();

                // Si pas de permissions définies, donner accès selon le rôle par défaut
                if (empty($permissions)) {
                    if (!$user->hasAnyRole(['Manager', 'Commercial', 'Agent Comptoir'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Vous n\'avez pas la permission d\'accéder aux activités'
                        ], 403);
                    }
                }
                // Si l'utilisateur a des permissions CRM (peu importe lesquelles), l'accès est accordé
                // Note: Cette logique correspond à l'index qui donne accès dès qu'il y a des permissions
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
            $query = CRMActivity::with('user:id,name,matricule,email')
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
            $actionTypes = CRMActivity::select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action');

            // Récupérer les utilisateurs qui ont des activités
            $users = CRMActivity::select('user_id', 'user_name')
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
            Log::error('Erreur lors de la récupération des activités CRM:', [
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