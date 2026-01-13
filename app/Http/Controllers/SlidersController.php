<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlidersRequest;
use App\Models\Sliders;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class SlidersController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataSliders = Sliders::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataSliders = Sliders::orderBy('libelle', 'asc')->get();
        return view('admin.sliders.sliders', compact('user1d', 'dataSliders'));
    }

    /**
     * Show Sliders List
     *
     * @param Request $request
     * @return mixed
     */
    public function getSlidersList(Request $request): mixed
    {
        $data = Sliders::get();
        $hasManageSliders = Auth::user()->can('manage_user');
    }

    /**
     * Sliders Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'user1d' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            //dd($request->all()); exit;
            $request->id_commande = 28;
            $directoryPathUser = public_path('upload/sliders/'); 
            /*
            $this->validate($request, [
                'img_sliders' => 'required|image|mimes:jpeg,png,jpg,pdf,JPEG,PNG,JPG,PDF|max:5000', // Validation des types de fichiers et de la taille
            ]);
            */
            if ($request->hasFile('img_sliders'))
            {
                $image = $request->file('img_sliders');
                $img_sliders = time() .$request->id_commande. '_img_sliders.' . $image->getClientOriginalExtension();
                $image->move($directoryPathUser, $img_sliders); // Stockage dans le dossier "images"
            }

            $Sliders = new Sliders;
            $Sliders->libelle =  $request->libelle;
            $Sliders->user1d =  $request->user1d;
            $Sliders->img_sliders =  $img_sliders;
            $Sliders->save();
            if ($Sliders)
            {
                return redirect('sliders')->with('success', 'Slider created succesfully!');
            }
            return redirect('sliders')->with('error', 'Failed to create Sliders! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('sliders')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Sliders
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
            if ($Sliders = Sliders::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Sliders->update($payload);
                return redirect()->back()->with('success', 'Slider information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Sliders! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Sliders
     *
     * @param SlidersRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SlidersRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Sliders::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('sliders')->with('success', 'New user created!');
            }

            return redirect('sliders')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Sliders
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Sliders::with('roles', 'permissions')->find($id);

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
     * Delete Sliders
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = Sliders::find($id)) {
            $user->delete();

            return redirect('sliders')->with('success', 'Slider removed!');
        }

        return redirect('sliders')->with('error', 'Slider not found');
    }
}
