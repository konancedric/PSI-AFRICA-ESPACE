<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grades;
use App\Models\Categories;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AgentsController extends Controller
{
    /**
     * Afficher la liste des agents internes
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        
        // Récupérer les agents internes (comptoir et commerciaux)
        $agents = User::whereIn('type_user', ['agent_comptoir', 'commercial', 'admin'])
            ->where('ent1d', 1)
            ->with('roles')
            ->orderBy('name', 'asc')
            ->get();

        // Récupérer les rôles pour agents internes
        $roles = Role::whereIn('name', ['Agent Comptoir', 'Commercial', 'Admin'])->get();
        
        $dataCategories = Categories::orderBy('libelle', 'asc')->get();
        $dataGrades = Grades::orderBy('libelle', 'asc')->get();

        return view('admin.agents.agents-list', compact(
            'user1d', 'agents', 'roles', 'dataCategories', 'dataGrades'
        ));
    }

    /**
     * API DataTables pour la liste des agents
     */
    public function getAgentsList(Request $request): mixed
    {
        $data = User::whereIn('type_user', ['agent_comptoir', 'commercial', 'admin'])
            ->where('ent1d', 1)
            ->with('roles')
            ->get();

        $hasManageAgents = Auth::user()->can('manage_agents');

        return Datatables::of($data)
            ->addColumn('roles', function ($data) {
                $roles = $data->getRoleNames()->toArray();
                $badge = '';
                if ($roles) {
                    foreach ($roles as $role) {
                        $color = $this->getRoleColor($role);
                        $badge .= '<span class="badge badge-' . $color . ' m-1">' . $role . '</span>';
                    }
                }
                return $badge;
            })
            ->addColumn('type_user_badge', function ($data) {
                $colors = [
                    'admin' => 'danger',
                    'agent_comptoir' => 'info', 
                    'commercial' => 'success'
                ];
                $labels = [
                    'admin' => 'Administrateur',
                    'agent_comptoir' => 'Agent Comptoir',
                    'commercial' => 'Commercial'
                ];
                $color = $colors[$data->type_user] ?? 'secondary';
                $label = $labels[$data->type_user] ?? ucfirst($data->type_user);
                return '<span class="badge badge-' . $color . '">' . $label . '</span>';
            })
            ->addColumn('status', function ($data) {
                $statusColors = [
                    'actif' => 'success',
                    'suspendu' => 'warning',
                    'conge' => 'info',
                    'demission' => 'danger'
                ];
                $color = $statusColors[$data->statut_emploi] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst($data->statut_emploi) . '</span>';
            })
            ->addColumn('photo', function ($data) {
                if ($data->photo_user && $data->photo_user != 'NULL') {
                    $photoUrl = asset('upload/users/' . $data->photo_user);
                    return '<img src="' . $photoUrl . '" alt="Photo" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">';
                }
                return '<div class="bg-secondary rounded text-white text-center" style="width: 40px; height: 40px; line-height: 40px; font-size: 14px;">' . 
                       strtoupper(substr($data->name, 0, 2)) . '</div>';
            })
            ->addColumn('action', function ($data) use ($hasManageAgents) {
                $output = '';
                if ($data->name == 'Super Admin' || $data->email == 'admin@psiafrica.ci') {
                    return '<span class="text-muted">Protégé</span>';
                }
                if ($hasManageAgents) {
                    $output = '<div class="table-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="editAgent(' . $data->id . ')" title="Modifier">
                            <i class="ik ik-edit-2"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info" onclick="viewAgent(' . $data->id . ')" title="Voir détails">
                            <i class="ik ik-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-warning" onclick="toggleStatus(' . $data->id . ')" title="Changer statut">
                            <i class="ik ik-power"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteAgent(' . $data->id . ')" title="Supprimer">
                            <i class="ik ik-trash-2"></i>
                        </button>
                    </div>';
                }
                return $output;
            })
            ->rawColumns(['roles', 'type_user_badge', 'status', 'photo', 'action'])
            ->make(true);
    }

    /**
     * Créer un nouvel agent
     */
    public function create(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'matricule' => 'required|string|unique:users,matricule',
            'type_user' => 'required|in:agent_comptoir,commercial',
            'contact' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'date_embauche' => 'required|date',
            'salaire' => 'nullable|numeric|min:0',
            'adresse' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            // Gestion de la photo
            $photo_user = "NULL";
            if ($request->hasFile('photo_user')) {
                $this->validate($request, [
                    'photo_user' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                
                $image = $request->file('photo_user');
                $photo_user = time() . '_' . $request->matricule . '_photo.' . $image->getClientOriginalExtension();
                $image->move(public_path('/upload/users/'), $photo_user);
            }

            // Créer l'agent
            $agent = new User();
            $agent->name = $request->name;
            $agent->email = $request->email;
            $agent->matricule = $request->matricule;
            $agent->contact = $request->contact;
            $agent->type_user = $request->type_user;
            $agent->password = Hash::make($request->password);
            $agent->photo_user = $photo_user;
            $agent->date_embauche = $request->date_embauche;
            $agent->salaire = $request->salaire;
            $agent->adresse = $request->adresse;
            $agent->id_categorie = $request->id_categorie;
            $agent->id_grade = $request->id_grade;
            $agent->ent1d = 1;
            $agent->etat = 1;
            $agent->statut_emploi = 'actif';
            $agent->user1d = Auth::user()->id;
            $agent->email_verified_at = now();
            $agent->save();

            // Assigner le rôle
            $role = Role::find($request->role_id);
            if ($role) {
                $agent->assignRole($role);
            }

            return redirect()->back()->with('success', 'Agent créé avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Mettre à jour un agent
     */
    public function update(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'matricule' => 'required|string|unique:users,matricule,' . $request->id,
            'contact' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'date_embauche' => 'required|date',
            'salaire' => 'nullable|numeric|min:0',
            'adresse' => 'nullable|string',
            'statut_emploi' => 'required|in:actif,suspendu,conge,demission',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            $agent = User::find($request->id);
            if (!$agent) {
                return redirect()->back()->with('error', 'Agent non trouvé');
            }

            // Gestion de la photo
            if ($request->hasFile('photo_user')) {
                $this->validate($request, [
                    'photo_user' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                ]);
                
                // Supprimer l'ancienne photo
                if ($agent->photo_user && $agent->photo_user != 'NULL' && file_exists(public_path('/upload/users/' . $agent->photo_user))) {
                    unlink(public_path('/upload/users/' . $agent->photo_user));
                }
                
                $image = $request->file('photo_user');
                $photo_user = time() . '_' . $request->matricule . '_photo.' . $image->getClientOriginalExtension();
                $image->move(public_path('/upload/users/'), $photo_user);
                $agent->photo_user = $photo_user;
            }

            // Mettre à jour les données
            $agent->name = $request->name;
            $agent->email = $request->email;
            $agent->matricule = $request->matricule;
            $agent->contact = $request->contact;
            $agent->date_embauche = $request->date_embauche;
            $agent->salaire = $request->salaire;
            $agent->adresse = $request->adresse;
            $agent->id_categorie = $request->id_categorie;
            $agent->id_grade = $request->id_grade;
            $agent->statut_emploi = $request->statut_emploi;
            $agent->update_user = Auth::user()->id;
            $agent->updated_at = now();

            // Mettre à jour le mot de passe si fourni
            if ($request->filled('password')) {
                $agent->password = Hash::make($request->password);
            }

            $agent->save();

            // Mettre à jour le rôle
            $role = Role::find($request->role_id);
            if ($role) {
                $agent->syncRoles([$role]);
            }

            return redirect()->back()->with('success', 'Agent mis à jour avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Changer le statut d'un agent
     */
    public function toggleStatus(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'statut_emploi' => 'required|in:actif,suspendu,conge,demission',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()->first()], 400);
        }

        try {
            $agent = User::find($request->id);
            if (!$agent) {
                return response()->json(['error' => 'Agent non trouvé'], 404);
            }

            $agent->statut_emploi = $request->statut_emploi;
            $agent->etat = ($request->statut_emploi == 'actif') ? 1 : 0;
            $agent->update_user = Auth::user()->id;
            $agent->save();

            return response()->json(['success' => 'Statut mis à jour avec succès']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un agent
     */
    public function delete($id): RedirectResponse
    {
        try {
            $agent = User::find($id);
            if (!$agent) {
                return redirect()->back()->with('error', 'Agent non trouvé');
            }

            // Vérifier les permissions
            if ($agent->email == 'admin@psiafrica.ci' || $agent->name == 'Super Admin') {
                return redirect()->back()->with('error', 'Impossible de supprimer cet utilisateur protégé');
            }

            // Supprimer la photo si elle existe
            if ($agent->photo_user && $agent->photo_user != 'NULL' && file_exists(public_path('/upload/users/' . $agent->photo_user))) {
                unlink(public_path('/upload/users/' . $agent->photo_user));
            }

            $agent->delete();

            return redirect()->back()->with('success', 'Agent supprimé avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Obtenir les détails d'un agent
     */
    public function getAgentDetails($id)
    {
        try {
            $agent = User::with('roles')->find($id);
            if (!$agent) {
                return response()->json(['error' => 'Agent non trouvé'], 404);
            }

            return response()->json([
                'success' => true,
                'agent' => $agent,
                'roles' => $agent->roles->pluck('name'),
                'photo_url' => $agent->photo_user && $agent->photo_user != 'NULL' 
                    ? asset('upload/users/' . $agent->photo_user) 
                    : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Statistiques des agents
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total_agents' => User::whereIn('type_user', ['agent_comptoir', 'commercial'])->count(),
                'agents_comptoir' => User::where('type_user', 'agent_comptoir')->count(),
                'commerciaux' => User::where('type_user', 'commercial')->count(),
                'agents_actifs' => User::whereIn('type_user', ['agent_comptoir', 'commercial'])
                    ->where('statut_emploi', 'actif')->count(),
                'agents_suspendus' => User::whereIn('type_user', ['agent_comptoir', 'commercial'])
                    ->where('statut_emploi', 'suspendu')->count(),
                'nouveaux_ce_mois' => User::whereIn('type_user', ['agent_comptoir', 'commercial'])
                    ->whereMonth('created_at', now()->month)->count(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Obtenir la couleur du badge selon le rôle
     */
    private function getRoleColor($roleName)
    {
        $colors = [
            'Admin' => 'danger',
            'Agent Comptoir' => 'info',
            'Commercial' => 'success',
        ];

        return $colors[$roleName] ?? 'secondary';
    }

    /**
     * Exporter la liste des agents
     */
    public function export(Request $request)
    {
        // Logique d'export selon le format demandé
        // À implémenter selon vos besoins (Excel, PDF, CSV)
    }

    /**
     * Réinitialiser le mot de passe d'un agent
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->messages()->first());
        }

        try {
            $agent = User::find($request->id);
            $agent->password = Hash::make($request->password);
            $agent->save();

            return redirect()->back()->with('success', 'Mot de passe réinitialisé avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
}