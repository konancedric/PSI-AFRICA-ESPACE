<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriesRequest;
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

class CategoriesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataCategories = Categories::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataCategories = Categories::orderBy('libelle', 'asc')->get();
        return view('admin.categories.categories', compact('user1d', 'dataCategories'));
    }

    /**
     * Show Categories List
     *
     * @param Request $request
     * @return mixed
     */
    public function getCategoriesList(Request $request): mixed
    {
        $data = Categories::get();
        $hasManageCategories = Auth::user()->can('manage_user');
    }

    /**
     * Categories Create
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
            $Categories = new Categories;
            $Categories->libelle =  $request->libelle;
            $Categories->user1d =  $request->user1d;
            $Categories->save();
            if ($Categories)
            {
                return redirect('categories')->with('success', 'Categorie created succesfully!');
            }
            return redirect('categories')->with('error', 'Failed to create Categories! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('categories')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Categories
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
            if ($Categories = Categories::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Categories->update($payload);
                return redirect()->back()->with('success', 'Categorie information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Categories! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Categories
     *
     * @param CategoriesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoriesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Categories::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('categories')->with('success', 'New user created!');
            }

            return redirect('categories')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Categories
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Categories::with('roles', 'permissions')->find($id);

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
     * Delete Categories
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = Categories::find($id)) {
            $user->delete();

            return redirect('categories')->with('success', 'Categorie removed!');
        }

        return redirect('categories')->with('error', 'Categorie not found');
    }
}
