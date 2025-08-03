<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tree;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class MigrateLocationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Migrating existing location data...');

        DB::transaction(function () {
            $distinctLocations = Tree::select('location_description', 'landmark')
                ->whereNotNull('location_description')
                ->groupBy('location_description', 'landmark')
                ->get();

            foreach ($distinctLocations as $locationData) {
                $location = Location::create([
                    'name' => $locationData->location_description,
                    'landmark' => $locationData->landmark,
                ]);

                Tree::where('location_description', $locationData->location_description)
                    ->where('landmark', $locationData->landmark ?: null)
                    ->update(['location_id' => $location->id]);

                $this->command->info("Created location: {$location->name}");
            }
        });

        $this->command->info('Location data migration completed!');
    }
}
