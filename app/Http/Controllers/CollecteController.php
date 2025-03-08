<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollecteController extends Controller
{
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
} 