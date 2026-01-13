<?php

namespace App\Http\Controllers;

use App\Http\Requests\TemoignagesRequest;
use App\Models\Temoignages;
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

class TemoignagesController extends Controller
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
        $dataTemoignages = Temoignages::where('ent1d', 1)->orderBy('libelle', 'asc')->get();
        // $dataTemoignages = Temoignages::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.temoignages.temoignages', compact('linkEditor', 'user1d', 'dataTemoignages', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }

    /**
     * Show Temoignages List
     *
     * @param Request $request
     * @return mixed
     */
    public function getTemoignagesList(Request $request): mixed
    {
        $data = Temoignages::get();
        $hasManageTemoignages = Auth::user()->can('manage_user');
    }

    /**
     * Temoignages Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'id_site' => 'required',
            'abreviation' => 'required',
           // 'contact' => 'required',
            'user1d' => 'required',
            'ent1d' => 'required',
        ]);
        if ($validator->fails())
        {

            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $Temoignages = new Temoignages;
            $Temoignages->libelle =  $request->libelle;
            $Temoignages->id_site =  $request->id_site;
            $Temoignages->abreviation =  $request->abreviation;
            $Temoignages->contact =  $request->contact;
            $Temoignages->description =  $request->description;
            $Temoignages->user1d =  $request->user1d;
            $Temoignages->ent1d =  $request->ent1d;
            $Temoignages->save();
            if ($Temoignages)
            {
                
            }
            else
            {
               
                return redirect('temoignages')->with('error', 'Failed to create Temoignages! Try again.');
            }
            return redirect('temoignages')->with('success', 'Temoignage created succesfully!');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('temoignages')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Temoignages
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
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
            if ($Temoignages = Temoignages::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Temoignages->update($payload);
                return redirect()->back()->with('success', 'Temoignage information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Temoignages! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Temoignages
     *
     * @param TemoignagesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TemoignagesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Temoignages::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('temoignages')->with('success', 'New user created!');
            }

            return redirect('temoignages')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Temoignages
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Temoignages::with('roles', 'permissions')->find($id);

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
     * Delete Temoignages
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Temoignages::find($id)) {
            $user->delete();

            return redirect('temoignages')->with('success', 'Temoignage removed!');
        }

        return redirect('temoignages')->with('error', 'Temoignage not found');
    }
}
