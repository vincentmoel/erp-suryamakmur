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
        return view('roles.create', [
            'modules' => $this->sortedModules(),
        ]);
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create(['name' => $request->name]);

        $this->syncPermissions($role, $request->input('permissions', []));

        return redirect('roles')->with([
            'success' => ["title" => "Success Add", "message" => "Role has been created."]
        ]);
    }

    public function show($encryptedId)
    {
        $role = Role::with(['permissions', 'users.user_created_by', 'users.user_updated_by'])
            ->findOrFail(Encryption::decrypt($encryptedId));

        if ($role->editable == false) {
            return redirect('roles')->with([
                'error' => ["code" => 403, "message" => "You cannot view this role."]
            ]);
        }

        return view('roles.show', [
            'role'        => $role,
            'encryptedId' => $encryptedId,
            'modules'     => $this->sortedModules(),
            'granted'     => $role->permissionsGrouped(),
        ]);
    }

    public function edit($encryptedId)
    {
        $role = Role::with('permissions')->findOrFail(Encryption::decrypt($encryptedId));

        if ($role->editable == false) {
            return redirect('roles')->with([
                'error' => ["code" => 403, "message" => "You cannot edit this role."]
            ]);
        }

        return view('roles.edit', [
            'role'        => $role,
            'encryptedId' => $encryptedId,
            'modules'     => $this->sortedModules(),
            'granted'     => $role->permissionsGrouped(),
        ]);
    }

    public function update(RoleRequest $request, $encryptedId)
    {
        $role = Role::findOrFail(Encryption::decrypt($encryptedId));

        if ($role->editable == false) {
            return redirect('roles')->with([
                'error' => ["code" => 403, "message" => "You cannot edit this role."]
            ]);
        }

        $role->update($request->validated());

        $this->syncPermissions($role, $request->input('permissions', []));

        return redirect('roles')->with([
            'success' => ["title" => "Success Update", "message" => "Role has been updated."]
        ]);
    }

    public function destroy($encryptedId)
    {
        $role = Role::findOrFail(Encryption::decrypt($encryptedId));

        if ($role->deletable == false) {
            return response()->json([
                'error' => ["code" => 403, "message" => "You cannot delete this role."]
            ], 403);
        }

        $role->delete();

        return response()->json([
            'data' => ["title" => "Success Delete", "message" => "Role has been deleted."]
        ]);
    }

    public function deleteUserRole($encryptedRoleId, $encryptedUserId)
    {
        $role = Role::findOrFail(Encryption::decrypt($encryptedRoleId));
        $role->users()->detach(Encryption::decrypt($encryptedUserId));

        return redirect()->back()->with([
            'success' => ["title" => "Success Delete", "message" => "User removed from role."]
        ]);
    }

    private function sortedModules(): array
    {
        return Module::cases();
    }

    private function syncPermissions(Role $role, array $incoming): void
    {
        $role->permissions()->delete();

        $rows = [];
        foreach (Module::cases() as $module) {
            $moduleKey = $module->value;
            $allowed   = $module->permissions();

            foreach ($allowed as $action) {
                if (!empty($incoming[$moduleKey][$action])) {
                    $rows[] = ['role_id' => $role->id, 'module' => $moduleKey, 'action' => $action];
                }
            }
        }

        if (!empty($rows)) {
            Permission::insert($rows);
        }
    }
}