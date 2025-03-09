@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Admin Signals Management Specific Styles */
    .admin-signals {
        padding-top: 80px !important;
    }

    .admin-signals .map-container {
        position: relative !important;
        height: 400px !important;
        width: 100% !important;
        z-index: 1 !important;
    }

    .admin-signals #map { 
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 100% !important;
        width: 100% !important;
        border-radius: 4px !important;
    }

    /* Custom Leaflet Controls Styling */
    .admin-signals .leaflet-touch .leaflet-control-layers,
    .admin-signals .leaflet-touch .leaflet-bar {
        border: none !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    .admin-signals .leaflet-touch .leaflet-bar a {
        width: 28px !important;
        height: 28px !important;
        line-height: 28px !important;
        background-color: white !important;
        color: #333 !important;
        transition: all 0.3s ease !important;
    }

    .admin-signals .leaflet-touch .leaflet-bar a:hover {
        background-color: #f4f4f4 !important;
        color: #000 !important;
    }

    /* Mobile Responsive Adjustments */
    @media (max-width: 768px) {
        .admin-signals .card-body {
            padding: 1rem !important;
        }
        .admin-signals .table-responsive {
            font-size: 0.9rem !important;
        }
        .admin-signals .btn-sm {
            padding: 0.2rem 0.4rem !important;
        }
        .admin-signals #map {
            height: 300px !important;
        }
        .admin-signals .statistics-card h2 {
            font-size: 1.5rem !important;
        }
    }

    /* Statistics Cards Enhancement */
    .admin-signals .statistics-card {
        transition: transform 0.2s !important;
        cursor: pointer !important;
        border: none !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }

    .admin-signals .statistics-card:hover {
        transform: translateY(-5px) !important;
    }

    /* Table Enhancements */
    .admin-signals .table th {
        background-color: #f8f9fa !important;
        font-weight: 600 !important;
        border-bottom: 2px solid #dee2e6 !important;
    }

    .admin-signals .table td {
        vertical-align: middle !important;
        border-bottom: 1px solid #dee2e6 !important;
    }

    /* Filter Form Improvements */
    .admin-signals .form-select, 
    .admin-signals .form-control {
        border-radius: 4px !important;
        border: 1px solid #ced4da !important;
        padding: 0.375rem 0.75rem !important;
    }

    .admin-signals .form-select:focus, 
    .admin-signals .form-control:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
        outline: none !important;
    }

    /* Card Styles */
    .admin-signals .card {
        border: none !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
        margin-bottom: 1.5rem !important;
    }

    .admin-signals .card-header {
        background-color: #fff !important;
        border-bottom: 1px solid rgba(0,0,0,.125) !important;
        padding: 1rem !important;
        font-weight: 600 !important;
    }

    /* Button Styles */
    .admin-signals .btn {
        font-weight: 500 !important;
        border-radius: 4px !important;
        transition: all 0.3s ease !important;
    }

    .admin-signals .btn-primary {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
    }

    .admin-signals .btn-primary:hover {
        background-color: #0a2751 !important;
        border-color: #0a2751 !important;
    }

    /* Pagination Styles */
    .admin-signals .pagination {
        margin-top: 1rem !important;
        justify-content: center !important;
    }

    .admin-signals .page-link {
        color: #0e346a !important;
        border: 1px solid #dee2e6 !important;
        margin: 0 2px !important;
    }

    .admin-signals .page-item.active .page-link {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
        color: #fff !important;
    }
</style>
@endsection

