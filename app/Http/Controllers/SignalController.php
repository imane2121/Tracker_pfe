<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use App\Models\Media;
use App\Models\WasteTypes;
use App\Services\SignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SignalController extends Controller
{
    protected $signalService;

    public function __construct(SignalService $signalService)
    {
        $this->middleware('auth');
        $this->signalService = $signalService;
    }

    public function create()
    {
        // Get waste types to show in the form, including specific waste types
        $wasteTypes = WasteTypes::with('specificWasteTypes')->whereNull('parent_id')->get();

        return view('signal.create', compact('wasteTypes'));
    }

    public function store(Request $request)
    {
        try {
            // Debug authentication
            $user = auth()->user();
            Log::info('Auth check:', [
                'is_authenticated' => auth()->check(),
                'user_id' => $user?->id,
                'user_role' => $user?->role
            ]);

            if (!$user) {
                throw new \Exception('User not authenticated');
            }

            // Log the raw request data
            Log::info('Raw request data:', $request->all());

            // Validate request
            $validated = $request->validate([
                'location' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'waste_types' => 'nullable|array',
                'waste_types.*' => 'nullable|exists:waste_types,id',
                'general_waste_type' => 'nullable|array',
                'general_waste_type.*' => 'nullable|exists:waste_types,id',
                'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
                'volume' => 'required|numeric|min:0',
                'custom_type' => 'nullable|string',
                'description' => 'nullable|string|max:1000',
            ]);

            // Combine and filter waste types
            $generalTypes = array_filter($validated['general_waste_type'] ?? []);
            $specificTypes = array_filter($validated['waste_types'] ?? []);
            $allWasteTypes = array_values(array_unique(array_merge($generalTypes, $specificTypes)));

            // Log waste types data
            Log::info('Waste types:', [
                'general' => $generalTypes,
                'specific' => $specificTypes,
                'combined' => $allWasteTypes
            ]);

            if (empty($allWasteTypes)) {
                throw new \Exception('At least one waste type must be selected');
            }

            // Prepare data for creation
            $signalData = [
                'created_by' => $user->id,
                'location' => $validated['location'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'volume' => $validated['volume'],
                'custom_type' => $validated['custom_type'] ?? '',
                'description' => $validated['description'] ?? null,
                'signal_date' => now(),
                'waste_types' => json_encode($allWasteTypes)
            ];

            // Log the data we're about to save
            Log::info('Attempting to create signal with data:', $signalData);

            // Create signal with DB transaction
            DB::beginTransaction();
            try {
                $signal = Signal::create($signalData);
                Log::info('Signal created:', $signal->toArray());

                // Attach waste types using the pivot table
                if (!empty($allWasteTypes)) {
                    $signal->wasteTypes()->attach($allWasteTypes);
                    Log::info('Waste types attached:', ['signal_id' => $signal->id, 'types' => $allWasteTypes]);
                }

                // Handle media uploads
                if ($request->hasFile('media')) {
                    foreach ($request->file('media') as $file) {
                        $filePath = $file->store('signals_media');
                        Media::create([
                            'signal_id' => $signal->id,
                            'media_type' => $file->getMimeType(),
                            'file_path' => $filePath,
                        ]);
                    }
                    Log::info('Media files processed for signal ID: ' . $signal->id);
                }

                DB::commit();

                if ($signal->status === 'rejected') {
                    return redirect()->route('signal.index')
                        ->with('warning', 'Your report has been submitted but has been flagged for review due to unusual travel time between locations.');
                }

                return redirect()->route('signal.thank-you');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error creating signal: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création du signal. Veuillez réessayer.']);
        }
    }

    // Show list of signals (for Admin, Supervisor, Contributor)
    public function index()
    {
        try {
            $signals = Signal::with(['media', 'wasteTypes'])
                ->where('created_by', auth()->id())
                ->latest()
                ->get();

            // Debug log
            $firstSignal = $signals->first();
            Log::info('Signals retrieved:', [
                'count' => $signals->count(),
                'first_signal' => $firstSignal ? [
                    'id' => $firstSignal->id,
                    'waste_types_count' => $firstSignal->wasteTypes()->count()
                ] : null
            ]);

            $wasteTypes = WasteTypes::whereNull('parent_id')->get();

            return view('signal.index', compact('signals', 'wasteTypes'));
        } catch (\Exception $e) {
            Log::error('Error in signals index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return back()->withErrors(['error' => 'An error occurred while loading your reports.']);
        }
    }

    // Show thank you page after signal submission
    public function thankYou()
    {
        return view('signal.thank-you');
    }

    public function destroy(Signal $signal)
    {
        try {
            // Check if the user owns this signal
            if ($signal->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            // Check if the signal has a collection
            if ($signal->collecte) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete a signal that is associated with a collection.'
                ], 400);
            }

            // Delete the signal
            $signal->delete();

            return response()->json([
                'success' => true,
                'message' => 'Signal deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting signal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting signal.'
            ], 500);
        }
    }

    public function edit(Signal $signal)
    {
        // Check if user owns the signal
        if ($signal->creator_id !== auth()->id()) {
            return redirect()->route('signal.index')
                ->with('error', 'You can only edit your own reports.');
        }

        // Check if signal has a collection
        if ($signal->collection) {
            return redirect()->route('signal.index')
                ->with('error', 'This report cannot be edited as it is part of a collection.');
        }

        $wasteTypes = WasteTypes::orderBy('name')->get();
        return view('signal.edit', compact('signal', 'wasteTypes'));
    }

    public function update(Request $request, Signal $signal)
    {
        // Check if user owns the signal
        if ($signal->creator_id !== auth()->id()) {
            return redirect()->route('signal.index')
                ->with('error', 'You can only edit your own reports.');
        }

        // Check if signal has a collection
        if ($signal->collection) {
            return redirect()->route('signal.index')
                ->with('error', 'This report cannot be edited as it is part of a collection.');
        }

        $validated = $request->validate([
            'volume' => 'required|numeric|min:0',
            'waste_types' => 'required|array|min:1',
            'waste_types.*' => 'exists:waste_types,id',
            'custom_type' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $signal->update([
                'volume' => $validated['volume'],
                'custom_type' => $validated['custom_type'],
                'description' => $validated['description'],
            ]);

            // Sync waste types
            $signal->wasteTypes()->sync($validated['waste_types']);

            DB::commit();
            return redirect()->route('signal.index')
                ->with('success', 'Report updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the report.')
                ->withInput();
        }
    }
}