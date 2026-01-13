<?php

namespace App\Http\Controllers;

use App\Models\DocumentsVoyage;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DocumentsVoyageController extends Controller
{
    /**
     * Afficher la liste des documents voyage
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Vérifier les permissions
            if (!$user->can('manage_documentsvoyage') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir'])) {
                return redirect('/dashboard')->with('error', 'Accès non autorisé');
            }

            $user1d = $user->id;
            
            // Récupérer les documents voyage
            $dataDocumentsVoyage = DocumentsVoyage::where('ent1d', 1)
                ->orderBy('created_at', 'desc')
                ->get();
            
            Log::info('DocumentsVoyageController index - Documents chargés', [
                'user_id' => $user1d,
                'count' => $dataDocumentsVoyage->count()
            ]);

            return view('admin.documents-voyage.documents-voyage', compact('user1d', 'dataDocumentsVoyage'));

        } catch (\Exception $e) {
            Log::error('Erreur DocumentsVoyageController index: ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Erreur lors du chargement des documents voyage');
        }
    }

    /**
     * API DataTables pour les documents voyage
     */
    public function getDocumentsVoyageList(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->can('manage_documentsvoyage') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir'])) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            $data = DocumentsVoyage::where('ent1d', 1)
                ->orderBy('created_at', 'desc')
                ->get();

            return Datatables::of($data)
                ->addColumn('numero_demande_formatted', function ($data) {
                    $badgeClass = $data->etat == 1 ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $badgeClass . ' badge-pill">' . $data->numero_demande . '</span>';
                })
                ->addColumn('sejour_period', function ($data) {
                    return $data->date_sejour_debut . ' - ' . $data->date_sejour_fin;
                })
                ->addColumn('documents_interess_formatted', function ($data) {
                    return $this->formatDocumentsInteress($data->documents_interess);
                })
                ->addColumn('status', function ($data) {
                    $badgeClass = $data->etat == 1 ? 'badge-success' : 'badge-danger';
                    $status = $data->etat == 1 ? 'Confirmé' : 'En attente';
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
                ->rawColumns(['numero_demande_formatted', 'documents_interess_formatted', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('Erreur getDocumentsVoyageList: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Créer un nouveau document voyage
     */
    public function create(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact' => 'required|string|max:20',
            'date_sejour_debut' => 'required|date',
            'date_sejour_fin' => 'required|date|after_or_equal:date_sejour_debut',
            'documents_interess' => 'required|string',
            'objet' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            DB::beginTransaction();

            // Générer un numéro de demande unique
            $numerodemande = 'DOC' . date('Y') . '-' . str_pad(DocumentsVoyage::count() + 1, 6, '0', STR_PAD_LEFT);

            $document = new DocumentsVoyage();
            $document->numero_demande = $numerodemande;
            $document->nom = $request->nom;
            $document->prenom = $request->prenom;
            $document->email = $request->email;
            $document->contact = $request->contact;
            $document->date_sejour_debut = $request->date_sejour_debut;
            $document->date_sejour_fin = $request->date_sejour_fin;
            $document->documents_interess = $request->documents_interess;
            $document->objet = $request->objet ?? '';
            $document->etat = 0; // En attente par défaut
            $document->user1d = Auth::user()->id;
            $document->ent1d = 1;
            $document->save();

            DB::commit();

            Log::info('Nouveau document voyage créé', [
                'document_id' => $document->id,
                'numero_demande' => $numerodemande,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()->with('success', 'Document voyage créé avec succès !');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur création document voyage: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un document voyage
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:documents_voyage,id',
            'etat' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->messages()->first());
        }

        try {
            $document = DocumentsVoyage::find($request->id);

            if (!$document) {
                return redirect()->back()->with('error', 'Document voyage non trouvé');
            }

            $document->etat = $request->etat;
            $document->update_user = Auth::user()->id;
            $document->save();

            $status = $request->etat == 1 ? 'confirmé' : 'mis en attente';

            Log::info('Document voyage mis à jour', [
                'document_id' => $document->id,
                'nouvel_etat' => $request->etat,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()->with('success', "Document voyage {$status} avec succès !");

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour document voyage: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un document voyage
     */
    public function delete($id): RedirectResponse
    {
        try {
            $document = DocumentsVoyage::find($id);

            if (!$document) {
                return redirect()->back()->with('error', 'Document voyage non trouvé');
            }

            $document->delete();

            Log::info('Document voyage supprimé', [
                'document_id' => $id,
                'user_id' => Auth::user()->id
            ]);

            return redirect()->back()->with('success', 'Document voyage supprimé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur suppression document voyage: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les statistiques des documents voyage
     */
    public function getStatistics()
    {
        try {
            $currentDate = Carbon::now();

            $stats = [
                'total_documents' => DocumentsVoyage::where('ent1d', 1)->count(),
                'documents_confirmes' => DocumentsVoyage::where('ent1d', 1)->where('etat', 1)->count(),
                'documents_en_attente' => DocumentsVoyage::where('ent1d', 1)->where('etat', 0)->count(),
                'documents_ce_mois' => DocumentsVoyage::where('ent1d', 1)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count(),
                'documents_aujourd_hui' => DocumentsVoyage::where('ent1d', 1)
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'document_populaire' => $this->getDocumentPopulaire(),
                'temps_moyen_traitement' => $this->getTempsTraitement(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            Log::error('Erreur getStatistics documents voyage: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération des statistiques'], 500);
        }
    }

    /**
     * Méthodes privées utilitaires
     */
    private function formatActions($document): string
    {
        $user = Auth::user();
        
        if (!$user->can('manage_documentsvoyage') && !$user->hasAnyRole(['Admin', 'Super Admin', 'Agent Comptoir'])) {
            return '';
        }

        $actions = '<div class="table-actions">';
        
        if ($document->etat == 1) {
            $actions .= '<button type="button" class="btn btn-danger btn-sm" onclick="updateDocument(' . $document->id . ', 0)" title="Mettre en attente">
                <i class="ik ik-trash-2"></i>
            </button>';
        } else {
            $actions .= '<button type="button" class="btn btn-success btn-sm" onclick="updateDocument(' . $document->id . ', 1)" title="Confirmer">
                <i class="fa fa-check-circle"></i>
            </button>';
        }
        
        $actions .= '<button type="button" class="btn btn-info btn-sm ml-1" onclick="viewDocument(' . $document->id . ')" title="Voir détails">
            <i class="ik ik-eye"></i>
        </button>';
        
        $actions .= '<button type="button" class="btn btn-warning btn-sm ml-1" onclick="deleteDocument(' . $document->id . ')" title="Supprimer">
            <i class="ik ik-trash"></i>
        </button>';
        
        $actions .= '</div>';

        return $actions;
    }

    private function formatDocumentsInteress($documentsString): string
    {
        if (empty($documentsString)) {
            return '<span class="badge badge-secondary badge-pill">Aucun document</span>';
        }

        $documents = explode('~', $documentsString);
        $html = '';
        
        foreach ($documents as $document) {
            if (!empty(trim($document))) {
                $html .= '<span class="badge badge-dark badge-pill mt-2 mr-1">' . htmlspecialchars(trim($document)) . '</span>';
            }
        }
        
        return $html ?: '<span class="badge badge-secondary badge-pill">Aucun document</span>';
    }

    private function getDocumentPopulaire(): string
    {
        try {
            $documents = DocumentsVoyage::where('ent1d', 1)
                ->where('documents_interess', '!=', '')
                ->whereNotNull('documents_interess')
                ->get();

            $documentCount = [];
            
            foreach ($documents as $doc) {
                $docArray = explode('~', $doc->documents_interess);
                foreach ($docArray as $document) {
                    $document = trim($document);
                    if (!empty($document)) {
                        $documentCount[$document] = ($documentCount[$document] ?? 0) + 1;
                    }
                }
            }

            if (empty($documentCount)) {
                return 'Aucun';
            }

            arsort($documentCount);
            return array_key_first($documentCount);
            
        } catch (\Exception $e) {
            return 'Indisponible';
        }
    }

    private function getTempsTraitement(): float
    {
        try {
            $result = DocumentsVoyage::where('ent1d', 1)
                ->where('etat', 1)
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->first();

            return round($result->avg_days ?? 0, 1);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Obtenir les détails d'un document voyage
     */
    public function getDocumentDetails($id)
    {
        try {
            $document = DocumentsVoyage::find($id);

            if (!$document) {
                return response()->json(['error' => 'Document voyage non trouvé'], 404);
            }

            // Formater les documents d'intérêt en tableau
            $documentsArray = !empty($document->documents_interess) 
                ? array_filter(explode('~', $document->documents_interess))
                : [];

            $documentData = $document->toArray();
            $documentData['documents_interess_array'] = $documentsArray;

            return response()->json([
                'success' => true,
                'document' => $documentData
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getDocumentDetails: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exporter les documents voyage
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
            Log::error('Erreur export documents voyage: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'export'], 500);
        }
    }

    /**
     * Recherche avancée dans les documents voyage
     */
    public function search(Request $request)
    {
        try {
            $query = DocumentsVoyage::where('ent1d', 1);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenom', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('numero_demande', 'like', "%{$search}%")
                      ->orWhere('documents_interess', 'like', "%{$search}%");
                });
            }

            if ($request->filled('etat')) {
                $query->where('etat', $request->etat);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $documents = $query->orderBy('created_at', 'desc')->paginate(50);

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur search documents voyage: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la recherche'], 500);
        }
    }
}