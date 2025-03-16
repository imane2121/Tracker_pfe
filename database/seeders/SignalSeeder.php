<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Models\Signal;
use App\Models\WasteTypes;
use App\Models\User;

class SignalSeeder extends Seeder
{
    private $testLocations = [
        // Casablanca coastal areas
        [
            'name' => 'Ain Diab Beach',
            'base_lat' => 33.5992,
            'base_lng' => -7.6970,
            'signals' => 4,
            'volume_range' => [50, 150]
        ],
        [
            'name' => 'Corniche Beach',
            'base_lat' => 33.6041,
            'base_lng' => -7.7084,
            'signals' => 3,
            'volume_range' => [30, 100]
        ],
        // Rabat area
        [
            'name' => 'Rabat Beach',
            'base_lat' => 34.0332,
            'base_lng' => -6.8334,
            'signals' => 5,
            'volume_range' => [80, 200]
        ],
        // Tangier area
        [
            'name' => 'Tangier Bay',
            'base_lat' => 35.7595,
            'base_lng' => -5.8330,
            'signals' => 3,
            'volume_range' => [40, 120]
        ],
        // Agadir area
        [
            'name' => 'Agadir Beach',
            'base_lat' => 30.4202,
            'base_lng' => -9.6026,
            'signals' => 4,
            'volume_range' => [60, 180]
        ]
    ];

    public function run()
    {
        // Get users first and validate
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Get waste types and validate
        $wasteTypes = WasteTypes::all();
        if ($wasteTypes->isEmpty()) {
            $this->command->error('No waste types found. Please run WasteTypesSeeder first.');
            return;
        }

        $faker = Faker::create();
        
        // Create random signals
        $this->command->info('Creating random signals...');
        
        for ($i = 1; $i <= 50; $i++) {
            // Random user from the collection
            $createdBy = $users->random()->id;
            $randomWasteTypes = $wasteTypes->random(rand(1, 3))->pluck('id')->toArray();
            $volume = $faker->numberBetween(1, 100);
            $location = $faker->city;
            $customType = $faker->word;
            $latitude = $faker->latitude;
            $longitude = $faker->longitude;
            $anomalyFlag = $faker->boolean(30);
            $signalDate = $faker->dateTimeThisYear;

            // Insert signal data
            $signalId = DB::table('signals')->insertGetId([
                'created_by' => $createdBy,
                'volume' => $volume,
                'waste_types' => json_encode($randomWasteTypes),
                'location' => $location,
                'custom_type' => $customType,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'anomaly_flag' => $anomalyFlag,
                'signal_date' => $signalDate,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Insert associated waste types
            foreach ($randomWasteTypes as $wasteTypeId) {
                DB::table('signal_waste_types')->insert([
                    'signal_id' => $signalId,
                    'waste_type_id' => $wasteTypeId,
                ]);
            }

            // Add random media files
            $mediaFiles = ['photo', 'video'];
            $numMedia = rand(2, 3);

            for ($j = 0; $j < $numMedia; $j++) {
                $mediaType = $faker->randomElement($mediaFiles);
                $filePath = 'uploads/' . $mediaType . '/' . $faker->uuid . '.' . ($mediaType == 'photo' ? 'jpg' : 'mp4');

                DB::table('signal_media')->insert([
                    'signal_id' => $signalId,
                    'media_type' => $mediaType,
                    'file_path' => $filePath,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // Create clustered test signals
        $this->command->info('Creating signals for test locations...');

        foreach ($this->testLocations as $location) {
            $this->command->info("Creating cluster for {$location['name']}...");

            // Create multiple signals for each location with slight coordinate variations
            for ($i = 0; $i < $location['signals']; $i++) {
                // Add small random variations to coordinates (within ~500m)
                $latVariation = (rand(-50, 50) / 10000);
                $lngVariation = (rand(-50, 50) / 10000);

                $signal = Signal::create([
                    'created_by' => $users->random()->id,
                    'location' => $location['name'],
                    'latitude' => $location['base_lat'] + $latVariation,
                    'longitude' => $location['base_lng'] + $lngVariation,
                    'volume' => rand($location['volume_range'][0], $location['volume_range'][1]),
                    'status' => rand(1, 10) > 3 ? 'validated' : 'pending',
                    'signal_date' => Carbon::now()->subDays(rand(1, 20)),
                    'description' => "Waste accumulation reported at {$location['name']}",
                    'waste_types' => $wasteTypes->random(rand(2, 4))->pluck('id')->toArray()
                ]);

                // Attach waste types
                $signal->wasteTypes()->attach(
                    $wasteTypes->random(rand(2, 4))->pluck('id')->toArray()
                );
            }
        }

        // Create additional random signals in Morocco
        $this->command->info('Creating additional random signals in Morocco...');
        
        for ($i = 0; $i < 10; $i++) {
            $signal = Signal::create([
                'created_by' => $users->random()->id,
                'location' => "Random Location " . ($i + 1),
                'latitude' => rand(2750, 3550) / 100,
                'longitude' => rand(-1100, -500) / 100,
                'volume' => rand(20, 100),
                'status' => rand(1, 10) > 3 ? 'validated' : 'pending',
                'signal_date' => Carbon::now()->subDays(rand(1, 30)),
                'description' => "Random waste accumulation site",
                'waste_types' => $wasteTypes->random(rand(2, 4))->pluck('id')->toArray()
            ]);

            $signal->wasteTypes()->attach(
                $wasteTypes->random(rand(2, 4))->pluck('id')->toArray()
            );
        }

        $this->command->info('Signal seeding completed!');
    }
}
