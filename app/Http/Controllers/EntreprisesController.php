<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntreprisesRequest;
use App\Models\Entreprises;
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
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EntreprisesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function GestionEntreprise(): View
    {
        $user1d = Auth::user()->id;
        //$dataEntreprises = Entreprises::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataEntreprises = Entreprises::orderBy('denomination', 'asc')->get();
        return view('admin.entreprises.entreprises', compact('user1d', 'dataEntreprises'));
    }
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $dataEntreprises = Entreprises::orderBy('denomination', 'asc')->get();
        return view('entreprises.entreprises', compact('dataEntreprises'));
    }
    public function compagnyIndex(): View
    {
        $urlBase = explode(url('').'/', request()->url());
        $urlTab = explode("/", $urlBase[1]);  
        $Compagny = $urlTab['1'];
        //$dataEntreprises = Entreprises::where('user1d', $user1d)->orderBy('denomination', 'asc')->get();
        //$dataEntrepriseSearch = Entreprises::find('username', $Compagny);
        $dataEntrepriseSearch = Entreprises::where('username', $Compagny)->first();
        if($dataEntrepriseSearch == null)
        {
            //return redirect('login')->with('error', 'Entreprise Introuvable !');
            return view('home')->with('error', 'Entreprise Introuvable !');;
        }
        //var_dump($dataEntrepriseSearch); exit();

        $dataCategorie = Categories::where('etat', 1)->where('ent1d', $dataEntrepriseSearch->id)->orderBy('libelle', 'asc')->get();
        return view('entreprises.index', compact('Compagny','dataEntrepriseSearch', 'dataCategorie'));
    }
    /**
     * Show Entreprises List
     *
     * @param Request $request
     * @return mixed
     */
    public function getEntreprisesList(Request $request): mixed
    {
        $data = Entreprises::get();
        $hasManageEntreprises = Auth::user()->can('manage_user');
    }

    public function configuration(Request $request): mixed
    {
        $dataEntreprise = Entreprises::where('user1d', Auth::user()->id)->first();
        return view('entreprises.configuration', compact('dataEntreprise'));
    }

    /**
     * Entreprises Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'denomination' => 'required',
            'localisation' => 'required',
            'contact' => 'required',
            'user1d' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $Entreprises = new Entreprises;
            $Entreprises->denomination =  $request->denomination;
            $Entreprises->localisation =  $request->localisation;
            $Entreprises->contact =  $request->contact;
            $Entreprises->description =  $request->description;
            $Entreprises->user1d =  $request->user1d;
            $Entreprises->save();
            if ($Entreprises)
            {
                return redirect('entreprises')->with('success', 'Entreprise created succesfully!');
            }
            return redirect('entreprises')->with('error', 'Failed to create Entreprises! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('entreprises')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Entreprises
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'denomination' => 'required',
            'contact' => 'required',
            'user1d' => 'required',
            'id' => 'required',
            'emailent' => 'required',
            'description' => 'required',
            'username' => 'required',
            'adresse' => 'required',
            //'id_ville' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Entreprises = Entreprises::find($request->id))
            {
                if($Entreprises->url_qr == "")
                {
                    $url_qr = 'https://ivoire-click.ci/entreprise/'.$Entreprises->id.'/'.$request->username;
                    $shot_url = '/entreprise/'.$Entreprises->id.'/'.$request->username;
                    $save_qr = 'upload/entreprise/qr_code/'.$request->username.'_qrcode.png';
                    $qrCode = QrCode::format('png')
                        ->size(300)
                        ->backgroundColor(0, 128, 255)
                        ->generate($url_qr);
                    file_put_contents(public_path($save_qr), $qrCode);
                }
                else
                { 
                    $shot_url = $Entreprises->url_qr;
                    $save_qr = $Entreprises->save_qr;
                } 
                $payload = [
                    'url_qr' => $shot_url,
                    'save_qr' => $save_qr,
                    'denomination' => $request->denomination,
                    'contact' => $request->contact,
                    'description' => $request->description,
                    'update_user' => $request->user1d,
                    'updated_at' => $currentDateTime,
                    'emailent' => $request->emailent,
                    'username' => $request->username,
                    'adresse' => $request->adresse,
                    'map' => $request->map,
                    'link_facebook' => $request->link_facebook,
                    'link_linkedin' => $request->link_linkedin,
                    'link_siteweb' => $request->link_siteweb,
                    'bg_color' => $request->bg_color,
                    'link_twitter' => $request->link_twitter,
                    //'id_ville' => $request->id_ville,
                ];
                $update = $Entreprises->update($payload);
                return redirect()->back()->with('success', 'Entreprise information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Entreprises! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Api
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function config_api(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'tokentPrivate' => 'required',
            'tokentPublic' => 'required',
            'user1d' => 'required',
            'id' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            $tokentPublic = Crypt::encrypt($request->tokentPublic);
            $tokentPrivate = Crypt::encrypt($request->tokentPrivate);
            if ($Entreprises = Entreprises::find($request->id))
            {
                $payload = [
                    '_tokent_public' => $tokentPublic,
                    '_tokent_private' => $tokentPrivate,
                    'update_user' => $request->user1d,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Entreprises->update($payload);
                return redirect()->back()->with('success', 'Api information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Api ! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Entreprises
     *
     * @param EntreprisesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EntreprisesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Entreprises::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('entreprises')->with('success', 'New user created!');
            }

            return redirect('entreprises')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Entreprises
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Entreprises::with('roles', 'permissions')->find($id);

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
     * Delete Entreprises
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOP($id): RedirectResponse
    {
        if ($user = Entreprises::find($id)) {
            $user->delete();

            return redirect('entreprises')->with('success', 'Entreprise removed!');
        }

        return redirect('entreprises')->with('error', 'Entreprise not found');
    }
}
