<?php

/**
 * @Author: Zie MC
 * @Date:   2024-05-10 10:20:56
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-06-20 07:13:44
 */
namespace App\Http\Controllers;

use App\Http\Requests\StatutsEtatRequest;
use App\Models\StatutsEtat;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Http\Requests\LogsRequest;
use App\Models\Logs;

class StatutsEtatController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request): mixed
    { 
        $log_ip = $request->ip();
        $user1d = Auth::user()->id;
        $priorite = 1;
        $log_detail = "Consultation de  statuts-etat | Success | Utilsateur : ".Auth::user()->name;
        $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);

        $user1d = Auth::user()->id;
        //$dataStatutsEtat = StatutsEtat::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataStatutsEtat = StatutsEtat::orderBy('libelle', 'asc')->get();
        return view('admin.statuts-etat.statuts-etat', compact('user1d', 'dataStatutsEtat'));
    }

    /**
     * Show StatutsEtat List
     *
     * @param Request $request
     * @return mixed
     */
    public function getStatutsEtatList(Request $request): mixed
    {
        $data = StatutsEtat::get();
        $hasManageStatutsEtat = Auth::user()->can('manage_user');
    }

    /**
     * StatutsEtat Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'bg_color' => 'required',
            'user1d' => 'required',
        ]);
        if ($validator->fails())
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Création de  statuts-etat ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $StatutsEtat = new StatutsEtat;
            $StatutsEtat->libelle =  $request->libelle;
            $StatutsEtat->bg_color =  $request->bg_color;
            $StatutsEtat->user1d =  $request->user1d;
            $StatutsEtat->save();
            if ($StatutsEtat)
            {
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 1;
                $log_detail = "Création de  statuts-etat ".$request->libelle." | Success | Utilsateur : ".Auth::user()->name;
                $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
                return redirect('statuts-etat')->with('success', 'StatutsEtat created succesfully!');
            }
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Création de  statuts-etat ".$request->libelle."| Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect('statuts-etat')->with('error', 'Failed to create StatutsEtat! Try again.');
        }
        catch (\Exception $e)
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Création de  statuts-etat ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);

            $bug = $e->getMessage();
            return redirect('statuts-etat')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update StatutsEtat
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'bg_color' => 'required',
            'user1d' => 'required',
            'id' => 'required',
            'etat' => 'required',
        ]);
        if ($validator->fails())
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Mise à jour de  statuts-etat ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($StatutsEtat = StatutsEtat::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'bg_color' => $request->bg_color,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $StatutsEtat->update($payload);
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 1;
                $log_detail = "Mise à jour de  statuts-etat ".$request->libelle." | Success | Utilsateur : ".Auth::user()->name;
                $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
                return redirect()->back()->with('success', 'StatutsEtat information updated succesfully!');
            }
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Mise à jour de  statuts-etat ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect()->back()->with('error', 'Failed to update StatutsEtat! Try again.');
        }
        catch (\Exception $e)
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Mise à jour de  statuts-etat ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store StatutsEtat
     *
     * @param StatutsEtatRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StatutsEtatRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = StatutsEtat::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('statuts-etat')->with('success', 'New user created!');
            }

            return redirect('statuts-etat')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit StatutsEtat
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = StatutsEtat::with('roles', 'permissions')->find($id);

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
     * Delete StatutsEtat
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = StatutsEtat::find($id)) {
            $user->delete();

            return redirect('statuts-etat')->with('success', 'StatutsEtat removed!');
        }

        return redirect('statuts-etat')->with('error', 'StatutsEtat not found');
    }
}
