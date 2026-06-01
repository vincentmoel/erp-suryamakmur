<?php

namespace App\Http\Controllers;

use App\DataTables\RoleDataTable;
use App\Enums\Module;
use App\Helpers\Encryption;
use App\Http\Requests\RoleRequest;
use App\Models\Permission;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(RoleDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    public function create()
    {
        $modules = Module::array();

        return view('roles.create', [
            'modules' => $modules
        ]);
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name'  => $request->name 
        ]);

        $modules = Module::array();

        $allPermissions = [];

        foreach($modules as $moduleKey => $moduleValue){
            $permission['role_id']  = $role->id;
            $permission['module']   = $moduleKey;
            $permission['menu']     = isset($request->permission[$moduleKey]['menu']) ? 1 : 0;
            $permission['create']   = isset($request->permission[$moduleKey]['create']) ? 1 : 0;
            $permission['read']     = isset($request->permission[$moduleKey]['read']) ? 1 : 0;
            $permission['update']   = isset($request->permission[$moduleKey]['update']) ? 1 : 0;
            $permission['delete']   = isset($request->permission[$moduleKey]['delete']) ? 1 : 0;
            $permission['restore']  = isset($request->permission[$moduleKey]['restore']) ? 1 : 0;

            array_push($allPermissions, $permission);
        }

        Permission::insert($allPermissions);

        return redirect('roles')->with([
            'success'   => ["title" => "Success Add", "message" => "Your data has been saved."]
        ]);
    }

    public function show($encryptedId)
    {
        $role = Role::with(['users.user_created_by', 'users.user_updated_by'])->findOrFail(Encryption::decrypt($encryptedId));

        if($role->editable == false)
        {
            return redirect('roles')->with([
                'error'   => [ "code"  => 403, "message" => "You cannot see this role."]
            ]);
        }
        
        $modules = Module::values();
        
        $permissions = $role->permissions;

        $resultPermissionsArray = [];

        foreach ($permissions as $innerArray) {
            $module = $innerArray["module"];
            $resultPermissionsArray[$module] = $innerArray;
        }

        return view('roles.show', [
            "role"          => $role,
            "modules"       => $modules,
            "permissions"   => $resultPermissionsArray
        ]);
    }

    public function edit($encryptedId)
    {
        $role = Role::with('permissions')->findOrFail(Encryption::decrypt($encryptedId));

        if($role->editable == false)
        {
            return redirect('roles')->with([
                'error'   => [ "code"  => 403, "message" => "You cannot edit this role."]
            ]);
        }
        
        $modules = Module::array();

        return view('roles.edit',[
            "role"      => $role,
            "modules"   => $modules
        ]);
    }

    public function update(RoleRequest $request, $encryptedId)
    {
        $role = Role::findOrFail(Encryption::decrypt($encryptedId));

        if($role->editable == false)
        {
            return redirect('roles')->with([
                'error'   => [ "code"  => 403, "message" => "You cannot edit this role."]
            ]);
        }

        $role->update($request->validated());

        $modules = Module::array();

        foreach($modules as $moduleKey => $moduleValue){
            Permission::updateOrCreate(
                ['role_id' => $role->id, 'module' => $moduleKey],
                [
                    'menu'      => isset($request->permission[$moduleKey]['menu']) ? 1 : 0,
                    'create'    => isset($request->permission[$moduleKey]['create']) ? 1 : 0,
                    'read'      => isset($request->permission[$moduleKey]['read']) ? 1 : 0,
                    'update'    => isset($request->permission[$moduleKey]['update']) ? 1 : 0,
                    'delete'    => isset($request->permission[$moduleKey]['delete']) ? 1 : 0,
                    'restore'   => isset($request->permission[$moduleKey]['restore']) ? 1 : 0,
                ]
            );
        }

        return redirect('roles')->with([
            'success'   => ["title" => "Success Update", "message" => "Your data has been updated."]
        ]);
    }

    public function destroy($encryptedId)
    {
        $role = Role::findOrFail(Encryption::decrypt($encryptedId));

        if($role->deletable == false)
        {
            return redirect('roles')->with([
                'error'   => [ "code"  => 403, "message" => "You cannot delete this role."]
            ]);
        }

        $role->delete();

        return redirect()->back()->with([
            'success'   => ["title" => "Success Delete", "message" => "Your data has been deleted."]
        ]);
    }

    public function deleteUserRole($encryptedRoleId, $encryptedUserId)
    {
        $role = Role::findOrFail(Encryption::decrypt($encryptedRoleId));
        
        $role->users()->detach(Encryption::decrypt($encryptedUserId));

        return redirect()->back()->with([
            'success'   => ["title" => "Success Delete", "message" => "Your data has been deleted."]
        ]);
    }
}