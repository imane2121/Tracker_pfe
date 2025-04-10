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
        // Initialize waste type select with Select2
        $('.waste-type-select').select2({
            placeholder: "Select waste types",
            allowClear: true,
            width: '100%'
        });

        // Handle waste type selection
        $('#waste_type_selector').on('change', function() {
            const selectedOptions = $(this).find(':selected');
            const container = $('#selected_waste_types_container');
            const badgesContainer = $('#selected_badges_container');
            
            // Clear current inputs and badges
            container.empty();
            badgesContainer.empty();
            
            if (selectedOptions.length === 0) {
                $('#no_types_message').show();
                return;
            }
            
            $('#no_types_message').hide();
            
            // Add hidden inputs and visual badges for each selected option
            selectedOptions.each(function() {
                const typeId = $(this).val();
                const typeName = $(this).text();
                
                // Add hidden input for form submission
                container.append(`<input type="hidden" name="waste_types[]" value="${typeId}">`);
                
                // Add visual badge
                badgesContainer.append(`
                    <span class="waste-type-badge" data-type-id="${typeId}">
                        ${typeName}
                        <span class="badge-remove ms-2" onclick="removeWasteType(${typeId})">
                            <i class="bi bi-x"></i>
                        </span>
                    </span>
                `);
            });
        });
        
        // Reset waste types selection
        $('#resetWasteTypes').click(function() {
            $('#waste_type_selector').val(null).trigger('change');
            $('#selected_waste_types_container').empty();
            $('#selected_badges_container').empty();
            $('#no_types_message').show();
        });

        // Add to global scope for onclick handler
        window.removeWasteType = function(typeId) {
            const select = $('#waste_type_selector');
            
            // Remove from select2
            const currentValues = select.val() || [];
            const newValues = currentValues.filter(value => value != typeId);
            select.val(newValues).trigger('change');
        };
        
        // Initialize the collection location map separately
        const locationMap = L.map('map').setView([{{ $centerLat }}, {{ $centerLng }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(locationMap);

        // Create draggable marker
        let locationMarker = L.marker([{{ $centerLat }}, {{ $centerLng }}], {
            draggable: true
        }).addTo(locationMap);

        // Update form inputs immediately when marker is dragged
        locationMarker.on('dragend', function(event) {
            const position = locationMarker.getLatLng();
            
            // Update hidden inputs
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
            
            // Debug log
            console.log('New marker position:', {
                lat: position.lat,
                lng: position.lng,
                formLat: document.getElementById('latitude').value,
                formLng: document.getElementById('longitude').value
            });
        });

        // Form submission validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Get final coordinates
            const finalLat = document.getElementById('latitude').value;
            const finalLng = document.getElementById('longitude').value;
            
            // Debug log before submission
            console.log('Submitting coordinates:', {
                lat: finalLat,
                lng: finalLng,
                markerPosition: locationMarker.getLatLng()
            });
        });

        // Reset Map Pin Handler with proper coordinate update
        document.getElementById('resetMapPin').addEventListener('click', function() {
            const initialLat = {{ $centerLat }};
            const initialLng = {{ $centerLng }};
            
            locationMarker.setLatLng([initialLat, initialLng]);
            locationMap.setView([initialLat, initialLng], 13);
            
            // Update form inputs
            document.getElementById('latitude').value = initialLat;
            document.getElementById('longitude').value = initialLng;
            
            // Debug log
            console.log('Reset to initial coordinates:', {
                lat: initialLat,
                lng: initialLng
            });
        });

        // Set minimum date for starting_date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('starting_date').min = today;

        // Update end_date minimum when starting_date changes
        document.getElementById('starting_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });

        // Media Upload Handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const mediaContainer = document.getElementById('mediaContainer');

        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles({ target: { files } });
        });

        fileInput.addEventListener('change', handleFiles);

        function handleFiles(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('File size should not exceed 2MB');
                    return;
                }

                if (!file.type.match('image.*') && !file.type.match('video.*')) {
                    alert('Only image and video files are allowed');
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => createPreviewItem(e.target.result, file.type);
                reader.readAsDataURL(file);
            });
        }

        function createPreviewItem(src, type) {
            const col = document.createElement('div');
            col.className = 'col-md-4 mb-3';
            
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';

            const media = type.startsWith('image/') ? 
                document.createElement('img') : 
                document.createElement('video');

            media.src = src;
            if (type.startsWith('video/')) {
                media.controls = true;
            }
            previewItem.appendChild(media);

            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.onclick = () => col.remove();

            previewItem.appendChild(removeBtn);
            col.appendChild(previewItem);
            mediaContainer.appendChild(col);
        }

        // Replace the signals map initialization section with:
        @if(!$isUrgent)
            // Initialize signals map
            const signalsMap = L.map('signalsMap').setView([{{ $centerLat }}, {{ $centerLng }}], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
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
            @endif
        @else
            // For urgent collectes
            const markers = {};
            let selectedSignals = new Set([]);
        @endif

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

        // Helper function to get status color
        function getStatusColor(status) {
            return status === 'validated' ? 'success' : 'warning';
        }

        // Fit map bounds to show all signals
        @if(!$isUrgent && $signals && $signals->count() > 0)
            const bounds = L.featureGroup(Object.values(markers)).getBounds();
            signalsMap.fitBounds(bounds);
        @endif

        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(e) {
            @if(!$isUrgent)
                if (selectedSignals.size === 0) {
                    e.preventDefault();
                    alert('Please select at least one signal for the collection.');
                }
            @endif
        });

        // Get waste types from selected signals
        @if(!$isUrgent && $signals)
            const signalWasteTypes = @json($signals->pluck('waste_types')->flatten()->unique());

            // Pre-select waste types from signals
            signalWasteTypes.forEach(wasteTypeId => {
                // Find the specific type button
                const specificTypeBtn = document.querySelector(`.wsf-specific-type[data-specific-id="${wasteTypeId}"]`);
                if (specificTypeBtn) {
                    // Get parent type button and container
                    const parentId = specificTypeBtn.dataset.parentId;
                    const parentBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
                    const subtypesContainer = document.getElementById(`subTypes_${parentId}`);

                    // Show subtypes container
                    if (subtypesContainer) {
                        subtypesContainer.classList.add('show');
                        parentBtn.classList.add('expanded');
                    }

                    // Activate the specific type
                    specificTypeBtn.classList.add('active');
                    const input = specificTypeBtn.previousElementSibling;
                    if (input) {
                        input.disabled = false;
                    }

                    // Update parent button state
                    if (parentBtn) {
                        parentBtn.classList.add('has-selected');
                    }

                    // Add to selected types set
                    selectedTypes.add(wasteTypeId.toString());
                }
            });
        @else
            const signalWasteTypes = [];
        @endif

        // Store initial coordinates
        const initialLat = {{ $centerLat }};
        const initialLng = {{ $centerLng }};

        // Reset Waste Types Handler
        document.getElementById('resetWasteTypes').addEventListener('click', function() {
            // Reset all specific types
            document.querySelectorAll('.wsf-specific-type.active').forEach(button => {
                button.classList.remove('active');
                const input = button.previousElementSibling;
                if (input) {
                    input.disabled = true;
                }
            });

            // Reset all parent buttons
            document.querySelectorAll('.wsf-general-type.has-selected').forEach(button => {
                button.classList.remove('has-selected');
            });

            // Clear selected types set
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the collection location map separately
    const locationMap = L.map('map').setView([{{ $centerLat }}, {{ $centerLng }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(locationMap);

    // Create draggable marker
    let locationMarker = L.marker([{{ $centerLat }}, {{ $centerLng }}], {
        draggable: true
    }).addTo(locationMap);

    // Update form inputs immediately when marker is dragged
    locationMarker.on('dragend', function(event) {
        const position = locationMarker.getLatLng();
        
        // Update hidden inputs
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
        
        // Debug log
        console.log('New marker position:', {
            lat: position.lat,
            lng: position.lng,
            formLat: document.getElementById('latitude').value,
            formLng: document.getElementById('longitude').value
        });
    });

    // Form submission validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Get final coordinates
        const finalLat = document.getElementById('latitude').value;
        const finalLng = document.getElementById('longitude').value;
        
        // Debug log before submission
        console.log('Submitting coordinates:', {
            lat: finalLat,
            lng: finalLng,
            markerPosition: locationMarker.getLatLng()
        });
    });

    // Reset Map Pin Handler with proper coordinate update
    document.getElementById('resetMapPin').addEventListener('click', function() {
        const initialLat = {{ $centerLat }};
        const initialLng = {{ $centerLng }};
        
        locationMarker.setLatLng([initialLat, initialLng]);
        locationMap.setView([initialLat, initialLng], 13);
        
        // Update form inputs
        document.getElementById('latitude').value = initialLat;
        document.getElementById('longitude').value = initialLng;
        
        // Debug log
        console.log('Reset to initial coordinates:', {
            lat: initialLat,
            lng: initialLng
        });
    });

    // Set minimum date for starting_date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('starting_date').min = today;

    // Update end_date minimum when starting_date changes
    document.getElementById('starting_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });

    // Media Upload Handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const mediaContainer = document.getElementById('mediaContainer');

    uploadArea.addEventListener('click', () => fileInput.click());
    
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles({ target: { files } });
    });

    fileInput.addEventListener('change', handleFiles);

    function handleFiles(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                alert('File size should not exceed 2MB');
                return;
            }

            if (!file.type.match('image.*') && !file.type.match('video.*')) {
                alert('Only image and video files are allowed');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => createPreviewItem(e.target.result, file.type);
            reader.readAsDataURL(file);
        });
    }

    function createPreviewItem(src, type) {
        const col = document.createElement('div');
        col.className = 'col-md-4 mb-3';
        
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';

        const media = type.startsWith('image/') ? 
            document.createElement('img') : 
            document.createElement('video');

        media.src = src;
        if (type.startsWith('video/')) {
            media.controls = true;
        }
        previewItem.appendChild(media);

        const removeBtn = document.createElement('button');
        removeBtn.className = 'remove-btn';
        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
        removeBtn.onclick = () => col.remove();

        previewItem.appendChild(removeBtn);
        col.appendChild(previewItem);
        mediaContainer.appendChild(col);
    }

    const generalTypes = document.querySelectorAll('.wsf-general-type');
    let selectedTypes = new Set();

    // Handle parent type clicks
    generalTypes.forEach(button => {
        button.addEventListener('click', function() {
            if (this.id === 'autreBtn') {
                handleAutreButton();
                return;
            }

            const wasteTypeId = this.dataset.wasteType;
            const subtypes = document.getElementById(`subTypes_${wasteTypeId}`);
            
            if (subtypes) {
                // Toggle subtypes visibility
                subtypes.classList.toggle('show');
                this.classList.toggle('expanded');
                
                // Update other buttons if needed
                if (this.id !== 'autreBtn') {
                    document.getElementById('autreInputContainer').classList.remove('show');
                    document.getElementById('autreBtn').classList.remove('expanded');
                }
            }
        });
    });
    
    // Handle specific type selection
    const specificTypes = document.querySelectorAll('.wsf-specific-type');
    specificTypes.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event bubbling
            
            const parentId = this.dataset.parentId;
            const input = this.previousElementSibling;
            const parentButton = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
            
            // Toggle selection
            this.classList.toggle('active');
            input.disabled = !this.classList.contains('active');

            // Update selected types set
            if (this.classList.contains('active')) {
                selectedTypes.add(this.dataset.specificId);
            } else {
                selectedTypes.delete(this.dataset.specificId);
            }

            // Update parent button state
            const hasSelected = document.querySelectorAll(
                `.wsf-specific-type[data-parent-id="${parentId}"].active`
            ).length > 0;
            parentButton.classList.toggle('has-selected', hasSelected);
        });
    });

    function handleAutreButton() {
        const autreBtn = document.getElementById('autreBtn');
        const autreInputContainer = document.getElementById('autreInputContainer');
        
        // Toggle autre input
        autreInputContainer.classList.toggle('show');
        autreBtn.classList.toggle('expanded');
        
        if (autreInputContainer.classList.contains('show')) {
            document.getElementById('autreInput').focus();
        }
    }

    // Close other expanded items when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.wsf-type-group')) {
            document.querySelectorAll('.wsf-subtypes.show').forEach(subtypes => {
                if (subtypes.id !== 'autreInputContainer') {
                    subtypes.classList.remove('show');
                    subtypes.previousElementSibling.classList.remove('expanded');
                }
            });
        }
    });

    // Replace the signals map initialization section with:
    @if(!$isUrgent)
        // Initialize signals map
        const signalsMap = L.map('signalsMap').setView([{{ $centerLat }}, {{ $centerLng }}], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
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
        @endif
    @else
        // For urgent collectes
        const markers = {};
        let selectedSignals = new Set([]);
    @endif

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

    // Helper function to get status color
    function getStatusColor(status) {
        return status === 'validated' ? 'success' : 'warning';
    }

    // Fit map bounds to show all signals
    @if(!$isUrgent && $signals && $signals->count() > 0)
        const bounds = L.featureGroup(Object.values(markers)).getBounds();
        signalsMap.fitBounds(bounds);
    @endif

    // Add form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
        @if(!$isUrgent)
            if (selectedSignals.size === 0) {
                e.preventDefault();
                alert('Please select at least one signal for the collection.');
            }
        @endif
    });

    // Get waste types from selected signals
    @if(!$isUrgent && $signals)
        const signalWasteTypes = @json($signals->pluck('waste_types')->flatten()->unique());

        // Pre-select waste types from signals
        signalWasteTypes.forEach(wasteTypeId => {
            // Find the specific type button
            const specificTypeBtn = document.querySelector(`.wsf-specific-type[data-specific-id="${wasteTypeId}"]`);
            if (specificTypeBtn) {
                // Get parent type button and container
                const parentId = specificTypeBtn.dataset.parentId;
                const parentBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
                const subtypesContainer = document.getElementById(`subTypes_${parentId}`);

                // Show subtypes container
                if (subtypesContainer) {
                    subtypesContainer.classList.add('show');
                    parentBtn.classList.add('expanded');
                }

                // Activate the specific type
                specificTypeBtn.classList.add('active');
                const input = specificTypeBtn.previousElementSibling;
                if (input) {
                    input.disabled = false;
                }

                // Update parent button state
                if (parentBtn) {
                    parentBtn.classList.add('has-selected');
                }

                // Add to selected types set
                selectedTypes.add(wasteTypeId.toString());
            }
        });
    @else
        const signalWasteTypes = [];
    @endif

    // Store initial coordinates
    const initialLat = {{ $centerLat }};
    const initialLng = {{ $centerLng }};

    // Reset Waste Types Handler
    document.getElementById('resetWasteTypes').addEventListener('click', function() {
        // Reset all specific types
        document.querySelectorAll('.wsf-specific-type.active').forEach(button => {
            button.classList.remove('active');
            const input = button.previousElementSibling;
            if (input) {
                input.disabled = true;
            }
        });

        // Reset all parent buttons
        document.querySelectorAll('.wsf-general-type.has-selected').forEach(button => {
            button.classList.remove('has-selected');
        });

        // Clear selected types set
        selectedTypes.clear();

        // Hide all expanded subtype containers
        document.querySelectorAll('.wsf-subtypes.show').forEach(container => {
            container.classList.remove('show');
        });

        // Remove expanded state from parent buttons
        document.querySelectorAll('.wsf-general-type.expanded').forEach(button => {
            button.classList.remove('expanded');
        });

        // Re-initialize with signal waste types
        signalWasteTypes.forEach(wasteTypeId => {
            const specificTypeBtn = document.querySelector(`.wsf-specific-type[data-specific-id="${wasteTypeId}"]`);
            if (specificTypeBtn) {
                const parentId = specificTypeBtn.dataset.parentId;
                const parentBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
                const subtypesContainer = document.getElementById(`subTypes_${parentId}`);

                if (subtypesContainer) {
                    subtypesContainer.classList.add('show');
                    parentBtn.classList.add('expanded');
                }

                specificTypeBtn.classList.add('active');
                const input = specificTypeBtn.previousElementSibling;
                if (input) {
                    input.disabled = false;
                }

                if (parentBtn) {
                    parentBtn.classList.add('has-selected');
                }

                selectedTypes.add(wasteTypeId.toString());
            }
        });
    });
});
</script>
@endpush
@endsection 