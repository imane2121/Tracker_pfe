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

class CollecteController extends Controller
{
    public function __construct()
    {
        // Basic auth check for all methods
        $this->middleware('auth');
        
        // Only admin and supervisor can create collections
        $this->middleware('role:admin,supervisor')->only(['create', 'store']);
        
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

    public function create()
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'supervisor') {
            return redirect()->back()->with('error', 'You do not have permission to create collections.');
        }
        
        // Get validated signals in the same area
        $signals = Signal::where('status', 'validated')
            ->whereDoesntHave('collecte')
            ->when(request('region'), function($query) {
                return $query->where('region', request('region'));
            })
            ->when(request('latitude') && request('longitude'), function($query) {
                $lat = request('latitude');
                $lng = request('longitude');
                $distance = 10; // 10km radius
                
                return $query->whereRaw("
                    ST_Distance_Sphere(
                        point(longitude, latitude),
                        point(?, ?)
                    ) <= ?
                ", [$lng, $lat, $distance * 1000]);
            })
            ->latest()
            ->get();

        $wasteTypes = WasteTypes::all();
        return view('collectes.create', compact('signals', 'wasteTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->role !== 'supervisor') {
            return redirect()->back()->with('error', 'You do not have permission to create collections.');
        }

        $validated = $request->validate([
            'signal_id' => 'required|exists:signals,id',
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

        $collecte = new Collecte($validated);
        $collecte->user_id = auth()->id();
        $collecte->status = 'planned';
        $collecte->current_contributors = 0;
        $collecte->save();

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
            ->with('success', 'Collection created successfully.');
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
} 