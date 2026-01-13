<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForfaitsRequest;
use App\Models\Forfaits;
use App\Models\Categories;
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

class ForfaitsController extends Controller
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
        $dataForfaits = Forfaits::where('ent1d', 1)->orderBy('libelle', 'asc')->get();
        // $dataForfaits = Forfaits::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataConseillerClients = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->where('users.etat', '=', 1)
        ->where('model_has_roles.role_id', '=', 7)
        ->select('users.*', 'model_has_roles.role_id')
        ->get();
        return view('admin.forfaits.forfaits', compact('linkEditor', 'user1d', 'dataForfaits', 'dataEntreprise', 'ent1d', 'dataConseillerClients'));
    }


    /**
     * Update Forfaits
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'etat' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($Forfaits = Forfaits::find($request->id))
            {
                $payload = [
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Forfaits->update($payload);
                return redirect()->back()->with('success', 'Forfait information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Forfaits! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Delete Forfaits
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteOK($id): RedirectResponse
    {
        if ($user = Forfaits::find($id)) {
            $user->delete();

            return redirect('forfaits')->with('success', 'Forfait removed!');
        }

        return redirect('forfaits')->with('error', 'Forfait not found');
    }
}
