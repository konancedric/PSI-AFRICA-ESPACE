<?php

namespace App\Http\Controllers;

use App\Models\CRMClient;
use App\Models\CRMActivity;
use App\Models\CRMInvoice;
use App\Models\CRMPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CRMClientsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = CRMClient::query();

        // Filtrer pour les utilisateurs publics
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'])) {
            $query->where('user_id', $user->id);
        }

        // Permettre de spécifier le nombre de résultats par page (par défaut 500)
        $perPage = $request->get('per_page', 500);

        // Si "all" est passé en paramètre, retourner tous les clients
        if ($request->get('all') === 'true') {
            $clients = $query->orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'clients' => [
                    'data' => $clients,
                    'total' => $clients->count(),
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ]);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'clients' => $clients
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'prestation' => 'required|string|max:255',
                'budget' => 'nullable|numeric|min:0',
                'statut' => 'required|in:Lead,Prospect,Opportunité,Négociation,Converti,Perdu',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
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

            // Log l'activité
            CRMActivity::create([
                'action' => 'Nouveau Client',
                'details' => "Client {$client->nom} {$client->prenoms} ajouté",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client créé avec succès',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur création client: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $client = CRMClient::findOrFail($id);

            // Vérification sécurité
            if (!$user->hasAnyRole(['Super Admin', 'Admin']) && $client->user_id != $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Accès non autorisé'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'statut' => 'required|in:Lead,Prospect,Opportunité,Négociation,Converti,Perdu',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $client->update(['statut' => $request->statut]);

            CRMActivity::create([
                'action' => 'Modification Client',
                'details' => "Statut changé: {$client->nom} -> {$request->statut}",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client mis à jour',
                'client' => $client
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();

            if (!$user->hasAnyRole(['Super Admin', 'Admin'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Seuls les administrateurs peuvent supprimer des clients'
                ], 403);
            }

            $client = CRMClient::findOrFail($id);
            $clientName = $client->nom;
            
            $client->delete();

            CRMActivity::create([
                'action' => 'Suppression Client',
                'details' => "Client {$clientName} supprimé",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show client portal with all invoices and payments (public route)
     */
    public function showPortal($token)
    {
        try {
            // Récupérer le client avec son token
            $client = CRMClient::where('client_portal_token', $token)->firstOrFail();

            // Récupérer toutes les factures du client
            $invoices = CRMInvoice::with('payments', 'user')
                ->where('client_id', $client->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Récupérer tous les paiements du client
            $payments = CRMPayment::whereHas('invoice', function($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->with('invoice')
            ->orderBy('payment_date', 'desc')
            ->get();

            // Calculer les totaux
            $totalPaid = $invoices->sum('paid_amount');
            $totalAmount = $invoices->sum('amount');
            $totalRemaining = $totalAmount - $totalPaid;

            return view('facturation.client-portal', compact(
                'client',
                'invoices',
                'payments',
                'totalPaid',
                'totalRemaining'
            ));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Lien de facturation introuvable ou invalide');
        } catch (\Exception $e) {
            \Log::error('Erreur affichage lien de facturation: ' . $e->getMessage());
            abort(500, 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Generate or get portal link for a client
     */
    public function generatePortalLink($id)
    {
        try {
            $user = Auth::user();
            $client = CRMClient::findOrFail($id);

            if (!$client->client_portal_token) {
                $client->generatePortalToken();
            }

            CRMActivity::create([
                'action' => 'Lien de Facturation Généré',
                'details' => "Lien de facturation généré pour {$client->nom}",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lien de facturation généré avec succès',
                'url' => $client->portal_url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export($format = 'csv')
    {
        // À implémenter selon vos besoins
        return response()->json([
            'success' => false,
            'error' => 'Export non encore implémenté'
        ]);
    }
}