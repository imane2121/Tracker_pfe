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
        margin-bottom: 20px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
    }

    .admin-signal-details #map {
        height: 100% !important;
        width: 100% !important;
        z-index: 1 !important;
        background-color: #f8f9fa !important;
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
                    Signal Details
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Location:</strong> {{ $signal->location }}</p>
                            <p><strong>Coordinates:</strong> {{ $signal->latitude }}, {{ $signal->longitude }}</p>
                            <p><strong>Volume:</strong> {{ $signal->volume }} m³</p>
                            <p><strong>Waste Types:</strong></p>
                            <ul>
                                @foreach($signal->wasteTypes as $type)
                                    <li>{{ $type->name }}</li>
                                @endforeach
                            </ul>
                            <p><strong>Description:</strong> {{ $signal->description ?? 'No description provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($signal->status) }}
                                </span>
                            </p>
                            <p><strong>Reporter:</strong> {{ $signal->creator->first_name }} {{ $signal->creator->last_name }}</p>
                            <p><strong>Date Reported:</strong> {{ $signal->signal_date->format('Y-m-d H:i') }}</p>
                            <p><strong>Created:</strong> {{ $signal->created_at->format('Y-m-d H:i') }}</p>
                            <p><strong>Last Updated:</strong> {{ $signal->updated_at->format('Y-m-d H:i') }}</p>
                            @if($signal->admin_note)
                                <p><strong>Admin Note:</strong> {{ $signal->admin_note }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Analysis Card -->
            @if($signal->aiAnalysis)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-robot me-1"></i>
                    AI Analysis Results
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Detection Summary</h5>
                            <p>
                                <strong>Debris Detected:</strong> 
                                {{ $signal->aiAnalysis->debris_detected ? 'Yes' : 'No' }}
                            </p>
                            <p>
                                <strong>Confidence Score:</strong> 
                                {{ number_format($signal->aiAnalysis->confidence_score * 100, 1) }}%
                            </p>
                            <p>
                                <strong>Matches Reporter Selection:</strong> 
                                {{ $signal->aiAnalysis->matches_reporter_selection ? 'Yes' : 'No' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Detected Waste Types</h5>
                            <ul class="list-unstyled">
                                @foreach($signal->aiAnalysis->detected_waste_types as $type => $confidence)
                                    <li>
                                        {{ ucfirst($type) }} 
                                        ({{ number_format($confidence * 100, 1) }}%)
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    @if($signal->aiAnalysis->analysis_notes)
                        <div class="mt-3">
                            <h5>Analysis Notes</h5>
                            <p class="text-muted">{{ $signal->aiAnalysis->analysis_notes }}</p>
                        </div>
                    @endif

                    @if($signal->aiAnalysis->media_analysis_results)
                        <div class="mt-3">
                            <h5>Media Analysis</h5>
                            @foreach($signal->aiAnalysis->media_analysis_results as $mediaId => $analysis)
                                <div class="mb-3">
                                    <h6>Media #{{ str_replace('media_', '', $mediaId) }}</h6>
                                    @if($analysis['annotated_image'])
                                        <img src="data:image/jpeg;base64,{{ $analysis['annotated_image'] }}" 
                                             class="img-fluid mb-2" 
                                             alt="Annotated Image">
                                    @endif
                                    <p>
                                        <small class="text-muted">
                                            Processing Time: {{ number_format($analysis['process_time'], 2) }}s
                                        </small>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endif

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

            <!-- Reporter Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Reporter Information
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $signal->creator->full_name ?? 'Unknown' }}</p>
                    <p><strong>Email:</strong> {{ $signal->creator->email ?? 'No email provided' }}</p>
                    <p><strong>Reported On:</strong> {{ $signal->signal_date->format('Y-m-d H:i') }}</p>
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
                    Nearby reports (5km radius)
                </div>
                <div class="card-body">
                    @if($nearbySignals->count() > 0)
                        <div class="list-group">
                            @foreach($nearbySignals as $nearby)
                                <a href="{{ route('admin.signals.show', $nearby) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Report #{{ $nearby->id }}</h6>
                                        <small>{{ number_format($nearby->distance, 1) }}km</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($nearby->location, 30) }}</p>
                                    <small>{{ $nearby->signal_date->format('Y-m-d H:i') }}</small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No nearby reports found.</p>
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

        // Get coordinates with proper floating point conversion
        const lat = {{ (float) $signal->latitude }};
        const lng = {{ (float) $signal->longitude }};

        // Initialize map with custom options
        map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true,
            dragging: true,
            tap: true
        }).setView([lat, lng], 14);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Force a resize
        map.invalidateSize(true);

        // Add markers after ensuring map is properly initialized
        setTimeout(() => {
            addMarkers();
        }, 100);
    } catch (error) {
        console.error('Error initializing map:', error);
    }
}

