<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\Signal;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\CriticalAreaService;
use Illuminate\Validation\ValidationException;

class CollecteController extends Controller
{
    public function __construct()
    {
        // Basic auth check for all methods
        $this->middleware('auth');
        
        // Only admin and owner (supervisor) can edit/delete their own collections
        $this->middleware('role:admin,supervisor')->only(['edit', 'update', 'destroy', 'updateStatus']);
    }

    public function index()
    {
        $collectes = Collecte::with(['creator', 'contributors'])
            ->latest()
            ->paginate(9);

        return view('collectes.index', compact('collectes'));
    }

    public function cluster()
    {
        // Get both validated and pending signals
        $signals = Signal::whereIn('status', ['validated', 'pending'])
            ->whereNotIn('id', function($query) {
                $query->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(signal_ids, "$[*]"))'))
                      ->from('collectes')
                      ->whereNotNull('signal_ids');
            })
            ->get();
        
        return view('collectes.cluster', compact('signals'));
    }

    public function create(Request $request)
    {

        $wasteTypes = WasteTypes::all();

        if ($request->has('signal_ids')) {
            $signalIds = explode(',', $request->signal_ids);
            $signals = Signal::whereIn('id', $signalIds)->get();
            
            // Calculate center point for the signals
            $centerLat = $signals->avg('latitude');
            $centerLng = $signals->avg('longitude');
            
            return view('collectes.create', [
                'signals' => $signals,
                'centerLat' => $centerLat,
                'centerLng' => $centerLng,
                'wasteTypes' => $wasteTypes
            ]);
        }
        
        // Handle cluster_id from existing logic
        if (!$request->has('cluster_id')) {
            return redirect()->route('collecte.clusters')
                ->with('error', 'Please select a cluster first');
        }

        $selectedCluster = session('clusters')[$request->cluster_id] ?? null;
        if (!$selectedCluster) {
            return redirect()->route('collecte.clusters')
                ->with('error', 'Invalid cluster selected');
        }

        return view('collectes.create', [
            'cluster' => $selectedCluster,
            'wasteTypes' => $wasteTypes
        ]);
    }

    private function getRegionFromCoordinates($lat, $lng)
    {
        // Implement geocoding logic here to get region from coordinates
        // You can use services like OpenStreetMap Nominatim or Google Geocoding API
    }

    public function store(Request $request)
    {
        try {

            // Validate the request
        $validated = $request->validate([
                'signal_ids' => 'required|json',
                'location' => 'required|string|max:255',
                'region' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nbrContributors' => 'required|integer|min:1',
                'actual_volume' => 'required|numeric|min:0',
                'starting_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:starting_date',
                'waste_types' => 'required|array|min:1',
                'waste_types.*' => 'exists:waste_types,id',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048'
        ]);

            // Decode signal_ids JSON
            $signalIds = json_decode($validated['signal_ids'], true);
            
            // Verify signals exist
            $signalsCount = Signal::whereIn('id', $signalIds)->count();
            if ($signalsCount !== count($signalIds)) {
                return redirect()->back()
                    ->with('error', 'One or more selected signals are invalid.')
                    ->withInput();
            }

            DB::beginTransaction();

            try {
                // Convert waste type IDs to integers
                $wasteTypeIds = array_map('intval', $validated['waste_types']);

                // Create the collecte with integer waste type IDs
                $collecte = Collecte::create([
                    'signal_ids' => $signalIds,
                    'region' => $validated['region'],
                    'location' => $validated['location'],
                    'description' => $validated['description'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'nbrContributors' => $validated['nbrContributors'],
                    'actual_volume' => $validated['actual_volume'],
                    'starting_date' => $validated['starting_date'],
                    'end_date' => $validated['end_date'],
                    'actual_waste_types' => $wasteTypeIds, // Now contains integer values
                    'status' => 'planned',
                    'current_contributors' => 0,
                    'user_id' => auth()->id()
                ]);

                // Handle media files
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                        if ($file->getSize() > 2048 * 1024) {
                            throw new \Exception('File size exceeds 2MB limit');
                        }
                $path = $file->store('collecte-media', 'public');
                $collecte->media()->create([
                    'file_path' => $path,
                    'media_type' => $file->getClientMimeType()
                ]);
            }
        }

                DB::commit();

        return redirect()->route('collecte.show', $collecte)
                    ->with('success', 'Collection created successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Failed to create collection: ' . $e->getMessage())
                    ->withInput();
            }

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    public function show(Collecte $collecte)
    {
        $collecte->load(['signal', 'creator', 'contributors', 'media']);
        return view('collectes.show', compact('collecte'));
    }

    public function edit(Collecte $collecte)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $collecte->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You can only edit your own collections.');
        }

        $wasteTypes = WasteTypes::all();
        return view('collectes.edit', compact('collecte', 'wasteTypes'));
    }

    public function update(Request $request, Collecte $collecte)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $collecte->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You can only update your own collections.');
        }

        $validated = $request->validate([
            'region' => 'required|string',
            'location' => 'required|string',
            'description' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nbrContributors' => 'required|integer|min:1',
            'starting_date' => 'required|date',
            'end_date' => 'required|date|after:starting_date',
            'waste_types' => 'required|array',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048'
        ]);

        $collecte->update($validated);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('collecte-media', 'public');
                $collecte->media()->create([
                    'file_path' => $path,
                    'media_type' => $file->getClientMimeType()
                ]);
            }
        }

        return redirect()->route('collecte.show', $collecte)
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collecte $collecte)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $collecte->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You can only delete your own collections.');
        }

        foreach ($collecte->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $collecte->delete();

        return redirect()->route('collecte.index')
            ->with('success', 'Collection deleted successfully.');
    }

    /**
     * Join a collecte as a contributor
     */
    public function join(Collecte $collecte)
    {
        if ($collecte->isFull) {
            return redirect()->back()->with('error', 'This collection is already full.');
        }

        $collecte->contributors()->attach(auth()->id(), ['joined_at' => now()]);
        $collecte->increment('current_contributors');

        return redirect()->back()->with('success', 'You have joined the collection.');
    }

    public function leave(Collecte $collecte)
    {
        $collecte->contributors()->detach(auth()->id());
        $collecte->decrement('current_contributors');

        return redirect()->back()->with('success', 'You have left the collection.');
    }

    public function updateStatus(Request $request, Collecte $collecte)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $collecte->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You can only update the status of your own collections.');
        }

        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,completed,validated,cancelled'
        ]);

        // If moving to completed status, ensure it was in progress
        if ($validated['status'] === Collecte::STATUS_COMPLETED && $collecte->status !== Collecte::STATUS_IN_PROGRESS) {
            return redirect()->back()->with('error', 'Collection must be in progress before being completed.');
        }

        $collecte->update($validated);

        return redirect()->back()->with('success', 'Collection status updated successfully.');
    }

    public function complete(Request $request, Collecte $collecte)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $collecte->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You can only complete your own collections.');
        }

        if (!$collecte->canBeCompleted) {
            return redirect()->back()->with('error', 'This collection cannot be completed at this time.');
        }

        $validated = $request->validate([
            'actual_waste_types' => 'required|array',
            'actual_waste_types.*' => 'exists:waste_types,id',
            'actual_volume' => 'required|numeric|min:0',
            'completion_notes' => 'nullable|string',
            'attendance_data' => 'required|array',
            'attendance_data.*.user_id' => 'required|exists:users,id',
            'attendance_data.*.attended' => 'required|boolean',
            'attendance_data.*.notes' => 'nullable|string',
            'completion_media.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048'
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Update collection
            $collecte->update([
                'status' => Collecte::STATUS_COMPLETED,
                'actual_waste_types' => $validated['actual_waste_types'],
                'actual_volume' => $validated['actual_volume'],
                'completion_date' => now(),
                'completion_notes' => $validated['completion_notes'],
                'attendance_data' => $validated['attendance_data']
            ]);

            // Update contributor attendance
            foreach ($validated['attendance_data'] as $data) {
                $collecte->contributors()
                    ->updateExistingPivot($data['user_id'], [
                        'attended' => $data['attended'],
                        'attendance_notes' => $data['notes'] ?? null
                    ]);
            }

            // Handle completion media
            if ($request->hasFile('completion_media')) {
                foreach ($request->file('completion_media') as $file) {
                    $path = $file->store('collecte-completion-media', 'public');
                    $collecte->media()->create([
                        'file_path' => $path,
                        'media_type' => $file->getClientMimeType(),
                        'type' => 'completion'
                    ]);
                }
            }

            // Generate report
            $this->generateReport($collecte);

            DB::commit();

            return redirect()->route('collecte.show', $collecte)
                ->with('success', 'Collection completed successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to complete collection. Please try again.');
        }
    }

    protected function generateReport(Collecte $collecte)
    {
        $wasteTypes = WasteTypes::all();
        
        $pdf = PDF::loadView('collectes.report', [
            'collecte' => $collecte,
            'wasteTypes' => $wasteTypes,
        ]);

        $filename = 'collection_report_' . $collecte->id . '_' . Carbon::now()->format('Ymd_His') . '.pdf';
        $path = 'reports/' . $filename;
        
        Storage::put($path, $pdf->output());
        
        return $path;
    }

    public function downloadReport(Collecte $collecte)
    {
        if (!$collecte->report_generated || !$collecte->report_path) {
            return back()->with('error', 'Report not available for this collection.');
        }

        if (!Storage::exists($collecte->report_path)) {
            return back()->with('error', 'Report file not found.');
        }

        return Storage::download($collecte->report_path, 'collection_report_' . $collecte->id . '.pdf');
    }

    public function showClusters()
    {
        $signals = Signal::where('status', 'validated', 'pending')
            ->whereNotIn('id', function($query) {
                $query->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(signal_ids, "$[*]"))'))
                      ->from('collectes')
                      ->whereNotNull('signal_ids');
            })
            ->with(['creator', 'wasteTypes'])
            ->get();

        return view('collectes.cluster', compact('signals'));
    }
} 