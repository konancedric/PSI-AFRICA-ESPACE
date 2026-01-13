<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfilVisa;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Carbon\Carbon;

class PublicUsersController extends Controller
{
    /**
     * Afficher la liste des utilisateurs publics SEULEMENT
     */
    public function index(): View
    {
        return view('admin.public-users.public-users-list');
    }

    /**
     * API DataTables pour la liste des utilisateurs publics
     */
    public function getPublicUsersList(Request $request): mixed
    {
        // FILTRER pour ne récupérer que les utilisateurs publics
        $data = User::where('type_user', 'public')
            ->orWhereNull('type_user')
            ->orWhere('type_user', '')
            ->where('ent1d', 1)
            ->with(['profilsVisa' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get();

        $hasManageUser = Auth::user()->can('manage_user');

        return Datatables::of($data)
            ->addColumn('photo', function ($data) {
                if ($data->photo_user && $data->photo_user != 'NULL') {
                    $photoUrl = asset('upload/users/' . $data->photo_user);
                    return '<img src="' . $photoUrl . '" alt="Photo" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">';
                }
                return '<div class="bg-primary rounded text-white text-center" style="width: 40px; height: 40px; line-height: 40px; font-size: 14px;">' . 
                       strtoupper(substr($data->name, 0, 2)) . '</div>';
            })
            ->addColumn('user_type', function ($data) {
                return '<span class="badge badge-primary">Utilisateur Public</span>';
            })
            ->addColumn('profils_visa_count', function ($data) {
                $count = $data->profilsVisa()->count();
                $color = $count > 0 ? 'success' : 'secondary';
                return '<span class="badge badge-' . $color . '">' . $count . ' profil(s)</span>';
            })
            ->addColumn('last_activity', function ($data) {
                $lastLogin = $data->updated_at ?? $data->created_at;
                return '<small class="text-muted">' . $lastLogin->diffForHumans() . '</small>';
            })
            ->addColumn('contact_info', function ($data) {
                $info = '';
                if ($data->contact) {
                    $info .= '<small class="text-muted d-block"><i class="fas fa-phone"></i> ' . $data->contact . '</small>';
                }
                if ($data->email) {
                    $info .= '<small class="text-muted d-block"><i class="fas fa-envelope"></i> ' . substr($data->email, 0, 20) . '...</small>';
                }
                return $info;
            })
            ->addColumn('status', function ($data) {
                $color = $data->etat == 1 ? 'success' : 'danger';
                $status = $data->etat == 1 ? 'Actif' : 'Inactif';
                return '<span class="badge badge-' . $color . '">' . $status . '</span>';
            })
            ->addColumn('registration', function ($data) {
                return '<small class="text-muted">' . $data->created_at->format('d/m/Y H:i') . '</small>';
            })
            ->addColumn('action', function ($data) use ($hasManageUser) {
                $output = '';
                if ($hasManageUser) {
                    $output = '<div class="table-actions">
                        <button class="btn btn-sm btn-outline-info" onclick="viewPublicUser(' . $data->id . ')" title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="viewUserProfiles(' . $data->id . ')" title="Voir profils visa">
                            <i class="fas fa-passport"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="toggleUserStatus(' . $data->id . ')" title="Changer statut">
                            <i class="fas fa-power-off"></i>
                        </button>';
                    
                    // Permettre la suppression seulement si pas de profils visa
                    if ($data->profilsVisa()->count() == 0) {
                        $output .= '<button class="btn btn-sm btn-outline-danger" onclick="deletePublicUser(' . $data->id . ')" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>';
                    }
                    
                    $output .= '</div>';
                }
                return $output;
            })
            ->rawColumns(['photo', 'user_type', 'profils_visa_count', 'last_activity', 'contact_info', 'status', 'registration', 'action'])
            ->make(true);
    }

    /**
     * Voir les détails d'un utilisateur public
     */
    public function getPublicUserDetails($id)
    {
        try {
            $user = User::with(['profilsVisa', 'profilsVisa.statutEtat'])
                ->where('type_user', 'public')
                ->orWhereNull('type_user')
                ->orWhere('type_user', '')
                ->find($id);
                
            if (!$user) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }

            $statistics = [
                'total_profils' => $user->profilsVisa()->count(),
                'profils_en_cours' => $user->profilsVisa()->whereHas('statutEtat', function($query) {
                    $query->where('libelle', 'like', '%cours%')
                          ->orWhere('libelle', 'like', '%attente%');
                })->count(),
                'profils_approuves' => $user->profilsVisa()->whereHas('statutEtat', function($query) {
                    $query->where('libelle', 'like', '%approuvé%')
                          ->orWhere('libelle', 'like', '%validé%');
                })->count(),
                'derniere_demande' => $user->profilsVisa()->latest()->first()?->created_at?->format('d/m/Y H:i'),
            ];

            return response()->json([
                'success' => true,
                'user' => $user,
                'statistics' => $statistics,
                'photo_url' => $user->photo_user && $user->photo_user != 'NULL' 
                    ? asset('upload/users/' . $user->photo_user) 
                    : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Changer le statut d'un utilisateur public
     */
    public function toggleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'etat' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()->first()], 400);
        }

        try {
            $user = User::where('id', $request->id)
                ->where(function($query) {
                    $query->where('type_user', 'public')
                          ->orWhereNull('type_user')
                          ->orWhere('type_user', '');
                })
                ->first();
                
            if (!$user) {
                return response()->json(['error' => 'Utilisateur public non trouvé'], 404);
            }

            $user->etat = $request->etat;
            $user->update_user = Auth::user()->id;
            $user->save();

            $status = $request->etat == 1 ? 'activé' : 'désactivé';
            return response()->json(['success' => "Utilisateur $status avec succès"]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un utilisateur public (seulement s'il n'a pas de profils visa)
     */
    public function delete($id): RedirectResponse
    {
        try {
            $user = User::where('id', $id)
                ->where(function($query) {
                    $query->where('type_user', 'public')
                          ->orWhereNull('type_user')
                          ->orWhere('type_user', '');
                })
                ->first();
                
            if (!$user) {
                return redirect()->back()->with('error', 'Utilisateur public non trouvé');
            }

            // Vérifier qu'il n'a pas de profils visa
            if ($user->profilsVisa()->count() > 0) {
                return redirect()->back()->with('error', 'Impossible de supprimer cet utilisateur car il a des profils visa associés');
            }

            // Supprimer la photo si elle existe
            if ($user->photo_user && $user->photo_user != 'NULL' && file_exists(public_path('/upload/users/' . $user->photo_user))) {
                unlink(public_path('/upload/users/' . $user->photo_user));
            }

            $user->delete();

            return redirect()->back()->with('success', 'Utilisateur public supprimé avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Statistiques des utilisateurs publics
     */
    public function getStatistics()
    {
        try {
            $currentDate = Carbon::now();
            
            $stats = [
                'total_users_public' => User::where('type_user', 'public')
                    ->orWhereNull('type_user')
                    ->orWhere('type_user', '')
                    ->count(),
                'users_actifs' => User::where(function($query) {
                        $query->where('type_user', 'public')
                              ->orWhereNull('type_user')
                              ->orWhere('type_user', '');
                    })
                    ->where('etat', 1)
                    ->count(),
                'nouveaux_aujourd_hui' => User::where(function($query) {
                        $query->where('type_user', 'public')
                              ->orWhereNull('type_user')
                              ->orWhere('type_user', '');
                    })
                    ->whereDate('created_at', $currentDate->toDateString())
                    ->count(),
                'nouveaux_ce_mois' => User::where(function($query) {
                        $query->where('type_user', 'public')
                              ->orWhereNull('type_user')
                              ->orWhere('type_user', '');
                    })
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->count(),
                'avec_profils_visa' => User::where(function($query) {
                        $query->where('type_user', 'public')
                              ->orWhereNull('type_user')
                              ->orWhere('type_user', '');
                    })
                    ->whereHas('profilsVisa')
                    ->count(),
                'sans_profils_visa' => User::where(function($query) {
                        $query->where('type_user', 'public')
                              ->orWhereNull('type_user')
                              ->orWhere('type_user', '');
                    })
                    ->whereDoesntHave('profilsVisa')
                    ->count(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exporter la liste des utilisateurs publics
     */
    public function export(Request $request)
    {
        // Logique d'export selon le format demandé
        // À implémenter selon vos besoins (Excel, PDF, CSV)
        
        $format = $request->input('format', 'excel');
        
        try {
            $users = User::where('type_user', 'public')
                ->orWhereNull('type_user')
                ->orWhere('type_user', '')
                ->with('profilsVisa')
                ->get();

            // Logique d'export à implémenter
            return response()->json([
                'message' => 'Export en cours de développement',
                'format' => $format,
                'count' => $users->count()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur d\'export : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Rechercher des utilisateurs publics
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $limit = $request->input('limit', 10);

            $users = User::where(function($q) {
                    $q->where('type_user', 'public')
                      ->orWhereNull('type_user')
                      ->orWhere('type_user', '');
                })
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%")
                      ->orWhere('contact', 'like', "%{$query}%");
                })
                ->limit($limit)
                ->get();

            return response()->json(['users' => $users]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur de recherche : ' . $e->getMessage()], 500);
        }
    }
}