<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use App\Models\Media;
use App\Models\WasteTypes;
use App\Services\SignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
                'volume' => 'required|numeric|min:0',
                'customType' => 'nullable|string',
                'description' => 'nullable|string|max:1000',
            ]);

            // Filter out any null values and ensure we have an array of integers
            $wasteTypes = array_values(array_filter(array_map('intval', $validated['waste_types'] ?? [])));

            if (empty($wasteTypes)) {
                throw new \Exception('At least one waste type must be selected');
            }

            // Prepare data for creation
            $signalData = [
                'created_by' => $user->id,
                'location' => $validated['location'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'volume' => $validated['volume'],
                'custom_type' => $validated['customType'] ?? '',
                'description' => $validated['description'] ?? null,
                'signal_date' => now(),
                'waste_types' => $wasteTypes
            ];

            // Log the data we're about to save
            Log::info('Attempting to create signal with data:', $signalData);

            // Create signal with DB transaction
            DB::beginTransaction();
            try {
                $signal = Signal::create($signalData);
                Log::info('Signal created:', $signal->toArray());

                // Attach waste types using the pivot table
                if (!empty($wasteTypes)) {
                    $signal->wasteTypes()->attach($wasteTypes);
                    Log::info('Waste types attached:', ['signal_id' => $signal->id, 'types' => $wasteTypes]);
                }

                // Handle media uploads
                if ($request->hasFile('media')) {
                    Log::info('Media files received:', [
                        'count' => count($request->file('media')),
                        'files' => array_map(function($file) {
                            return [
                                'name' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType(),
                                'size' => $file->getSize(),
                                'error' => $file->getError(),
                                'is_valid' => $file->isValid()
                            ];
                        }, $request->file('media'))
                    ]);

                    foreach ($request->file('media') as $file) {
                        try {
                            Log::info('Processing individual file:', [
                                'name' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType(),
                                'size' => $file->getSize(),
                                'error' => $file->getError(),
                                'is_valid' => $file->isValid()
                            ]);

                            // Store the file in storage/app/signals_media directory
                            $filePath = $file->storeAs('signals_media', $file->getClientOriginalName(), 'public');
                            
                            // Get the full storage path
                            $fullPath = Storage::path('public/' . $filePath);
                            Log::info('Attempting to store file:', [
                                'original_name' => $file->getClientOriginalName(),
                                'storage_path' => $filePath,
                                'full_path' => $fullPath,
                                'disk' => config('filesystems.default'),
                                'exists_before' => Storage::disk('public')->exists($filePath)
                            ]);
                            
                            // Verify file exists after storage
                            if (!Storage::disk('public')->exists($filePath)) {
                                throw new \Exception("File was not stored successfully at path: {$filePath}");
                            }
                            
                            // Create media record
                            $media = Media::create([
                                'signal_id' => $signal->id,
                                'media_type' => $file->getMimeType(),
                                'file_path' => $filePath,
                            ]);
                            
                            Log::info('Media file stored successfully:', [
                                'signal_id' => $signal->id,
                                'media_id' => $media->id,
                                'file_path' => $filePath,
                                'mime_type' => $file->getMimeType(),
                                'storage_path' => $fullPath,
                                'media_record' => $media->toArray()
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Error storing media file:', [
                                'error' => $e->getMessage(),
                                'file_name' => $file->getClientOriginalName(),
                                'signal_id' => $signal->id,
                                'trace' => $e->getTraceAsString()
                            ]);
                            throw $e;
                        }
                    }
                    Log::info('Media files processing completed for signal ID: ' . $signal->id);
                    
                    // Manually trigger AI analysis after all media has been attached
                    $signal->triggerAiAnalysisAfterMediaAttached();
                } else {
                    Log::info('No media files received in request');
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
        // Check if user owns the signal
        if ($signal->created_by !== auth()->id()) {
            return redirect()->route('signal.index')
                ->with('error', 'You can only delete your own reports.');
        }

        // Check if signal has a collection
        if ($signal->collecte) {
            return redirect()->route('signal.index')
                ->with('error', 'This report cannot be deleted as it is part of a collection.');
        }

        try {
            // Delete associated media files first
            foreach ($signal->media as $media) {
                Storage::delete('public/' . $media->file_path);
                $media->delete();
            }

            // Delete the signal
            $signal->delete();

            return redirect()->route('signal.index')
                ->with('success', 'Report deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('signal.index')
                ->with('error', 'An error occurred while deleting the report.');
        }
    }

    public function edit(Signal $signal)
    {
        // Check if user owns the signal
        if ($signal->created_by !== auth()->id()) {
            return redirect()->route('signal.index')
                ->with('error', 'You can only edit your own reports.');
        }

        // Check if signal has a collection
        if ($signal->collecte) {
            return redirect()->route('signal.index')
                ->with('error', 'This report cannot be edited as it is part of a collection.');
        }

        $wasteTypes = WasteTypes::orderBy('name')->get();
        return view('signal.edit', compact('signal', 'wasteTypes'));
    }

    public function update(Request $request, Signal $signal)
    {
        // Check if user owns the signal
        if ($signal->created_by !== auth()->id()) {
            return redirect()->route('signal.index')
                ->with('error', 'You can only edit your own reports.');
        }

        // Check if signal has a collection
        if ($signal->collecte) {
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