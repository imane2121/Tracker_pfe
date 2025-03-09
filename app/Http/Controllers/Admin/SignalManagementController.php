<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SignalManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Signal::with(['creator', 'wasteTypes', 'media'])
            ->when($request->status, function($q, $status) {
                return $q->where('status', $status);
            })
            ->when($request->date_from, function($q, $date) {
                return $q->where('signal_date', '>=', Carbon::parse($date));
            })
            ->when($request->date_to, function($q, $date) {
                return $q->where('signal_date', '<=', Carbon::parse($date));
            })
            ->when($request->waste_type, function($q, $wasteType) {
                return $q->whereJsonContains('waste_types', $wasteType);
            })
            ->when($request->region, function($q, $region) {
                return $q->where('location', 'like', "%$region%");
            });

        // Handle anomaly filter
        if ($request->has('anomaly')) {
            $query->where('anomaly_flag', true);
        }

        $signals = $query->latest()->paginate(10);
        $wasteTypes = WasteTypes::all();

        // Get statistics
        $statistics = [
            'total' => Signal::count(),
            'pending' => Signal::where('status', 'pending')->count(),
            'validated' => Signal::where('status', 'validated')->count(),
            'rejected' => Signal::where('status', 'rejected')->count(),
            'anomalies' => Signal::where('anomaly_flag', true)->count(),
        ];

        // Get heatmap data
        $heatmapData = Signal::select(
            'latitude', 
            'longitude',
            DB::raw('count(*) as intensity')
        )
        ->groupBy('latitude', 'longitude')
        ->get();

        return view('admin.signals.index', compact(
            'signals', 
            'wasteTypes', 
            'statistics',
            'heatmapData'
        ));
    }

    public function show(Signal $signal)
    {
        $signal->load(['creator', 'wasteTypes', 'media']);
        
        // Get nearby signals within 5km radius
        $nearbySignals = Signal::select(
            '*',
            DB::raw("(
                6371 * acos(
                    cos(radians($signal->latitude)) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians($signal->longitude)) + 
                    sin(radians($signal->latitude)) * 
                    sin(radians(latitude))
                )
            ) AS distance")
        )
        ->having('distance', '<=', 5)
        ->where('id', '!=', $signal->id)
        ->get();

        return view('admin.signals.show', compact('signal', 'nearbySignals'));
    }

    public function updateStatus(Request $request, Signal $signal)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,validated,rejected',
            'admin_note' => 'nullable|string|max:500'
        ]);

        $signal->update([
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note'] ?? null
        ]);

        // If validating a signal, update user's credibility score
        if ($validated['status'] === 'validated' && $signal->creator->isContributor()) {
            $signal->creator->increment('credibility_score', 2);
        }
        // If rejecting a signal, decrease user's credibility score
        elseif ($validated['status'] === 'rejected' && $signal->creator->isContributor()) {
            $signal->creator->decrement('credibility_score', 1);
        }

        return redirect()->back()->with('success', 'Signal status updated successfully.');
    }

    public function getStatistics(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Get signals by waste type
        $wasteTypeStats = DB::table('signals')
            ->join('signal_waste_types', 'signals.id', '=', 'signal_waste_types.signal_id')
            ->join('waste_types', 'signal_waste_types.waste_type_id', '=', 'waste_types.id')
            ->whereBetween('signals.signal_date', [$startDate, $endDate])
            ->select('waste_types.name', DB::raw('count(*) as count'))
            ->groupBy('waste_types.name')
            ->get();

        // Get signals by region
        $regionStats = Signal::whereBetween('signal_date', [$startDate, $endDate])
            ->select('location', DB::raw('count(*) as count'))
            ->groupBy('location')
            ->get();

        // Get temporal distribution
        $temporalStats = Signal::whereBetween('signal_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(signal_date) as date'),
                DB::raw('count(*) as count')
            )
            ->groupBy('date')
            ->get();

        return response()->json([
            'waste_types' => $wasteTypeStats,
            'regions' => $regionStats,
            'temporal' => $temporalStats
        ]);
    }

    public function export(Request $request)
    {
        $format = $request->format ?? 'csv';
        $signals = Signal::with(['creator', 'wasteTypes'])
            ->when($request->status, function($q, $status) {
                return $q->where('status', $status);
            })
            ->get();

        switch ($format) {
            case 'pdf':
                return $this->exportPDF($signals);
            case 'csv':
                return $this->exportCSV($signals);
            default:
                return back()->with('error', 'Unsupported export format.');
        }
    }

    private function exportPDF($signals)
    {
        // Implement PDF export logic
        // You'll need to install and configure a PDF package like dompdf
    }

    private function exportCSV($signals)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="signals-export.csv"',
        ];

        $callback = function() use ($signals) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'ID', 'Location', 'Waste Types', 'Volume', 
                'Reporter', 'Status', 'Date', 'Coordinates'
            ]);

            foreach ($signals as $signal) {
                fputcsv($file, [
                    $signal->id,
                    $signal->location,
                    $signal->wasteTypes->pluck('name')->implode(', '),
                    $signal->volume,
                    $signal->creator->name,
                    $signal->status,
                    $signal->signal_date,
                    "{$signal->latitude}, {$signal->longitude}"
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 