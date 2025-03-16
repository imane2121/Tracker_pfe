<?php

namespace App\Services;

use App\Models\Signal;
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
        $signals = Signal::with(['collecte', 'creator', 'wasteTypes'])
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', '!=', 'rejected')
            ->get()
            ->map(function ($signal) {
                $volume = $signal->collecte && $signal->collecte->status === 'completed' 
                    ? $signal->collecte->actual_volume 
                    : $signal->volume;

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
        $signals = Signal::with(['collecte', 'creator', 'wasteTypes'])
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', '!=', 'rejected')
            ->get();

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
            
            // Format waste types for display
            $wasteTypes = collect($cluster['waste_types'])->take(3);
            $remainingCount = count($cluster['waste_types']) - 3;
            $wasteTypesDisplay = $wasteTypes->join(', ');
            if ($remainingCount > 0) {
                $wasteTypesDisplay .= " (+{$remainingCount} more)";
            }

            return [
                'name' => $cluster['location'] ?: $this->getAreaName($cluster['center_lat'], $cluster['center_lng']),
                'severity' => $severityPercentage,
                'report_count' => $cluster['report_count'],
                'total_volume' => round($cluster['total_volume'], 2),
                'latest_report' => Carbon::parse($cluster['latest_report'])->diffForHumans(),
                'coordinates' => [
                    'lat' => $cluster['center_lat'],
                    'lng' => $cluster['center_lng']
                ],
                'waste_types' => $wasteTypesDisplay,
                'has_collection' => $cluster['has_collection'],
                'collection_id' => $cluster['collection_id'],
                'collection_status' => $cluster['collection_status'],
                'signal_id' => $cluster['signal_id']
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
            if (isset($processed[$signal->id])) {
                continue;
            }

            // Get the correct volume based on collection status
            $volume = $signal->collecte && $signal->collecte->status === 'completed'
                ? $signal->collecte->actual_volume
                : $signal->volume;

            // Start a new cluster with this signal
            $cluster = [
                'signals' => [$signal],
                'location' => $signal->location,
                'total_volume' => $volume,
                'report_count' => 1,
                'latest_report' => $signal->created_at,
                'center_lat' => $signal->latitude,
                'center_lng' => $signal->longitude,
                'credibility_scores' => [$signal->creator->credibility_score ?? 100],
                'waste_types' => $signal->wasteTypes->pluck('name')->toArray(),
                'has_collection' => !is_null($signal->collecte),
                'collection_id' => $signal->collecte ? $signal->collecte->id : null,
                'collection_status' => $signal->collecte ? $signal->collecte->status : null,
                'signal_id' => $signal->id
            ];
            $processed[$signal->id] = true;

            // Find nearby signals
            foreach ($signals as $nearby) {
                if ($signal->id !== $nearby->id && !isset($processed[$nearby->id])) {
                    $distance = $this->calculateDistance(
                        $signal->latitude,
                        $signal->longitude,
                        $nearby->latitude,
                        $nearby->longitude
                    );

                    if ($distance <= self::CLUSTER_RADIUS_KM) {
                        $cluster['signals'][] = $nearby;
                        
                        // Get the correct volume for the nearby signal
                        $nearbyVolume = $nearby->collecte && $nearby->collecte->status === 'completed'
                            ? $nearby->collecte->actual_volume
                            : $nearby->volume;

                        // Add credibility score
                        $cluster['credibility_scores'][] = $nearby->creator->credibility_score ?? 100;
                        
                        // Update cluster data based on highest credibility score
                        $maxCredibilityIndex = array_search(max($cluster['credibility_scores']), $cluster['credibility_scores']);
                        $maxCredibilitySignal = $cluster['signals'][$maxCredibilityIndex];
                        
                        $cluster['total_volume'] = $maxCredibilitySignal->collecte && $maxCredibilitySignal->collecte->status === 'completed'
                            ? $maxCredibilitySignal->collecte->actual_volume
                            : $maxCredibilitySignal->volume;
                            
                        $cluster['waste_types'] = $maxCredibilitySignal->wasteTypes->pluck('name')->toArray();
                        $cluster['report_count']++;
                        $cluster['latest_report'] = max($cluster['latest_report'], $nearby->created_at);
                        
                        // Update collection information
                        if (!$cluster['has_collection'] && !is_null($nearby->collecte)) {
                            $cluster['has_collection'] = true;
                            $cluster['collection_id'] = $nearby->collecte->id;
                            $cluster['collection_status'] = $nearby->collecte->status;
                        }
                        
                        $processed[$nearby->id] = true;

                        // Recalculate cluster center
                        $cluster['center_lat'] = collect($cluster['signals'])->avg('latitude');
                        $cluster['center_lng'] = collect($cluster['signals'])->avg('longitude');
                    }
                }
            }

            $clusters[] = $cluster;
        }

        return $clusters;
    }

    /**
     * Calculate distance between two points in kilometers using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

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
} 