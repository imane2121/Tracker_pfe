@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Admin Signal Edit Specific Styles */
    .admin-signal-edit {
        padding-top: 80px !important;
    }

    .admin-signal-edit .map-container {
        position: relative !important;
        height: 400px !important;
        width: 100% !important;
        z-index: 1 !important;
        margin-bottom: 1rem !important;
    }

    .admin-signal-edit #map { 
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 100% !important;
        width: 100% !important;
        border-radius: 4px !important;
    }

    /* Form Styles */
    .admin-signal-edit .form-group {
        margin-bottom: 1.5rem !important;
    }

    .admin-signal-edit .form-label {
        font-weight: 600 !important;
        color: #495057 !important;
        margin-bottom: 0.5rem !important;
    }

    .admin-signal-edit .form-control,
    .admin-signal-edit .form-select {
        border-radius: 4px !important;
        border: 1px solid #ced4da !important;
        padding: 0.5rem 0.75rem !important;
    }

    .admin-signal-edit .form-control:focus,
    .admin-signal-edit .form-select:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
    }

    /* Card Styles */
    .admin-signal-edit .card {
        border: none !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        border-radius: 8px !important;
        margin-bottom: 1.5rem !important;
    }

    .admin-signal-edit .card-header {
        background-color: #fff !important;
        border-bottom: 1px solid rgba(0,0,0,.125) !important;
        padding: 1rem !important;
        font-weight: 600 !important;
    }

    /* Button Styles */
    .admin-signal-edit .btn {
        font-weight: 500 !important;
        border-radius: 4px !important;
        padding: 0.5rem 1rem !important;
        transition: all 0.3s ease !important;
    }

    .admin-signal-edit .btn-primary {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
    }

    .admin-signal-edit .btn-primary:hover {
        background-color: #0a2751 !important;
        border-color: #0a2751 !important;
    }

    /* Media Gallery */
    .admin-signal-edit .media-preview {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)) !important;
        gap: 1rem !important;
        margin-top: 1rem !important;
    }

    .admin-signal-edit .media-item {
        position: relative !important;
        border-radius: 4px !important;
        overflow: hidden !important;
    }

    .admin-signal-edit .media-item img,
    .admin-signal-edit .media-item video {
        width: 100% !important;
        height: 150px !important;
        object-fit: cover !important;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .admin-signal-edit .map-container {
            height: 300px !important;
        }

        .admin-signal-edit .btn {
            width: 100% !important;
            margin-bottom: 0.5rem !important;
        }

        .admin-signal-edit .media-preview {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important;
        }
    }
</style>
@endsection

@section('content')
<div class="admin-signal-edit container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Edit Signal #{{ $signal->id }}</h1>
        <a href="{{ route('admin.signals.show', $signal) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Details
        </a>
    </div>

    <div class="row mt-4">
        <div class="col-lg-8">
            <form action="{{ route('admin.signals.update', $signal) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        Location Information
                    </div>
                    <div class="card-body">
                        <div class="map-container mb-3">
                            <div id="map"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label class="form-label">Location Description</label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                    value="{{ old('location', $signal->location) }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="any" name="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                    value="{{ old('latitude', $signal->latitude) }}" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="any" name="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                                    value="{{ old('longitude', $signal->longitude) }}" required>
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-1"></i>
                        Report Details
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="form-label">Volume (m³)</label>
                                <input type="number" step="0.01" name="volume" class="form-control @error('volume') is-invalid @enderror" 
                                    value="{{ old('volume', $signal->volume) }}" required>
                                @error('volume')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $signal->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="validated" {{ old('status', $signal->status) == 'validated' ? 'selected' : '' }}>Validated</option>
                                    <option value="rejected" {{ old('status', $signal->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label">Waste Types</label>
                                <select name="waste_types[]" class="form-select @error('waste_types') is-invalid @enderror" multiple required>
                                    @foreach($wasteTypes as $type)
                                        <option value="{{ $type->id }}" 
                                            {{ in_array($type->id, old('waste_types', $signal->wasteTypes->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('waste_types')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                    rows="3">{{ old('description', $signal->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="form-label">Admin Note</label>
                                <textarea name="admin_note" class="form-control @error('admin_note') is-invalid @enderror" 
                                    rows="3">{{ old('admin_note', $signal->admin_note) }}</textarea>
                                @error('admin_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.signals.show', $signal) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Signal</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Media Gallery -->
            @if($signal->media->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-images me-1"></i>
                    Media Gallery
                </div>
                <div class="card-body">
                    <div class="media-preview">
                        @foreach($signal->media as $media)
                            <div class="media-item">
                                @if(str_contains($media->media_type, 'image'))
                                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="Signal media">
                                @else
                                    <video controls>
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

        try {
            // Initialize map
            const map = L.map('map', {
                zoomControl: true,
                scrollWheelZoom: true,
                dragging: true,
                tap: true
            }).setView([{{ $signal->latitude }}, {{ $signal->longitude }}], 14);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Create custom marker icon
            const customIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Add draggable marker
            let marker = L.marker([{{ $signal->latitude }}, {{ $signal->longitude }}], {
                icon: customIcon,
                draggable: true
            }).addTo(map);

            // Update form fields when marker is dragged
            marker.on('dragend', function(event) {
                const position = event.target.getLatLng();
                document.querySelector('input[name="latitude"]').value = position.lat.toFixed(6);
                document.querySelector('input[name="longitude"]').value = position.lng.toFixed(6);
            });

            // Update marker position when form fields change
            document.querySelector('input[name="latitude"]').addEventListener('change', updateMarkerPosition);
            document.querySelector('input[name="longitude"]').addEventListener('change', updateMarkerPosition);

            function updateMarkerPosition() {
                const lat = parseFloat(document.querySelector('input[name="latitude"]').value);
                const lng = parseFloat(document.querySelector('input[name="longitude"]').value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng]);
                }
            }

            // Force a resize to ensure proper rendering
            map.invalidateSize(true);

            // Add resize handler
            window.addEventListener('resize', function() {
                map.invalidateSize(true);
            });

        } catch (error) {
            console.error('Error initializing map:', error);
        }
    }

    // Initialize map
    initializeMap();

    // Also try after a short delay to ensure container is ready
    setTimeout(initializeMap, 500);
});
</script>
@endpush 