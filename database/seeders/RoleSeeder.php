<?php

namespace Database\Seeders;

use App\Enums\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = ['menu', 'create', 'read', 'update', 'delete', 'restore'];

        $role = Role::create([
            'name'      => 'Developer',
            'editable'  => false,
            'deletable' => false,
            'hidden'    => true
        ]);

        $modules = Module::names();

        foreach ($modules as $module) {
            $permission = new Permission();
            $permission->role_id = $role->id;
            $permission->module = $module;

            foreach ($permissions as $field) {
                $permission->{$field} = true;
            }

            $permission->save();
        }
    }
}
