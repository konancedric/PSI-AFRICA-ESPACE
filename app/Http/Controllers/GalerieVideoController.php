<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalerieVideoRequest;
use App\Models\GalerieVideo;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class GalerieVideoController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataGalerieVideo = GalerieVideo::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataGalerieVideo = GalerieVideo::orderBy('libelle', 'asc')->get();
        return view('admin.galerie-video.galerie-video', compact('user1d', 'dataGalerieVideo'));
    }

    /**
     * Show GalerieVideo List
     *
     * @param Request $request
     * @return mixed
     */
    public function getGalerieVideoList(Request $request): mixed
    {
        $data = GalerieVideo::get();
        $hasManageGalerieVideo = Auth::user()->can('manage_user');
    }

    /**
     * GalerieVideo Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'save_url' => 'required',
            'user1d' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $GalerieVideo = new GalerieVideo;
            $GalerieVideo->libelle =  $request->libelle;
            $GalerieVideo->save_url =  $request->save_url;
            $GalerieVideo->user1d =  $request->user1d;
            $GalerieVideo->save();
            if ($GalerieVideo)
            {
                return redirect('galerie-video')->with('success', 'Galerie Video created succesfully!');
            }
            return redirect('galerie-video')->with('error', 'Failed to create GalerieVideo! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('galerie-video')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update GalerieVideo
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'save_url' => 'required',
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
            if ($GalerieVideo = GalerieVideo::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'save_url' => $request->save_url,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $GalerieVideo->update($payload);
                return redirect()->back()->with('success', 'Galerie Video information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update GalerieVideo! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store GalerieVideo
     *
     * @param GalerieVideoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GalerieVideoRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = GalerieVideo::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('galerie-video')->with('success', 'New user created!');
            }

            return redirect('galerie-video')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit GalerieVideo
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = GalerieVideo::with('roles', 'permissions')->find($id);

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
     * Delete GalerieVideo
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = GalerieVideo::find($id)) {
            $user->delete();

            return redirect('galerie-video')->with('success', 'Galerie Video removed!');
        }

        return redirect('galerie-video')->with('error', 'Galerie Video not found');
    }
}
