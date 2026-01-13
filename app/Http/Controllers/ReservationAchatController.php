<?php

/**
 * @Author: MARS
 * @Date:   2024-04-11 07:30:55
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-05-13 13:00:31
 */
namespace App\Http\Controllers;

use App\Http\Requests\ReservationAchatRequest;
use App\Models\ReservationAchat;
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

class ReservationAchatController extends Controller
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
        $dataReservationAchat = ReservationAchat::where('ent1d', 1)->orderBy('created_at', 'asc')->get();
        // $dataReservationAchat = ReservationAchat::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.reservation-achat.reservation-achat', compact('linkEditor', 'user1d', 'dataReservationAchat', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }

    /**
     * Show ReservationAchat List
     *
     * @param Request $request
     * @return mixed
     */
    public function getReservationAchatList(Request $request): mixed
    {
        $data = ReservationAchat::get();
        $hasManageReservationAchat = Auth::user()->can('manage_user');
    }

    /**
     * ReservationAchat Create
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
            $ReservationAchat = new ReservationAchat;
            $ReservationAchat->libelle =  $request->libelle;
            $ReservationAchat->id_site =  $request->id_site;
            $ReservationAchat->abreviation =  $request->abreviation;
            $ReservationAchat->contact =  $request->contact;
            $ReservationAchat->description =  $request->description;
            $ReservationAchat->user1d =  $request->user1d;
            $ReservationAchat->ent1d =  $request->ent1d;
            $ReservationAchat->save();
            if ($ReservationAchat)
            {
                
            }
            else
            {
               
                return redirect('reservation-achat')->with('error', 'Failed to create ReservationAchat! Try again.');
            }
            return redirect('reservation-achat')->with('success', 'Faq created succesfully!');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('reservation-achat')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update ReservationAchat
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
            if ($ReservationAchat = ReservationAchat::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $ReservationAchat->update($payload);
                return redirect()->back()->with('success', 'Faq information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update ReservationAchat! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store ReservationAchat
     *
     * @param ReservationAchatRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ReservationAchatRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = ReservationAchat::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('reservation-achat')->with('success', 'New user created!');
            }

            return redirect('reservation-achat')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit ReservationAchat
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = ReservationAchat::with('roles', 'permissions')->find($id);

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
     * Delete ReservationAchat
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = ReservationAchat::find($id)) {
            $user->delete();

            return redirect('reservation-achat')->with('success', 'Faq removed!');
        }

        return redirect('reservation-achat')->with('error', 'Faq not found');
    }
}
