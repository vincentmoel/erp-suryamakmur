<?php

namespace App\Http\Controllers;

use App\DataTables\UsersDataTable;
use App\Enums\Module;
use App\Helpers\Encryption;
use App\Helpers\FileManager;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct(
            User::class,
            'users',
            'User',
            'users',
            Module::User->name,
            UserRequest::class,
            UsersDataTable::class,
        );
    }

    public function index()
    {
        $dataTable = new UsersDataTable(false);

        return $dataTable->render('users.index', [
            'title'  => $this->title,
            'route'  => $this->route,
            'module' => $this->module,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'title'  => $this->title,
            'route'  => $this->route,
            'roles'  => Role::get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = app(UserRequest::class)->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = FileManager::store($request->file('photo'), 'users');
        }

        $user = User::create($data);

        foreach ($request->roles ?? [] as $role) {
            $user->roles()->attach($role);
        }

        $flash = ['success' => ['title' => 'Success Add', 'message' => 'Your data has been saved.']];

        if ($request->input('_action') === 'save_and_create') {
            return redirect()->route('users.create')->with($flash);
        }

        return redirect()->route('users.index')->with($flash);
    }

    public function show($encryptedId)
    {
        $user = User::with('roles', 'user_created_by')->findOrFail(Encryption::decrypt($encryptedId));

        return view('users.show', [
            'title'       => $this->title,
            'route'       => $this->route,
            'user'        => $user,
            'encryptedId' => $encryptedId,
        ]);
    }

    public function edit($encryptedId)
    {
        $user = User::with('roles')->findOrFail(Encryption::decrypt($encryptedId));

        return view('users.edit', [
            'title'       => $this->title,
            'route'       => $this->route,
            'user'        => $user,
            'roles'       => Role::get(),
            'encryptedId' => $encryptedId,
        ]);
    }

    public function update(Request $request, $encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        $data = app(UserRequest::class)->validated();

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

        foreach ($request->roles ?? [] as $role) {
            $user->roles()->attach($role);
        }

        return redirect()->route('users.index')->with([
            'success' => ['title' => 'Success Update', 'message' => 'Your data has been updated.'],
        ]);
    }

    public function destroy($encryptedId)
    {
        $user = User::findOrFail(Encryption::decrypt($encryptedId));

        if ($user->id == auth()->user()->id) {
            $error = ['code' => 403, 'message' => 'You cannot delete your own account.'];

            if (request()->ajax()) {
                return response()->json(['error' => $error], 403);
            }

            return redirect()->back()->with(['error' => $error]);
        }

        $user->delete();

        $success = ['title' => 'Success Delete', 'message' => 'Your data has been deleted.'];

        if (request()->ajax()) {
            return response()->json(['success' => $success]);
        }

        return redirect()->back()->with(['success' => $success]);
    }

    public function trashed()
    {
        $dataTable = new UsersDataTable(true);

        return $dataTable->render('users.index', [
            'title'  => $this->title,
            'route'  => $this->route,
            'module' => $this->module,
        ]);
    }
}
