<?php

namespace App\Http\Controllers;

use App\Services\CriticalAreaService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $criticalAreaService;

    public function __construct(CriticalAreaService $criticalAreaService)
    {
        $this->middleware('auth');
        $this->criticalAreaService = $criticalAreaService;
    }

    public function index()
    {
        $heatmapPoints = [];
        $topAreas = [];

        if (auth()->user()->isAdmin() || auth()->user()->isSupervisor()) {
            $heatmapPoints = $this->criticalAreaService->getHeatmapPoints();
            $topAreas = $this->criticalAreaService->getTopAffectedAreas();
        }

        return view('dashboard', compact('heatmapPoints', 'topAreas'));
    }
} 