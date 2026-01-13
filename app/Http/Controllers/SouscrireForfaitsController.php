<?php

namespace App\Http\Controllers;

use App\Models\SouscrireForfaits;
use App\Models\Forfaits;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SouscrireForfaitsController extends Controller
{
    /**
     * Afficher la liste des souscriptions forfaits
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Vérifier les permissions
            if (!$user->can('manage_souscrire_forfaits') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Commercial'])) {
                return redirect('/dashboard')->with('error', 'Accès non autorisé');
            }

            $user1d = $user->id;
            
            // Récupérer les souscriptions forfaits
            $dataSouscrireForfaits = SouscrireForfaits::orderBy('created_at', 'desc')->get();
            
            Log::info('SouscrireForfaitsController index - Souscriptions chargées', [
                'user_id' => $user1d,
                'count' => $dataSouscrireForfaits->count()
            ]);

            return view('admin.souscrire-forfaits.souscrire-forfaits', compact('user1d', 'dataSouscrireForfaits'));

        } catch (\Exception $e) {
            Log::error('Erreur SouscrireForfaitsController index: ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Erreur lors du chargement des souscriptions forfaits');
        }
    }

    /**
     * API DataTables pour les souscriptions forfaits
     */
    public function getSouscrireForfaitsList(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->can('manage_souscrire_forfaits') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Commercial'])) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $data = SouscrireForfaits::with('forfait')->orderBy('created_at', 'desc')->get();

            return Datatables::of($data)
                ->addColumn('forfait_name', function ($data) {
                    if ($data->forfait) {
                        $badgeClass = $data->etat == 1 ? 'badge-success' : 'badge-danger';
                        return '<span class="badge ' . $badgeClass . ' badge-pill">' . $data->forfait->titre . '</span>';
                    }
                    return '<span class="badge badge-secondary badge-pill">Forfait non trouvé</span>';
                })
                ->addColumn('status', function ($data) {
                    $badgeClass = $data->etat == 1 ? 'badge-success' : 'badge-danger';
                    $status = $data->etat == 1 ? 'Actif' : 'Inactif';
                    return '<span class="badge ' . $badgeClass . '">' . $status . '</span>';
                })
                ->addColumn('created_at_formatted', function ($data) {
                    return $data->created_at ? $data->created_at->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('updated_at_formatted', function ($data) {
                    return $data->updated_at ? $data->updated_at->format('d/m/Y H:i') : 'N/A';
                })
                ->addColumn('action', function ($data) {
                    return $this->formatActions($data);
                })
                ->rawColumns(['forfait_name', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('Erreur getSouscrireForfaitsList: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Créer une nouvelle souscription forfait
     */
    public function create(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:20',
            'numero_whatsapp' => 'nullable|string|max:20',
            'id_type_forfait' => 'required|exists:forfaits,id',
            'montant' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            $souscription = new SouscrireForfaits();
            $souscription->nom = $request->nom;
            $souscription->prenom = $request->prenom;
            $souscription->email = $request->email;
            $souscription->contact = $request->contact;
            $souscription->numero_whatsapp = $request->numero_whatsapp;
            $souscription->id_type_forfait = $request->id_type_forfait;
            $souscription->montant = $request->montant ?? 0;
            $souscription->etat = 1; // Actif par défaut
            $souscription->user1d = Auth::user()->id;
            $souscription->ent1d = 1;
            $souscription->save();

            DB::commit();

            Log::info('Nouvelle souscription forfait créée', [
                'souscription_id' => $souscription->id,
                'forfait_id' => $request->id_type_forfait,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()->with('success', 'Souscription forfait créée avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création souscription forfait: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour une souscription forfait
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:souscrire_forfaits,id',
            'etat' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->messages()->first());
        }

        try {
            $souscription = SouscrireForfaits::find($request->id);

            if (!$souscription) {
                return redirect()->back()->with('error', 'Souscription forfait non trouvée');
            }

            $souscription->etat = $request->etat;
            $souscription->update_user = Auth::user()->id;
            $souscription->save();

            $status = $request->etat == 1 ? 'activée' : 'désactivée';

            Log::info('Souscription forfait mise à jour', [
                'souscription_id' => $souscription->id,
                'nouvel_etat' => $request->etat,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()->with('success', "Souscription forfait {$status} avec succès !");

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour souscription forfait: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une souscription forfait
     */
    public function delete($id): RedirectResponse
    {
        try {
            $souscription = SouscrireForfaits::find($id);

            if (!$souscription) {
                return redirect()->back()->with('error', 'Souscription forfait non trouvée');
            }

            $souscription->delete();

            Log::info('Souscription forfait supprimée', [
                'souscription_id' => $id,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()->with('success', 'Souscription forfait supprimée avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur suppression souscription forfait: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques des souscriptions forfaits
     */
    public function getStatistics()
    {
        try {
            $currentDate = Carbon::now();

            $stats = [
                'total_souscriptions' => SouscrireForfaits::count(),
                'souscriptions_actives' => SouscrireForfaits::where('etat', 1)->count(),
                'souscriptions_inactives' => SouscrireForfaits::where('etat', 0)->count(),
                'souscriptions_ce_mois' => SouscrireForfaits::whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count(),
                'chiffre_affaires_total' => SouscrireForfaits::where('etat', 1)->sum('montant'),
                'chiffre_affaires_ce_mois' => SouscrireForfaits::where('etat', 1)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->sum('montant'),
                'forfait_populaire' => $this->getForfaitPopulaire(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur getStatistics souscriptions forfaits: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /**
     * Méthodes privées utilitaires
     */
    private function formatActions($souscription): string
    {
        $user = Auth::user();
        
        if (!$user->can('manage_souscrire_forfaits') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Commercial'])) {
            return '';
        }

        $actions = '<div class="table-actions">';
        
        if ($souscription->etat == 1) {
            $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="updateSouscription(' . $souscription->id . ', 0)" title="Désactiver">
                <i class="ik ik-trash-2"></i>
            </button>';
        } else {
            $actions .= '<button type="button" class="btn btn-success btn-sm" onclick="updateSouscription(' . $souscription->id . ', 1)" title="Activer">
                <i class="fa fa-check-circle"></i>
            </button>';
        }
        
        $actions .= '<button type="button" class="btn btn-info btn-sm ml-1" onclick="viewSouscription(' . $souscription->id . ')" title="Voir détails">
            <i class="ik ik-eye"></i>
        </button>';
        
        $actions .= '<button type="button" class="btn btn-warning btn-sm ml-1" onclick="deleteSouscription(' . $souscription->id . ')" title="Supprimer">
            <i class="ik ik-trash"></i>
        </button>';
        
        $actions .= '</div>';

        return $actions;
    }

    private function getForfaitPopulaire(): string
    {
        try {
            $forfait = DB::table('souscrire_forfaits')
                ->join('forfaits', 'souscrire_forfaits.id_type_forfait', '=', 'forfaits.id')
                ->select('forfaits.titre', DB::raw('COUNT(souscrire_forfaits.id) as count'))
                ->groupBy('forfaits.id', 'forfaits.titre')
                ->orderBy('count', 'desc')
                ->first();

            return $forfait ? $forfait->titre : 'Aucun';
        } catch (\Exception $e) {
            return 'Indisponible';
        }
    }

    /**
     * Obtenir les détails d'une souscription forfait
     */
    public function getSouscriptionDetails($id)
    {
        try {
            $souscription = SouscrireForfaits::with('forfait')->find($id);

            if (!$souscription) {
                return response()->json(['error' => 'Souscription forfait non trouvée'], 404);
            }

            return response()->json([
                'success' => true,
                'souscription' => $souscription
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getSouscriptionDetails: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exporter les souscriptions forfaits
     */
    public function export(Request $request, $format = 'excel')
    {
        try {
            // Logique d'export selon le format
            return response()->json([
                'success' => true,
                'message' => "Export {$format} en cours de génération"
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur export souscriptions forfaits: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'export'], 500);
        }
    }
}