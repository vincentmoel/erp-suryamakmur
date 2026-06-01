<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereIn('id', [1, 2])->get();
        foreach($users as $user)
        {
            $user->roles()->attach(1);
        }

        $users = User::whereIn('id', [3])->get();
        foreach($users as $user)
        {
            $user->roles()->attach(2);
        }

        $users = User::whereIn('id', [4, 5, 6])->get();
        foreach($users as $user)
        {
            $user->roles()->attach(3);
        }
    }
}