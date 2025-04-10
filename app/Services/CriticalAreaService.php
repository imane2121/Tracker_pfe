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
            ->where('status', 'validated')
            ->get()
            ->map(function ($signal) {
                return [
                    'latitude' => $signal->latitude,
                    'longitude' => $signal->longitude,
                    'volume' => $signal->volume,
                    'credibility_score' => $signal->creator->credibility_score ?? 100
                ];
            })
            ->groupBy(function ($signal) {
                // Group by coordinates (rounded to 5 decimal places for proximity)
                return round($signal['latitude'], 5) . ',' . round($signal['longitude'], 5);
            })
            ->map(function ($group) {
                // Get the signal with highest credibility score
                $highestCredibilitySignal = $group->sortByDesc('credibility_score')->first();
                $count = $group->count();
                
                // Calculate intensity based on report count only
                // Normalize to a value between 0 and 1
                $intensity = min(1.0, $count / 10); // Assuming 10 reports is maximum intensity
                
                return [
                    $highestCredibilitySignal['latitude'],
                    $highestCredibilitySignal['longitude'],
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
        // Get all validated signals from the last X days that are not in any collection
        $signals = Signal::with(['creator', 'wasteTypes'])
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', 'validated')
            ->whereRaw('NOT EXISTS (
                SELECT 1 FROM collectes 
                WHERE JSON_CONTAINS(collectes.signal_ids, CAST(signals.id AS CHAR))
            )')
            ->get()
            ->map(function ($signal) {
                return [
                    'id' => $signal->id,
                    'latitude' => floatval($signal->latitude),
                    'longitude' => floatval($signal->longitude),
                    'volume' => floatval($signal->volume),
                    'location' => $signal->location,
                    'created_at' => $signal->created_at,
                    'credibility_score' => $signal->creator->credibility_score ?? 100,
                    'waste_types' => $signal->wasteTypes->pluck('name')->join(', ')
                ];
            });

        // Group signals into clusters based on proximity
        $clusters = $this->clusterSignals($signals);

        // Sort clusters by report count and get top N
        $topClusters = collect($clusters)
            ->filter(function ($cluster) {
                // Only include clusters with at least 5 signals
                return $cluster['signal_count'] >= 5;
            })
            ->sortByDesc('signal_count')
            ->take($limit)
            ->values();  // Convert to array with numeric keys

        $maxCount = $topClusters->max('signal_count');

        return $topClusters->map(function ($cluster) use ($maxCount) {
            // Get the signal with highest credibility score
            $highestCredibilitySignal = collect($cluster['signals'])
                ->sortByDesc('credibility_score')
                ->first();

            $severityPercentage = $maxCount > 0
                ? round(($cluster['signal_count'] / $maxCount) * 100)
                : 0;

            return [
                'name' => $cluster['location'],
                'coordinates' => [
                    'lat' => floatval($cluster['center']['lat']),
                    'lng' => floatval($cluster['center']['lng'])
                ],
                'total_volume' => round(floatval($highestCredibilitySignal['volume']), 2),
                'report_count' => $cluster['signal_count'],
                'severity' => $severityPercentage,
                'latest_report' => Carbon::parse(collect($cluster['signals'])->max('created_at'))->diffForHumans(),
                'waste_types' => $highestCredibilitySignal['waste_types'],
                'signal_id' => $highestCredibilitySignal['id']
            ];
        })->values()->toArray();
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