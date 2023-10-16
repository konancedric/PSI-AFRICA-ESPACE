<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradesRequest;
use App\Models\Grades;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class GradesController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $user1d = Auth::user()->id;
        //$dataGrades = Grades::where('user1d', $user1d)->orderBy('libelle', 'asc')->get();
        $dataGrades = Grades::orderBy('libelle', 'asc')->get();
        return view('admin.grades.grades', compact('user1d', 'dataGrades'));
    }

    /**
     * Show Grades List
     *
     * @param Request $request
     * @return mixed
     */
    public function getGradesList(Request $request): mixed
    {
        $data = Grades::get();
        $hasManageGrades = Auth::user()->can('manage_user');
    }

    /**
     * Grades Create
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
            $Grades = new Grades;
            $Grades->libelle =  $request->libelle;
            $Grades->user1d =  $request->user1d;
            $Grades->save();
            if ($Grades)
            {
                return redirect('grades')->with('success', 'Grade created succesfully!');
            }
            return redirect('grades')->with('error', 'Failed to create Grades! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect('grades')->with('error', $bug);
            //return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Update Grades
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
            if ($Grades = Grades::find($request->id))
            {
                $payload = [
                    'libelle' => $request->libelle,
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $Grades->update($payload);
                return redirect()->back()->with('success', 'Grade information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update Grades! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Store Grades
     *
     * @param GradesRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GradesRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = Grades::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('grades')->with('success', 'New user created!');
            }

            return redirect('grades')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Edit Grades
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = Grades::with('roles', 'permissions')->find($id);

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
     * Delete Grades
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        if ($user = Grades::find($id)) {
            $user->delete();

            return redirect('grades')->with('success', 'Grade removed!');
        }

        return redirect('grades')->with('error', 'Grade not found');
    }
}
