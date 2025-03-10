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
        margin-bottom: 20px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
    }

    .admin-signals #map {
        height: 100% !important;
        width: 100% !important;
        z-index: 1 !important;
        background-color: #f8f9fa !important;
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
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 0.25rem !important;
        padding: 0 !important;
        list-style: none !important;
    }

    .admin-signals .page-item {
        margin: 0 2px !important;
    }

    .admin-signals .page-link {
        color: #0e346a !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.5rem 0.75rem !important;
        border-radius: 4px !important;
        background-color: #fff !important;
        transition: all 0.2s ease !important;
    }

    .admin-signals .page-item.active .page-link {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
        color: #fff !important;
    }

    .admin-signals .page-item:not(.active) .page-link:hover {
        background-color: #f8f9fa !important;
        border-color: #0e346a !important;
    }

    .admin-signals .page-item.disabled .page-link {
        color: #6c757d !important;
        pointer-events: none !important;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
    }

    /* Override Tailwind Pagination Styles */
    .admin-signals .hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between {
        display: none !important;
    }

    /* Fix Pagination Container Size */
    .admin-signals nav > div {
        width: auto !important;
        height: auto !important;
        display: flex !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
        gap: 0.25rem !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* Fix Navigation Arrows Container */
    .admin-signals nav > div > div {
        width: auto !important;
        height: auto !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
    }

    /* Fix Navigation Arrows */
    .admin-signals nav > div > div > span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 32px !important;
        height: 32px !important;
    }

    .admin-signals nav > div > div > span > a,
    .admin-signals nav > div > div > span > span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        height: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 4px !important;
        background-color: #fff !important;
        color: #0e346a !important;
        transition: all 0.2s ease !important;
    }

    .admin-signals nav > div > div > span > a svg,
    .admin-signals nav > div > div > span > span svg {
        width: 12px !important;
        height: 12px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Fix Page Numbers */
    .admin-signals nav > div > div:not(:first-child):not(:last-child) {
        display: flex !important;
        gap: 0.25rem !important;
    }

    .admin-signals nav > div > div:not(:first-child):not(:last-child) > span,
    .admin-signals nav > div > div:not(:first-child):not(:last-child) > a {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 32px !important;
        height: 32px !important;
        padding: 0 8px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 4px !important;
        background-color: #fff !important;
        color: #0e346a !important;
        transition: all 0.2s ease !important;
    }

    /* Hover States */
    .admin-signals nav > div > div > span > a:hover,
    .admin-signals nav > div > div:not(:first-child):not(:last-child) > a:hover {
        background-color: #f8f9fa !important;
        border-color: #0e346a !important;
    }

    /* Active State */
    .admin-signals nav > div > div:not(:first-child):not(:last-child) > span {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
        color: #fff !important;
    }

    /* Disabled State */
    .admin-signals nav > div > div > span > span {
        color: #6c757d !important;
        pointer-events: none !important;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .admin-signals nav > div > div > span,
        .admin-signals nav > div > div:not(:first-child):not(:last-child) > span,
        .admin-signals nav > div > div:not(:first-child):not(:last-child) > a {
            min-width: 28px !important;
            height: 28px !important;
        }

        .admin-signals nav > div > div > span > a svg,
        .admin-signals nav > div > div > span > span svg {
            width: 10px !important;
            height: 10px !important;
        }
    }

    /* Override SVG and Navigation Styles */
    .admin-signals nav > div > div > span > a > svg,
    .admin-signals nav > div > div > span > a > span > svg {
        width: 16px !important;
        height: 16px !important;
        margin: 0 !important;
        padding: 0 !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }

    .admin-signals nav > div > div > span > a,
    .admin-signals nav > div > div > span > a > span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0.5rem 0.75rem !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 4px !important;
        background-color: #fff !important;
        color: #0e346a !important;
        transition: all 0.2s ease !important;
        min-width: 32px !important;
        height: 32px !important;
        line-height: 1 !important;
    }

    .admin-signals nav > div > div > span > a:hover,
    .admin-signals nav > div > div > span > a > span:hover {
        background-color: #f8f9fa !important;
        border-color: #0e346a !important;
    }

    .admin-signals nav > div > div > span > a:disabled,
    .admin-signals nav > div > div > span > a > span:disabled {
        color: #6c757d !important;
        pointer-events: none !important;
        background-color: #fff !important;
        border-color: #dee2e6 !important;
    }

    /* Remove any relative positioning that might affect layout */
    .admin-signals nav > div > div > span > a.relative,
    .admin-signals nav > div > div > span > a > span.relative {
        position: static !important;
    }

    /* Leaflet Controls Fix */
    .admin-signals .leaflet-control-zoom {
        border: none !important;
        margin: 15px !important;
    }

    .admin-signals .leaflet-control-zoom a {
        width: 30px !important;
        height: 30px !important;
        line-height: 30px !important;
        color: #333 !important;
        font-size: 16px !important;
        background-color: white !important;
        border: none !important;
        border-radius: 4px !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    /* Action Buttons Fix */
    .admin-signals .action-buttons {
        display: flex !important;
        gap: 5px !important;
        flex-wrap: nowrap !important;
        justify-content: flex-start !important;
    }

    .admin-signals .action-buttons .btn {
        padding: 0.25rem 0.5rem !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        border-radius: 0.2rem !important;
        white-space: nowrap !important;
        min-width: 32px !important;
    }

    /* Export Button Fix */
    .admin-signals .export-dropdown .dropdown-menu {
        min-width: 100px !important;
        padding: 0.5rem 0 !important;
        margin: 0.125rem 0 0 !important;
        font-size: 0.875rem !important;
    }

    .admin-signals .export-dropdown .dropdown-item {
        padding: 0.5rem 1rem !important;
        color: #333 !important;
    }

    .admin-signals .export-dropdown .dropdown-item:hover {
        background-color: #f8f9fa !important;
    }

    /* Mobile Responsive Fixes */
    @media (max-width: 768px) {
        .admin-signals .action-buttons {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 4px !important;
        }

        .admin-signals .action-buttons .btn {
            width: 100% !important;
            padding: 0.375rem !important;
        }

        .admin-signals .export-dropdown {
            width: 100% !important;
        }

        .admin-signals .export-dropdown .btn {
            width: 100% !important;
            margin-bottom: 0.5rem !important;
        }

        .admin-signals .table td {
            white-space: normal !important;
            min-width: 100px !important;
        }
    }

    /* Table Responsive Fix */
    .admin-signals .table-responsive {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    .admin-signals .table th,
    .admin-signals .table td {
        vertical-align: middle !important;
        padding: 0.75rem !important;
    }

    /* Action Buttons Container */
    .action-buttons {
        display: flex !important;
        gap: 0.5rem !important;
        flex-wrap: wrap !important;
        justify-content: flex-end !important;
    }

    .action-buttons .btn {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.875rem !important;
        line-height: 1.5 !important;
        border-radius: 0.25rem !important;
        white-space: nowrap !important;
    }

    /* Export Dropdown */
    .export-dropdown {
        position: relative !important;
    }

    .export-dropdown .dropdown-menu {
        min-width: 10rem !important;
        padding: 0.5rem 0 !important;
        margin: 0.125rem 0 0 !important;
        background-color: #fff !important;
        border: 1px solid rgba(0,0,0,.15) !important;
        border-radius: 0.25rem !important;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175) !important;
    }

    .export-dropdown .dropdown-item {
        display: block !important;
        width: 100% !important;
        padding: 0.5rem 1rem !important;
        clear: both !important;
        font-weight: 400 !important;
        color: #212529 !important;
        text-align: inherit !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        background-color: transparent !important;
        border: 0 !important;
    }

    .export-dropdown .dropdown-item:hover {
        color: #16181b !important;
        background-color: #f8f9fa !important;
    }

    /* Mobile Responsive Table */
    @media (max-width: 768px) {
        .table-responsive {
            margin-bottom: 1rem !important;
            border: 0 !important;
        }

        .table-responsive table {
            border: 0 !important;
        }

        .table-responsive th {
            display: none !important;
        }

        .table-responsive tr {
            margin-bottom: 1rem !important;
            display: block !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.25rem !important;
        }

        .table-responsive td {
            display: block !important;
            text-align: right !important;
            padding: 0.75rem !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .table-responsive td::before {
            content: attr(data-label) !important;
            float: left !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            font-size: 0.85em !important;
        }

        .table-responsive td:last-child {
            border-bottom: 0 !important;
        }

        .action-buttons {
            justify-content: center !important;
            margin-top: 0.5rem !important;
        }

        .action-buttons .btn {
            flex: 1 1 auto !important;
            text-align: center !important;
        }
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
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.signals.export', ['format' => 'csv']) }}">
                                <i class="fas fa-file-csv me-1"></i> Export as CSV
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.signals.export', ['format' => 'excel']) }}">
                                <i class="fas fa-file-excel me-1"></i> Export as Excel
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.signals.export', ['format' => 'pdf']) }}">
                                <i class="fas fa-file-pdf me-1"></i> Export as PDF
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Waste Types</th>
                            <th>Volume</th>
                            <th>Reporter</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($signals as $signal)
                            <tr>
                                <td>{{ $signal->location }}</td>
                                <td>
                                    @foreach($signal->wasteTypes as $wasteType)
                                        <span class="badge bg-info">{{ $wasteType->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $signal->volume }} m³</td>
                                <td>{{ $signal->creator->first_name }} {{ $signal->creator->last_name }}</td>
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
                                    <a href="{{ route('admin.signals.edit', $signal) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.signals.destroy', $signal) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this signal?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No signals found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $signals->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
let map = null;

function ensureMapContainer() {
    const container = document.getElementById('map');
    if (!container) return false;

    // Force container dimensions
    container.style.height = '400px';
    container.style.width = '100%';
    return true;
}

function initializeMap() {
    if (!ensureMapContainer()) {
        console.error('Map container not found or not properly sized');
        return;
    }

    try {
        if (map !== null) {
            map.remove(); // Clean up existing map instance if any
        }

        // Center coordinates for Morocco (approximately center of the country)
        const initialLat = {{ $signals->first() ? (float) $signals->first()->latitude : 31.7917 }};
        const initialLng = {{ $signals->first() ? (float) $signals->first()->longitude : -7.0926 }};
        const initialZoom = {{ $signals->first() ? 7 : 6 }}; // Zoom out more when no signals

        // Initialize map with custom options
        map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true,
            dragging: true,
            tap: true
        }).setView([initialLat, initialLng], initialZoom);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Set map bounds to Morocco's approximate boundaries
        const moroccoBounds = L.latLngBounds(
            L.latLng(20.7, -17.1), // Southwest corner
            L.latLng(36.0, 1.2)    // Northeast corner
        );

        // If no signals, set view to Morocco's bounds
        if (!{{ $signals->first() ? 'true' : 'false' }}) {
            map.fitBounds(moroccoBounds);
        }

        // Force a resize
        map.invalidateSize(true);

        // Add markers and heatmap after ensuring map is properly initialized
        setTimeout(() => {
            addMarkersAndHeatmap();
        }, 100);
    } catch (error) {
        console.error('Error initializing map:', error);
    }
}

function addMarkersAndHeatmap() {
    try {
        // Prepare heatmap data with proper floating point conversion
        const points = @json($heatmapData->map(function($point) {
            return [
                (float) $point->latitude,
                (float) $point->longitude,
                (int) $point->intensity
            ];
        }));

        if (points && points.length > 0) {
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
        }

        // Add markers for each signal with proper floating point conversion
        @foreach($allSignals as $signal)
            (function() {
                const lat = {{ (float) $signal->latitude }};
                const lng = {{ (float) $signal->longitude }};
                
                // Create custom marker icon based on status
                const markerColor = '{{ $signal->status === "validated" ? "green" : ($signal->status === "pending" ? "orange" : "red") }}';
                const customIcon = L.icon({
                    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${markerColor}.png`,
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                const signalMarker = L.marker([lat, lng], {
                    icon: customIcon,
                    title: 'Signal #{{ $signal->id }}'
                });

                const popupContent = `
                    <div class="signal-popup" style="min-width: 200px; padding: 10px;">
                        <h6 style="margin: 0 0 10px 0; color: #0e346a; font-weight: 600;">Signal #{{ $signal->id }}</h6>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ Str::limit($signal->location, 30) }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-map-pin" style="color: #666;"></i>
                            <span style="margin-left: 5px;">${lat}, ${lng}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-user" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ $signal->creator->full_name ?? 'Unknown' }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-calendar" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ $signal->signal_date->format('Y-m-d H:i') }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-trash" style="color: #666;"></i>
                            <span style="margin-left: 5px;">
                                @foreach($signal->wasteTypes as $type)
                                    {{ $type->name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </span>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'pending' ? 'warning' : 'danger') }}" 
                                  style="padding: 5px 10px; font-size: 12px;">
                                {{ ucfirst($signal->status) }}
                            </span>
                            @if($signal->anomaly_flag)
                                <span class="badge bg-danger" style="padding: 5px 10px; font-size: 12px; margin-left: 5px;">
                                    Anomaly
                                </span>
                            @endif
                        </div>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('admin.signals.show', $signal) }}" 
                               class="btn btn-info btn-sm" 
                               style="flex: 1; font-size: 12px; padding: 4px 8px; text-decoration: none; color: white; border-radius: 4px; text-align: center;">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('admin.signals.edit', $signal) }}" 
                               class="btn btn-warning btn-sm" 
                               style="flex: 1; font-size: 12px; padding: 4px 8px; text-decoration: none; color: white; border-radius: 4px; text-align: center;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                `;

                signalMarker.bindPopup(popupContent);
                signalMarker.addTo(map);
            })();
        @endforeach

        // If there are markers, fit the map bounds to show all markers
        if ({{ $allSignals->count() }} > 0) {
            const bounds = [];
            @foreach($allSignals as $signal)
                bounds.push([{{ (float) $signal->latitude }}, {{ (float) $signal->longitude }}]);
            @endforeach
            map.fitBounds(bounds);
        }

    } catch (error) {
        console.error('Error adding markers and heatmap:', error);
    }
}

// Initialize map when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initial initialization
    initializeMap();

    // Reinitialize after a short delay to ensure proper rendering
    setTimeout(initializeMap, 500);
});

// Handle window resize
window.addEventListener('resize', function() {
    if (map) {
        map.invalidateSize(true);
    }
});

// Handle tab changes or any other events that might affect map visibility
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.attributeName === 'style' || mutation.attributeName === 'class') {
            if (map) {
                map.invalidateSize(true);
            }
        }
    });
});

// Observe the map container for changes
const mapContainer = document.getElementById('map');
if (mapContainer) {
    observer.observe(mapContainer, { attributes: true });
}

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

// Initialize Bootstrap dropdowns
var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
    return new bootstrap.Dropdown(dropdownToggleEl);
});
</script>
@endpush 