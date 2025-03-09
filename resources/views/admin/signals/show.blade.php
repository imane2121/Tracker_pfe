@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Admin Signal Details Specific Styles */
    .admin-signal-details {
        padding-top: 80px !important;
    }

    .admin-signal-details .map-container {
        position: relative !important;
        height: 400px !important;
        width: 100% !important;
        z-index: 1 !important;
    }

    .admin-signal-details #map { 
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
    .admin-signal-details .leaflet-touch .leaflet-control-layers,
    .admin-signal-details .leaflet-touch .leaflet-bar {
        border: none !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    .admin-signal-details .leaflet-touch .leaflet-bar a {
        width: 28px !important;
        height: 28px !important;
        line-height: 28px !important;
        background-color: white !important;
        color: #333 !important;
        transition: all 0.3s ease !important;
    }

    .admin-signal-details .leaflet-touch .leaflet-bar a:hover {
        background-color: #f4f4f4 !important;
        color: #000 !important;
    }

    /* Mobile Responsive Adjustments */
    @media (max-width: 768px) {
        .admin-signal-details .card-body {
            padding: 1rem !important;
        }
        .admin-signal-details #map {
            height: 300px !important;
        }
        .admin-signal-details .media-gallery img,
        .admin-signal-details .media-gallery video {
            max-height: 200px !important;
            object-fit: cover !important;
        }
    }

    /* Media Gallery Enhancements */
    .admin-signal-details .media-gallery img,
    .admin-signal-details .media-gallery video {
        width: 100% !important;
        height: 200px !important;
        object-fit: cover !important;
        transition: transform 0.2s !important;
        cursor: pointer !important;
        border-radius: 4px !important;
    }

    .admin-signal-details .media-gallery img:hover,
    .admin-signal-details .media-gallery video:hover {
        transform: scale(1.05) !important;
    }

    /* Card Styles */
    .admin-signal-details .card {
        border: none !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
        margin-bottom: 1.5rem !important;
        background: #fff !important;
    }

    .admin-signal-details .card-header {
        background-color: #fff !important;
        border-bottom: 1px solid rgba(0,0,0,.125) !important;
        padding: 1rem !important;
        font-weight: 600 !important;
    }

    /* Info Cards Styling */
    .admin-signal-details .info-label {
        font-weight: 600 !important;
        color: #495057 !important;
        margin-bottom: 0.25rem !important;
    }

    .admin-signal-details .info-value {
        color: #212529 !important;
    }

    .admin-signal-details .waste-type-item {
        padding: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        background-color: #f8f9fa !important;
        border-radius: 4px !important;
        transition: background-color 0.2s !important;
    }

    .admin-signal-details .waste-type-item:hover {
        background-color: #e9ecef !important;
    }

    /* Form Controls */
    .admin-signal-details .form-select,
    .admin-signal-details .form-control {
        border-radius: 4px !important;
        border: 1px solid #ced4da !important;
        padding: 0.375rem 0.75rem !important;
    }

    .admin-signal-details .form-select:focus,
    .admin-signal-details .form-control:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
        outline: none !important;
    }

    /* Button Styles */
    .admin-signal-details .btn {
        font-weight: 500 !important;
        border-radius: 4px !important;
        transition: all 0.3s ease !important;
    }

    .admin-signal-details .btn-primary {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
    }

    .admin-signal-details .btn-primary:hover {
        background-color: #0a2751 !important;
        border-color: #0a2751 !important;
    }

    /* Nearby Signals List */
    .admin-signal-details .list-group-item {
        border: none !important;
        margin-bottom: 0.5rem !important;
        border-radius: 4px !important;
        transition: all 0.3s ease !important;
    }

    .admin-signal-details .list-group-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(5px) !important;
    }

    .admin-signal-details .list-group-item h6 {
        color: #0e346a !important;
        margin-bottom: 0.25rem !important;
    }

    /* Status Badge Styles */
    .admin-signal-details .badge {
        padding: 0.5em 0.75em !important;
        font-weight: 500 !important;
        border-radius: 4px !important;
    }

    /* Leaflet Controls Fix */
    .admin-signal-details .leaflet-control-zoom {
        border: none !important;
        margin: 15px !important;
    }

    .admin-signal-details .leaflet-control-zoom a {
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

    /* Reporter Info Styles */
    .admin-signal-details .reporter-info {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.5rem !important;
    }

    .admin-signal-details .reporter-info .name {
        font-weight: 600 !important;
        color: #333 !important;
    }

    .admin-signal-details .reporter-info .email {
        color: #666 !important;
        font-size: 0.9rem !important;
    }

    /* Mobile Responsive Fixes */
    @media (max-width: 768px) {
        .admin-signal-details .reporter-info {
            margin-bottom: 1rem !important;
        }
    }
</style>
@endsection

@section('content')
<div class="admin-signal-details container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Signal Details #{{ $signal->id }}</h1>
        <a href="{{ route('admin.signals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row mt-4">
        <!-- Signal Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Signal Information
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Location</h5>
                            <p>{{ $signal->location }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Reporter</h5>
                            <div class="reporter-info">
                                <span class="name">{{ $signal->creator->name ?? 'Unknown' }}</span>
                                <span class="email">{{ $signal->creator->email ?? 'No email provided' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Date Reported</h5>
                            <p>{{ $signal->signal_date->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Status</h5>
                            <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'pending' ? 'warning' : 'danger') }}">
                                {{ ucfirst($signal->status) }}
                            </span>
                            @if($signal->anomaly_flag)
                                <span class="badge bg-danger ms-2">Anomaly Detected</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Waste Types</h5>
                            <ul class="list-unstyled">
                                @foreach($signal->wasteTypes as $type)
                                    <li><i class="fas fa-trash me-2"></i>{{ $type->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Volume</h5>
                            <p>{{ $signal->volume }} m³</p>
                        </div>
                    </div>
                    @if($signal->description)
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5>Description</h5>
                                <p>{{ $signal->description }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Media Gallery -->
            @if($signal->media->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-images me-1"></i>
                    Media Gallery
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($signal->media as $media)
                            <div class="col-md-4 mb-3">
                                @if(str_contains($media->media_type, 'image'))
                                    <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid rounded" alt="Signal media">
                                @else
                                    <video class="img-fluid rounded" controls>
                                        <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ $media->media_type }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Location Map -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    Location
                </div>
                <div class="card-body">
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Management -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tasks me-1"></i>
                    Status Management
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.signals.update-status', $signal) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Update Status</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $signal->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="validated" {{ $signal->status === 'validated' ? 'selected' : '' }}>Validated</option>
                                <option value="rejected" {{ $signal->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Note</label>
                            <textarea name="admin_note" class="form-control" rows="3">{{ $signal->admin_note }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>

            <!-- Nearby Signals -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-map-signs me-1"></i>
                    Nearby Signals (5km radius)
                </div>
                <div class="card-body">
                    @if($nearbySignals->count() > 0)
                        <div class="list-group">
                            @foreach($nearbySignals as $nearby)
                                <a href="{{ route('admin.signals.show', $nearby) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Signal #{{ $nearby->id }}</h6>
                                        <small>{{ number_format($nearby->distance, 1) }}km</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($nearby->location, 30) }}</p>
                                    <small>{{ $nearby->signal_date->format('Y-m-d H:i') }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No nearby signals found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
            }).setView([{{ $signal->latitude }}, {{ $signal->longitude }}], 14);
            
            // Add OpenStreetMap tiles with custom options
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Force a resize to ensure proper rendering
            map.invalidateSize(true);

            try {
                // Create custom marker icon
                const customIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                // Add marker for current signal with custom icon
                const currentMarker = L.marker([{{ $signal->latitude }}, {{ $signal->longitude }}], {
                    icon: customIcon,
                    title: 'Signal #{{ $signal->id }}'
                })
                .bindPopup(`
                    <div class="popup-content">
                        <strong>Signal #{{ $signal->id }}</strong><br>
                        <i class="fas fa-map-marker-alt me-1"></i> {{ Str::limit($signal->location, 30) }}<br>
                        <i class="fas fa-user me-1"></i> {{ $signal->creator->name ?? 'Unknown' }}<br>
                        <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($signal->status) }}
                        </span>
                    </div>
                `)
                .addTo(map);

                // Add markers for nearby signals with custom icons
                const nearbyIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                @foreach($nearbySignals as $nearby)
                    L.marker([{{ $nearby->latitude }}, {{ $nearby->longitude }}], {
                        icon: nearbyIcon,
                        title: 'Signal #{{ $nearby->id }}'
                    })
                    .bindPopup(`
                        <div class="popup-content">
                            <strong>Signal #{{ $nearby->id }}</strong><br>
                            <i class="fas fa-map-marker-alt me-1"></i> {{ Str::limit($nearby->location, 30) }}<br>
                            <i class="fas fa-ruler-horizontal me-1"></i> {{ number_format($nearby->distance, 1) }}km<br>
                            <i class="fas fa-clock me-1"></i> {{ $nearby->signal_date->format('Y-m-d H:i') }}<br>
                            <a href="{{ route('admin.signals.show', $nearby) }}" class="btn btn-sm btn-info mt-2 w-100">
                                <i class="fas fa-eye me-1"></i> View Details
                            </a>
                        </div>
                    `)
                    .addTo(map);
                @endforeach
            } catch (error) {
                console.error('Error adding markers:', error);
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

    // Add click handlers for media gallery
    document.querySelectorAll('.media-gallery img, .media-gallery video').forEach(media => {
        media.addEventListener('click', function() {
            // Implement lightbox or modal for media preview
            // You can add a lightbox library of your choice here
        });
    });
});
</script>
@endpush 