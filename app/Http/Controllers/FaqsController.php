<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaqsRequest;
use App\Models\Faqs;
use App\Models\User;
use App\Models\Entreprises;
use App\Models\ModelHasRoles;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FaqsController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request): mixed
    {
        $linkEditor = "https://ed.psiafrica.ci";
        $user1d = Auth::user()->id;
        $dataEntreprise = Entreprises::where('user1d', $user1d)->first();
        $ent1d = 1;
        $dataFaqs = Faqs::where('ent1d', 1)->orderBy('libelle', 'asc')->get();
        // $dataFaqs = Faqs::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.faqs.faqs', compact('linkEditor', 'user1d', 'dataFaqs', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }

    /**
     * Show Faqs List
     *
     * @param Request $request
     * @return mixed
     */
    public function getFaqsList(Request $request): mixed
    {
        $data = Faqs::get();
        $hasManageFaqs = Auth::user()->can('manage_user');
    }

    /**
     * Faqs Create
     *
     * @return mixed
     */
    public function create(Request $request): RedirectResponse
    {
         $validator = Validator::make($request->all(), [
            'libelle' => 'required',
            'id_site' => 'required',
            'abreviation' => 'required',
           // 'contact' => 'required',
            'user1d' => 'required',
            'ent1d' => 'required',
        ]);
        if ($validator->fails())
        {

            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try
        {
            $Faqs = new Faqs;
            $Faqs->libelle =  $request->libelle;
            $Faqs->id_site =  $request->id_site;
            $Faqs->abreviation =  $request->abreviation;
            $Faqs->contact =  $request->contact;
            $Faqs->description =  $request->description;
            $Faqs->user1d =  $request->user1d;
            $Faqs->ent1d =  $request->ent1d;
            $Faqs->save();
            if ($Faqs)
            {
                
            }
            else
            {
               
                return redirect('faqs')->with('error', 'Failed to create Faqs! Try again.');
            }
            return redirect('faqs')->with('success', 'Faq created succesfully!');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('faqs')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Faqs
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
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
            if ($Faqs = Faqs::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Faqs->update($payload);
                return redirect()->back()->with('success', 'Faq information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Faqs! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Faqs
     *
     * @param FaqsRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(FaqsRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Faqs::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('faqs')->with('success', 'New user created!');
            }

            return redirect('faqs')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Faqs
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Faqs::with('roles', 'permissions')->find($id);

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
     * Delete Faqs
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Faqs::find($id)) {
            $user->delete();

            return redirect('faqs')->with('success', 'Faq removed!');
        }

        return redirect('faqs')->with('error', 'Faq not found');
    }
}
