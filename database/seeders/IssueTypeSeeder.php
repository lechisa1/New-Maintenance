<?php

namespace Database\Seeders;
use App\Models\IssueType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IssueTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'name' => 'Hardware Issue',
                'description' => 'Problems related to physical computer equipment such as desktops, laptops, printers, or peripherals.',
            ],
            [
                'name' => 'Software Issue',
                'description' => 'Errors, crashes, or malfunctions in installed software applications or operating systems.',
            ],
            [
                'name' => 'Network / Connectivity',
                'description' => 'Internet, network access, or connectivity-related problems.',
            ],
            [
                'name' => 'Performance Problem',
                'description' => 'System slowness, freezing, or poor performance issues.',
            ],
            [
                'name' => 'New Setup / Installation',
                'description' => 'Requests for new device setup, software installation, or system configuration.',
            ],
            [
                'name' => 'Upgrade Request',
                'description' => 'Requests to upgrade hardware components or software versions.',
            ],
            [
                'name' => 'Other',
                'description' => 'Any issue that does not fall under the predefined categories.',
            ],
        ];

        foreach ($types as $type) {
            IssueType::firstOrCreate(
                ['slug' => Str::slug($type['name'])],
                [
                    'name'        => $type['name'],
                    'description' => $type['description'],
                ]
            );
        }
    }
}

