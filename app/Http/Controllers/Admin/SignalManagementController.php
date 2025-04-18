<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signal;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SignalsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class SignalManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Signal::with(['creator', 'wasteTypes', 'media']);

        // Handle status filter - show all signals when no status is selected
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Only apply date filters if they are provided
        if ($request->filled('date_from')) {
            $query->where('signal_date', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('signal_date', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // Only apply waste type filter if a specific type is selected
        if ($request->filled('waste_type') && $request->waste_type !== '') {
            $query->whereHas('wasteTypes', function($q) use ($request) {
                $q->where('waste_types.id', $request->waste_type);
            });
        }

        // Only apply region filter if a value is provided
        if ($request->filled('region') && $request->region !== '') {
            $query->where('location', 'like', "%{$request->region}%");
        }

        // Handle anomaly filter - explicitly handle both checked and unchecked states
        if ($request->has('anomaly')) {
            $query->where('anomaly_flag', true);
        }

        // Clone the query for markers (we want all signals for markers)
        $markersQuery = clone $query;
        $allSignals = $markersQuery->get();

        // Get paginated signals for the table
        $signals = $query->latest()->paginate(10);
        
        $wasteTypes = WasteTypes::all();

        // Get statistics
        $statistics = [
            'total' => Signal::count(),
            'pending' => Signal::where('status', 'pending')->count(),
            'validated' => Signal::where('status', 'validated')->count(),
            'anomalies' => Signal::where('anomaly_flag', true)->count(),
        ];

        // Get heatmap data with the same filters as the main query
        $heatmapQuery = Signal::select(
            'latitude', 
            'longitude',
            DB::raw('count(*) as intensity')
        );

        // Apply the same filters to heatmap data
        if ($request->filled('status') && $request->status !== '') {
            $heatmapQuery->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $heatmapQuery->where('signal_date', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->filled('date_to')) {
            $heatmapQuery->where('signal_date', '<=', Carbon::parse($request->date_to)->endOfDay());
        }
        if ($request->filled('waste_type') && $request->waste_type !== '') {
            $heatmapQuery->whereHas('wasteTypes', function($q) use ($request) {
                $q->where('waste_types.id', $request->waste_type);
            });
        }
        if ($request->filled('region') && $request->region !== '') {
            $heatmapQuery->where('location', 'like', "%{$request->region}%");
        }
        if ($request->has('anomaly')) {
            $heatmapQuery->where('anomaly_flag', true);
        }

        $heatmapData = $heatmapQuery->groupBy('latitude', 'longitude')->get();

        return view('admin.signals.index', compact(
            'signals',
            'allSignals', // Add all signals for markers
            'wasteTypes', 
            'statistics',
            'heatmapData'
        ));
    }

    public function show(Signal $signal)
    {
        $signal->load(['creator', 'wasteTypes', 'media', 'aiAnalysis']);
        
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

    public function export($format)
    {
        try {
            $signals = Signal::with(['creator', 'wasteTypes'])->get();
            
            switch ($format) {
                case 'csv':
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="signals.csv"',
                    ];
                    
                    $callback = function() use ($signals) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, ['ID', 'Location', 'Waste Types', 'Volume', 'Reporter', 'Status', 'Date']);
                        
                        foreach ($signals as $signal) {
                            fputcsv($file, [
                                $signal->id,
                                $signal->location,
                                $signal->wasteTypes->pluck('name')->join(', '),
                                $signal->volume . ' m³',
                                $signal->creator->first_name . ' ' . $signal->creator->last_name,
                                $signal->status,
                                $signal->signal_date->format('Y-m-d H:i')
                            ]);
                        }
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                    
                case 'excel':
                    return Excel::download(new SignalsExport($signals), 'signals.xlsx');
                    
                case 'pdf':
                    $pdf = PDF::loadView('admin.signals.export-pdf', compact('signals'));
                    return $pdf->download('signals.pdf');
                    
                default:
                    return back()->with('error', 'Invalid export format');
            }
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while exporting the data.');
        }
    }

    public function edit(Signal $signal)
    {
        $signal->load(['creator', 'wasteTypes', 'media']);
        $wasteTypes = WasteTypes::all();
        return view('admin.signals.edit', compact('signal', 'wasteTypes'));
    }

    public function update(Request $request, Signal $signal)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'volume' => 'required|numeric|min:0',
            'waste_types' => 'required|array',
            'waste_types.*' => 'exists:waste_types,id',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,validated,rejected',
            'admin_note' => 'nullable|string|max:500'
        ]);

        $signal->update([
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'volume' => $validated['volume'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'admin_note' => $validated['admin_note']
        ]);

        $signal->wasteTypes()->sync($validated['waste_types']);

        return redirect()->route('admin.signals.show', $signal)
            ->with('success', 'Signal updated successfully.');
    }

    public function destroy(Signal $signal)
    {
        // Delete associated media files
        foreach ($signal->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }

        $signal->delete();

        return redirect()->route('admin.signals.index')
            ->with('success', 'Signal deleted successfully.');
    }

    public function showBatchValidate()
    {
        $pendingSignals = Signal::where('status', 'pending')
            ->with(['creator', 'wasteTypes', 'aiAnalysis'])
            ->latest()
            ->paginate(10);
            
        return view('admin.signals.batch-validate', compact('pendingSignals'));
    }

    public function batchValidate(Request $request)
    {
        $validated = $request->validate([
            'signals' => 'required|array',
            'signals.*' => 'exists:signals,id'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['signals'] as $signalId) {
                $signal = Signal::with('aiAnalysis')->findOrFail($signalId);
                
                // Check if AI analysis exists and matches reporter selection
                $aiValidationNote = '';
                if ($signal->aiAnalysis) {
                    if ($signal->aiAnalysis->debris_detected) {
                        if ($signal->aiAnalysis->matches_reporter_selection) {
                            $aiValidationNote = 'AI analysis confirms reporter selection.';
                        } else {
                            $aiValidationNote = 'AI analysis detected different waste types than reported.';
                        }
                    } else {
                        $aiValidationNote = 'AI analysis did not detect debris in the media.';
                    }
                }
                
                $signal->update([
                    'status' => 'validated',
                    'admin_note' => 'Batch validated. ' . $aiValidationNote
                ]);

                // Update user's credibility score
                if ($signal->creator->isContributor()) {
                    $signal->creator->increment('credibility_score', 2);
                }
            }
            DB::commit();
            return redirect()->route('admin.signals.index')
                ->with('success', 'Selected signals have been validated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while validating signals.');
        }
    }

    public function anomalies()
    {
        $anomalies = Signal::where('anomaly_flag', true)
            ->with(['creator', 'wasteTypes'])
            ->latest()
            ->paginate(10);
            
        return view('admin.signals.anomalies', compact('anomalies'));
    }
} 