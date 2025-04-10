@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-banner p-4 rounded-4 bg-gradient position-relative overflow-hidden">
                <div class="position-relative z-2">
                    <h2 class="welcome-title text-white mb-2 d-flex align-items-center">
                        <i class="bi bi-person-circle me-2"></i>
                        Welcome back, {{ Auth::user()->first_name }}! Your contribution matters!
                    </h2>
                    <p class="text-white-50 mb-0">
                        @if(Auth::user()->isAdmin())
                            Manage and oversee marine waste reports and collection activities.
                        @else
                            Track, report, and help clean our oceans. Your contribution matters!
                        @endif
                    </p>
                </div>
                <div class="welcome-decoration"></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and Stats Row -->
    <div class="row g-4 mb-4">
        <!-- Quick Actions Card -->
        <div class="col-12 {{ Auth::user()->isAdmin() ? 'col-md-4' : 'col-md-6 col-lg-4' }}">
            <div class="card h-100 border-0 shadow-sm rounded-4">
                <div class="card-header bg-gradient-primary text-white rounded-top-4">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Quick Actions</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-3">
                        @if(Auth::user()->isAdmin())
                            <!-- Admin-specific actions -->
                            <a href="{{ route('admin.signals.index') }}" class="btn btn-primary btn-lg rounded-3 shadow-sm hover-lift">
                                <i class="bi bi-flag"></i> Manage Reports
                            </a>
                            <a href="{{ route('collecte.cluster') }}" class="btn btn-success btn-lg rounded-3 shadow-sm hover-lift">
                                <i class="bi bi-people"></i> Create Collection
                            </a>                           
                            <a href="{{ route('collecte.create', ['type' => 'urgent']) }}" class="btn btn-danger">
                                <i class="bi bi-people"></i> Create Urgent Collection
                            </a>
                            <div class="d-flex gap-3">
                                <a href="{{ route('collecte.index') }}" class="btn btn-outline-success rounded-3 flex-grow-1 hover-lift">
                                    <i class="bi bi-list-ul"></i> View Collections
                                </a>
                                <a href="{{ route('admin.signals.anomalies') }}" class="btn btn-outline-danger rounded-3 flex-grow-1 hover-lift">
                                    <i class="bi bi-exclamation-triangle"></i> Anomalies
                                </a>
                            </div>
                            <a href="{{ route('admin.contact.index') }}" class="btn btn-outline-info rounded-3 shadow-sm hover-lift">
                                <i class="bi bi-envelope"></i> Contact Messages
                            </a>
                        @else
                            <!-- Regular user actions -->
                            <a href="{{ route('signal.create') }}" class="btn btn-primary btn-lg rounded-3 shadow-sm hover-lift">
                                <i class="bi bi-plus-circle"></i> Report Marine Waste
                            </a>
                            
                            @if(auth()->user()->role === 'supervisor')
                                <a href="{{ route('collecte.cluster') }}" class="btn btn-success btn-lg rounded-3 shadow-sm hover-lift">
                                            <i class="bi bi-plus-circle"></i> Create Collection
                                        </a>
                                <a href="{{ route('collecte.create', ['type' => 'urgent']) }}" class="btn btn-danger btn-lg rounded-3 shadow-sm hover-lift">
                                            <i class="bi bi-exclamation-triangle"></i> Create Urgent Collection
                                        </a>
                            @endif
                            
                            <div class="d-flex gap-3">
                                <a href="{{ route('collecte.index') }}" class="btn btn-outline-success rounded-3 flex-grow-1 hover-lift">
                                    <i class="bi bi-list-ul"></i> View Collections
                                </a>
                                <a href="{{ route('signal.index') }}" class="btn btn-outline-primary rounded-3 flex-grow-1 hover-lift">
                                    <i class="bi bi-list-ul"></i> My Reports
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if(Auth::user()->isAdmin())
            <!-- Admin Statistics Card -->
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-gradient-success text-white rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> System Statistics</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-primary-subtle rounded-circle mb-2">
                                        <i class="bi bi-flag text-primary"></i>
                                    </div>
                                    <h3 class="text-primary mb-1">{{ \App\Models\Signal::count() }}</h3>
                                    <small class="text-muted">Total Reports</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-success-subtle rounded-circle mb-2">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                    <h3 class="text-success mb-1">{{ \App\Models\Signal::where('status', 'validated')->count() }}</h3>
                                    <small class="text-muted">Validated Reports</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-warning-subtle rounded-circle mb-2">
                                        <i class="bi bi-clock text-warning"></i>
                                    </div>
                                    <h3 class="text-warning mb-1">{{ \App\Models\Signal::where('status', 'pending')->count() }}</h3>
                                    <small class="text-muted">Pending Reports</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-danger-subtle rounded-circle mb-2">
                                        <i class="bi bi-exclamation-triangle text-danger"></i>
                                    </div>
                                    <h3 class="text-danger mb-1">{{ \App\Models\Signal::where('status', 'rejected')->count() }}</h3>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Activity Card for Admin -->
            <div class="col-12 col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-gradient-info text-white rounded-top-4">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history"></i> 
                            Recent System Activity
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="activity-feed">
                            @forelse(\App\Models\Signal::latest()->take(5)->get() as $signal)
                                <div class="activity-item p-3 mb-3 rounded-3 bg-light hover-lift">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'rejected' ? 'danger' : 'warning') }} rounded-pill">
                                            {{ ucfirst($signal->status) }}
                                        </span>
                                        <small class="text-muted ms-auto">{{ $signal->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">
                                        <i class="bi bi-person text-primary"></i>
                                        {{ $signal->creator->first_name }} {{ $signal->creator->last_name }}
                                    </p>
                                    <p class="mb-0 text-truncate">
                                        <i class="bi bi-geo-alt-fill text-primary"></i>
                                        {{ $signal->location }}
                                    </p>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="bi bi-inbox text-muted"></i>
                                    </div>
                                    <p class="text-muted">No recent activity in the system</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Personal Statistics Card - Not shown for admin -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-gradient-success text-white rounded-top-4">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Your Impact</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-primary-subtle rounded-circle mb-2">
                                        <i class="bi bi-flag text-primary"></i>
                                    </div>
                                    <h3 class="text-primary mb-1">{{ Auth::user()->signals()->count() }}</h3>
                                    <small class="text-muted">Total Reports</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-success-subtle rounded-circle mb-2">
                                        <i class="bi bi-check-circle text-success"></i>
                                    </div>
                                    <h3 class="text-success mb-1">{{ Auth::user()->signals()->where('status', 'validated')->count() }}</h3>
                                    <small class="text-muted">Validated</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-info-subtle rounded-circle mb-2">
                                        <i class="bi bi-shield-check text-info"></i>
                                    </div>
                                    <h3 class="text-info mb-1">{{ Auth::user()->credibility_score ?? 100 }}</h3>
                                    <small class="text-muted">Trust Score</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-card bg-light rounded-4 p-3 text-center hover-lift">
                                    <div class="stat-icon bg-warning-subtle rounded-circle mb-2">
                                        <i class="bi bi-star text-warning"></i>
                                    </div>
                                    <h3 class="text-warning mb-1">{{ Auth::user()->points ?? 0 }}</h3>
                                    <small class="text-muted">Points</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Recent Activity Card for Supervisor -->
            <div class="col-12 col-md-12 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-gradient-info text-white rounded-top-4">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history"></i> 
                            Recent Activity
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="activity-feed">
                            @forelse(Auth::user()->signals()->latest()->take(5)->get() as $signal)
                                <div class="activity-item p-3 mb-3 rounded-3 bg-light hover-lift">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'rejected' ? 'danger' : 'warning') }} rounded-pill">
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
                                <div class="text-center py-5">
                                    <div class="empty-state-icon mb-3">
                                        <i class="bi bi-inbox text-muted"></i>
                                    </div>
                                    <p class="text-muted">No recent activity yet</p>
                                    <a href="{{ route('signal.create') }}" class="btn btn-sm btn-primary rounded-pill">
                                        Make Your First Report
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>


