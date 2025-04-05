<?php

namespace App\Http\Controllers;

use App\Models\Rapport;
use App\Models\Collecte;
use App\Models\WasteTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use PDF;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    // Constructor for any middleware if needed
    public function __construct()
    {
        // Add any middleware here if needed
        $this->middleware('auth');
    }

    public function generate(Collecte $collecte)
    {
        // Check if user is authorized (supervisor of this collecte or admin)
        if (!auth()->user()->isAdmin() && !(auth()->user()->isSupervisor() && $collecte->user_id === auth()->id())) {
            return redirect()->back()->with('error', 'You are not authorized to generate a report.');
        }

        // Check if rapport already exists
        $existingRapport = Rapport::where('collecte_id', $collecte->id)->first();
        if ($existingRapport) {
            return redirect()->route('rapport.edit', $collecte)
                ->with('info', 'A rapport already exists for this collection.');
        }

        // Check if the status is being changed from in_progress
        if ($collecte->status !== 'in_progress') {
            return redirect()->back()
                ->with('error', 'Collection must be in progress before completing and generating a report.');
        }

        // Get all waste types for the form
        $wasteTypes = WasteTypes::all();

        return view('rapports.generate', compact('collecte', 'wasteTypes'));
    }

    public function store(Request $request, Collecte $collecte)
    {
        // Simplified authorization check
        if (!auth()->user()->isAdmin() && !(auth()->user()->isSupervisor() && $collecte->user_id === auth()->id())) {
            return redirect()->back()->with('error', 'You are not authorized to generate a report.');
        }

        // Add debugging information
        \Log::info('User attempting to store rapport:', [
            'user_id' => auth()->id(),
            'is_admin' => auth()->user()->isAdmin(),
            'is_supervisor' => auth()->user()->isSupervisor(),
            'collecte_user_id' => $collecte->user_id
        ]);

        // Validate request
        $validated = $request->validate([
            'description' => 'required|string',
            'volume' => 'required|numeric|min:0',
            'waste_types' => 'required|array',
            'nbrContributors' => 'required|integer|min:1',
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'starting_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:starting_date',
            'participants' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            // Create the rapport
            $rapport = new Rapport([
                'collecte_id' => $collecte->id,
                'supervisor_id' => auth()->id(),
                'description' => $validated['description'],
                'volume' => $validated['volume'],
                'waste_types' => array_map('intval', $validated['waste_types']),
                'nbrContributors' => $validated['nbrContributors'],
                'location' => $validated['location'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'starting_date' => $validated['starting_date'],
                'end_date' => $validated['end_date'],
                'participants' => array_map('intval', $validated['participants'])
            ]);

            if ($rapport->save()) {
                // Update collecte status to completed
                $collecte->update([
                    'status' => 'completed'
                ]);

                DB::commit();
                return redirect()->route('collecte.show', $collecte)
                    ->with('success', 'Rapport generated and collection marked as completed successfully.');
            }

            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to generate rapport. Please try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Rapport creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while generating the rapport: ' . $e->getMessage());
        }
    }

    public function edit(Collecte $collecte)
    {
        // Check if user is authorized to edit rapport
        if (!Auth::user()->isSupervisor() || Auth::id() !== $collecte->user_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Get the rapport for this collecte
        $rapport = Rapport::where('collecte_id', $collecte->id)->firstOrFail();
        
        // Get all waste types for the form
        $wasteTypes = WasteTypes::all();

        return view('rapports.generate', compact('collecte', 'wasteTypes', 'rapport'));
    }

    public function update(Request $request, Collecte $collecte)
    {
        try {
            $validated = $request->validate([
                'collecte_id' => 'required|exists:collectes,id',
                'supervisor_id' => 'required|exists:users,id',
                'description' => 'required|string',
                'volume' => 'required|numeric|min:0',
                'waste_types' => 'required|array',
                'participants' => 'array|nullable',
                'participants.*' => 'exists:users,id',
                'nbrContributors' => 'required|integer|min:1',
                'location' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'starting_date' => 'required|date',
                'end_date' => 'required|date|after:starting_date',
            ]);

            // Find the rapport by collecte_id
            $rapport = Rapport::where('collecte_id', $collecte->id)->first();

            if (!$rapport) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Rapport not found for this collecte.');
            }

            // Ensure participants is an array, even if empty
            $validated['participants'] = $request->has('participants')
                ? array_map('intval', (array)$request->participants)
                : [];

            // Convert waste_types to integers
            $validated['waste_types'] = array_map('intval', (array)$validated['waste_types']);

            $rapport->update($validated);

            return redirect()->route('collecte.show', $collecte)
                ->with('success', 'Report updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to update report: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update report: ' . $e->getMessage());
        }
    }

    public function export(Collecte $collecte, Request $request)
    {
        $rapport = Rapport::where('collecte_id', $collecte->id)->first();
        
        if (!$rapport) {
            return redirect()->back()->with('error', 'No rapport found for this collection.');
        }

        // Authorization check
        if (!auth()->user()->isAdmin() && !(auth()->user()->isSupervisor() && $collecte->user_id === auth()->id())) {
            return redirect()->back()->with('error', 'You are not authorized to export this rapport.');
        }

        $format = $request->query('format', 'pdf');

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="rapport-'.$collecte->id.'.csv"',
            ];

            $callback = function() use ($rapport, $collecte) {
                $file = fopen('php://output', 'w');
                
                // Set UTF-8 encoding for Excel
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Title and Generation Date
                fputcsv($file, ['Collection Report Details']);
                fputcsv($file, ['Generated Date:', now()->format('F d, Y')]);
                fputcsv($file, []); // Empty row for spacing

                // Basic Information
                fputcsv($file, ['Basic Information']);
                fputcsv($file, ['Field', 'Value']); // Column headers
                fputcsv($file, ['Location', $rapport->location]);
                fputcsv($file, ['Region', $collecte->region]);
                fputcsv($file, ['Supervisor', $rapport->supervisor->first_name . ' ' . $rapport->supervisor->last_name]);
                fputcsv($file, ['Total Volume', $rapport->volume . ' m³']);
                fputcsv($file, ['Number of Contributors', $rapport->nbrContributors]);
                fputcsv($file, ['Coordinates', $rapport->latitude . ', ' . $rapport->longitude]);
                fputcsv($file, []); // Empty row for spacing

                // Dates
                fputcsv($file, ['Collection Period']);
                fputcsv($file, ['Event', 'Date and Time']); // Column headers
                fputcsv($file, ['Start Date', $rapport->starting_date->format('M d, Y H:i')]);
                fputcsv($file, ['End Date', $rapport->end_date->format('M d, Y H:i')]);
                fputcsv($file, []); // Empty row for spacing

                // Description
                fputcsv($file, ['Collection Description']);
                fputcsv($file, ['Description:', $rapport->description]);
                fputcsv($file, []); // Empty row for spacing
                
                // Waste Types
                fputcsv($file, ['Waste Types Collected']);
                fputcsv($file, ['Type ID', 'Waste Type Name']); // Column headers
                foreach($rapport->waste_types as $wasteTypeId) {
                    $wasteType = \App\Models\WasteTypes::find($wasteTypeId);
                    if($wasteType) {
                        fputcsv($file, [$wasteTypeId, $wasteType->name]);
                    }
                }
                fputcsv($file, []); // Empty row for spacing
                
                // Participants
                fputcsv($file, ['Attended Contributors']);
                fputcsv($file, ['ID', 'First Name', 'Last Name', 'Phone Number']); // Column headers
                foreach($rapport->participants as $participantId) {
                    $participant = \App\Models\User::find($participantId);
                    if($participant) {
                        fputcsv($file, [
                            $participant->id,
                            $participant->first_name,
                            $participant->last_name,
                            $participant->phone_number ?? 'N/A'
                        ]);
                    }
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Default to PDF
        $pdf = PDF::loadView('rapports.pdf', [
            'rapport' => $rapport,
            'collecte' => $collecte
        ]);

        return $pdf->download('rapport-' . $collecte->id . '.pdf');
    }

    // Functions will be added here later as per your requirements
}