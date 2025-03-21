<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Collecte;
use App\Models\Signal;
use App\Models\WasteTypes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverviewController extends Controller
{
    public function index()
    {
        // Get ALL collectes with their own coordinates
        $mapCollectes = Collecte::select(
            'id', 
            'signal_ids', 
            'starting_date', 
            'status', 
            'current_contributors', 
            'nbrContributors',
            'actual_volume',
            'latitude',  // Use collecte's own coordinates
            'longitude',
            'location'
        )->get();

        // Map the collectes to include necessary data
        $mapCollectes = $mapCollectes->map(function ($collecte) {
            // Keep the collecte's own coordinates
            $collecte->display_latitude = $collecte->latitude;
            $collecte->display_longitude = $collecte->longitude;
            
            // If collecte has signals, use them for volume calculation and heatmap
            if (!empty($collecte->signal_ids)) {
                $signalIds = is_array($collecte->signal_ids) ? $collecte->signal_ids : json_decode($collecte->signal_ids, true);
                if ($signalIds) {
                    $signals = Signal::whereIn('id', $signalIds)->get();
                    // Calculate total volume from signals if needed
                    $collecte->volume = $signals->sum('volume');
                    // Keep signals for heatmap
                    $collecte->signals = $signals;
                }
            } else {
                // For collectes without signals (urgent ones)
                $collecte->volume = $collecte->actual_volume;
                $collecte->signals = [];
            }
            
            return $collecte;
        });

        // Debug: Log the final count
        \Log::info('Collectes after processing: ' . $mapCollectes->count());

        // Get upcoming collectes
        $upcomingCollectes = Collecte::where('starting_date', '>', Carbon::now())
            ->where('status', 'planned')
            ->orderBy('starting_date')
            ->take(6)
            ->get()
            ->map(function ($collecte) {
                if (!empty($collecte->signal_ids)) {
                    $signalIds = is_array($collecte->signal_ids) ? $collecte->signal_ids : json_decode($collecte->signal_ids, true);
                    $firstSignal = Signal::with('wasteTypes')->find($signalIds[0] ?? null);
                    $collecte->signal = $firstSignal;
                }
                return $collecte;
            });

        // Get articles
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

        // Get waste types
        $wasteTypes = WasteTypes::all();

        // Simplified locations query
        $locations = Signal::with('wasteTypes')
            ->select('id', 'latitude', 'longitude', 'volume', 'signal_date')
            ->get();

        return view('overview', compact('mapCollectes', 'upcomingCollectes', 'articles', 'locations', 'wasteTypes'));
    }
} 