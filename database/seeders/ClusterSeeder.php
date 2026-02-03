<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Models\Cluster;
use App\Models\Division;

class ClusterSeeder extends Seeder
{
    public function run(): void
    {
        // Get the organization we seeded earlier
        $organization = Organization::where('name', 'Ethiopian Artificial Intelligence (AI)')->first();

        if (!$organization) {
            $this->command->error('Organization not found. Please seed organizations first.');
            return;
        }

        // Define clusters and their corresponding division
        $clusters = [
            [
                'name' => 'Research and Development Cluster',
                'division' => 'Machine Learning Division',
            ],
            [
                'name' => 'IOT Cluster',
                'division' => 'Robotics Division',
            ],
            [
                'name' => 'Finance Cluster',
                'division' => 'Finance Division',
            ],
        ];

        foreach ($clusters as $clusterData) {
            // Create cluster
            $cluster = Cluster::create([
                'name' => $clusterData['name'],
                'organization_id' => $organization->id,
                'cluster_chairman' => null, // no chairman for now
            ]);

            // Create corresponding division
            Division::create([
                'name' => $clusterData['division'],
                'cluster_id' => $cluster->id,
                'division_chairman' => null, // no chairman for now
            ]);
        }

        $this->command->info('Clusters and divisions seeded successfully!');
    }
}
