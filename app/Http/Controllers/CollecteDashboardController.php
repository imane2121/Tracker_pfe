<?php

namespace App\Http\Controllers;

use App\Models\Collecte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollecteDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        $upcomingCollectes = Collecte::with(['signal', 'creator', 'contributors'])
            ->where('status', 'planned')
            ->where('starting_date', '>', now())
            ->latest()
            ->take(5)
            ->get();

        $inProgressCollectes = Collecte::with(['signal', 'creator', 'contributors'])
            ->where('status', 'in_progress')
            ->latest()
            ->take(5)
            ->get();

        $completedCollectes = Collecte::with(['signal', 'creator', 'contributors'])
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $userCollectes = Collecte::where('user_id', $user->id)
            ->orWhereHas('contributors', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        $userContributions = Collecte::whereHas('contributors', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();

        return view('collectes.dashboard', compact(
            'upcomingCollectes',
            'inProgressCollectes',
            'completedCollectes',
            'userCollectes',
            'userContributions'
        ));
    }
} 