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
            ['id' => Str::uuid(), 'name' => 'Ethiopian Artificial Intelligence (AI)'],

        ];

        DB::table('organizations')->insert($organizations);

        $this->command->info('Organizations seeded successfully!');
    }
}
