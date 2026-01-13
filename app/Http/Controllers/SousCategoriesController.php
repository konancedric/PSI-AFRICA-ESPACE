<?php

namespace App\Http\Controllers;

use App\Http\Requests\SousCategoriesRequest;
use App\Models\SousCategories;
use App\Models\Categories;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SousCategoriesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataSousCategories = SousCategories::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataSousCategories = SousCategories::orderBy('libelle', 'asc')->get();
        $dataCategories = Categories::where('etat', 1)->orderBy('libelle', 'asc')->get();
        return view('admin.souscategories.souscategories', compact('dataCategories', 'user1d', 'dataSousCategories'));
    }

    /**
     * Show SousCategories List
     *
     * @param Request $request
     * @return mixed
     */
    public function getSousCategoriesList(Request $request): mixed
    {
        $data = SousCategories::get();
        $hasManageSousCategories = Auth::user()->can('manage_user');
    }

    /**
     * SousCategories Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'user1d' => 'required',
            'id_categorie' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $SousCategories = new SousCategories;
            $SousCategories->libelle =  $request->libelle;
            $SousCategories->id_categorie =  $request->id_categorie;
            $SousCategories->user1d =  $request->user1d;
            $SousCategories->save();
            if ($SousCategories)
            {
                return redirect('souscategories')->with('success', 'SousCategorie created succesfully!');
            }
            return redirect('souscategories')->with('error', 'Failed to create SousCategories! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('souscategories')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update SousCategories
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
            'id_categorie' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($SousCategories = SousCategories::find($request->id))
            {
                $payload = [
                    'id_categorie' => $request->id_categorie,
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $SousCategories->update($payload);
                return redirect()->back()->with('success', 'SousCategorie information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update SousCategories! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store SousCategories
     *
     * @param SousCategoriesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SousCategoriesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = SousCategories::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('souscategories')->with('success', 'New user created!');
            }

            return redirect('souscategories')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit SousCategories
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = SousCategories::with('roles', 'permissions')->find($id);

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
     * Delete SousCategories
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        if ($user = SousCategories::find($id)) {
            $user->delete();

            return redirect('souscategories')->with('success', 'SousCategorie removed!');
        }

        return redirect('souscategories')->with('error', 'SousCategorie not found');
    }
}
