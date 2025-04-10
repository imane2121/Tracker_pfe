<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteTypesSeeder extends Seeder
{
    public function run()
    {
        // General Waste Types aligned with AI detection categories
        $generalWasteTypes = [
            'Plastic',
            'Metal',
            'Glass',
            'Hazardous Waste',
            'Fishing Equipment',
            'Wood',
            'Organic Waste',
            'Textile'
        ];

        // Insert General Waste Types
        foreach ($generalWasteTypes as $waste) {
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

    // Helper method to return specific waste types based on the general type
    private function getSpecificWasteTypes($generalWasteType)
    {
        $specificWasteTypes = [
            'Plastic' => [
                'Plastic Bottles',
                'Food Packaging',
                'Plastic Bags and Sachets',
                'Microplastics'
            ],
            'Metal' => [
                'Cans and Metal Containers',
                'Metal Fragments',
                'Construction Materials'
            ],
            'Glass' => [
                'Glass Bottles',
                'Broken Glass'
            ],
            'Hazardous Waste' => [
                'Batteries',
                'Used Oil',
                'Chemical Containers'
            ],
            'Fishing Equipment' => [
                'Fishing Nets',
                'Ropes and Fishing Lines',
                'Hooks and Weights'
            ],
            'Wood' => [
                'Treated Wood',
                'Natural Wood',
                'Wooden Objects'
            ],
            'Organic Waste' => [
                'Food Waste',
                'Algae',
                'Plant Debris'
            ],
            'Textile' => [
                'Clothing',
                'Fabric Pieces',
                'Textile Items'
            ]
        ];

        return $specificWasteTypes[$generalWasteType] ?? [];
    }
}
