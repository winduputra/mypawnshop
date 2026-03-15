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
        User::factory()->create([
            'name' => 'Admin MyPawnShop',
            'email' => 'admin@mypawnshop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Kasir MyPawnShop',
            'email' => 'kasir@mypawnshop.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
