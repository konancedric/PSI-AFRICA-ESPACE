<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServicesRequest;
use App\Models\Services;
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

class ServicesController extends Controller
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
        $dataServices = Services::where('ent1d', 1)->orderBy('libelle', 'asc')->get();
        // $dataServices = Services::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.services.services', compact('linkEditor', 'user1d', 'dataServices', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }

    /**
     * Show Services List
     *
     * @param Request $request
     * @return mixed
     */
    public function getServicesList(Request $request): mixed
    {
        $data = Services::get();
        $hasManageServices = Auth::user()->can('manage_user');
    }

    /**
     * Services Create
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
            $Services = new Services;
            $Services->libelle =  $request->libelle;
            $Services->id_site =  $request->id_site;
            $Services->abreviation =  $request->abreviation;
            $Services->contact =  $request->contact;
            $Services->description =  $request->description;
            $Services->user1d =  $request->user1d;
            $Services->ent1d =  $request->ent1d;
            $Services->save();
            if ($Services)
            {
                
            }
            else
            {
               
                return redirect('services')->with('error', 'Failed to create Services! Try again.');
            }
            return redirect('services')->with('success', 'Service created succesfully!');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('services')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Services
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
            if ($Services = Services::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Services->update($payload);
                return redirect()->back()->with('success', 'Service information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Services! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Services
     *
     * @param ServicesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ServicesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Services::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('services')->with('success', 'New user created!');
            }

            return redirect('services')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Services
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Services::with('roles', 'permissions')->find($id);

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
     * Delete Services
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Services::find($id)) {
            $user->delete();

            return redirect('services')->with('success', 'Service removed!');
        }

        return redirect('services')->with('error', 'Service not found');
    }
}
