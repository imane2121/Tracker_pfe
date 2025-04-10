<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarineWasteTypesSeeder extends Seeder
{
    public function run()
    {
        // Marine Mammals and Oil-related waste types
        $marineWasteTypes = [
            'Marine Mammals',
            'Oil and Fuel',
            'Chemical Spills'
        ];

        // Insert General Waste Types
        foreach ($marineWasteTypes as $waste) {
            $parentId = DB::table('waste_types')->insertGetId([
                'name' => $waste,
                'type' => 'general',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Specific Waste Types
            $specificWasteTypes = $this->getSpecificWasteTypes($waste);

            // Insert Specific Waste Types
            foreach ($specificWasteTypes as $specificWaste) {
                DB::table('waste_types')->insert([
                    'name' => $specificWaste,
                    'type' => 'specific',
                    'parent_id' => $parentId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function getSpecificWasteTypes($marineWasteType)
    {
        $specificWasteTypes = [
            'Marine Mammals' => [
                'Whale Carcass',
                'Dolphin Carcass',
                'Orca Carcass',
                'Seal Carcass',
                'Sea Lion Carcass',
                'Porpoise Carcass',
                'Unidentified Marine Mammal Remains'
            ],
            'Oil and Fuel' => [
                'Crude Oil Spill',
                'Fuel Oil Traces',
                'Diesel Spill',
                'Oil Sheen on Water',
                'Tar Balls',
                'Oil-Contaminated Debris',
                'Petroleum Product Residue'
            ],
            'Chemical Spills' => [
                'Industrial Chemical Discharge',
                'Agricultural Runoff',
                'Unknown Chemical Substance',
                'Toxic Algae Bloom',
                'Chemical Foam',
                'Discolored Water'
            ]
        ];

        return $specificWasteTypes[$marineWasteType] ?? [];
    }
} 