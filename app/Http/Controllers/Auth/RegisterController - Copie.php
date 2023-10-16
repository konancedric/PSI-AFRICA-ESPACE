<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\EntreprisesRequest;
use App\Models\Entreprises;
use App\Http\Requests\TicketsRequest;
use App\Models\Tickets;
use App\Http\Requests\ServicesRequest;
use App\Models\Services;
use App\Http\Requests\PlageHoraireRequest;
use App\Models\PlageHoraire;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function storeregisterpro(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed'],
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', $validator->messages()->first());
        }
        try
        {
            $User = new User;
            $User->name =  $request->name;
            $User->email =  $request->email;
            $User->password =  Hash::make($request['password']);
            $User->type_user =  9;
            $User->save();
            if ($User)
            {
                $user1d = User::latest()->first()->id;
                $validator = Validator::make($request->all(), [
                    'denomination' => 'required',
                    'emailent' => 'required',
                    'adresse' => 'required',
                    'contact' => 'required',
                    'username' => 'required',
                    'logo_ent' => 'required',
                ]);
                if ($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->messages()->first());
                }
                try
                {
                    $this->validate($request, [
                        'logo_ent' => 'required|image|mimes:jpeg,png,jpg,gif,JPEG,PNG,JPG,GIF|max:2048', // Validation des types de fichiers et de la taille
                    ]);
                    if ($request->hasFile('logo_ent'))
                    {
                        $image = $request->file('logo_ent');
                        $logoEnt = time() .$request->user1d.'_logo_ent.' . $image->getClientOriginalExtension();
                        $image->move(public_path('/upload/entreprise/'), $logoEnt); // Stockage dans le dossier "images"
                    }
                    $Entreprises = new Entreprises;
                    $Entreprises->denomination =  $request->denomination;
                    $Entreprises->contact =  $request->contact;
                    $Entreprises->description =  $request->description;
                    $Entreprises->emailent =  $request->emailent;
                    $Entreprises->username =  $request->username;
                    $Entreprises->adresse =  $request->adresse;
                    $Entreprises->user1d =  $user1d;
                    $Entreprises->logo_ent =  $logoEnt;
                    $Entreprises->save();
                    if ($Entreprises)
                    {
                        return redirect('register/pro')->with('success', 'Votre compte entreprise a été crée avec succès et sera activé dans les plus bref delais. Vous pouvez vous connecter afin de complèter les informations manquantes !');
                    }
                    return redirect('register/pro')->with('error', 'Failed to create Entreprises! Try again.');
                }
                catch (\Exception $e)
                {
                    $bug = $e->getMessage();
                    return redirect('register/pro')->with('error', $bug);
                    //return redirect()->back()->with('error', $bug);
                }
            }
            return redirect('register/pro')->with('error', 'Failed to create user! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('register/pro')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    public function takeRdvByUserNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'max:255'],
            'prenoms' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact' => ['required', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'confirmed'],
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', $validator->messages()->first());
        }
        try
        {
            $nameUser = $request->nom." ".$request->prenoms;
            $User = new User;
            $User->name =  $nameUser;
            $User->email =  $request->email;
            $User->adresse =  $request->adresse;
            $User->contact =  $request->contact;
            $User->password =  Hash::make($request['password']);
            $User->type_user =  1;
            $User->save();
            if ($User)
            {
                $user1d = User::latest()->first()->id;
                $validator = Validator::make($request->all(), [
                    'ent1d' => 'required',
                    'id_site' => 'required',
                    'id_service' => 'required',
                    'id_rdv' => 'required',
                    'id_horaire' => 'required',
                ]);
                if ($validator->fails())
                {
                    return redirect()->back()->with('error', $validator->messages()->first());
                }
                try
                {
                    $numero= Tickets::NumeroTicket($request->id_site, $request->id_service, $request->id_rdv);
                    $searchService = Services::find($request->id_service);
                    $code= $searchService->abreviation." ".$numero;
                    $Tickets = new Tickets;
                    $Tickets->id_service =  $request->id_service;
                    $Tickets->id_site =  $request->id_site;
                    $Tickets->id_rdv =  $request->id_rdv;
                    $Tickets->id_horaire =  $request->id_horaire;
                    $Tickets->numero =  $numero;
                    $Tickets->code =  $code;
                    $Tickets->id_client =  $user1d;
                    $Tickets->ent1d =  $request->ent1d;
                    $Tickets->save();
                    try
                    {
                        if ($PlageHoraire = PlageHoraire::find($request->id_horaire))
                        {
                            $payload = [
                                'etat' => 0,
                            ];
                            $update = $PlageHoraire->update($payload);
                            $LastTicket = Tickets::latest()->first()->id;
                            $etatPlageHoraire = 1;
                            $bugReturn ="Votre rendez vous a été programmé avec succès";
                            $statusBugReturn ="success";
                            //return redirect('success-search-ticket-rdv')->with('success', 'Votre compte entreprise a été crée avec succès et sera activé dans les plus bref delais. Vous pouvez vous connecter afin de complèter les informations manquantes !');
                            return view('page.success-search-ticket-rdv', compact('user1d', 'LastTicket', 'etatPlageHoraire'));
                        }
                        else
                        {
                            $bugReturn ="Votre rendez vous a été programmé avec succès";
                            $statusBugReturn ="warning";
                            $etatPlageHoraire = 0;
                            return view('page.success-search-ticket-rdv', compact('user1d', 'LastTicket', 'etatPlageHoraire'));
                        }
                    }
                    catch (\Exception $e)
                    {
                        $bug = $e->getMessage();
                        //return redirect('register/pro')->with('error', $bug);
                        return redirect()->back()->with('error', $bug);
                    }
                }
                catch (\Exception $e)
                {
                    $bug = $e->getMessage();
                    //return redirect('register/pro')->with('error', $bug);
                    return redirect()->back()->with('error', $bug);
                }
            }
            return redirect()->back()->with('error', 'Failed to create user! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('register/pro')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    public function updateRdvByUserNewGet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero' => ['required', 'string', 'max:255'],
            'date_rdv' => ['required',],
            'ent1d' => ['required',],
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', $validator->messages()->first());
        }
        try
        {
            $dataTicketSearch = Tickets::SearchTicketByDateRdvAndNumeroTicket($request->numero, $request->date_rdv, $request->ent1d);
            $nbreTicket = count($dataTicketSearch);
            if($nbreTicket != 1)
            {
                return redirect()->back()->with('error', 'Ticket Introuvable, Veuillez reéssayer à nouveau !');
            }
            return view('pages.edit-ticket-by-user', compact('dataTicketSearch'));
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    public function UpdateRdvByUserNewPost(Request $request)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'ent1d' => 'required',
            'id_site' => 'required',
            'id_service' => 'required',
            'id_rdv' => 'required',
            'id_horaire' => 'required',
            'btnUpdateTicket' => 'required',
            'id_client' => 'required',
            'id' => 'required',
            'entname' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', $validator->messages()->first());
        }
        try
        {
            $bugUrl ="compagny/".$request->entname;
            if($request->btnUpdateTicket == 1)
            {
                if ($Tickets = Tickets::find($request->id))
                {
                    /*$numero= Tickets::NumeroTicket($request->id_site, $request->id_service, $request->id_rdv);
                    $searchService = Services::find($request->id_service);
                    $code= $searchService->abreviation." ".$numero;*/

                    $payload = [
                        'id_rdv' => $request->id_rdv,
                        'id_horaire' => $request->id_horaire,
                        'updated_at' => $currentDateTime,
                        /*'numero' => $numero,
                        'code' => $code,*/
                    ];
                    $update = $Tickets->update($payload);
                    $LastTicket = $request->id;
                    $user1d = $request->id_client;
                    try
                    {
                        if ($PlageHoraire = PlageHoraire::find($request->id_horaire))
                        {
                            $payload = [
                                'etat' => 0,
                            ];
                            $update = $PlageHoraire->update($payload);
                            if ($PlageHoraire = PlageHoraire::find($request->last_id_horaire))
                            {
                                $payload = [
                                    'etat' => 1,
                                ];
                                $update = $PlageHoraire->update($payload);
                                $bugReturn ="Votre ticket Mis à jour avec succès";
                                $statusBugReturn ="success";
                                $etatPlageHoraire = 1;
                                return view('page.success-search-ticket-rdv', compact('user1d', 'LastTicket', 'etatPlageHoraire'));
                            }
                            else
                            {
                                $bugReturn ="Votre ticket Mis à jour avec succès";
                                $statusBugReturn ="info";
                                $etatPlageHoraire = 0;
                                return view('page.success-search-ticket-rdv', compact('user1d', 'LastTicket', 'etatPlageHoraire'));
                            }
                        }
                        else
                        {
                            $bugReturn ="Votre ticket Mis à jour avec succès";
                            $statusBugReturn ="warning";
                            $etatPlageHoraire = 0;
                            return view('page.success-search-ticket-rdv', compact('user1d', 'LastTicket', 'etatPlageHoraire'));
                        }
                    }
                    catch (\Exception $e)
                    {
                        $bug = $e->getMessage();
                        //return redirect('register/pro')->with('error', $bug);
                        return redirect($bugUrl)->back()->with('error', $bug);
                    }
                }
                return redirect($bugUrl)->with('error', $bug);
            }
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            //return redirect('register/pro')->with('error', $bug);
            return redirect()->back()->with('error', $bug);
        }
    }

    public function CancelRdvByUserNew($id_ticket, $entname)
    {
        $bugUrl ="compagny/".$entname;
        if ($Tickets = Tickets::find($id_ticket))
        {
            $payload = [
                'etat' => 0,
                /*'numero' => $numero,
                'code' => $code,*/
            ];
            $update = $Tickets->update($payload);
            try
            {
                if ($PlageHoraire = PlageHoraire::find($Tickets->id_horaire))
                {
                    $payload = [
                        'etat' => 1,
                    ];
                    $update = $PlageHoraire->update($payload);
                    $bugReturn ="Votre ticket annulé avec succès";
                    $statusBugReturn ="success";
                    $etatPlageHoraire = 1;
                    return redirect($bugUrl)->with('success', $bugReturn);
                }
                else
                {
                    $bugReturn ="Votre ticket annulé avec succès";
                    $statusBugReturn ="warning";
                    $etatPlageHoraire = 0;
                    return redirect($bugUrl)->with('success', $bugReturn);
                }
            }
            catch (\Exception $e)
            {
                $bug = $e->getMessage();
                //return redirect('register/pro')->with('error', $bug);
                return redirect($bugUrl)->with('error', $bug);
            }
        }
        return redirect($bugUrl)->with('error', $bug);
    }
}
