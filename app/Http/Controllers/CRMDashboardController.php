<?php

namespace App\Http\Controllers;

use App\Models\CRMClient;
use App\Models\CRMInvoice;
use App\Models\CRMActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CRMDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('crm.index');
    }

    public function getRealtimeStats()
    {
        try {
            $user = Auth::user();
            
            // Filtrer selon le rôle
            $clientsQuery = $this->getFilteredQuery(CRMClient::query(), $user);
            $invoicesQuery = $this->getFilteredQuery(CRMInvoice::query(), $user);

            $stats = [
                'clients' => [
                    'total' => $clientsQuery->count(),
                    'ce_mois' => (clone $clientsQuery)->whereMonth('created_at', now()->month)->count(),
                    'conversions' => (clone $clientsQuery)->where('statut', 'Converti')->count(),
                    'taux_conversion' => $this->calculateConversionRate($clientsQuery),
                ],
                'invoices' => [
                    'total' => $invoicesQuery->count(),
                    'paid' => (clone $invoicesQuery)->where('status', 'paid')->count(),
                    'pending' => (clone $invoicesQuery)->where('status', 'pending')->count(),
                    'overdue' => (clone $invoicesQuery)->where('status', 'overdue')->count(),
                    'partial' => (clone $invoicesQuery)->where('status', 'partial')->count(),
                ],
                'revenue' => [
                    'total_invoiced' => (clone $invoicesQuery)->sum('amount'),
                    'total_paid' => (clone $invoicesQuery)->sum('paid_amount'),
                    'total_remaining' => (clone $invoicesQuery)->sum(DB::raw('amount - paid_amount')),
                ],
                'activities' => CRMActivity::orderBy('created_at', 'desc')->take(5)->get(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'user' => [
                    'name' => $user->name,
                    'role' => $user->getRoleNames()->first() ?? 'user',
                ],
                'timestamp' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getFilteredQuery($query, $user)
    {
        // Les utilisateurs publics voient seulement leurs données
        if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Agent Comptoir', 'Commercial'])) {
            $query->where('user_id', $user->id);
        }
        
        return $query;
    }

    private function calculateConversionRate($clientsQuery)
    {
        $total = $clientsQuery->count();
        if ($total == 0) return 0;
        
        $conversions = (clone $clientsQuery)->where('statut', 'Converti')->count();
        return round(($conversions / $total) * 100, 1);
    }
}