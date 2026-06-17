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
        $role = Role::create([
            'name'      => 'Developer',
            'editable'  => false,
            'deletable' => false,
            'hidden'    => true
        ]);

        $rows = [];
        foreach (Module::cases() as $module) {
            foreach ($module->permissions() as $action) {
                $rows[] = ['role_id' => $role->id, 'module' => $module->value, 'action' => $action];
            }
        }

        Permission::insert($rows);
    }
}
