<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Collecte;
use App\Models\User;
use Carbon\Carbon;

class CollecteContributorSeeder extends Seeder
{
    public function run(): void
    {
        // Get all collectes
        $collectes = Collecte::all();
        
        if ($collectes->isEmpty()) {
            echo "No collectes found. Please run CollecteSeeder first.\n";
            return;
        }

        // Get all contributors
        $contributors = User::whereHas('roles', function($query) {
            $query->where('title', 'contributor');
        })->get();

        if ($contributors->isEmpty()) {
            echo "No contributors found. Please ensure there are users with the contributor role.\n";
            return;
        }

        foreach ($collectes as $collecte) {
            // Calculate maximum allowed contributors based on nbrContributors
            $maxAllowed = $collecte->nbrContributors;
            
            // Determine number of contributors based on status, but never exceed maxAllowed
            $numContributors = min($maxAllowed, match($collecte->status) {
                'validated', 'completed' => rand(ceil($maxAllowed * 0.6), $maxAllowed), // 60-100% capacity for completed events
                'in_progress' => rand(ceil($maxAllowed * 0.3), ceil($maxAllowed * 0.8)), // 30-80% capacity for ongoing events
                'planned' => rand(0, ceil($maxAllowed * 0.5)), // 0-50% capacity for planned events
                default => 0
            });

            // Get random contributors
            $selectedContributors = $contributors->random(min($numContributors, $contributors->count()));

            // Clear any existing contributors first to avoid duplicates
            $collecte->contributors()->detach();

            foreach ($selectedContributors as $contributor) {
                // Determine status based on collecte status
                $contributorStatus = match($collecte->status) {
                    'validated', 'completed' => 'completed',
                    'in_progress' => array_rand(['approved' => 1, 'completed' => 2]),
                    'planned' => array_rand(['pending' => 1, 'approved' => 2]),
                    default => 'pending'
                };

                // Calculate joined_at date
                $joinedAt = match($collecte->status) {
                    'validated', 'completed' => $collecte->starting_date->copy()->subDays(rand(1, 7)),
                    'in_progress' => $collecte->starting_date->copy()->subDays(rand(1, 3)),
                    'planned' => Carbon::now()->subDays(rand(1, 5)),
                    default => Carbon::now()
                };

                // Attach contributor to collecte
                $collecte->contributors()->attach($contributor->id, [
                    'status' => $contributorStatus,
                    'joined_at' => $joinedAt
                ]);
            }

            // Update current_contributors count
            $currentCount = $selectedContributors->count();
            echo "Collecte {$collecte->id}: {$currentCount}/{$maxAllowed} contributors\n";
            
            $collecte->update([
                'current_contributors' => $currentCount
            ]);
        }
    }
} 