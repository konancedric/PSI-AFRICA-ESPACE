<?php

namespace App\Http\Controllers;

use App\Http\Requests\VillesRequest;
use App\Models\Villes;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class VillesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataVilles = Villes::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataVilles = Villes::orderBy('libelle', 'asc')->get();
        return view('admin.villes.villes', compact('user1d', 'dataVilles'));
    }

    /**
     * Show Villes List
     *
     * @param Request $request
     * @return mixed
     */
    public function getVillesList(Request $request): mixed
    {
        $data = Villes::get();
        $hasManageVilles = Auth::user()->can('manage_user');
    }

    /**
     * Villes Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'user1d' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $Villes = new Villes;
            $Villes->libelle =  $request->libelle;
            $Villes->user1d =  $request->user1d;
            $Villes->save();
            if ($Villes)
            {
                return redirect('villes')->with('success', 'Ville created succesfully!');
            }
            return redirect('villes')->with('error', 'Failed to create Villes! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('villes')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Villes
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'libelle' => 'required',
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
            if ($Villes = Villes::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Villes->update($payload);
                return redirect()->back()->with('success', 'Ville information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Villes! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Villes
     *
     * @param VillesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(VillesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Villes::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('villes')->with('success', 'New user created!');
            }

            return redirect('villes')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Villes
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Villes::with('roles', 'permissions')->find($id);

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
     * Delete Villes
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = Villes::find($id)) {
            $user->delete();

            return redirect('villes')->with('success', 'Ville removed!');
        }

        return redirect('villes')->with('error', 'Ville not found');
    }

    public function show($id): View
    {
        $user1d = Auth::user()->id;
        //$dataVilles = Villes::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataVilles = Villes::where('id', $id)->get();
        return view('admin.villes.show-villes', compact('user1d', 'dataVilles'));
    }

    /**
     * Update Villes
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function write_historique(Request $request)
    {   
        $historique = $request->input('content');
        $id = $request->input('id');
        $user1d = $request->input('user1d');
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        if ($Villes = Villes::find($id))
        {
            $payload = [
                'historique' => $historique,
                'update_user' => $user1d,
                'updated_at' => $currentDateTime,
            ];
            $update = $Villes->update($payload);
            return response()->json(['success' => true]);
        }
        return response()->json(['false' => true]);
        /*
        $validator = Validator::make($request->all(), [
            'user1d' => 'required',
            'id' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Villes = Villes::find($request->id))
            {
                $payload = [
                    'historique' => $request->historique,
                    'update_user' => $request->user1d,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Villes->update($payload);
                return redirect()->back()->with('success', 'Ville historique updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Ville historique! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
        */
    }
}
