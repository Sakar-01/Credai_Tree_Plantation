<?php

namespace Database\Seeders;

use App\Models\Tree;
use App\Models\User;
use App\Models\Inspection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TreeSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $volunteer = User::where('role', 'volunteer')->first();
        
        if (!$volunteer) {
            echo "No volunteer user found. Please run AdminUserSeeder first.\n";
            return;
        }

        // Jalgaon area coordinates with some variation
        $jalgaonTrees = [
            ['lat' => 21.0077, 'lng' => 75.5626, 'species' => 'Mango', 'location' => 'Jalgaon City Center', 'status' => 'healthy'],
            ['lat' => 21.0123, 'lng' => 75.5689, 'species' => 'Neem', 'location' => 'Jalgaon North', 'status' => 'healthy'],
            ['lat' => 21.0034, 'lng' => 75.5578, 'species' => 'Banyan', 'location' => 'Jalgaon South', 'status' => 'needs_attention'],
            ['lat' => 21.0089, 'lng' => 75.5734, 'species' => 'Peepal', 'location' => 'Jalgaon East', 'status' => 'under_inspection'],
            ['lat' => 21.0156, 'lng' => 75.5534, 'species' => 'Gulmohar', 'location' => 'Jalgaon West', 'status' => 'planted'],
            ['lat' => 21.0198, 'lng' => 75.5612, 'species' => 'Coconut Palm', 'location' => 'Jalgaon Highway', 'status' => 'healthy'],
            ['lat' => 21.0001, 'lng' => 75.5698, 'species' => 'Teak', 'location' => 'Jalgaon Industrial Area', 'status' => 'healthy'],
            ['lat' => 21.0167, 'lng' => 75.5445, 'species' => 'Jamun', 'location' => 'Jalgaon Residential', 'status' => 'needs_attention'],
            ['lat' => 21.0045, 'lng' => 75.5789, 'species' => 'Ashoka', 'location' => 'Jalgaon Market Area', 'status' => 'under_inspection'],
            ['lat' => 21.0134, 'lng' => 75.5501, 'species' => 'Flame Tree', 'location' => 'Jalgaon College Area', 'status' => 'planted'],
        ];

        foreach ($jalgaonTrees as $treeData) {
            $tree = Tree::create([
                'tree_id' => 'TREE-' . strtoupper(Str::random(8)),
                'species' => $treeData['species'],
                'location_description' => $treeData['location'],
                'latitude' => $treeData['lat'] + (rand(-50, 50) * 0.0001), // Add small random variation
                'longitude' => $treeData['lng'] + (rand(-50, 50) * 0.0001),
                'plantation_date' => now()->subDays(rand(30, 365)),
                'next_inspection_date' => now()->addDays(rand(7, 60)),
                'photo_path' => 'tree-photos/sample.jpg', // You can add a sample image
                'description' => 'Sample tree planted as part of Jalgaon Green Initiative',
                'planted_by' => $volunteer->id,
                'status' => $treeData['status'],
            ]);

            // Add some sample inspections for older trees
            if (rand(1, 3) === 1) { // 33% chance of having an inspection
                Inspection::create([
                    'tree_id' => $tree->id,
                    'inspection_date' => now()->subDays(rand(5, 30)),
                    'photo_path' => 'inspection-photos/sample.jpg',
                    'latitude' => $tree->latitude,
                    'longitude' => $tree->longitude,
                    'tree_height_cm' => rand(50, 200),
                    'tree_health' => ['good', 'average', 'poor'][rand(0, 2)],
                    'observation_notes' => 'Sample inspection notes for tree monitoring',
                    'next_inspection_date' => now()->addDays(rand(14, 45)),
                    'inspected_by' => $volunteer->id,
                ]);
            }
        }

        echo "Created " . count($jalgaonTrees) . " sample trees in Jalgaon area.\n";
    }
}