</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize critical areas map
        var criticalAreasMap = L.map('critical-areas-map', {
            zoomControl: false,
            dragging: true,
            scrollWheelZoom: true
        }).setView([31.7917, -7.0926], 6);

        // Add zoom control to top-right
        L.control.zoom({
            position: 'topright'
        }).addTo(criticalAreasMap);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(criticalAreasMap);

        // Add heatmap layer first (underneath)
        var heatmapPoints = @json($heatmapPoints);
        var heatmapLayer;
        if (heatmapPoints && heatmapPoints.length > 0) {
            var validHeatmapPoints = heatmapPoints.map(function(point) {
                return [
                    parseFloat(point[0]),
                    parseFloat(point[1]),
                    parseFloat(point[2])
                ];
            }).filter(function(point) {
                return !isNaN(point[0]) && !isNaN(point[1]) && !isNaN(point[2]);
            });

            if (validHeatmapPoints.length > 0) {
                heatmapLayer = L.heatLayer(validHeatmapPoints, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 10,
                    gradient: {
                        0.4: 'blue',
                        0.6: 'cyan',
                        0.7: 'lime',
                        0.8: 'yellow',
                        1.0: 'red'
                    }
                }).addTo(criticalAreasMap);
            }
        }

        // Add markers for critical areas (on top)
        var criticalAreaPoints = @json($topAreas);
        var markers = [];
        var markerLayer = L.layerGroup().addTo(criticalAreasMap);
        
        if (criticalAreaPoints && criticalAreaPoints.length > 0) {
            criticalAreaPoints.forEach(function(area) {
                var lat = parseFloat(area.coordinates.lat);
                var lng = parseFloat(area.coordinates.lng);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    var marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'critical-area-marker',
                            html: `<div style="
                                width: 30px;
                                height: 30px;
                                background-color: ${area.severity >= 75 ? '#dc3545' : (area.severity >= 50 ? '#ffc107' : '#28a745')};
                                border: 2px solid white;
                                border-radius: 50%;
                                box-shadow: 0 0 10px rgba(0,0,0,0.3);
                            "></div>`,
                            iconSize: [30, 30],
                            iconAnchor: [15, 15]
                        })
                    });

                    marker.bindPopup(`
                        <div class="p-2">
                            <h6 class="mb-2">${area.name}</h6>
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-tags"></i> Waste Types:
                                </small>
                                <div class="d-flex flex-wrap gap-1">
                                    ${area.waste_types ? `<span class="badge bg-info">${area.waste_types}</span>` : 'No waste types specified'}
                                </div>
                            </div>
                            <div class="border-top pt-2">
                                <p class="mb-1"><i class="bi bi-flag"></i> ${area.report_count} reports</p>
                                <p class="mb-1"><i class="bi bi-trash"></i> ${area.total_volume}m³</p>
                                <p class="mb-0"><i class="bi bi-clock"></i> ${area.latest_report}</p>
                            </div>
                            @if(Auth::user()->isAdmin() || Auth::user()->isSupervisor())
                            <div class="border-top mt-2 pt-2">
                                <a href="{{ route('collecte.create') }}?signal_id=${area.signal_id}" 
                                   class="btn btn-sm btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Create Collection
                                </a>
                            </div>
                            @endif
                        </div>
                    `, {
                        maxWidth: 300
                    });

                    markerLayer.addLayer(marker);
                    markers.push(marker);
                }
            });

            // Fit bounds if we have markers
            if (markers.length > 0) {
                var group = L.featureGroup(markers);
                criticalAreasMap.fitBounds(group.getBounds().pad(0.1));
            }
        }

        // Debug logging
        console.log('Critical Area Points:', criticalAreaPoints);
        console.log('Heatmap Points:', heatmapPoints);

        // Handle window resize
        window.addEventListener('resize', function() {
            criticalAreasMap.invalidateSize();
        });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    /* Welcome Banner */
    .welcome-banner {
        background: linear-gradient(135deg, #28266F 0%, #3498db 100%) !important;
        margin-top: 30px !important;
    }
    .text-white-50{
        color:rgb(254, 254, 254) !important;
    }
    .text-white{
        color:rgb(255, 255, 255) !important;
    }
    .welcome-decoration {
        position: absolute;
        bottom: 0;
        left: 0;
        background: linear-gradient(135deg, #28266F 0%, #3498db 100%) !important;
        opacity: 0.8;
        z-index: 1;
    }

    .z-2 {
        z-index: 2;
    }

    /* Gradients */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #2980b9 0%, #3498db 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #0099cc 0%, #33b5e5 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
    }

    .bg-gradient-dark {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    }

    /* Cards */
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    /* Stat Cards */
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .stat-icon i {
        font-size: 24px;
    }

    /* Hover Effects */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
    }

    /* Activity Feed */
    .activity-feed {
        max-height: 400px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .activity-feed::-webkit-scrollbar {
        width: 6px;
    }

    .activity-feed::-webkit-scrollbar-track {
        background: #f8f9fa;
        border-radius: 3px;
    }

    .activity-feed::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }

    /* Empty States */
    .empty-state-icon {
        font-size: 48px;
        color: #dee2e6;
    }

    /* Map Styles */
    .leaflet-container {
        border-radius: 0 0 1rem 1rem;
    }

    /* Critical Area Markers */
    .critical-area-marker {
        z-index: 1000 !important;
    }

    .critical-area-marker path {
        stroke-width: 2px !important;
        stroke: #fff !important;
        fill-opacity: 1 !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .welcome-banner {
            padding: 1.5rem !important;
        }

        .card-body {
            padding: 1.25rem !important;
        }

        #map {
            height: 50vh !important;
        }

        .activity-feed {
            max-height: 300px;
        }
    }
</style>
@endpush
@endsection 