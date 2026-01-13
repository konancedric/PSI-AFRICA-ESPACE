<?php

/**
 * @Author: MARS
 * @Date:   2024-04-11 07:30:55
 * @Last Modified by:   MARS
 * @Last Modified time: 2024-06-09 11:07:01
 */
namespace App\Http\Controllers;

use App\Http\Requests\ParrainagesRequest;
use App\Models\Parrainages;
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

class ParrainagesController extends Controller
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
        $dataParrainages = Parrainages::where('ent1d', 1)->orderBy('created_at', 'asc')->get();
        // $dataParrainages = Parrainages::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.parrainages.parrainages', compact('linkEditor', 'user1d', 'dataParrainages', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }


    /**
     * Update Parrainages
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
            if ($Parrainages = Parrainages::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Parrainages->update($payload);
                return redirect()->back()->with('success', 'Faq information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Parrainages! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Parrainages
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Parrainages::with('roles', 'permissions')->find($id);

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
     * Delete Parrainages
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Parrainages::find($id)) {
            $user->delete();

            return redirect('parrainages')->with('success', 'Faq removed!');
        }

        return redirect('parrainages')->with('error', 'Faq not found');
    }
}
