<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $director = User::create([
            'full_name' => 'ICT Director',
            'email' => 'director@example.com',
            'password' => Hash::make('password123'),
            'phone' => '1234567890',
        ]);

        $tech1 = User::create([
            'full_name' => 'Technician 1',
            'email' => 'tech1@example.com',
            'password' => Hash::make('password123'),
            'phone' => '2345678901',
        ]);

        $tech2 = User::create([
            'full_name' => 'Technician 2',
            'email' => 'tech2@example.com',
            'password' => Hash::make('password123'),
            'phone' => '3456789012',
        ]);

        // Roles will be assigned after roles are seeded
        // $director->assignRole('ict_director');
        // $tech1->assignRole('technician');
        // $tech2->assignRole('technician');
    }
}
