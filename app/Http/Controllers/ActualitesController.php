<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActualitesRequest;
use App\Models\Actualites;
use App\Models\Categories;
use App\Models\User;
use App\Models\Entreprises;
use App\Models\ModelHasRoles;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActualitesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request): mixed
    {
        $linkEditor = "https://ed.psiafrica.ci";
        $user1d = Auth::user()->id;
        $dataEntreprise = Entreprises::where('user1d', $user1d)->first();
        $ent1d = 1;
        $dataActualites = Actualites::where('ent1d', 1)->orderBy('libelle', 'asc')->get();
        // $dataActualites = Actualites::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.actualites.actualites', compact('linkEditor', 'user1d', 'dataActualites', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }

    /**
     * Show Actualites List
     *
     * @param Request $request
     * @return mixed
     */
    public function getActualitesList(Request $request): mixed
    {
        $data = Actualites::get();
        $hasManageActualites = Auth::user()->can('manage_user');
    }

    /**
     * Actualites Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'id_site' => 'required',
            'abreviation' => 'required',
            'id_categorie' => 'required',
            'user1d' => 'required',
            'ent1d' => 'required',
        ]);
        if ($validator->fails())
        {

            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $Actualites = new Actualites;
            $Actualites->libelle =  $request->libelle;
            $Actualites->id_site =  $request->id_site;
            $Actualites->abreviation =  $request->abreviation;
            $Actualites->id_categorie =  $request->id_categorie;
            $Actualites->description =  $request->description;
            $Actualites->user1d =  $request->user1d;
            $Actualites->ent1d =  $request->ent1d;
            $Actualites->save();
            if ($Actualites)
            {
                
            }
            else
            {
               
                return redirect('actualites')->with('error', 'Failed to create Actualites! Try again.');
            }
            return redirect('actualites')->with('success', 'Actualite created succesfully!');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('actualites')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Actualites
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'etat' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Actualites = Actualites::find($request->id))
            {
                $payload = [
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Actualites->update($payload);
                return redirect()->back()->with('success', 'Actualite information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Actualites! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Actualites
     *
     * @param ActualitesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ActualitesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Actualites::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('actualites')->with('success', 'New user created!');
            }

            return redirect('actualites')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Actualites
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Actualites::with('roles', 'permissions')->find($id);

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
     * Delete Actualites
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Actualites::find($id)) {
            $user->delete();

            return redirect('actualites')->with('success', 'Actualite removed!');
        }

        return redirect('actualites')->with('error', 'Actualite not found');
    }
}
