@extends('layouts.app')

@section('content')
<div class="container my-5 mb-7">
    <!-- Encouraging Message -->
    <div class="text-center mb-5">
        <div class="encouragement-card">
            <div class="wave-animation">
                <div class="wave"></div>
                <div class="wave"></div>
                <div class="wave"></div>
                </div>
            <div class="card-content">
                <h2 class="title">
                    <span class="highlight">
                        @if($isUrgent)
                            Create Urgent Collection
                        @else
                            Create Collection From Selected Signals
                        @endif
                    </span>
                </h2>
                <p class="message">
                    Organize cleanup efforts and make a difference
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('collecte.store') }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
                        @csrf
                        
        @if(!$isUrgent)
            <input type="hidden" name="signal_ids" value="{{ json_encode($signals->pluck('id')) }}">
        @endif
        <input type="hidden" name="is_urgent" value="{{ $isUrgent ? '1' : '0' }}">

        <!-- Location Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Collection Location</h10>
            </div>
            <div class="card-body">
                <div class="row">
                                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Location Name</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="region" class="form-label">Region</label>
                            <select class="form-select @error('region') is-invalid @enderror" 
                                   id="region" name="region" required>
                                <option value="">Select a region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ $centerLat }}" required>
                                @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ $centerLng }}" required>
                                @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                    </div>
                                <div class="col-md-6">
                        <div id="map" class="rounded shadow-sm" style="height: 300px;"></div>
                        <button type="button" class="btn btn-outline-secondary mt-2" id="resetMapPin">
                            <i class="bi bi-geo-alt"></i> Reset Pin Location
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Replace the signals checkbox section with this map-based selection -->
        @unless($isUrgent)
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h10 class="mb-0">Selected Signals</h10>
                    <span class="badge bg-primary" id="selectedCount">{{ $signals->count() }} signals selected</span>
                </div>
                <div class="card-body">
                    <div id="signalsMap" style="height: 400px;" class="rounded mb-3"></div>
                    <input type="hidden" name="signal_ids" id="finalSignalIds" value="{{ json_encode($signals->pluck('id')) }}">
                </div>
            </div>
        @endunless

        <!-- Collection Details -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Collection Details</h10>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="actual_volume" class="form-label">Estimated Volume (m³)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" 
                                   class="form-control @error('actual_volume') is-invalid @enderror"
                                   id="actual_volume" name="actual_volume" required>
                            <span class="input-group-text">m³</span>
                            @error('actual_volume')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                    <div class="col-md-6 mb-3">
                        <label for="nbrContributors" class="form-label">Number of Contributors Needed</label>
                        <input type="number" min="1" 
                                               class="form-control @error('nbrContributors') is-invalid @enderror" 
                               id="nbrContributors" name="nbrContributors" required>
                                        @error('nbrContributors')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="starting_date" class="form-label">Starting Date</label>
                        <input type="datetime-local" 
                               class="form-control @error('starting_date') is-invalid @enderror"
                               id="starting_date" name="starting_date" required>
                        @error('starting_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="datetime-local" 
                               class="form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date" required>
                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                    </div>
                </div>
                                    </div>
                                </div>

        <!-- Waste Types Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h10 class="mb-0">Please Select Waste Type</h10>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="waste_type_selector" class="form-label">Select Waste Types</label>
                        <select class="form-select waste-type-select" id="waste_type_selector" multiple="multiple">
                            @foreach($wasteTypes->where('parent_id', null) as $wasteType)
                                <optgroup label="{{ $wasteType->name }}">
                                    @foreach($wasteTypes->where('parent_id', $wasteType->id) as $specificType)
                                        <option value="{{ $specificType->id }}" data-parent-id="{{ $wasteType->id }}">
                                            {{ $specificType->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div id="selected_waste_types_container">
                            <!-- Hidden inputs will be added here via JavaScript -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Selected Types:</label>
                        <div id="selected_types_display" class="p-2 border rounded bg-light min-height-100">
                            <p class="text-muted mb-0" id="no_types_message">No waste types selected</p>
                            <div id="selected_badges_container" class="d-flex flex-wrap gap-2"></div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="resetWasteTypes">
                            <i class="bi bi-arrow-counterclockwise"></i> Clear Selection
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Additional Details</h10>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Add any additional information about the collection..."></textarea>
                    @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                            </div>
                        </div>
        <!-- Media Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Add Photos/Videos</h10>
            </div>
                                            <div class="card-body">
                <div class="media-upload-section mb-4">
                    <div class="upload-area" id="uploadArea">
                        <i class="bi bi-cloud-upload"></i>
                        <p>Drag & drop files here or click to select</p>
                        <small class="text-muted">Supported formats: Images and Videos (max 2MB)</small>
                        <input type="file" id="fileInput" name="media[]" accept="image/*,video/*" multiple class="d-none">
                        </div>

                    <div class="preview-container position-relative">
                        <div id="mediaContainer" class="row g-3">
                            <!-- Preview items will be added here -->
                        </div>
                        <div class="preview-navigation d-none">
                            <button type="button" class="btn btn-light preview-nav" id="prevPreview">
                                <i class="bi bi-arrow-left-short fs-4"></i>
                            </button>
                            <div id="slideCounter" class="slide-counter">0 / 0</div>
                            <button type="button" class="btn btn-light preview-nav" id="nextPreview">
                                <i class="bi bi-arrow-right-short fs-4"></i>
                            </button>
                                                            </div>
                                                        </div>
                                            </div>
                                        </div>
                                    </div>


        <!-- Submit Buttons -->
        <div class="d-flex gap-3 justify-content-end">
            <a href="{{ route('collecte.cluster') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Create Collection
                            </button>
                        </div>
                    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Add all the styles from signal/create.blade.php */
    .mb-7 { margin-bottom: 7rem !important; }

    .waste-signal-form .card {
        border: none;
        transition: transform 0.2s;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }

    .waste-signal-form .card-header {
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
        border: none;
        padding: 1rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    .btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .signal-card {
        transition: all 0.3s ease;
    }
    
    .signal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    #map {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* New Waste Type Selection Styles */
    .select2-container--default .select2-selection--multiple {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem;
        min-height: 100px;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    .waste-type-badge {
        background-color: #364e9c;
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.9rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-flex;
        align-items: center;
    }

    .waste-type-badge .badge-remove {
        margin-left: 0.5rem;
        cursor: pointer;
    }

    .min-height-100 {
        min-height: 100px;
    }

    #selected_badges_container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-top: 0.5rem;
    }

    /* Media Upload Styles */
    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-area:hover, .upload-area.dragover {
        border-color: var(--primary-gradient-start);
        background-color: rgba(14, 52, 106, 0.05);
    }

    .upload-area i {
        font-size: 2rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }

    .media-preview-container {
        position: relative;
        min-height: 100px;
    }

    .preview-item {
        position: relative;
        width: 100%;
        height: 200px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .preview-item img, 
    .preview-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .preview-item .remove-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .preview-item .remove-btn:hover {
        background: #dc3545;
        transform: scale(1.1);
    }

    .preview-navigation {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        z-index: 10;
        pointer-events: none;
    }

    .preview-nav {
        pointer-events: auto;
        background: rgba(0, 0, 0, 0.5) !important;
        color: white !important;
        border: none !important;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        margin: 0 0.5rem;
        transition: all 0.2s ease;
    }

    .preview-nav:hover {
        background: rgba(0, 0, 0, 0.7) !important;
        transform: scale(1.1);
    }

    .slide-counter {
        pointer-events: auto;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .preview-navigation {
            padding: 0 0.5rem;
        }

        .preview-nav {
            width: 35px;
            height: 35px;
        }

        .slide-counter {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
        }

        .preview-item {
            height: 150px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the collection location map
        const locationMap = L.map('map', {
            center: [{{ $centerLat }}, {{ $centerLng }}],
            zoom: 13,
            zoomControl: true
        });

        // Add the OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(locationMap);

        // Create draggable marker
        let locationMarker = L.marker([{{ $centerLat }}, {{ $centerLng }}], {
            draggable: true
        }).addTo(locationMap);

        // Update form inputs when marker is dragged
        locationMarker.on('dragend', function(event) {
            const position = locationMarker.getLatLng();
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
        });

        // Reset Map Pin Handler
        document.getElementById('resetMapPin').addEventListener('click', function() {
            const initialLat = {{ $centerLat }};
            const initialLng = {{ $centerLng }};
            
            locationMarker.setLatLng([initialLat, initialLng]);
            locationMap.setView([initialLat, initialLng], 13);
            
            document.getElementById('latitude').value = initialLat;
            document.getElementById('longitude').value = initialLng;
        });

        @unless($isUrgent)
            // Initialize signals map
            const signalsMap = L.map('signalsMap', {
                center: [{{ $centerLat }}, {{ $centerLng }}],
                zoom: 13,
                zoomControl: true
            });

            // Add the OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(signalsMap);

            // Store markers and selected signals
            const markers = {};
            let selectedSignals = new Set({{ json_encode($signals ? $signals->pluck('id') : []) }});

            // Define marker colors
            const markerColors = {
                selected: '#0d6efd',   // blue for selected
                unselected: '#dc3545', // red for unselected
                validated: '#198754',  // green for validated status
                pending: '#ffc107'     // yellow for pending status
            };

            // Create markers for all signals
            @if($signals)
                @foreach($signals as $signal)
                    const marker{{ $signal->id }} = L.circleMarker(
                        [{{ $signal->latitude }}, {{ $signal->longitude }}],
                        {
                            radius: 8,
                            fillColor: markerColors.selected,
                            color: '#fff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.8
                        }
                    ).addTo(signalsMap);

                    // Add popup with signal info and toggle button
                    marker{{ $signal->id }}.bindPopup(`
                        <div class="text-center">
                            <strong>{{ $signal->location }}</strong><br>
                            Volume: {{ $signal->volume }}m³<br>
                            Status: <span class="badge bg-${getStatusColor('{{ $signal->status }}')}">
                                {{ ucfirst($signal->status) }}
                            </span><br>
                            <button type="button" 
                                    class="btn btn-sm btn-toggle mt-2 ${selectedSignals.has({{ $signal->id }}) ? 'btn-danger' : 'btn-primary'}"
                                    onclick="toggleSignal({{ $signal->id }})">
                                ${selectedSignals.has({{ $signal->id }}) ? 'Remove' : 'Add'} Signal
                            </button>
                        </div>
                    `);

                    markers[{{ $signal->id }}] = marker{{ $signal->id }};
                @endforeach

                // Fit map bounds to show all signals
                const bounds = L.featureGroup(Object.values(markers)).getBounds();
                signalsMap.fitBounds(bounds);
            @endif
        @endunless

        // Helper function to get status color
        function getStatusColor(status) {
            return status === 'validated' ? 'success' : 'warning';
        }

        // Function to toggle signal selection
        window.toggleSignal = function(signalId) {
            if (selectedSignals.has(signalId)) {
                selectedSignals.delete(signalId);
                markers[signalId].setStyle({ fillColor: markerColors.unselected });
            } else {
                selectedSignals.add(signalId);
                markers[signalId].setStyle({ fillColor: markerColors.selected });
            }

            // Update hidden input and counter
            document.getElementById('finalSignalIds').value = JSON.stringify(Array.from(selectedSignals));
            document.getElementById('selectedCount').textContent = `${selectedSignals.size} signals selected`;
            
            // Update popup content
            const marker = markers[signalId];
            const popup = marker.getPopup();
            const content = popup.getContent();
            popup.setContent(content.replace(
                selectedSignals.has(signalId) ? 'btn-primary' : 'btn-danger',
                selectedSignals.has(signalId) ? 'btn-danger' : 'btn-primary'
            ).replace(
                selectedSignals.has(signalId) ? 'Add' : 'Remove',
                selectedSignals.has(signalId) ? 'Remove' : 'Add'
            ));
        }
    });
</script>
@endpush
@endsection 