<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Grades;
use App\Models\Categories;
use Auth;
use DataTables;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Show the users dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        return view('admin.users.users');
    }

    public function listUsers(): View
    {
        $user1d = Auth::user()->id;
        $dataUsers = User::orderBy('name', 'asc')->get();
        $dataCategories = Categories::orderBy('libelle', 'asc')->get();
        $dataGrades = Grades::orderBy('libelle', 'asc')->get();
        return view('admin.users.users-list', compact('user1d', 'dataUsers', 'dataCategories', 'dataGrades'));
    }

    /**
     * Show User List
     *
     * @param Request $request
     * @return mixed
     */
    public function getUserList(Request $request): mixed
    {
        $data = User::get();
        $hasManageUser = Auth::user()->can('manage_user');

        return Datatables::of($data)
            ->addColumn('roles', function ($data) {
                $roles = $data->getRoleNames()->toArray();
                $badge = '';
                if ($roles) {
                    $badge = implode(' , ', $roles);
                }

                return $badge;
            })
            ->addColumn('permissions', function ($data) {
                $roles = $data->getAllPermissions();
                $badges = '';
                foreach ($roles as $key => $role) {
                    $badges .= '<span class="badge badge-dark m-1">' . $role->name . '</span>';
                }

                return $badges;
            })
            ->addColumn('action', function ($data) use ($hasManageUser) {
                $output = '';
                if ($data->name == 'Super Admin') {
                    return '';
                }
                if ($hasManageUser) {
                    $output = '<div class="table-actions">
                                <a href="' . url('user/' . $data->id) . '" ><i class="ik ik-edit-2 f-16 mr-15 text-green"></i></a>
                                <a href="' . url('user/delete/' . $data->id) . '"><i class="ik ik-trash-2 f-16 text-red"></i></a>
                            </div>';
                }

                return $output;
            })
            ->rawColumns(['roles', 'permissions', 'action'])
            ->make(true);
    }

    /**
     * User Create
     *
     * @return mixed
     */
    public function create(): mixed
    {
        try {
            $roles = Role::pluck('name', 'id');

            return view('admin.users.create-user', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store User
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request): RedirectResponse
    {
        try {
            // store user information
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);


            if ($user) {
                // assign new role to the user
                $user->syncRoles($request->role);

                return redirect('users')->with('success', 'New user created!');
            }

            return redirect('users')->with('error', 'Failed to create new user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * New User Add
     *
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newuseradd(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'user1d' => 'required',
            'name' => 'required | string ',
            'matricule' => 'required',
            'id_categorie' => 'required',
            'id_grade' => 'required',
        ]);

        // check validation for password match
        if (isset($request->password)) {
            $validator = Validator::make($request->all(), [
                'password' => 'required | confirmed',
            ]);
        }
        try 
        {
            $this->validate($request, [
                'photo_user' => 'required|image|mimes:jpeg,png,jpg,gif,JPEG,PNG,JPG,GIF|max:2048', // Validation des types de fichiers et de la taille
            ]);
            if ($request->hasFile('photo_user'))
            {
                $image = $request->file('photo_user');
                $photo_user = time() .$request->user1d.'_photo_user.' . $image->getClientOriginalExtension();
                $image->move(public_path('/upload/users/'), $photo_user); // Stockage dans le dossier "images"
            }
            else
            {
                $photo_user = "NULL";
            }
            $Users = new User;
            $Users->photo_user =  $photo_user;
            $Users->user1d =  $request->user1d;
            $Users->contact =  $request->contact;
            $Users->name =  $request->name;
            $Users->matricule =  $request->matricule;
            $Users->id_categorie =  $request->id_categorie;
            $Users->id_grade =  $request->id_grade;
            $Users->email =  $request->matricule."@mail.com";
            $Users->password =  "1234";
            $Users->save();
            if ($Users) 
            {
                return redirect('list-users')->with('success', 'New user created!');
            }

            return redirect('list-users')->with('error', 'Failed to create new user! Try again.');
        } 
        catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }
    public function editetat(Request $request): RedirectResponse
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
            if ($User = User::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'etat' => $request->etat,
                    'updated_at' => $currentDateTime,
                ];
                $update = $User->update($payload);
                return redirect()->back()->with('success', 'User information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update User! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    public function newuserupdate(Request $request): RedirectResponse
    {
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'user1d' => 'required',
            'id' => 'required',
            'name' => 'required',
            'matricule' => 'required',
            'contact' => 'required',
            'id_categorie' => 'required',
            'id_grade' => 'required',
        ]);
        if ($validator->fails())
        {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }
        try
        {
            if ($User = User::find($request->id))
            {
                $payload = [
                    'update_user' => $request->user1d,
                    'name' => $request->name,
                    'matricule' => $request->matricule,
                    'contact' => $request->contact,
                    'id_categorie' => $request->id_categorie,
                    'id_grade' => $request->id_grade,
                    'updated_at' => $currentDateTime,
                ];
                $update = $User->update($payload);
                return redirect()->back()->with('success', 'User information updated succesfully!');
            }
            return redirect()->back()->with('error', 'Failed to update User ! Try again.');
        }
        catch (\Exception $e)
        {
            $bug = $e->getMessage();
            return redirect()->back()->with('error', $bug);
        }
    }
    /**
     * Edit User
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            $user = User::with('roles', 'permissions')->find($id);

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
     * Update User
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        // update user info
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required | string ',
            'email' => 'required | email',
            'role' => 'required',
        ]);

        // check validation for password match
        if (isset($request->password)) {
            $validator = Validator::make($request->all(), [
                'password' => 'required | confirmed',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('error', $validator->messages()->first());
        }

        try {
            if ($user = User::find($request->id)) {
                $payload = [
                    'name' => $request->name,
                    'email' => $request->email,
                ];
                // update password if user input a new password
                if (isset($request->password) && $request->password) {
                    $payload['password'] = $request->password;
                }

                $update = $user->update($payload);
                // sync user role
                $user->syncRoles($request->role);

                return redirect()->back()->with('success', 'User information updated succesfully!');
            }

            return redirect()->back()->with('error', 'Failed to update user! Try again.');
        } catch (\Exception $e) {
            $bug = $e->getMessage();

            return redirect()->back()->with('error', $bug);
        }
    }

    /**
     * Delete User
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        if ($user = User::find($id)) {
            $user->delete();

            return redirect('users')->with('success', 'User removed!');
        }

        return redirect('users')->with('error', 'User not found');
    }
}
