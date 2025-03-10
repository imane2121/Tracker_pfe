@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="welcome-title d-flex align-items-center">
                <i class="bi bi-person-circle me-2"></i>
                Welcome, {{ Auth::user()->first_name }}!
            </h2>
        </div>
    </div>

    <!-- Quick Actions and Stats Row -->
    <div class="row g-3 mb-4">
        <!-- Quick Actions Card -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('signal.create') }}" class="btn btn-primary btn-lg mb-3">
                            <i class="bi bi-plus-circle"></i> Report Marine Waste
                        </a>
                        <a href="{{ route('collecte.create') }}" class="btn btn-success btn-lg mb-3">
                            <i class="bi bi-people"></i> Create Collecte
                        </a>
                        <a href="{{ route('collecte.index') }}" class="btn btn-outline-success mb-3">
                            <i class="bi bi-list-ul"></i> View Collectes
                        </a>
                        <a href="{{ route('signal.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul"></i> View My Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Statistics Card -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Your Impact</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <h3 class="text-primary mb-0">{{ Auth::user()->signals()->count() }}</h3>
                                <small class="text-muted">Total Reports</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <h3 class="text-success mb-0">{{ Auth::user()->signals()->where('status', 'validated')->count() }}</h3>
                                <small class="text-muted">Validated</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <h3 class="text-info mb-0">{{ Auth::user()->credibility_score ?? 100 }}</h3>
                                <small class="text-muted">Trust Score</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded bg-light">
                                <h3 class="text-warning mb-0">{{ Auth::user()->points ?? 0 }}</h3>
                                <small class="text-muted">Points</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Card -->
        <div class="col-12 col-md-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        @forelse(Auth::user()->signals()->latest()->take(5)->get() as $signal)
                            <div class="activity-item p-2 mb-2 rounded hover-bg">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'rejected' ? 'danger' : 'warning') }} me-2">
                                        {{ ucfirst($signal->status) }}
                                    </span>
                                    <small class="text-muted ms-auto">{{ $signal->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-primary"></i>
                                    {{ $signal->location }}
                                </p>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-inbox text-muted fs-2"></i>
                                <p class="text-muted mt-2">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map and Critical Areas Row -->
    <div class="row g-3">
        <!-- Interactive Map -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Marine Waste Map</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-light active" id="mapViewBtn">
                            <i class="bi bi-map"></i> Map
                        </button>
                        <button class="btn btn-outline-light" id="satelliteViewBtn">
                            <i class="bi bi-globe"></i> Satellite
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" class="rounded" style="height: 60vh; min-height: 400px;"></div>
                </div>
            </div>
        </div>

        <!-- Critical Areas -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Critical Areas</h5>
                </div>
                <div class="card-body">
                    <div id="heatmap" class="mb-4" style="height: 200px;"></div>
                    <hr>
                    <h6 class="text-muted mb-3">Top Affected Areas</h6>
                    <ul class="list-unstyled">
                        @forelse($topAreas as $area)
                        <li class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <span class="text-dark fw-medium">{{ $area['name'] }}</span>
                                <span class="ms-auto text-muted small">{{ $area['severity'] }}%</span>
                            </div>
                            <div class="progress" style="height: 0.5rem;">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ $area['severity'] }}%"
                                     aria-valuenow="{{ $area['severity'] }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="mt-1">
                                <small class="text-muted">
                                    {{ $area['report_count'] }} reports ({{ $area['total_volume'] }}m³) - Last report {{ $area['latest_report'] }}
                                </small>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted">
                            No critical areas found
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        var map = L.map('map', {
            zoomControl: false
        }).setView([31.7917, -9.5541], 6); // Centered on Morocco

        // Add zoom control to top-right
        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add heatmap layer
        var heatmapPoints = @json($heatmapPoints);
        L.heatLayer(heatmapPoints, {
            radius: 25,
            blur: 15,
            maxZoom: 10
        }).addTo(map);

        // Handle window resize
        window.addEventListener('resize', function() {
            map.invalidateSize();
        });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    .welcome-title {
        color: #2c3e50;
        margin-bottom: 1.5rem;
        font-size: calc(1.2rem + 0.6vw);
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        margin-bottom: 1rem;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .card-header {
        border-bottom: none;
        padding: 1rem;
    }

    .activity-feed {
        max-height: 400px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .activity-feed::-webkit-scrollbar {
        width: 6px;
    }

    .activity-feed::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .activity-feed::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .hover-bg:hover {
        background-color: rgba(0,0,0,0.03);
    }

    .progress {
        height: 0.5rem;
        border-radius: 1rem;
        background-color: rgba(0,0,0,0.05);
    }

    .waste-marker {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .waste-marker i {
        filter: drop-shadow(0 0 1px rgba(0,0,0,0.5));
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        #map {
            height: 50vh !important;
        }

        .activity-feed {
            max-height: 300px;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .bg-light {
            background-color: rgba(255,255,255,0.05) !important;
        }
    }
</style>
@endpush
@endsection 