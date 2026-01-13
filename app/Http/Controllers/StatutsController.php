<?php

/**
 * @Author: Zie MC
 * @Date:   2024-05-10 10:20:56
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-06-09 22:03:27
 */
namespace App\Http\Controllers;

use App\Http\Requests\StatutsRequest;
use App\Models\Statuts;
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

class StatutsController extends Controller
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
        $log_detail = "Consultation de  statuts | Success | Utilsateur : ".Auth::user()->name;
        $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);

        $user1d = Auth::user()->id;
        //$dataStatuts = Statuts::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataStatuts = Statuts::orderBy('libelle', 'asc')->get();
        return view('admin.statuts.statuts', compact('user1d', 'dataStatuts'));
    }

    /**
     * Show Statuts List
     *
     * @param Request $request
     * @return mixed
     */
    public function getStatutsList(Request $request): mixed
    {
        $data = Statuts::get();
        $hasManageStatuts = Auth::user()->can('manage_user');
    }

    /**
     * Statuts Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'bg_color' => 'required',
            'user1d' => 'required',
            'numero_etape' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails())
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Création de  statuts ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $Statuts = new Statuts;
            $Statuts->libelle =  $request->libelle;
            $Statuts->bg_color =  $request->bg_color;
            $Statuts->user1d =  $request->user1d;
            $Statuts->numero_etape =  $request->numero_etape;
            $Statuts->description =  $request->description;
            $Statuts->save();
            if ($Statuts)
            {
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 1;
                $log_detail = "Création de  statuts ".$request->libelle." | Success | Utilsateur : ".Auth::user()->name;
                $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
                return redirect('statuts')->with('success', 'Statuts created succesfully!');
            }
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Création de  statuts ".$request->libelle."| Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect('statuts')->with('error', 'Failed to create Statuts! Try again.');
        }
        catch (\Exception $e)
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Création de  statuts ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);

            $bug = $e->getMessage();
            return redirect('statuts')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Statuts
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
            'numero_etape' => 'required',
            'description' => 'required',
        ]);
        if ($validator->fails())
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Mise à jour de  statuts ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Statuts = Statuts::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'bg_color' => $request->bg_color,
                    'update_user' => $request->user1d,
                    'numero_etape' => $request->numero_etape,
                    'description' => $request->description,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Statuts->update($payload);
                $log_ip = $request->ip();
                $user1d = Auth::user()->id;
                $priorite = 1;
                $log_detail = "Mise à jour de  statuts ".$request->libelle." | Success | Utilsateur : ".Auth::user()->name;
                $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
                return redirect()->back()->with('success', 'Statuts information updated succesfully!');
            }
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Mise à jour de  statuts ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            return redirect()->back()->with('error', 'Failed to update Statuts! Try again.');
        }
        catch (\Exception $e)
        {
            $log_ip = $request->ip();
            $user1d = Auth::user()->id;
            $priorite = 2;
            $log_detail = "Mise à jour de  statuts ".$request->libelle." | Error | Utilsateur : ".Auth::user()->name;
            $saveLogs = Logs::saveLogs($log_detail, $user1d, 0, $log_ip, $priorite);
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Statuts
     *
     * @param StatutsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StatutsRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Statuts::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('statuts')->with('success', 'New user created!');
            }

            return redirect('statuts')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Statuts
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Statuts::with('roles', 'permissions')->find($id);

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
     * Delete Statuts
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Statuts::find($id)) {
            $user->delete();

            return redirect('statuts')->with('success', 'Statuts removed!');
        }

        return redirect('statuts')->with('error', 'Statuts not found');
    }
}
