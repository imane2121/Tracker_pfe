<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WasteTypesSeeder extends Seeder
{
    public function run()
    {
        // General Waste Types from cahier des charges
        $generalWasteTypes = [
            'Plastiques',
            'Métaux',
            'Verre',
            'Déchets dangereux',
            'Matériel de pêche abandonné',
            'Déchets de bois',
            'Déchets organiques',
            'Textiles'
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
            'Plastiques' => [
                'Bouteilles en plastique',
                'Emballages alimentaires',
                'Sachets et sacs en plastique',
                'Microplastiques'
            ],
            'Métaux' => [
                'Canettes et boîtes métalliques',
                'Fragments de ferraille',
                'Matériaux de construction'
            ],
            'Verre' => [
                'Bouteilles en verre',
                'Fragments de verre cassé'
            ],
            'Déchets dangereux' => [
                'Piles et batteries',
                'Huiles usagées',
                'Contenants de produits chimiques'
            ],
            'Matériel de pêche abandonné' => [
                'Filets de pêche perdus',
                'Cordes et lignes de pêche',
                'Hameçons et plombs'
            ],
            'Déchets de bois' => [
                'Bois traité',
                'Bois naturel',
                'Objets en bois'
            ],
            'Déchets organiques' => [
                'Restes alimentaires',
                'Algues',
                'Débris végétaux'
            ],
            'Textiles' => [
                'Vêtements',
                'Morceaux de tissu',
                'Articles textiles'
            ]
        ];

        return $specificWasteTypes[$generalWasteType] ?? [];
    }
}
