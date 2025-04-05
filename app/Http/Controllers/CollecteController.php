<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\Signal;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\CriticalAreaService;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Messaging\ChatRoomController;
use App\Models\RegionSubscription;
use App\Notifications\NewCollectionInRegion;
use App\Models\City;

/**
 * @modified Added automatic chat room creation for non-urgent collectes
 */
class CollecteController extends Controller
{
    public function __construct()
    {
        // Basic auth check for all methods
        $this->middleware('auth');
        
        // Only admin and owner (supervisor) can edit/delete their own collections
        $this->middleware('role:admin,supervisor')->only(['edit', 'update', 'destroy', 'updateStatus']);
    }

    public function index(Request $request)
    {
        $query = Collecte::with(['creator', 'contributors']);
        $user = auth()->user();

        // For contributors, only show planned collectes
        if ($user->role === 'contributor') {
            $query->where('status', 'planned');
        }

        // Handle search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                  ->orWhere('region', 'like', "%{$search}%");
            });
        }

        // Handle status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Handle sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'participants':
                $query->orderBy('current_contributors', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $collectes = $query->paginate(9);

        return view('collectes.index', compact('collectes'));
    }


    public function cluster()
    {
        // Get all signal IDs used in collectes (from the JSON signal_ids field)
        $usedSignalIds = DB::table('collectes')
            ->whereNotNull('signal_ids')
            ->pluck('signal_ids')
            ->flatMap(function ($ids) {
                return json_decode($ids, true);
            })
            ->unique()
            ->toArray();
    
        // Get only signals that are not used in any collecte
        $signals = Signal::whereNotIn('id', $usedSignalIds)
            ->whereIn('status', ['validated', 'pending'])
            ->get();
    
        return view('collectes.cluster', compact('signals'));
    }
    

    public function create(Request $request)
    {
        $isUrgent = $request->query('type') === 'urgent';
        $signals = null;
        
        // Set center coordinates from request or default
        $centerLat = $request->query('lat', 31.7917);
        $centerLng = $request->query('lng', -7.0926);
        
        if (!$isUrgent && $request->has('signals')) {
            $signalIds = explode(',', $request->signals);
            $signals = Signal::with('wasteTypes')->whereIn('id', $signalIds)->get();
        }

        // Get unique regions from cities table
        $regions = City::distinct()->pluck('region')->sort()->values();
        
        $wasteTypes = WasteTypes::all();

        return view('collectes.create', compact('wasteTypes', 'isUrgent', 'centerLat', 'centerLng', 'signals', 'regions'));
    }

    private function getRegionFromCoordinates($lat, $lng)
    {
        // Implement geocoding logic here to get region from coordinates
        // You can use services like OpenStreetMap Nominatim or Google Geocoding API
    }

    public function store(Request $request)
    {
        try {
            $collecte = new Collecte();
            $collecte->location = $request->location;
            $collecte->description = $request->description;
            $collecte->starting_date = $request->starting_date;
            $collecte->end_date = $request->end_date;
            $collecte->actual_waste_types = array_map('intval', $request->waste_types);
            $collecte->nbrContributors = $request->nbrContributors;
            $collecte->current_contributors = 0;
            $collecte->status = 'planned';
            $collecte->user_id = auth()->id();
            $collecte->actual_volume = $request->actual_volume;
            $collecte->latitude = $request->latitude;
            $collecte->longitude = $request->longitude;
            $collecte->region = $request->region;
            
            // Convert signal_ids to integers before saving
            if (!$request->is_urgent && $request->signal_ids) {
                $signalIds = json_decode($request->signal_ids, true);
                $collecte->signal_ids = array_map('intval', $signalIds);
            }
            
            $collecte->saveOrFail();

            // Handle media files upload
            if ($request->hasFile('media')) {
                foreach ($request->file('media') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('collecte-media', 'public');
                        $collecte->media()->create([
                            'file_path' => $path,
                            'media_type' => $file->getClientMimeType()
                        ]);
                    }
                }
            }

            // Send notifications to subscribed users
            $subscribedUsers = RegionSubscription::where('region', $collecte->region)
                ->where(function($query) {
                    $query->where('email_notifications', true)
                          ->orWhere('push_notifications', true);
                })
                ->with('user')
                ->get();

            foreach ($subscribedUsers as $subscription) {
                $user = $subscription->user;
                if ($user->id !== auth()->id()) { // Don't notify the creator
                    $user->notify(new NewCollectionInRegion($collecte));
                }
            }

            // Add this block to create chat room automatically
            if (!$collecte->is_urgent) {
                app(ChatRoomController::class)->create($collecte);
            }

            return redirect()->route('collecte.show', $collecte)
                ->with('success', 'Collection created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Collecte $collecte)
    {
        // Get all waste types
        $wasteTypes = WasteTypes::all();
        
        $collecte->load(['signal', 'creator', 'contributors', 'media']);
        return view('collectes.show', compact('collecte', 'wasteTypes'));
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

        // Map waste_types to actual_waste_types in the validated data
        $validated['actual_waste_types'] = array_map('intval', (array)$validated['waste_types']);
        unset($validated['waste_types']); // Remove the original waste_types key

        $collecte->update($validated);

        // Add this block to create chat room if needed
        if (!$collecte->is_urgent && !$collecte->chatRoom) {
            app(ChatRoomController::class)->create($collecte);
        }

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
        try {
            DB::beginTransaction();
            
            // Join the collecte
            $collecte->contributors()->attach(auth()->id(), [
                'joined_at' => now()
            ]);
            
            // Increment contributors count
            $collecte->increment('current_contributors');
            
            // If collecte has a chat room, add user as participant
            if ($chatRoom = $collecte->chatRoom) {
                $chatRoom->addParticipant(auth()->user(), 'participant');
            }
            
            DB::commit();
            
            return redirect()->route('collectes.show', $collecte)
                ->with('success', 'Successfully joined the collection.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to join the collection. Please try again.');
        }
    }

    public function leave(Collecte $collecte)
    {
        try {
            DB::beginTransaction();
            
            // Remove from collecte
        $collecte->contributors()->detach(auth()->id());
            
            // Decrement contributors count
        $collecte->decrement('current_contributors');

            // If collecte has a chat room, remove user from participants
            if ($chatRoom = $collecte->chatRoom) {
                $chatRoom->removeParticipant(auth()->user());
            }
            
            DB::commit();
            
            return redirect()->route('collectes.index')
                ->with('success', 'Successfully left the collection.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to leave the collection. Please try again.');
        }
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