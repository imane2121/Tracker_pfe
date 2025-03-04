<?php

namespace App\Services;

use App\Models\Signal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CriticalAreaService
{
    /**
     * Get heatmap points based on signal data
     * 
     * @param int $days Number of days to look back
     * @return array
     */
    public function getHeatmapPoints($days = 30)
    {
        return Signal::where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', '!=', 'rejected')
            ->select([
                'latitude',
                'longitude',
                DB::raw('COUNT(*) as report_count'),
                DB::raw('SUM(volume) as total_volume')
            ])
            ->groupBy('latitude', 'longitude')
            ->get()
            ->map(function ($point) {
                // Calculate intensity based on report count and volume
                $intensity = min(1.0, ($point->report_count * $point->total_volume) / 1000);
                return [
                    $point->latitude,
                    $point->longitude,
                    $intensity
                ];
            })
            ->toArray();
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
        // Group signals into areas using a grid system
        // We'll use 0.1 degree grid squares (roughly 11km)
        $areas = Signal::where('created_at', '>=', Carbon::now()->subDays($days))
            ->where('status', '!=', 'rejected')
            ->select([
                DB::raw('ROUND(latitude, 1) as area_lat'),
                DB::raw('ROUND(longitude, 1) as area_lon'),
                DB::raw('COUNT(*) as report_count'),
                DB::raw('SUM(volume) as total_volume'),
                DB::raw('MAX(created_at) as latest_report')
            ])
            ->groupBy('area_lat', 'area_lon')
            ->orderByDesc('total_volume')
            ->limit($limit)
            ->get();

        $maxVolume = $areas->max('total_volume');

        return $areas->map(function ($area) use ($maxVolume) {
            // Calculate severity percentage based on volume relative to max
            $severityPercentage = round(($area->total_volume / $maxVolume) * 100);
            
            // Get a descriptive name for the area
            $areaName = $this->getAreaName($area->area_lat, $area->area_lon);
            
            return [
                'name' => $areaName,
                'severity' => $severityPercentage,
                'report_count' => $area->report_count,
                'total_volume' => $area->total_volume,
                'latest_report' => Carbon::parse($area->latest_report)->diffForHumans()
            ];
        })->toArray();
    }

    /**
     * Get a descriptive name for an area based on coordinates
     * In a real application, this could use reverse geocoding
     * 
     * @param float $lat
     * @param float $lon
     * @return string
     */
    private function getAreaName($lat, $lon)
    {
        return "Area {$lat}°N, {$lon}°E";
    }
} 