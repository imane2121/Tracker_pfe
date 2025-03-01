<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteTypesSeeder extends Seeder
{
    public function run()
    {
        // General Waste Types
        $generalWasteTypes = [
            'Plastic',
            'Glass',
            'Organic',
            'Metal',
            'Paper',
            'Electronic'
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
            'Plastic' => ['Plastic Bottle', 'Plastic Bag', 'Plastic Wrapper'],
            'Glass' => ['Glass Bottle', 'Glass Jar'],
            'Organic' => ['Food Waste', 'Plant Waste'],
            'Metal' => ['Aluminum Can', 'Iron Scrap'],
            'Paper' => ['Newspaper', 'Cardboard', 'Magazine'],
            'Electronic' => ['Phone', 'TV', 'Laptop']
        ];

        return $specificWasteTypes[$generalWasteType] ?? [];
    }
}
