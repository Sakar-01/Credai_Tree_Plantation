<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'NGO Admin',
            'email' => 'admin@treeplantation.org',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'assigned_region' => 'All Regions',
        ]);

        User::create([
            'name' => 'John Volunteer',
            'email' => 'volunteer@treeplantation.org',
            'password' => Hash::make('password123'),
            'role' => 'volunteer',
            'phone' => '+1234567891',
            'assigned_region' => 'District A',
        ]);
    }
}