function addMarkers() {
    try {
        // Get coordinates with proper floating point conversion
        const currentLat = {{ (float) $signal->latitude }};
        const currentLng = {{ (float) $signal->longitude }};

        // Create custom marker icon for current signal based on status
        const currentMarkerColor = '{{ $signal->status === "validated" ? "green" : ($signal->status === "pending" ? "orange" : "red") }}';
        const currentIcon = L.icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${currentMarkerColor}.png`,
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Add marker for current signal
        const currentSignalMarker = L.marker([currentLat, currentLng], {
            icon: currentIcon,
            title: 'Signal #{{ $signal->id }}'
        });

        const currentPopupContent = `
            <div class="signal-popup" style="min-width: 250px; padding: 10px;">
                <h6 style="margin: 0 0 10px 0; color: #0e346a; font-weight: 600;">Signal #{{ $signal->id }}</h6>
                <div style="margin-bottom: 8px;">
                    <i class="fas fa-map-marker-alt" style="color: #666;"></i>
                    <span style="margin-left: 5px;">{{ Str::limit($signal->location, 30) }}</span>
                </div>
                <div style="margin-bottom: 8px;">
                    <i class="fas fa-map-pin" style="color: #666;"></i>
                    <span style="margin-left: 5px;">${currentLat}, ${currentLng}</span>
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
                <div style="margin-bottom: 8px;">
                    <i class="fas fa-cube" style="color: #666;"></i>
                    <span style="margin-left: 5px;">Volume: {{ $signal->volume }} m³</span>
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
            </div>
        `;

        currentSignalMarker.bindPopup(currentPopupContent);
        currentSignalMarker.addTo(map);

        // Add markers for nearby signals with proper floating point conversion
        @foreach($nearbySignals as $nearby)
            (function() {
                const nearbyLat = {{ (float) $nearby->latitude }};
                const nearbyLng = {{ (float) $nearby->longitude }};
                
                // Create custom marker icon for nearby signal based on status
                const nearbyMarkerColor = '{{ $nearby->status === "validated" ? "green" : ($nearby->status === "pending" ? "orange" : "red") }}';
                const nearbyIcon = L.icon({
                    iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${nearbyMarkerColor}.png`,
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });
                
                const nearbySignalMarker = L.marker([nearbyLat, nearbyLng], {
                    icon: nearbyIcon,
                    title: 'Signal #{{ $nearby->id }}'
                });

                const nearbyPopupContent = `
                    <div class="signal-popup" style="min-width: 250px; padding: 10px;">
                        <h6 style="margin: 0 0 10px 0; color: #0e346a; font-weight: 600;">Signal #{{ $nearby->id }}</h6>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-map-marker-alt" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ Str::limit($nearby->location, 30) }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-map-pin" style="color: #666;"></i>
                            <span style="margin-left: 5px;">${nearbyLat}, ${nearbyLng}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-user" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ $nearby->creator->full_name ?? 'Unknown' }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-calendar" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ $nearby->signal_date->format('Y-m-d H:i') }}</span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-trash" style="color: #666;"></i>
                            <span style="margin-left: 5px;">
                                @foreach($nearby->wasteTypes as $type)
                                    {{ $type->name }}{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <i class="fas fa-ruler-horizontal" style="color: #666;"></i>
                            <span style="margin-left: 5px;">{{ number_format($nearby->distance, 1) }}km away</span>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <span class="badge bg-{{ $nearby->status === 'validated' ? 'success' : ($nearby->status === 'pending' ? 'warning' : 'danger') }}" 
                                  style="padding: 5px 10px; font-size: 12px;">
                                {{ ucfirst($nearby->status) }}
                            </span>
                            @if($nearby->anomaly_flag)
                                <span class="badge bg-danger" style="padding: 5px 10px; font-size: 12px; margin-left: 5px;">
                                    Anomaly
                                </span>
                            @endif
                        </div>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('admin.signals.show', $nearby) }}" 
                               class="btn btn-info btn-sm" 
                               style="flex: 1; font-size: 12px; padding: 4px 8px; text-decoration: none; color: white; border-radius: 4px; text-align: center;">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('admin.signals.edit', $nearby) }}" 
                               class="btn btn-warning btn-sm" 
                               style="flex: 1; font-size: 12px; padding: 4px 8px; text-decoration: none; color: white; border-radius: 4px; text-align: center;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                `;

                nearbySignalMarker.bindPopup(nearbyPopupContent);
                nearbySignalMarker.addTo(map);
            })();
        @endforeach

    } catch (error) {
        console.error('Error adding markers:', error);
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

// Add click handlers for media gallery
document.querySelectorAll('.media-gallery img, .media-gallery video').forEach(media => {
    media.addEventListener('click', function() {
        // Implement lightbox or modal for media preview
        // You can add a lightbox library of your choice here
    });
});
</script>
@endpush 