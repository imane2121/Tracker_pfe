<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use App\Models\Media;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignalController extends Controller
{
    public function create()
    {
        // Get waste types to show in the form
        $wasteTypes = WasteTypes::whereNull('parent_id')->get(); // General types
        return view('signal.create', compact('wasteTypes'));
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
            'waste_types' => 'required|array',
            'waste_types.*' => 'exists:waste_types,id',
            'media.*' => 'file|mimes:jpg,jpeg,png,mp4|max:10240', // Photos and videos
        ]);

        // Create signal
        $signal = Signal::create([
            'user_id' => auth()->id(), // Contributor, Supervisor, or Admin
            'location' => $validated['location'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'wasteTypes' => json_encode($validated['waste_types']),
            'status' => 'pending',
            'signalDate' => now(),
            'anomalyFlag' => false,
        ]);

        // Attach waste types (many-to-many)
        $signal->wasteTypes()->attach($validated['waste_types']);

        // Handle media uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $filePath = $file->store('signals_media'); // Store file in signals_media folder
                Media::create([
                    'signal_id' => $signal->id,
                    'media_type' => $file->getMimeType(),
                    'file_path' => $filePath,
                ]);
            }
        }

        return redirect()->route('signal.index')->with('success', 'Signal created successfully!');
    }

    // Show list of signals (for Admin, Supervisor, Contributor)
    public function index()
    {
        $signals = Signal::with('media')->get();
        $wasteTypes = WasteTypes::whereNull('parent_id')->get(); // Fetch waste types
    
        return view('signal.index', compact('signals', 'wasteTypes'));
    }
    
}
