<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CRMInvoice;
use App\Models\CRMPayment;

class MesFacturesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher les factures et paiements du client
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer les factures du client depuis la table CRM
        $factures = collect();
        $paiements = collect();
        $statistiques = [
            'total_factures' => 0,
            'total_montant' => 0,
            'factures_payees' => 0,
            'factures_impayees' => 0,
            'montant_paye' => 0,
            'montant_impaye' => 0
        ];

        try {
            // Vérifier si la table CRMInvoice existe
            if (class_exists('App\Models\CRMInvoice')) {
                $factures = CRMInvoice::where('email_client', $user->email)
                    ->orWhere('telephone_client', $user->contact)
                    ->orderBy('created_at', 'desc')
                    ->get();

                // Calculer les statistiques
                $statistiques['total_factures'] = $factures->count();
                $statistiques['total_montant'] = $factures->sum('montant_total');
                $statistiques['factures_payees'] = $factures->where('statut_paiement', 'payé')->count();
                $statistiques['factures_impayees'] = $factures->where('statut_paiement', 'impayé')->count();
                $statistiques['montant_paye'] = $factures->where('statut_paiement', 'payé')->sum('montant_total');
                $statistiques['montant_impaye'] = $factures->where('statut_paiement', 'impayé')->sum('montant_total');

                // Récupérer les paiements associés
                $invoiceIds = $factures->pluck('id');
                if (class_exists('App\Models\CRMPayment') && $invoiceIds->isNotEmpty()) {
                    $paiements = CRMPayment::whereIn('invoice_id', $invoiceIds)
                        ->orderBy('date_paiement', 'desc')
                        ->get();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Erreur récupération factures: ' . $e->getMessage());
        }

        return view('clients.mes-factures', compact('factures', 'paiements', 'statistiques'));
    }

    /**
     * Voir les détails d'une facture
     */
    public function show($id)
    {
        $user = Auth::user();

        try {
            $facture = CRMInvoice::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('email_client', $user->email)
                          ->orWhere('telephone_client', $user->contact);
                })
                ->firstOrFail();

            // Récupérer les paiements associés
            $paiements = CRMPayment::where('invoice_id', $id)
                ->orderBy('date_paiement', 'desc')
                ->get();

            return view('clients.facture-detail', compact('facture', 'paiements'));

        } catch (\Exception $e) {
            return redirect()->route('mes-factures')
                ->with('error', 'Facture introuvable');
        }
    }

    /**
     * Télécharger une facture en PDF
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();

        try {
            $facture = CRMInvoice::where('id', $id)
                ->where(function($query) use ($user) {
                    $query->where('email_client', $user->email)
                          ->orWhere('telephone_client', $user->contact);
                })
                ->firstOrFail();

            // Ici vous pouvez générer un PDF avec une bibliothèque comme DomPDF ou TCPDF
            // Pour l'instant, on redirige vers la vue détaillée
            return redirect()->route('mes-factures.show', $id);

        } catch (\Exception $e) {
            return redirect()->route('mes-factures')
                ->with('error', 'Impossible de télécharger la facture');
        }
    }
}
