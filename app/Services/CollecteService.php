<?php

namespace App\Services;

use App\Models\Signal;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CollecteService
{
    const MIN_PENDING_SIGNALS = 4;
    const MIN_VALIDATED_SIGNALS = 1;
    const NEARBY_RADIUS_KM = 5; // Consider signals within 5km radius

    public function canCreateCollecte(User $user, ?Signal $signal = null, $latitude = null, $longitude = null)
    {
        // Both admins and supervisors can create collecte without signals
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }

        return false;
    }

    private function hasRequiredSignals($latitude, $longitude)
    {
        // Get nearby signals within NEARBY_RADIUS_KM
        $nearbySignals = Signal::select(
            '*',
            DB::raw("(
                6371 * acos(
                    cos(radians($latitude)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians($longitude)) + 
                    sin(radians($latitude)) * 
                    sin(radians(latitude))
                )
            ) AS distance")
        )
        ->having('distance', '<=', self::NEARBY_RADIUS_KM)
        ->get();

        // Count validated and pending signals
        $validatedCount = $nearbySignals->where('status', 'validated')->count();
        $pendingCount = $nearbySignals->where('status', 'pending')->count();

        // Return true if either condition is met
        return $validatedCount >= self::MIN_VALIDATED_SIGNALS || 
               $pendingCount >= self::MIN_PENDING_SIGNALS;
    }
} 