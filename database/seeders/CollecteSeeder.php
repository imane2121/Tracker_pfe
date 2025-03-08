<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collecte;
use App\Models\User;
use App\Models\Signal;
use Carbon\Carbon;

class CollecteSeeder extends Seeder
{
    public function run(): void
    {
        // Get some users with admin or supervisor role for creators
        $creators = User::whereHas('roles', function($query) {
            $query->whereIn('title', ['admin', 'supervisor']);
        })->get();

        // If no creators found, use the first user as fallback
        if ($creators->isEmpty()) {
            $creators = User::take(1)->get();
        }

        // Get some signals to associate with collectes
        $signals = Signal::all();

        // If no signals exist, we can't create collectes
        if ($signals->isEmpty()) {
            echo "No signals found. Please run SignalSeeder first.\n";
            return;
        }

        // Regions in Morocco
        $regions = [
            'Tanger-Tétouan-Al Hoceïma',
            'Oriental',
            'Fès-Meknès',
            'Rabat-Salé-Kénitra',
            'Béni Mellal-Khénifra',
            'Casablanca-Settat',
            'Marrakech-Safi',
            'Drâa-Tafilalet',
            'Souss-Massa',
            'Guelmim-Oued Noun',
            'Laâyoune-Sakia El Hamra',
            'Dakhla-Oued Ed-Dahab'
        ];

        // Create 20 collectes with different statuses
        foreach(range(1, 20) as $index) {
            $signal = $signals->random();
            $startingDate = Carbon::now()->addDays(rand(-30, 30)); // Some past, some future
            
            $collecte = Collecte::create([
                'signal_id' => $signal->id,
                'user_id' => $creators->random()->id,
                'region' => $regions[array_rand($regions)],
                'location' => $signal->location,
                'image' => $signal->media()->first()?->path, // Use one of the signal's images
                'description' => "Clean-up operation for " . $signal->waste_type . " at " . $signal->location,
                'latitude' => $signal->latitude,
                'longitude' => $signal->longitude,
                'nbrContributors' => rand(10, 30), // Set max capacity
                'current_contributors' => rand(0, 35), // Model will automatically cap this if too high
                'status' => $this->getStatusBasedOnDate($startingDate),
                'starting_date' => $startingDate,
                'end_date' => $startingDate->copy()->addHours(rand(2, 8))
            ]);

            // Add some contributors - but only up to the current_contributors count
            $contributors = User::whereHas('roles', function($query) {
                $query->where('title', 'contributor');
            })
            ->inRandomOrder()
            ->take($collecte->current_contributors) // Take exactly the number of current_contributors
            ->get();

            if ($contributors->isNotEmpty()) {
                foreach($contributors as $contributor) {
                    // For past events, mark some contributors as completed
                    $status = $startingDate->isPast() 
                        ? array_rand(['approved' => 1, 'completed' => 2])
                        : array_rand(['pending' => 0, 'approved' => 1]);

                    $collecte->contributors()->attach($contributor->id, [
                        'status' => $status,
                        'joined_at' => Carbon::now()->subDays(rand(1, 10))
                    ]);
                }
            }
        }
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