<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartenairesRequest;
use App\Models\Partenaires;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class PartenairesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataPartenaires = Partenaires::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataPartenaires = Partenaires::orderBy('libelle', 'asc')->get();
        return view('admin.partenaires.partenaires', compact('user1d', 'dataPartenaires'));
    }

    /**
     * Show Partenaires List
     *
     * @param Request $request
     * @return mixed
     */
    public function getPartenairesList(Request $request): mixed
    {
        $data = Partenaires::get();
        $hasManagePartenaires = Auth::user()->can('manage_user');
    }

    /**
     * Partenaires Create
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
            $request->id_commande = 28;
            $directoryPathUser = public_path('upload/partenaires/'); 
            $this->validate($request, [
                'img_partenaires' => 'required|image|mimes:jpeg,png,jpg,pdf,JPEG,PNG,JPG,PDF|max:2000', // Validation des types de fichiers et de la taille
            ]);
            if ($request->hasFile('img_partenaires'))
            {
                $image = $request->file('img_partenaires');
                $img_partenaires = time() .$request->id_commande. '_img_partenaires.' . $image->getClientOriginalExtension();
                $image->move($directoryPathUser, $img_partenaires); // Stockage dans le dossier "images"
            }

            $Partenaires = new Partenaires;
            $Partenaires->libelle =  $request->libelle;
            $Partenaires->site_web =  $request->site_web;
            $Partenaires->user1d =  $request->user1d;
            $Partenaires->img_partenaires =  $img_partenaires;
            $Partenaires->save();
            if ($Partenaires)
            {
                return redirect('partenaires')->with('success', 'Partenaire created succesfully!');
            }
            return redirect('partenaires')->with('error', 'Failed to create Partenaires! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('partenaires')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Partenaires
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
            if ($Partenaires = Partenaires::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Partenaires->update($payload);
                return redirect()->back()->with('success', 'Partenaire information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Partenaires! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Partenaires
     *
     * @param PartenairesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PartenairesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Partenaires::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('partenaires')->with('success', 'New user created!');
            }

            return redirect('partenaires')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Partenaires
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Partenaires::with('roles', 'permissions')->find($id);

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
     * Delete Partenaires
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = Partenaires::find($id)) {
            $user->delete();

            return redirect('partenaires')->with('success', 'Partenaire removed!');
        }

        return redirect('partenaires')->with('error', 'Partenaire not found');
    }
}
