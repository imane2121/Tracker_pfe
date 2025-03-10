<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use App\Models\Signal;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CollecteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $collectes = Collecte::with(['signal', 'creator', 'contributors'])
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('contributors', function($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->latest()
            ->paginate(10);

        return view('collectes.index', compact('collectes'));
    }

    public function create()
    {
        $signals = Signal::where('status', 'validated')
            ->whereDoesntHave('collecte')
            ->get();
        $wasteTypes = WasteTypes::all();
        return view('collectes.create', compact('signals', 'wasteTypes'));
    }

    public function store(Request $request)
    {
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
        $collecte->user_id = Auth::id();
        $collecte->status = 'planned';
        $collecte->current_contributors = 0;
        $collecte->save();

        // Handle media uploads
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
            ->with('success', 'Collecte created successfully!');
    }

    public function show(Collecte $collecte)
    {
        $collecte->load(['signal', 'creator', 'contributors', 'media']);
        return view('collectes.show', compact('collecte'));
    }

    public function edit(Collecte $collecte)
    {
        $this->authorize('update', $collecte);
        $wasteTypes = WasteTypes::all();
        return view('collectes.edit', compact('collecte', 'wasteTypes'));
    }

    public function update(Request $request, Collecte $collecte)
    {
        $this->authorize('update', $collecte);

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

        // Handle media uploads
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
            ->with('success', 'Collecte updated successfully!');
    }

    public function destroy(Collecte $collecte)
    {
        $this->authorize('delete', $collecte);

        // Delete associated media files
        foreach ($collecte->media as $media) {
            Storage::disk('public')->delete($media->file_path);
        }

        $collecte->delete();

        return redirect()->route('collecte.index')
            ->with('success', 'Collecte deleted successfully!');
    }

    /**
     * Join a collecte as a contributor
     */
    public function join(Request $request, Collecte $collecte)
    {
        // Check if the collecte is not full
        if ($collecte->is_full) {
            return back()->with('error', 'This collecte is already full.');
        }

        // Check if user hasn't already joined
        if ($collecte->contributors->contains(Auth::id())) {
            return back()->with('error', 'You have already joined this collecte.');
        }

        // Add user as a contributor
        $collecte->contributors()->attach(Auth::id(), [
            'status' => 'pending',
            'joined_at' => now()
        ]);

        // Increment current contributors count
        $collecte->increment('current_contributors');

        return back()->with('success', 'You have successfully joined the collecte!');
    }

    public function leave(Request $request, Collecte $collecte)
    {
        // Check if user is a contributor
        if (!$collecte->contributors->contains(Auth::id())) {
            return back()->with('error', 'You are not a contributor to this collecte.');
        }

        // Remove user from contributors
        $collecte->contributors()->detach(Auth::id());

        // Decrement current contributors count
        $collecte->decrement('current_contributors');

        return back()->with('success', 'You have successfully left the collecte.');
    }

    public function updateStatus(Request $request, Collecte $collecte)
    {
        $this->authorize('update', $collecte);

        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,completed,validated,cancelled'
        ]);

        $collecte->update($validated);

        return back()->with('success', 'Collecte status updated successfully!');
    }
} 