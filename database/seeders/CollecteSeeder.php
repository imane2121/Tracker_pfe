<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collecte;
use App\Models\User;
use App\Models\Signal;
use App\Models\WasteTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CollecteSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::table('collecte_contributor')->delete();
        DB::table('collectes')->delete();
        DB::table('signal_waste_types')->delete();
        DB::table('signals')->delete();

        // Get some users with admin or supervisor role for creators
        $creators = User::whereHas('roles', function($query) {
            $query->whereIn('title', ['admin', 'supervisor']);
        })->get();

        // If no creators found, use the first user as fallback
        if ($creators->isEmpty()) {
            $creators = User::take(1)->get();
        }

        if ($creators->isEmpty()) {
            throw new \Exception('No users found in the database. Please run UserSeeder first.');
        }

        echo "Found " . $creators->count() . " creators\n";
        foreach ($creators as $creator) {
            echo "Creator ID: {$creator->id}, Name: {$creator->name}\n";
        }

        // Use the first creator as a fallback
        $defaultCreator = $creators->first();
        echo "Using default creator ID: {$defaultCreator->id}\n";

        // Get waste types for signals
        $wasteTypes = WasteTypes::all();
        if ($wasteTypes->isEmpty()) {
            throw new \Exception('No waste types found. Please run WasteTypesSeeder first.');
        }

        // Moroccan beaches with accurate coordinates
        $beaches = [
            [
                'name' => 'Plage Ain Diab',
                'lat' => 33.6044,
                'lng' => -7.7089,
                'region' => 'Casablanca-Settat',
                'city' => 'Casablanca'
            ],
            [
                'name' => 'Plage Lalla Meryem',
                'lat' => 33.5989,
                'lng' => -7.7028,
                'region' => 'Casablanca-Settat',
                'city' => 'Casablanca'
            ],
            [
                'name' => 'Plage Municipale',
                'lat' => 35.7885,
                'lng' => -5.8126,
                'region' => 'Tanger-Tétouan-Al Hoceïma',
                'city' => 'Tangier'
            ],
            [
                'name' => 'Plage Taghazout',
                'lat' => 30.5451,
                'lng' => -9.7088,
                'region' => 'Souss-Massa',
                'city' => 'Agadir'
            ],
            [
                'name' => "Plage d'Agadir",
                'lat' => 30.4202,
                'lng' => -9.6024,
                'region' => 'Souss-Massa',
                'city' => 'Agadir'
            ],
            [
                'name' => 'Plage Essaouira',
                'lat' => 31.5130,
                'lng' => -9.7667,
                'region' => 'Marrakech-Safi',
                'city' => 'Essaouira'
            ],
            [
                'name' => 'Plage El Haouzia',
                'lat' => 33.2537,
                'lng' => -8.5214,
                'region' => 'Casablanca-Settat',
                'city' => 'El Jadida'
            ],
            [
                'name' => 'Plage Martil',
                'lat' => 35.6167,
                'lng' => -5.2667,
                'region' => 'Tanger-Tétouan-Al Hoceïma',
                'city' => 'Tetouan'
            ]
        ];

        $collectes = collect([]);
        $allSignals = collect([]);

        // First, create signals for each beach
        foreach ($beaches as $beach) {
            try {
                echo "\nProcessing beach: {$beach['name']}\n";
                
                // Create 5-10 signals per beach location with slight coordinate variations
                for ($i = 0; $i < rand(5, 10); $i++) {
                    $timestamp = now()->format('Y-m-d H:i:s');
                    
                    // Add small random variations to coordinates to simulate different spots
                    $latVariation = (rand(-100, 100) / 10000); // ±0.01 degree
                    $lngVariation = (rand(-100, 100) / 10000);
                    
                    $selectedWasteTypes = $wasteTypes->random(rand(2, 3));
                    $wasteTypeIds = $selectedWasteTypes->pluck('id')->toArray();
                    
                    $signalId = DB::table('signals')->insertGetId([
                        'location' => $beach['name'],
                        'description' => "Waste collection point at {$beach['name']}, {$beach['city']}",
                        'latitude' => $beach['lat'] + $latVariation,
                        'longitude' => $beach['lng'] + $lngVariation,
                        'volume' => rand(20, 150),
                        'created_by' => $defaultCreator->id,
                        'waste_types' => json_encode($wasteTypeIds),
                        'status' => 'validated',
                        'signal_date' => $timestamp,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);

                    $allSignals->push([
                        'id' => $signalId,
                        'lat' => $beach['lat'] + $latVariation,
                        'lng' => $beach['lng'] + $lngVariation,
                        'region' => $beach['region'],
                        'location' => $beach['name']
                    ]);

                    // Attach waste types to signal
                    foreach ($wasteTypeIds as $wasteTypeId) {
                        DB::table('signal_waste_types')->insert([
                            'signal_id' => $signalId,
                            'waste_type_id' => $wasteTypeId
                        ]);
                    }
                }

                // Create collectes based on grouped signals
                $beachSignals = $allSignals->where('region', $beach['region'])->values();
                
                // Create 2-3 collectes for each beach
                foreach(range(1, rand(2, 3)) as $index) {
                    // Select 5-8 random signals that are close to each other
                    $selectedSignals = $beachSignals->random(rand(5, 8));
                    $signalIds = $selectedSignals->pluck('id')->toArray();
                    
                    // Calculate average coordinates for the collecte
                    $avgLat = $selectedSignals->avg('lat');
                    $avgLng = $selectedSignals->avg('lng');
                    
                    $startingDate = Carbon::now()->addDays(rand(-30, 30));
                    $status = $this->getStatusBasedOnDate($startingDate);
                    
                    $collecteId = DB::table('collectes')->insertGetId([
                        'signal_ids' => json_encode($signalIds),
                        'user_id' => $defaultCreator->id,
                        'region' => $beach['region'],
                        'location' => $beach['name'],
                        'description' => "Beach cleanup event at {$beach['name']}. Join us in keeping our beaches clean!",
                        'latitude' => $avgLat,
                        'longitude' => $avgLng,
                        'nbrContributors' => rand(10, 30),
                        'current_contributors' => 0,
                        'status' => $status,
                        'starting_date' => $startingDate->format('Y-m-d H:i:s'),
                        'end_date' => $startingDate->copy()->addHours(rand(2, 8))->format('Y-m-d H:i:s'),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp
                    ]);

                    $collectes->push($collecteId);
                    echo "Created collecte with ID: {$collecteId} based on " . count($signalIds) . " signals\n";
                }
            } catch (\Exception $e) {
                echo "Error creating data for {$beach['name']}: " . $e->getMessage() . "\n";
                echo "Stack trace: " . $e->getTraceAsString() . "\n";
            }
        }

        // Store collectes in a temporary file for the ContributorSeeder
        file_put_contents(storage_path('collectes.json'), json_encode($collectes->toArray()));
        echo "\nSeeding completed. Created " . $collectes->count() . " collectes.\n";
    }

    private function getStatusBasedOnDate($startingDate): string
    {
        $now = Carbon::now();
        
        if ($startingDate->isFuture()) {
            return 'planned';
        } elseif ($startingDate->isPast() && $startingDate->copy()->addHours(8)->isFuture()) {
            return 'in_progress';
        } elseif (rand(0, 1)) {
            return 'completed';
        } else {
            return 'validated';
        }
    }
} 