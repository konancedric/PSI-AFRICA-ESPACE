<?php

namespace App\Http\Controllers;

use App\Models\CRMInvoice;
use App\Models\CRMClient;
use App\Models\CRMPayment;
use App\Models\CRMActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CRMInvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        $query = CRMInvoice::with('client');

        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'])) {
            $query->where('user_id', $user->id);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(100);

        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $invoice = CRMInvoice::with('client')->findOrFail($id);

            // Vérifier les permissions
            if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial']) && $invoice->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous n\'avez pas la permission de voir cette facture'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:crm_clients,id',
                'service' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $client = CRMClient::findOrFail($request->client_id);

            $invoice = CRMInvoice::create([
                'client_id' => $client->id,
                'client_name' => $client->nom . ' ' . ($client->prenoms ?? ''),
                'service' => $request->service,
                'amount' => $request->amount,
                'due_date' => $request->due_date,
                'agent' => $user->name,
                'notes' => $request->notes,
                'user_id' => $user->id,
            ]);

            CRMActivity::create([
                'action' => 'Nouvelle Facture',
                'details' => "Facture {$invoice->number} créée pour {$client->nom}",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facture créée avec succès',
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'client_id' => 'required|exists:crm_clients,id',
                'service' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'due_date' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $invoice = CRMInvoice::findOrFail($id);

            // Vérifier les permissions
            if (!$user->hasAnyRole(['Super Admin', 'Admin']) && $invoice->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vous n\'avez pas la permission de modifier cette facture'
                ], 403);
            }

            // Ne pas permettre la modification si la facture est validée par le client
            if ($invoice->isValidatedByClient()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Impossible de modifier une facture déjà validée par le client'
                ], 403);
            }

            $client = CRMClient::findOrFail($request->client_id);

            // Sauvegarder les anciennes valeurs pour l'historique
            $oldValues = [
                'service' => $invoice->service,
                'amount' => $invoice->amount,
                'due_date' => $invoice->due_date
            ];

            // Mettre à jour la facture
            $invoice->update([
                'client_id' => $client->id,
                'client_name' => $client->nom . ' ' . ($client->prenoms ?? ''),
                'service' => $request->service,
                'amount' => $request->amount,
                'due_date' => $request->due_date,
                'notes' => $request->notes,
            ]);

            // Créer une activité
            $changes = [];
            if ($oldValues['service'] !== $invoice->service) {
                $changes[] = "Service: {$oldValues['service']} → {$invoice->service}";
            }
            if ($oldValues['amount'] != $invoice->amount) {
                $changes[] = "Montant: {$oldValues['amount']} → {$invoice->amount}";
            }
            if ($oldValues['due_date'] !== $invoice->due_date) {
                $changes[] = "Date d'échéance: {$oldValues['due_date']} → {$invoice->due_date}";
            }

            CRMActivity::create([
                'action' => 'Facture Modifiée',
                'details' => "Facture {$invoice->number} modifiée: " . implode(', ', $changes),
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facture modifiée avec succès',
                'invoice' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recordPayment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();
            $invoice = CRMInvoice::findOrFail($id);

            DB::beginTransaction();

            $payment = CRMPayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date ?? now(),
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'user_id' => $user->id,
            ]);

            $invoice->paid_amount += $request->amount;
            $invoice->save();

            CRMActivity::create([
                'action' => 'Paiement Enregistré',
                'details' => "Paiement de {$request->amount} FCFA pour facture {$invoice->number}",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paiement enregistré avec succès',
                'invoice' => $invoice->fresh(),
                'payment' => $payment
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
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
                    'error' => 'Seuls les administrateurs peuvent supprimer des factures'
                ], 403);
            }

            $invoice = CRMInvoice::findOrFail($id);
            $invoiceNumber = $invoice->number;

            $invoice->delete();

            CRMActivity::create([
                'action' => 'Suppression Facture',
                'details' => "Facture {$invoiceNumber} supprimée",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Facture supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique link for the invoice
     */
    public function generateLink($id)
    {
        try {
            $user = Auth::user();
            $invoice = CRMInvoice::findOrFail($id);

            if (!$invoice->view_token) {
                $invoice->generateViewToken();
            }

            CRMActivity::create([
                'action' => 'Lien Facture Généré',
                'details' => "Lien de facturation généré pour facture {$invoice->number}",
                'user_name' => $user->name,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lien généré avec succès',
                'url' => $invoice->public_url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the public invoice page (accessible without authentication)
     * Affiche toutes les factures du client avec menu déroulant
     */
    public function showPublic($token)
    {
        \Log::info('Accès à la page de facturation publique avec token: ' . $token);

        try {
            // Trouver la facture par son token
            $currentInvoice = CRMInvoice::with(['client', 'payments', 'user'])
                ->where('view_token', $token)
                ->firstOrFail();

            $client = $currentInvoice->client;

            if (!$client) {
                \Log::error('Client introuvable pour la facture avec token: ' . $token);
                abort(404, 'Client introuvable');
            }

            \Log::info('Facture trouvée: ' . $currentInvoice->number . ' - Client: ' . $client->nom);

            // Récupérer TOUTES les factures du client
            $allInvoices = CRMInvoice::with(['payments', 'user'])
                ->where('client_id', $client->id)
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Nombre total de factures pour ce client: ' . $allInvoices->count());

            // Afficher la vue de facturation avec toutes les factures
            return view('facturation.facturation', compact('currentInvoice', 'allInvoices', 'client'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Facture introuvable pour token: ' . $token);
            abort(404, 'Lien invalide ou expiré');
        } catch (\Exception $e) {
            \Log::error('Erreur affichage facturation: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            abort(500, 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Validate invoice by client (public route)
     */
    public function validateByClient(Request $request, $token)
    {
        try {
            $invoice = CRMInvoice::where('view_token', $token)->firstOrFail();

            // Marquer comme validé (sans signature - seuls les reçus nécessitent une signature)
            $invoice->markAsValidatedByClient();

            \Log::info('Facture validée par le client: ' . $invoice->number);

            return response()->json([
                'success' => true,
                'message' => 'Facture validée avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur validation facture par client: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sign receipt by client (public route)
     * Gère maintenant la signature individuelle de chaque paiement
     */
    public function signReceipt(Request $request, $token)
    {
        try {
            $invoice = CRMInvoice::where('view_token', $token)->firstOrFail();

            // Vérifier que la facture est validée
            if (!$invoice->isValidatedByClient()) {
                return response()->json([
                    'success' => false,
                    'error' => 'La facture doit être validée avant de signer le reçu'
                ], 400);
            }

            // Récupérer l'ID du paiement
            $paymentId = $request->input('payment_id');

            if (!$paymentId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Aucun paiement sélectionné'
                ], 400);
            }

            // Trouver le paiement
            $payment = \App\Models\CRMPayment::find($paymentId);

            if (!$payment || $payment->invoice_id !== $invoice->id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Paiement introuvable ou non associé à cette facture'
                ], 400);
            }

            // Vérifier que ce paiement n'est pas déjà signé
            if ($payment->isReceiptSigned()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce reçu de paiement a déjà été signé'
                ], 400);
            }

            // Vérifier que ce paiement peut être signé
            if (!$payment->canBeSigned()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ce paiement ne peut pas encore être signé'
                ], 400);
            }

            // Enregistrer la signature pour ce paiement
            $signatureData = $request->input('signature_data');
            $clientIp = $request->ip();

            $payment->receipt_signature = $signatureData;
            $payment->receipt_signed_at = now();
            $payment->client_ip = $clientIp;

            // Générer le numéro de reçu si non existant
            if (!$payment->receipt_number) {
                $payment->receipt_number = $payment->generateReceiptNumber();
            }

            $payment->save();

            \Log::info('Reçu de paiement signé par le client', [
                'invoice_number' => $invoice->number,
                'payment_id' => $payment->id,
                'receipt_number' => $payment->receipt_number,
                'client_ip' => $clientIp
            ]);

            // Créer une activité CRM
            \App\Models\CRMActivity::create([
                'action' => 'Reçu de Paiement Signé',
                'details' => "Reçu {$payment->receipt_number} signé par le client pour facture {$invoice->number}",
                'user_name' => 'Client',
                'user_id' => $invoice->user_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reçu de paiement signé avec succès',
                'receipt_number' => $payment->receipt_number
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur signature reçu par client: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}