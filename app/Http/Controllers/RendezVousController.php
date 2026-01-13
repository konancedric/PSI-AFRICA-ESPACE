<?php

/**
 * @Author: MARS
 * @Date:   2024-04-11 07:30:55
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-04-11 08:49:56
 */
namespace App\Http\Controllers;

use App\Http\Requests\RendezVousRequest;
use App\Models\RendezVous;
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

class RendezVousController extends Controller
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
        $dataRendezVous = RendezVous::where('ent1d', 1)->orderBy('created_at', 'asc')->get();
        // $dataRendezVous = RendezVous::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.rendez-vous.rendez-vous', compact('linkEditor', 'user1d', 'dataRendezVous', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }

    /**
     * Show RendezVous List
     *
     * @param Request $request
     * @return mixed
     */
    public function getRendezVousList(Request $request): mixed
    {
        $data = RendezVous::get();
        $hasManageRendezVous = Auth::user()->can('manage_user');
    }

    /**
     * RendezVous Create
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
            $RendezVous = new RendezVous;
            $RendezVous->libelle =  $request->libelle;
            $RendezVous->id_site =  $request->id_site;
            $RendezVous->abreviation =  $request->abreviation;
            $RendezVous->contact =  $request->contact;
            $RendezVous->description =  $request->description;
            $RendezVous->user1d =  $request->user1d;
            $RendezVous->ent1d =  $request->ent1d;
            $RendezVous->save();
            if ($RendezVous)
            {
                
            }
            else
            {
               
                return redirect('rendez-vous')->with('error', 'Failed to create RendezVous! Try again.');
            }
            return redirect('rendez-vous')->with('success', 'Faq created succesfully!');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('rendez-vous')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update RendezVous
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
            if ($RendezVous = RendezVous::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $RendezVous->update($payload);
                return redirect()->back()->with('success', 'Faq information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update RendezVous! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store RendezVous
     *
     * @param RendezVousRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(RendezVousRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = RendezVous::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('rendez-vous')->with('success', 'New user created!');
            }

            return redirect('rendez-vous')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit RendezVous
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = RendezVous::with('roles', 'permissions')->find($id);

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
     * Delete RendezVous
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = RendezVous::find($id)) {
            $user->delete();

            return redirect('rendez-vous')->with('success', 'Faq removed!');
        }

        return redirect('rendez-vous')->with('error', 'Faq not found');
    }
}
