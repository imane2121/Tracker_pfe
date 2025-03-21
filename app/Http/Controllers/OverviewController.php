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
        // Get all collectes for the map
        $mapCollectes = Collecte::with(['contributors'])
            ->get()
            ->map(function ($collecte) {
                // Get the first signal's data for display purposes
                $signalIds = is_array($collecte->signal_ids) ? $collecte->signal_ids : json_decode($collecte->signal_ids, true);
                $firstSignal = null;
                
                if (!empty($signalIds)) {
                    $firstSignal = Signal::with('wasteTypes')
                        ->find($signalIds[0] ?? null);
                }
                
                // Attach the signal data to the collecte
                $collecte->signal = $firstSignal;
                return $collecte;
            });

        // Get upcoming collectes for the slider
        $upcomingCollectes = Collecte::with(['contributors'])
            ->whereNotNull('signal_ids')
            ->where('signal_ids', '!=', '[]')
            ->where('starting_date', '>', Carbon::now())
            ->where('status', 'planned')
            ->orderBy('starting_date')
            ->take(6)
            ->get()
            ->map(function ($collecte) {
                $signalIds = is_array($collecte->signal_ids) ? $collecte->signal_ids : json_decode($collecte->signal_ids, true);
                $firstSignal = Signal::with('wasteTypes')
                    ->find($signalIds[0] ?? null);
                $collecte->signal = $firstSignal;
                return $collecte;
            });

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
        $locations = Signal::with('wasteTypes')
            ->whereIn('id', function($query) {
                $query->selectRaw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(signal_ids, "$[*]"))')
                    ->from('collectes')
                    ->whereIn('status', ['planned', 'completed', 'validated'])
                    ->whereNotNull('signal_ids')
                    ->where('signal_ids', '!=', '[]');
            })
            ->select('id', 'latitude', 'longitude', 'volume', 'signal_date')
            ->get()
            ->map(function($signal) {
                $collecte = Collecte::whereRaw('JSON_CONTAINS(signal_ids, ?)', [json_encode($signal->id)])
                    ->first();

                return [
                    'id' => $signal->id,
                    'latitude' => $signal->latitude,
                    'longitude' => $signal->longitude,
                    'volume' => $collecte && $collecte->status === 'completed' ? 
                               $collecte->actual_volume : $signal->volume,
                    'waste_types' => $signal->wasteTypes->pluck('name'),
                    'waste_type_ids' => $signal->wasteTypes->pluck('id'),
                    'starting_date' => $collecte ? $collecte->starting_date : null,
                    'status' => $collecte ? $collecte->status : null,
                ];
            });

        return view('overview', compact('mapCollectes', 'upcomingCollectes', 'articles', 'locations', 'wasteTypes'));
    }
} 