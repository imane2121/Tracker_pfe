<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;
use App\Models\Signal;
use App\Models\WasteTypes;


class SignalSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        // Get all users (contributors, supervisors, admins)
        $users = DB::table('users')->pluck('id')->toArray();

        // Get all waste types
        $wasteTypes = DB::table('waste_types')->pluck('id')->toArray();

        // Create random signals
        for ($i = 1; $i <= 50; $i++) {
            // Random user (could be contributor, supervisor, or admin)
            $createdBy = $faker->randomElement($users);
            $randomWasteTypes = $faker->randomElements($wasteTypes, rand(1, 3));  // Randomly select 1-3 waste types
            $volume = $faker->numberBetween(1, 100);  // Random volume
            $location = $faker->city;  // Random city as location
            $customType = $faker->word;  // Random custom type name
            $latitude = $faker->latitude;  // Random latitude
            $longitude = $faker->longitude;  // Random longitude
            $anomalyFlag = $faker->boolean(30);  // 30% chance of being an anomaly (true/false)
            $signalDate = $faker->dateTimeThisYear;  // Random signal date within this year

            // Insert signal data
            $signalId = DB::table('signals')->insertGetId([
                'created_by' => $createdBy,  // Tracks who created the signal
                'volume' => $volume,
                'waste_types' => json_encode($randomWasteTypes),  // Convert array of waste types to JSON
                'location' => $location,
                'custom_type' => $customType,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'anomaly_flag' => $anomalyFlag,  // Boolean value (true/false)
                'signal_date' => $signalDate,
                'status' => 'pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Insert associated waste types into the pivot table (signal_waste_type)
            foreach ($randomWasteTypes as $wasteTypeId) {
                DB::table('signal_waste_types')->insert([
                    'signal_id' => $signalId,
                    'waste_type_id' => $wasteTypeId,
                ]);
            }

            // Add random photos and videos (2-3 media files)
            $mediaFiles = ['photo', 'video']; // Media types
            $numMedia = rand(2, 3); // 2-3 files per signal

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
    }
}
