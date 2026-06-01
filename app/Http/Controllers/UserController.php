<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Helpers\Encryption;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;

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
        $user = User::create($request->validated());

        foreach($request->roles as $role){
            $user->roles()->attach($role);
        }

        return redirect('users')->with([
            'success'   => ["title" => "Success Add", "message" => "Your data has been saved."]
        ]);
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
            "user"  => $user,
            "roles" => $roles
        ]);
    }

    public function update(UserRequest $request, $encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        $user->update($request->validated());

        $user->roles()->detach();

        foreach($request->roles ?? [] as $role){
            $user->roles()->attach($role);
        }

        return redirect('users')->with([
            'success'   => ["title" => "Success Update", "message" => "Your data has been updated."]
        ]);
    }

    public function destroy($encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        if($user->id == auth()->user()->id)
        {
            return redirect()->back()->with([
                'error'   => [ "code"  => 403, "message" => "You cannot delete your own account."]
            ]);
        }
        $user->delete();

        return redirect()->back()->with([
            'success'   => ["title" => "Success Delete", "message" => "Your data has been deleted."]
        ]);
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
