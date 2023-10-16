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
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ModelHasRoles;

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
                    //Add Default Role
                    /*
                    $ModelHasRoles = new ModelHasRoles;
                    $ModelHasRoles->model_type = "App\Models\User";
                    $ModelHasRoles->model_id =  $user1d;
                    $ModelHasRoles->role_id =  6;
                    $ModelHasRoles->save();
                    */
                    $ModelHasRoles = [
                        'model_type' => "App\Models\User",
                        'model_id' => $user1d,
                        'role_id' => 6,
                    ];
                    ModelHasRoles::insert($ModelHasRoles);
                    //
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
}
