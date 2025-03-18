<?php

namespace App\Services;

use App\Models\Signal;
use App\Models\Collecte;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CriticalAreaService
{
    const CLUSTER_RADIUS_KM = 1; // 1 kilometer radius for clustering

    /**
     * Get heatmap points based on signal data
     * 
     * @param int $days Number of days to look back
     * @return array
     */
    public function getHeatmapPoints($days = 30)
    {
        $signals = Signal::with(['creator', 'wasteTypes'])
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', '!=', 'rejected')
            ->get()
            ->map(function ($signal) {
                // Get all collectes that contain this signal
                $collecte = Collecte::whereJsonContains('signal_ids', $signal->id)
                                  ->where('status', 'completed')
                                  ->first();

                $volume = $collecte ? $collecte->actual_volume : $signal->volume;

                return [
                    'latitude' => $signal->latitude,
                    'longitude' => $signal->longitude,
                    'volume' => $volume,
                    'credibility_score' => $signal->creator->credibility_score ?? 100
                ];
            })
            ->groupBy(function ($signal) {
                // Group by coordinates (rounded to 5 decimal places for proximity)
                return round($signal['latitude'], 5) . ',' . round($signal['longitude'], 5);
            })
            ->map(function ($group) {
                $maxCredibilitySignal = $group->sortByDesc('credibility_score')->first();
                $count = $group->count();
                
                // Calculate intensity based on both volume and report count
                // Normalize to a value between 0 and 1
                $intensity = min(1.0, ($count * $maxCredibilitySignal['volume']) / 1000);
                
                return [
                    $maxCredibilitySignal['latitude'],
                    $maxCredibilitySignal['longitude'],
                    $intensity
                ];
            })
            ->values()
            ->toArray();

        return $signals;
    }

    /**
     * Get top affected areas based on signal density and volume
     * 
     * @param int $limit Number of areas to return
     * @param int $days Number of days to look back
     * @return array
     */
    public function getTopAffectedAreas($limit = 5, $days = 30)
    {
        // Get all non-rejected signals from the last X days
        $signals = Signal::with(['creator', 'wasteTypes'])
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', '!=', 'rejected')
            ->get()
            ->map(function ($signal) {
                return [
                    'id' => $signal->id,
                    'latitude' => $signal->latitude,
                    'longitude' => $signal->longitude,
                    'volume' => $signal->volume,
                    'location' => $signal->location,
                    'created_at' => $signal->created_at,
                    'credibility_score' => $signal->creator->credibility_score ?? 100
                ];
            });

        // Group signals into clusters based on proximity
        $clusters = $this->clusterSignals($signals);

        // Sort clusters by total volume and get top N
        $topClusters = collect($clusters)
            ->sortByDesc('total_volume')
            ->take($limit);

        $maxVolume = $topClusters->max('total_volume');

        return $topClusters->map(function ($cluster) use ($maxVolume) {
            $severityPercentage = $maxVolume > 0
                ? round(($cluster['total_volume'] / $maxVolume) * 100)
                : 0;

            // Get the latest report date from the cluster
            $latestReport = collect($cluster['signals'])
                ->max('created_at');

            return [
                'name' => $cluster['location'],
                'coordinates' => [
                    'lat' => $cluster['center']['lat'],
                    'lng' => $cluster['center']['lng']
                ],
                'total_volume' => round($cluster['total_volume'], 2),
                'report_count' => count($cluster['signals']),
                'severity' => $severityPercentage,
                'latest_report' => Carbon::parse($latestReport)->diffForHumans()
            ];
        });
    }

    /**
     * Cluster signals based on proximity
     */
    private function clusterSignals($signals)
    {
        $clusters = [];
        $processed = [];

        foreach ($signals as $signal) {
            if (isset($processed[$signal['id']])) {
                continue;
            }

            // Start a new cluster with this signal
            $cluster = [
                'signals' => [$signal],
                'center' => [
                    'lat' => $signal['latitude'],
                    'lng' => $signal['longitude']
                ],
                'total_volume' => $signal['volume'],
                'signal_count' => 1,
                'location' => $signal['location']
            ];

            // Find nearby signals within 1km radius
            foreach ($signals as $otherSignal) {
                if ($signal['id'] === $otherSignal['id'] || isset($processed[$otherSignal['id']])) {
                    continue;
                }

                $distance = $this->calculateDistance(
                    $signal['latitude'],
                    $signal['longitude'],
                    $otherSignal['latitude'],
                    $otherSignal['longitude']
                );

                if ($distance <= self::CLUSTER_RADIUS_KM) {
                    $cluster['signals'][] = $otherSignal;
                    $cluster['total_volume'] += $otherSignal['volume'];
                    $cluster['signal_count']++;
                    $processed[$otherSignal['id']] = true;

                    // Update cluster center (average coordinates)
                    $cluster['center'] = [
                        'lat' => array_sum(array_column($cluster['signals'], 'latitude')) / count($cluster['signals']),
                        'lng' => array_sum(array_column($cluster['signals'], 'longitude')) / count($cluster['signals'])
                    ];
                }
            }

            $processed[$signal['id']] = true;
            $clusters[] = $cluster;
        }

        return $clusters;
    }

    /**
     * Calculate distance between two points in kilometers using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the earth in km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    /**
     * Get a descriptive name for an area based on coordinates
     * 
     * @param float $lat
     * @param float $lon
     * @return string
     */
    private function getAreaName($lat, $lon)
    {
        return sprintf("Area %.4f°N, %.4f°E", $lat, $lon);
    }

    public function clusterSignalsForCollection($signals)
    {
        $clusters = [];
        $processed = [];

        foreach ($signals as $signal) {
            if (isset($processed[$signal->id])) {
                continue;
            }

            // Start a new cluster with this signal
            $cluster = [
                'signals' => [$signal],
                'center' => [
                    'lat' => $signal->latitude,
                    'lng' => $signal->longitude
                ],
                'total_volume' => $signal->volume,
                'signal_count' => 1,
                'location' => $signal->location,
                'region' => $signal->region
            ];

            // Find nearby signals within 1km radius
            foreach ($signals as $otherSignal) {
                if ($signal->id === $otherSignal->id || isset($processed[$otherSignal->id])) {
                    continue;
                }

                $distance = $this->calculateDistance(
                    $signal->latitude,
                    $signal->longitude,
                    $otherSignal->latitude,
                    $otherSignal->longitude
                );

                if ($distance <= self::CLUSTER_RADIUS_KM) {
                    $cluster['signals'][] = $otherSignal;
                    $cluster['total_volume'] += $otherSignal->volume;
                    $cluster['signal_count']++;
                    $processed[$otherSignal->id] = true;

                    // Update cluster center (average coordinates)
                    $cluster['center'] = [
                        'lat' => collect($cluster['signals'])->avg('latitude'),
                        'lng' => collect($cluster['signals'])->avg('longitude')
                    ];
                }
            }

            $processed[$signal->id] = true;
            
            // Only add clusters with 5 or more signals
            if ($cluster['signal_count'] >= 5) {
                $clusters[] = $cluster;
            }
        }

        return $clusters;
    }
} 