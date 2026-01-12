<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $organizations = [
            ['id' => Str::uuid(), 'name' => 'ICT Department'],
            ['id' => Str::uuid(), 'name' => 'Human Resources'],
            ['id' => Str::uuid(), 'name' => 'Finance Department'],
            ['id' => Str::uuid(), 'name' => 'Maintenance & Support'],
            ['id' => Str::uuid(), 'name' => 'Operations'],
        ];

        DB::table('organizations')->insert($organizations);

        $this->command->info('Organizations seeded successfully!');
    }
}
