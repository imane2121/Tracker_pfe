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
                    <span class="highlight">Create Collection</span>
                    <span class="highlight">From Selected Signals</span>
                </h2>
                <p class="message">
                    Organize cleanup efforts and make a difference
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('collecte.store') }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
                        @csrf
        <input type="hidden" name="signal_ids" value="{{ json_encode($signals->pluck('id')) }}">

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
                            <input type="text" class="form-control @error('region') is-invalid @enderror" 
                                   id="region" name="region" required>
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
                    </div>
                </div>
            </div>
        </div>

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
            <div class="card-header">
                <h10 class="mb-0">Waste Types</h10>
            </div>
            <div class="card-body">
                <div class="wsf-buttons-container d-flex flex-wrap gap-2">
                    @foreach($wasteTypes as $wasteType)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                   name="waste_types[]" value="{{ $wasteType->id }}" 
                                   id="waste_type_{{ $wasteType->id }}">
                            <label class="form-check-label" for="waste_type_{{ $wasteType->id }}">
                                {{ $wasteType->name }}
                                                        </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('waste_types')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
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

    .wsf-buttons-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .form-check {
        padding: 1rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-check:hover {
        border-color: var(--primary-gradient-start);
        background-color: rgba(32, 84, 144, 0.05);
    }

    .form-check-input:checked ~ .form-check-label {
        color: var(--primary-gradient-start);
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .wsf-buttons-container {
            grid-template-columns: 1fr;
        }
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    const map = L.map('map').setView([{{ $centerLat }}, {{ $centerLng }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add marker for collection center
    let marker = L.marker([{{ $centerLat }}, {{ $centerLng }}], {
        draggable: true
    }).addTo(map);

    // Update coordinates when marker is dragged
    marker.on('dragend', function(e) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
    });

    // Update marker when coordinates are manually changed
    document.getElementById('latitude').addEventListener('change', updateMarker);
    document.getElementById('longitude').addEventListener('change', updateMarker);

    function updateMarker() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng]);
        }
    }

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
});
</script>
@endpush
@endsection 