<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Collecte;
use App\Models\Signal;
use App\Models\WasteTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function index()
    {
        // Get all collectes for the map
        $mapCollectes = Collecte::with(['signal', 'signal.wasteTypes', 'contributors'])
            ->whereHas('signal') // Ensure signal exists
            ->get();

        // Get upcoming collectes for the slider
        $upcomingCollectes = Collecte::with(['signal', 'signal.wasteTypes', 'contributors'])
            ->where('starting_date', '>', Carbon::now())
            ->where('status', 'planned')
            ->orderBy('starting_date')
            ->take(6)
            ->get();

        // Get featured and recent articles
        $articles = Article::with(['author', 'tags'])
            ->where('published_at', '<=', Carbon::now())
            ->where(function($query) {
                $query->where('is_featured', true)
                      ->orWhere('published_at', '>=', Carbon::now()->subDays(30));
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        // Get all waste types for filters
        $wasteTypes = WasteTypes::all();

        // Get locations for the map with additional data for filtering
        $locations = Signal::with(['wasteTypes', 'collecte'])
            ->whereHas('collecte', function($query) {
                $query->whereIn('status', ['planned', 'completed', 'validated']);
            })
            ->select('id', 'latitude', 'longitude', 'volume', 'signal_date')
            ->get()
            ->map(function($signal) {
                return [
                    'id' => $signal->id,
                    'latitude' => $signal->latitude,
                    'longitude' => $signal->longitude,
                    'volume' => $signal->collecte->status === 'completed' ? $signal->collecte->actual_volume : $signal->volume,
                    'waste_types' => $signal->wasteTypes->pluck('name'),
                    'waste_type_ids' => $signal->wasteTypes->pluck('id'),
                    'starting_date' => $signal->collecte->starting_date,
                    'status' => $signal->collecte->status,
                    'intensity' => ($signal->collecte->status === 'completed' ? $signal->collecte->actual_volume : $signal->volume) / 1000, // Normalize volume for heatmap intensity
                    'date' => $signal->signal_date->format('Y-m-d'),
                ];
            });

        return view('overview', compact('mapCollectes', 'upcomingCollectes', 'articles', 'locations', 'wasteTypes'));
    }
} 