@section('content')
<div class="admin-signals container-fluid px-4">
    <h1 class="mt-4">Signals Management</h1>
    
    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    Total Signals
                    <h2>{{ $statistics['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    Pending Signals
                    <h2>{{ $statistics['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    Validated Signals
                    <h2>{{ $statistics['validated'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    Anomalies
                    <h2>{{ $statistics['anomalies'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filters
        </div>
        <div class="card-body">
            <form id="filters-form" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="validated" {{ request('status') == 'validated' ? 'selected' : '' }}>Validated</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Waste Type</label>
                    <select name="waste_type" class="form-select">
                        <option value="">All</option>
                        @foreach($wasteTypes as $type)
                            <option value="{{ $type->id }}" {{ request('waste_type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Region</label>
                    <input type="text" name="region" class="form-control" value="{{ request('region') }}">
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="anomaly" value="1" {{ request('anomaly') ? 'checked' : '' }}>
                        <label class="form-check-label">Show Anomalies Only</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.signals.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Heatmap -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-map-marked-alt me-1"></i>
            Signals Heatmap
        </div>
        <div class="card-body">
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <!-- Signals Table -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Signals List
                </div>
                <div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.signals.export', ['format' => 'csv']) }}">CSV</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.signals.export', ['format' => 'pdf']) }}">PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Location</th>
                            <th>Reporter</th>
                            <th>Waste Types</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($signals as $signal)
                        <tr>
                            <td>{{ $signal->id }}</td>
                            <td>{{ $signal->location }}</td>
                            <td>{{ $signal->creator->name }}</td>
                            <td>{{ $signal->wasteTypes->pluck('name')->implode(', ') }}</td>
                            <td>
                                <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($signal->status) }}
                                </span>
                            </td>
                            <td>{{ $signal->signal_date->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.signals.show', $signal) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-success" onclick="updateStatus('{{ $signal->id }}', 'validated')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="updateStatus('{{ $signal->id }}', 'rejected')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $signals->links() }}
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function initializeMap() {
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }

        // Force the container to have dimensions
        mapContainer.style.height = '400px';
        mapContainer.style.width = '100%';

        try {
            // Initialize map with custom options
            const map = L.map('map', {
                zoomControl: true,
                scrollWheelZoom: true,
                dragging: true,
                tap: true
            }).setView([{{ $signals->first()?->latitude ?? 31.7917 }}, {{ $signals->first()?->longitude ?? -7.0926 }}], 7);
            
            // Add OpenStreetMap tiles with custom options
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Force a resize to ensure proper rendering
            map.invalidateSize(true);

            // Prepare heatmap data
            const points = @json($heatmapData->map(function($point) {
                return [
                    floatval($point->latitude),
                    floatval($point->longitude),
                    intval($point->intensity)
                ];
            }));

            if (points && points.length > 0) {
                try {
                    // Configure heatmap layer
                    const maxIntensity = Math.max(...points.map(p => p[2] || 0));
                    const heat = L.heatLayer(points, {
                        radius: 25,
                        blur: 15,
                        maxZoom: 10,
                        max: maxIntensity > 0 ? maxIntensity : 1,
                        gradient: {
                            0.4: 'blue',
                            0.6: 'lime',
                            0.8: 'yellow',
                            1.0: 'red'
                        }
                    }).addTo(map);

                    // Add legend
                    const legend = L.control({ position: 'bottomright' });
                    legend.onAdd = function(map) {
                        const div = L.DomUtil.create('div', 'info legend');
                        div.style.backgroundColor = 'white';
                        div.style.padding = '6px';
                        div.style.borderRadius = '4px';
                        div.style.boxShadow = '0 1px 5px rgba(0,0,0,0.4)';
                        div.innerHTML = '<h4 style="margin:0 0 5px 0;font-size:14px;">Signal Density</h4>' +
                            '<div style="background: linear-gradient(to right, blue, lime, yellow, red);height:20px;width:100px;"></div>' +
                            '<div style="display:flex;justify-content:space-between;width:100px;font-size:12px;">' +
                            '<span>Low</span><span>High</span></div>';
                        return div;
                    };
                    legend.addTo(map);
                } catch (error) {
                    console.error('Error initializing heatmap:', error);
                }
            }

            // Add resize handler
            window.addEventListener('resize', function() {
                map.invalidateSize(true);
            });

        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    // Try to initialize map immediately
    initializeMap();

    // Also try after a short delay to ensure container is ready
    setTimeout(initializeMap, 500);

    // Existing status update function
    window.updateStatus = function(signalId, status) {
        if (confirm('Are you sure you want to ' + status + ' this signal?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/signals/${signalId}/status`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            
            form.appendChild(csrfToken);
            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
});
</script>
@endpush 