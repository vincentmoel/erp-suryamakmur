<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'      => "System",
            'username'  => 'system',
            'password'  => 'vincentnathaniel'
        ]);

        User::create([
            'name'      => "Vincent Nathaniel Moeljopranoto",
            'username'  => 'vincent',
            'password'  => 'SMATheresiana01'
        ]);

        User::create([
            'name'      => "Giovano Billy",
            'username'  => 'billy',
            'password'  => 'Street01'
        ]);

        User::create([
            'name'      => "Edo",
            'username'  => 'edo',
            'password'  => 'q'
        ]);

        User::create([
            'name'      => "Louis",
            'username'  => 'louis',
            'password'  => 'q'
        ]);

        User::create([
            'name'      => "Daffa",
            'username'  => 'daffa',
            'password'  => 'q'
        ]);
    }
}
