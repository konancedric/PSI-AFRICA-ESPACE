<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriesImagesRequest;
use App\Models\CategoriesImages;
use App\Models\GalerieImages;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class CategoriesImagesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataCategoriesImages = CategoriesImages::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataCategoriesImages = CategoriesImages::orderBy('libelle', 'asc')->get();
        //$dataGalerieImages = GalerieImages::orderBy('libelle', 'asc')->get();
        return view('admin.categories-images.categories-images', compact('user1d', 'dataCategoriesImages'));
    }

    /**
     * Show CategoriesImages List
     *
     * @param Request $request
     * @return mixed
     */
    public function getCategoriesImagesList(Request $request): mixed
    {
        $data = CategoriesImages::get();
        $hasManageCategoriesImages = Auth::user()->can('manage_user');
    }

    /**
     * CategoriesImages Create
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
            $CategoriesImages = new CategoriesImages;
            $CategoriesImages->libelle =  $request->libelle;
            $CategoriesImages->user1d =  $request->user1d;
            $CategoriesImages->save();
            if ($CategoriesImages)
            {
                return redirect('categories-images')->with('success', 'Categorie Image created succesfully!');
            }
            return redirect('categories-images')->with('error', 'Failed to create CategoriesImages! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('categories-images')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update CategoriesImages
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
            if ($CategoriesImages = CategoriesImages::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $CategoriesImages->update($payload);
                return redirect()->back()->with('success', 'Categorie Image information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update CategoriesImages! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store CategoriesImages
     *
     * @param CategoriesImagesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoriesImagesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = CategoriesImages::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('categories-images')->with('success', 'New user created!');
            }

            return redirect('categories-images')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit CategoriesImages
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = CategoriesImages::with('roles', 'permissions')->find($id);

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
     * Delete CategoriesImages
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = CategoriesImages::find($id)) {
            $user->delete();

            return redirect('categories-images')->with('success', 'Categorie Image removed!');
        }

        return redirect('categories-images')->with('error', 'Categorie Image not found');
    }

    /**
     * GalerieImages Create Images
     *
     * @return mixed
     */
    public function createImages(Request $request): RedirectResponse
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
            $request->id_commande = 12;
            $directoryPathUser = public_path('upload/galerie-images/'); 
            $this->validate($request, [
                'save_url' => 'required|image|mimes:jpeg,png,jpg,pdf,JPEG,PNG,JPG,PDF|max:2000', // Validation des types de fichiers et de la taille
            ]);
            if ($request->hasFile('save_url'))
            {
                $image = $request->file('save_url');
                $save_url = time() .$request->id_commande. '_save_url.' . $image->getClientOriginalExtension();
                $image->move($directoryPathUser, $save_url); // Stockage dans le dossier "images"
            }

            $GalerieImages = new GalerieImages;
            $GalerieImages->libelle =  $request->libelle;
            $GalerieImages->user1d =  $request->user1d;
            $GalerieImages->id_categorie =  $request->id_categorie;
            $GalerieImages->save_url =  $save_url;
            $GalerieImages->save();
            if ($GalerieImages)
            {
                return redirect('categories-images')->with('success', 'GalerieImages created succesfully!');
            }
            return redirect('categories-images')->with('error', 'Failed to create GalerieImages! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('categories-images')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }
}
