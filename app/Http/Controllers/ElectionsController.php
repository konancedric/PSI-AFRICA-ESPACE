<?php

namespace App\Http\Controllers;

use App\Http\Requests\ElectionsRequest;
use App\Models\Elections;
use App\Models\Grades;
use App\Models\User;
use App\Models\Categories;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class ElectionsController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        $dataCategories = Categories::orderBy('libelle', 'asc')->get();
        $dataGrades = Grades::orderBy('libelle', 'asc')->get();
        $dataElections = Elections::orderBy('libelle', 'asc')->get();
        return view('admin.elections.elections', compact('user1d', 'dataElections', 'dataGrades', 'dataCategories'));
    }

    /**
     * Show Elections List
     *
     * @param Request $request
     * @return mixed
     */
    public function getElectionsList(Request $request): mixed
    {
        $data = Elections::get();
        $hasManageElections = Auth::user()->can('manage_user');
    }

    /**
     * Elections Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'type_election' => 'required',
            'user1d' => 'required',
            'id_grade' => 'required',
            'id_categorie' => 'required',
            'tete_liste' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            $tabPersTeteListe = "0";
            for($i = 0; $i < count($request->tete_liste); $i++)
            {
                $tabPersTeteListe = $request->tete_liste[$i]."~".$tabPersTeteListe;
            }
            $Elections = new Elections;
            $Elections->libelle =  $request->libelle;
            $Elections->type_election =  $request->type_election;
            $Elections->date_debut =  $request->date_debut;
            $Elections->date_fin =  $request->date_fin;
            $Elections->id_categorie =  $request->id_categorie;
            $Elections->user1d =  $request->user1d;
            $Elections->id_grade =  $request->id_grade;
            $Elections->tete_liste =  $tabPersTeteListe;
            $Elections->save();
            if ($Elections)
            {
                return redirect('elections')->with('success', 'Election created succesfully!');
            }
            return redirect('elections')->with('error', 'Failed to create Elections! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('elections')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Elections
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'id_commune' => 'required',
            'localisation' => 'required',
            'contact' => 'required',
            'user1d' => 'required',
            'id' => 'required',
            'etat' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Elections = Elections::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'id_commune' => $request->id_commune,
                    'localisation' => $request->localisation,
                    'contact' => $request->contact,
                    'description' => $request->description,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Elections->update($payload);
                return redirect()->back()->with('success', 'Election information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Elections! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * update_etat Elections
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_etat(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'user1d' => 'required',
            'id' => 'required',
            'etat' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Elections = Elections::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Elections->update($payload);
                return redirect()->back()->with('success', 'Election information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Elections! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Elections
     *
     * @param ElectionsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ElectionsRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Elections::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('elections')->with('success', 'New user created!');
            }

            return redirect('elections')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Elections
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Elections::with('roles', 'permissions')->find($id);

            if ($user) {
                $user_role = $user->roles->first();
                $roles = Role::pluck('name', 'id');

                return view('user-edit', compact('user', 'user_role', 'roles'));
            }

            return redirect('404');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }



    /**
     * Delete Elections
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        if ($user = Elections::find($id)) {
            $user->delete();

            return redirect('elections')->with('success', 'Election removed!');
        }

        return redirect('elections')->with('error', 'Election not found');
    }


    public function getUserTete2ListeByGradeWithCategorie($id_grade, $id_categorie)
    {
        $dateHier = Carbon::yesterday();
        $dateHierFormatee = $dateHier->format('Y-m-d');
        $resultats = User::where('etat',1)
        ->where('id_grade', $id_grade)
        ->where('id_categorie', $id_categorie)
        ->get();

        // Renvoyez les résultats au format JSON
        return response()->json($resultats);
    }

    public function getUserTete2ListeByGrade($id_grade)
    {
        $dateHier = Carbon::yesterday();
        $dateHierFormatee = $dateHier->format('Y-m-d');
        $resultats = User::where('etat',1)
        ->where('id_grade', $id_grade)
        ->get();

        // Renvoyez les résultats au format JSON
        return response()->json($resultats);
    }
    public function getUserTete2ListeByCategorie($id_categorie)
    {
        $dateHier = Carbon::yesterday();
        $dateHierFormatee = $dateHier->format('Y-m-d');
        $resultats = User::where('etat',1)
        ->where('id_categorie', $id_categorie)
        ->get();

        // Renvoyez les résultats au format JSON
        return response()->json($resultats);
    }
}
