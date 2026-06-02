<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Helpers\Encryption;
use App\Helpers\FileManager;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function create()
    {
        $roles = Role::get();

        return view('users.create', [
            'roles' => $roles
        ]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = FileManager::store($request->file('photo'), 'users');
        }

        $user = User::create($data);

        foreach($request->roles as $role){
            $user->roles()->attach($role);
        }

        $flash = ['success' => ["title" => "Success Add", "message" => "Your data has been saved."]];

        if ($request->input('_action') === 'save_and_create') {
            return redirect()->route('users.create')->with($flash);
        }

        return redirect()->route('users.index')->with($flash);
    }

    public function show($encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        return view('users.show', [
            "user" => $user
        ]);
    }

    public function edit($encryptedId)
    {
        $user = User::with('roles')->findOrFail(Encryption::decrypt($encryptedId));
        $roles = Role::get();

        return view('users.edit', [
            "user"         => $user,
            "roles"        => $roles,
            "encryptedId"  => $encryptedId,
        ]);
    }

    public function update(UserRequest $request, $encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password'], $data['password_confirmation']);
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = FileManager::store($request->file('photo'), 'users');
        } else {
            unset($data['photo']);
        }

        $user->update($data);

        $user->roles()->detach();

        foreach($request->roles ?? [] as $role){
            $user->roles()->attach($role);
        }

        return redirect('users')->with([
            'success'   => ["title" => "Success Update", "message" => "Your data has been updated."]
        ]);
    }

    public function destroy(Request $request, $encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        if ($user->id == auth()->user()->id) {
            $error = ['code' => 403, 'message' => 'You cannot delete your own account.'];

            if ($request->ajax()) {
                return response()->json(['error' => $error], 403);
            }

            return redirect()->back()->with(['error' => $error]);
        }

        $user->delete();

        $success = ['title' => 'Success Delete', 'message' => 'Your data has been deleted.'];

        if ($request->ajax()) {
            return response()->json(['success' => $success]);
        }

        return redirect()->back()->with(['success' => $success]);
    }

    public function trashed()
    {
        $dataTable = new UsersDataTable(true);

        return $dataTable->render('users.index');
    }

    public function restore($encryptedId)
    {
        $user = User::onlyTrashed()->findOrFail(Encryption::decrypt($encryptedId));
        $user->restore();
        
        return redirect()->back()->with([
            'success'   => ["title" => "Success Restore", "message" => "Your data has been restored."]
        ]);
    }
}
