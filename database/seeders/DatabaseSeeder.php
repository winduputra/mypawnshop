<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin MyPawnShop', 'email' => 'admin@mypawnshop.com', 'role' => 'admin'],
            ['name' => 'Owner MyPawnShop', 'email' => 'owner@mypawnshop.com', 'role' => 'owner'],
            ['name' => 'Superadmin MyPawnShop', 'email' => 'superadmin@mypawnshop.com', 'role' => 'superadmin'],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                ]
            );
        }
    }
}
