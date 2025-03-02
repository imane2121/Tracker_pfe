<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use App\Models\Media;
use App\Models\WasteTypes;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    public function create()
    {
        // Get waste types to show in the form, including specific waste types
        $wasteTypes = WasteTypes::with('specificWasteTypes')->whereNull('parent_id')->get();

        return view('signal.create', compact('wasteTypes'));
    }

    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'waste_types' => 'array',
            'waste_types.*' => 'exists:waste_types,id',
            'general_waste_type' => 'array',
            'general_waste_type.*' => 'exists:waste_types,id',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4|max:10240',
            'volume' => 'required|numeric',
            'customType' => 'nullable|string',
            'description' => 'nullable|string|max:1000',
        ]);

        // Combine general and specific waste types
        $allWasteTypes = array_merge(
            array_filter($validated['general_waste_type'] ?? []), // Remove empty values
            $validated['waste_types'] ?? []
        );

        // Create signal
        $signal = Signal::create([
            'created_by' => auth()->id(),
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'wasteTypes' => json_encode($allWasteTypes),
            'volume' => $validated['volume'],
            'customType' => $validated['customType'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
            'signalDate' => now(),
            'anomalyFlag' => false,
        ]);

        // Attach waste types (many-to-many)
        if (!empty($allWasteTypes)) {
            $signal->wasteTypes()->attach($allWasteTypes);
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
        }

        return redirect()->route('signal.thank-you');
    }

    // Show list of signals (for Admin, Supervisor, Contributor)
    public function index()
    {
        $signals = Signal::with('media')->get();
        $wasteTypes = WasteTypes::whereNull('parent_id')->get(); // Fetch waste types

        return view('signal.index', compact('signals', 'wasteTypes'));
    }

    // Show thank you page after signal submission
    public function thankYou()
    {
        return view('signal.thank-you');
    }
}