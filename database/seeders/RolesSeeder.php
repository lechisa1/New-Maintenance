<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'ict_director']);
        Role::firstOrCreate(['name' => 'technician']);
        Role::firstOrCreate(['name' => 'employee']);
        Role::firstOrCreate(['name' => 'admin']);
    }
}
