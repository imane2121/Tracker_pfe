<?php

namespace App\Services;

use App\Models\Signal;
use App\Models\User;
use Carbon\Carbon;

class SignalService
{
    const MINIMUM_TRUST_SCORE = 40;
    const MAX_SPEED_KMH = 120; // Maximum reasonable speed in km/h
    
    public function determineSignalStatus(Signal $signal, User $user)
    {
        // If user is admin, automatically validate
        if ($user->isAdmin()) {
            return 'validated';
        }

        // Check for temporal anomalies
        if ($this->hasTemporalAnomaly($signal, $user)) {
            $signal->anomaly_flag = true;
            return 'pending';
        }

        // For contributors, check trust score
        if ($user->isContributor()) {
            return $user->credibility_score >= self::MINIMUM_TRUST_SCORE ? 'validated' : 'pending';
        }

        return 'pending';
    }

    private function hasTemporalAnomaly(Signal $signal, User $user)
    {
        // Get user's last signal
        $lastSignal = Signal::where('created_by', $user->id)
            ->where('id', '!=', $signal->id)
            ->orderBy('signal_date', 'desc')
            ->first();

        if (!$lastSignal) {
            return false;
        }

        // Calculate time difference in hours
        $timeDiff = $signal->signal_date->diffInHours($lastSignal->signal_date);
        if ($timeDiff === 0) {
            $timeDiff = $signal->signal_date->diffInMinutes($lastSignal->signal_date) / 60;
        }

        // Calculate distance in kilometers
        $distance = Signal::calculateDistance(
            $signal->latitude,
            $signal->longitude,
            $lastSignal->latitude,
            $lastSignal->longitude
        );

        // If time difference is 0, it means signals are too close in time
        if ($timeDiff === 0 && $distance > 1) { // If distance > 1km and time diff is 0
            return true;
        }

        // Calculate speed (km/h)
        $speed = $distance / $timeDiff;

        // If speed is greater than MAX_SPEED_KMH, it's probably an anomaly
        return $speed > self::MAX_SPEED_KMH;
    }

    /**
     * Update signal status when anomaly is detected
     */
    public function handleAnomalyDetection(Signal $signal)
    {
        // No longer automatically reject signals with anomaly flag
        // The admin will handle these manually
        return;
    }
} 