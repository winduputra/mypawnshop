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
            ['name' => "Admin Harmans Gadai Syariah", 'email' => 'admin@mypawnshop.com', 'role' => 'admin'],
            ['name' => "Owner Harmans Gadai Syariah", 'email' => 'owner@mypawnshop.com', 'role' => 'owner'],
            ['name' => "Superadmin Harmans Gadai Syariah", 'email' => 'superadmin@mypawnshop.com', 'role' => 'superadmin'],
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
