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
                    <span class="highlight">Edit Your</span>
                    <span class="highlight">Report</span>
                </h2>
                <p class="message">
                    Update your report to help us protect marine life
                    and keep our waters clean
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('signal.update', $signal->id) }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
        @csrf
        @method('PUT')
        
        <!-- Waste Types Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Please Select Waste Type</h10>
            </div>
            <div class="card-body">
                <div class="waste-type-selection">
                    <!-- Main Category Dropdown -->
                    <div class="form-group mb-3">
                        <label for="generalWasteType" class="form-label">Main Category</label>
                        <select class="form-select main-category" id="generalWasteType">
                            <option value="">Select a main category</option>
                            @foreach($wasteTypes as $wasteType)
                                <option value="{{ $wasteType->id }}" {{ in_array($wasteType->id, $signal->wasteTypes->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $wasteType->name }}
                                </option>
                            @endforeach
                            <option value="other" {{ $signal->custom_type ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <!-- Specific Types Container -->
                    <div class="specific-types-container mb-3" style="display: none;">
                        <label class="form-label">Specific Types</label>
                        <div class="specific-types-grid">
                            @foreach($wasteTypes as $wasteType)
                                <div class="specific-type-group" data-parent="{{ $wasteType->id }}" style="display: none;">
                                    @foreach($wasteType->specificWasteTypes as $specificType)
                                        <div class="form-check specific-type-item">
                                            <input type="checkbox" 
                                                class="form-check-input specific-type-checkbox" 
                                                name="waste_types[]" 
                                                value="{{ $specificType->id }}" 
                                                id="type_{{ $specificType->id }}"
                                                {{ in_array($specificType->id, old('waste_types', $signal->wasteTypes->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="type_{{ $specificType->id }}">
                                                {{ $specificType->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Other Type Input -->
                    <div class="form-group other-type-input" style="display: none;">
                        <label for="customType" class="form-label">Specify Other Type</label>
                        <input type="text" class="form-control" id="customType" name="customType" 
                               value="{{ old('customType', $signal->custom_type) }}" placeholder="Enter waste type">
                    </div>

                    <!-- Selected Types Preview -->
                    <div class="selected-types-preview mt-3" style="display: none;">
                        <label class="form-label">Selected Types:</label>
                        <div class="selected-types-badges"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Location Details</h10>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Location Name</label>
                            <div class="mb-2">
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="{{ old('location', $signal->location) }}" required placeholder="Enter or validate a location">
                            </div>
                            <button type="button" class="btn btn-primary w-100 use-location-btn" id="validateLocationBtn" title="Validate Location">
                                <i class="bi bi-check-circle me-1"></i> Validate Location
                            </button>
                        </div>
                        <div class="mb-3 volume-input">
                            <label for="volume" class="form-label">Volume (m³)</label>
                            <input type="number" class="form-control" id="volume" name="volume" 
                                   value="{{ old('volume', $signal->volume) }}" min="0" step="0.1" required placeholder="Enter waste volume">
                        </div>
                        <div class="row coordinate-inputs">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" class="form-control" id="latitude" name="latitude" 
                                       value="{{ old('latitude', $signal->latitude) }}" step="any" required placeholder="Latitude">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" class="form-control" id="longitude" name="longitude" 
                                       value="{{ old('longitude', $signal->longitude) }}" step="any" required placeholder="Longitude">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100 use-location-btn" id="useLocationBtn">
                            <i class="bi bi-geo-alt"></i> Use My Location
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div id="locationMap" class="rounded shadow-sm" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h10 class="mb-0">Update Photos/Videos</h10>
            </div>
            <div class="card-body">
                <div class="media-upload-section mb-4">
                    <div class="upload-container">
                        <div class="upload-area" id="uploadArea" style="cursor: pointer;">
                            <i class="bi bi-cloud-upload"></i>
                            <p>Drag & drop files here or click to select</p>
                            <small class="text-muted">Supported formats: Images and Videos (max 10MB)</small>
                            <input type="file" id="fileInput" name="media[]" accept="image/*,video/*" multiple class="d-none">
                        </div>
                    </div>
                    
                    <div class="preview-container position-relative">
                        <div id="mediaContainer" class="row g-3">
                            <!-- Existing media previews -->
                            @foreach($signal->media as $media)
                                <div class="col-md-4 mb-3">
                                    <div class="preview-item">
                                        @if(str_contains($media->file_path, '.jpg') || str_contains($media->file_path, '.jpeg') || str_contains($media->file_path, '.png') || str_contains($media->file_path, '.gif'))
                                            <img src="{{ Storage::url($media->file_path) }}" alt="Media preview" class="media-preview">
                                        @else
                                            <video src="{{ Storage::url($media->file_path) }}" controls class="media-preview"></video>
                                        @endif
                                        <button type="button" class="remove-btn" data-media-id="{{ $media->id }}">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Add any additional information about the waste...">{{ old('description', $signal->description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex gap-3 justify-content-end w-100 align-items-center">
            <a href="{{ route('signal.index') }}" class="btn btn-outline-secondary btn-lg cancel-btn">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
            <button type="submit" class="btn btn-success btn-lg btn btn-primary w-50 use-location-btn ms-auto">
                <i class="bi bi-check-circle"></i> Update Report
            </button>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Add all the styles from the create page */
    .mb-7 {
        margin-bottom: 7rem !important;
    }

    .waste-signal-form .card {
        border: none;
        transition: transform 0.2s;
    }

    .waste-signal-form .card:hover {
        transform: translateY(-2px);
    }

    .waste-signal-form .card-header {
        border-bottom: none;
        padding: 1rem;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
    }

    /* Add all other styles from the create page */
    /* ... (copy all the styles from create.blade.php) ... */

    /* Media Upload Styles */
    .upload-container {
        width: 100%;
    }

    .upload-area {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }

    .upload-area:hover, .upload-area.dragover {
        border-color: #0e346a;
        background-color: rgba(14, 52, 106, 0.05);
    }

    .upload-area i {
        font-size: 2.5rem;
        color: #0e346a;
        margin-bottom: 1rem;
    }

    .upload-area p {
        margin-bottom: 0.5rem;
        color: #495057;
        font-weight: 500;
    }

    .upload-area small {
        color: #6c757d;
    }

    .media-upload-section {
        width: 100%;
    }

    .media-actions {
        margin-top: 1rem;
        text-align: center;
        width: 100%;
    }

    .media-actions .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        border: none;
        color: white;
    }

    .media-actions .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .media-actions .btn i {
        margin-right: 0.5rem;
    }

    /* Media Preview Styles */
    .preview-container {
        margin-top: 1.5rem;
    }

    .preview-item {
        position: relative;
        width: 100%;
        height: 200px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .preview-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .media-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(130, 50, 50, 0.9);
        border: none;
        border-radius: 50%;
        width: 30px !important;
        height: 30px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: white;
        padding: 0;
        z-index: 10;
    }

    .remove-btn:hover {
        background: #fff;
        transform: scale(1.1);
        color: #dc3545;
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
        padding: 0 !important;
    }

    .preview-nav i {
        font-size: 1.5rem;
        line-height: 1;
    }

    .preview-nav:disabled {
        opacity: 0.3;
        cursor: not-allowed;
    }

    .preview-nav:not(:disabled):hover {
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
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .preview-navigation {
            padding: 0 0.5rem;
        }

        .preview-nav {
            width: 35px;
            height: 35px;
            margin: 0 0.25rem;
        }

        .preview-nav i {
            font-size: 1.25rem;
        }

        .slide-counter {
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
            min-width: 60px;
            text-align: center;
        }

        .preview-item {
            height: 150px;
        }
    }

    /* Camera Modal Styles */
    .camera-preview-container {
        position: relative;
        width: 100%;
        height: 100%;
        background-color: #000;
    }

    .camera-preview-container video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .camera-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
    }

    .camera-grid {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: linear-gradient(to right, rgba(255,255,255,0.1) 1px, transparent 1px),
                          linear-gradient(to bottom, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 20% 20%;
    }

    .camera-focus-point {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 2px solid rgba(255,255,255,0.5);
        border-radius: 50%;
    }

    .camera-controls {
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 1rem;
        background-color: rgba(0,0,0,0.5);
    }

    .camera-controls .btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        background-color: rgba(255,255,255,0.2);
        border: none;
        transition: all 0.3s ease;
    }

    .camera-controls .btn:hover {
        background-color: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .camera-controls .btn.active {
        background-color: #0e346a;
    }

    .capture-btn {
        width: 70px !important;
        height: 70px !important;
        background-color: #0e346a !important;
    }

    .capture-btn:hover {
        transform: scale(1.1);
    }

    .capture-btn.recording {
        background-color: #dc3545 !important;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* Waste Type Selection Styles */
    .waste-type-selection {
        max-width: 800px;
        margin: 0 auto;
    }

    .main-category {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .main-category:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    .specific-types-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .specific-type-item {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .specific-type-item:hover {
        border-color: var(--primary-gradient-start);
        background-color: #f8f9fa;
    }

    .form-check-input:checked + .form-check-label {
        color: var(--primary-gradient-start);
        font-weight: 500;
    }

    .selected-types-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .selected-type-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        gap: 0.5rem;
    }

    .remove-type {
        background: none;
        border: none;
        color: white;
        padding: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .remove-type:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    @media (max-width: 768px) {
        .specific-types-grid {
            grid-template-columns: 1fr;
        }

        .specific-type-item {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Waste Type Selection
    const mainCategorySelect = document.querySelector('.main-category');
    const specificTypesContainer = document.querySelector('.specific-types-container');
    const otherTypeInput = document.querySelector('.other-type-input');
    const selectedTypesPreview = document.querySelector('.selected-types-preview');
    const selectedTypesBadges = document.querySelector('.selected-types-badges');
    const specificTypeGroups = document.querySelectorAll('.specific-type-group');
    const specificTypeCheckboxes = document.querySelectorAll('.specific-type-checkbox');

    // Initialize with existing selections
    function initializeSelections() {
        const selectedValue = mainCategorySelect.value;
        if (selectedValue) {
            if (selectedValue === 'other') {
                specificTypesContainer.style.display = 'none';
                otherTypeInput.style.display = 'block';
            } else {
                specificTypesContainer.style.display = 'block';
                otherTypeInput.style.display = 'none';
                
                // Show specific types for selected category
                const selectedGroup = document.querySelector(`.specific-type-group[data-parent="${selectedValue}"]`);
                if (selectedGroup) {
                    selectedGroup.style.display = 'block';
                }
            }
        }
        updateSelectedTypesPreview();
    }

    // Handle main category selection
    mainCategorySelect.addEventListener('change', function() {
        const selectedValue = this.value;
        
        // Hide all specific type groups first
        specificTypeGroups.forEach(group => group.style.display = 'none');
        
        if (selectedValue === 'other') {
            specificTypesContainer.style.display = 'none';
            otherTypeInput.style.display = 'block';
        } else if (selectedValue) {
            specificTypesContainer.style.display = 'block';
            otherTypeInput.style.display = 'none';
            
            // Show specific types for selected category
            const selectedGroup = document.querySelector(`.specific-type-group[data-parent="${selectedValue}"]`);
            if (selectedGroup) {
                selectedGroup.style.display = 'block';
            }
        } else {
            specificTypesContainer.style.display = 'none';
            otherTypeInput.style.display = 'none';
        }
        
        updateSelectedTypesPreview();
    });

    // Handle checkbox changes
    specificTypeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedTypesPreview);
    });

    function updateSelectedTypesPreview() {
        const selectedTypes = Array.from(specificTypeCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => ({
                id: cb.value,
                name: cb.nextElementSibling.textContent.trim()
            }));

        if (selectedTypes.length > 0) {
            selectedTypesPreview.style.display = 'block';
            selectedTypesBadges.innerHTML = selectedTypes.map(type => `
                <div class="selected-type-badge">
                    ${type.name}
                    <button type="button" class="remove-type" data-id="${type.id}">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `).join('');

            // Add click handlers for remove buttons
            document.querySelectorAll('.remove-type').forEach(btn => {
                btn.addEventListener('click', function() {
                    const typeId = this.dataset.id;
                    const checkbox = document.querySelector(`input[value="${typeId}"]`);
                    if (checkbox) {
                        checkbox.checked = false;
                        updateSelectedTypesPreview();
                    }
                });
            });
        } else {
            selectedTypesPreview.style.display = 'none';
        }
    }

    // Initialize selections on page load
    initializeSelections();

    // Volume Input
    const volumeInput = document.getElementById('volume');
    if (volumeInput) {
        volumeInput.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (value < 0) {
                this.value = 0;
            }
        });
    }

    // Initialize map with the signal's coordinates
    var map = L.map('locationMap').setView([{{ $signal->latitude }}, {{ $signal->longitude }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add marker for the signal's location
    var marker = L.marker([{{ $signal->latitude }}, {{ $signal->longitude }}]).addTo(map);

    // Define Morocco's coastal boundaries (rough approximation)
    const coastalBoundaries = [
        // Atlantic Coast (North to South)
        { lat: 35.9191, lng: -5.8659 },  // Tangier
        { lat: 34.0531, lng: -6.7988 },  // Rabat
        { lat: 33.5992, lng: -7.6338 },  // Casablanca
        { lat: 32.2994, lng: -9.2372 },  // El Jadida
        { lat: 31.5085, lng: -9.7595 },  // Essaouira
        { lat: 30.4278, lng: -9.5981 },  // Agadir
        { lat: 28.4520, lng: -11.1514 }, // Sidi Ifni
        { lat: 27.9397, lng: -12.9264 }, // Laayoune
        { lat: 23.7141, lng: -15.9369 }, // Dakhla
        
        // Mediterranean Coast (East to West)
        { lat: 35.1736, lng: -2.9287 },  // Nador
        { lat: 35.2540, lng: -3.9375 },  // Al Hoceima
        { lat: 35.5689, lng: -5.3565 }   // Tetouan
    ];

    // Maximum distance from coast in kilometers
    const MAX_DISTANCE_FROM_COAST = 5;

    // Function to calculate distance between two points using Haversine formula
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in kilometers
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Function to check if location is near the coast
    function isNearCoast(lat, lng) {
        let minDistance = Infinity;
        let nearestPoint = null;

        // Convert coordinates to numbers to ensure proper comparison
        lat = parseFloat(lat);
        lng = parseFloat(lng);

        coastalBoundaries.forEach(point => {
            const distance = calculateDistance(lat, lng, point.lat, point.lng);
            if (distance < minDistance) {
                minDistance = distance;
                nearestPoint = point;
            }
        });

        return {
            isValid: minDistance <= MAX_DISTANCE_FROM_COAST,
            distance: minDistance,
            nearestPoint: nearestPoint
        };
    }

    // Function to update marker position with coastal validation
    function updateMarkerPosition(lat, lng, shouldZoom = true) {
        // Convert coordinates to numbers
        lat = parseFloat(lat);
        lng = parseFloat(lng);
        
        const coastalCheck = isNearCoast(lat, lng);
        
        if (!coastalCheck.isValid) {
            Swal.fire({
                title: 'Invalid Location',
                html: `This location is too far from the coast.<br>
                      Please select a location within ${MAX_DISTANCE_FROM_COAST}km of the coastline.<br>
                      Current distance: ${coastalCheck.distance.toFixed(2)}km`,
                icon: 'error'
            });
            return false;
        }

        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);
        if (shouldZoom) {
            map.setView([lat, lng], 13);
        }

        // Update form inputs
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Reverse geocode to get location name
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                const locationName = data.display_name;
                document.getElementById('location').value = locationName;
            })
            .catch(error => {
                console.error('Error getting location name:', error);
            });

        return true;
    }

    // Add coastal boundaries visualization
    const coastalLine = L.polyline(coastalBoundaries.map(point => [point.lat, point.lng]), {
        color: '#0e346a',
        weight: 3,
        opacity: 0.7,
        dashArray: '5, 10'
    }).addTo(map);

    // Add buffer zone visualization
    coastalBoundaries.forEach(point => {
        L.circle([point.lat, point.lng], {
            radius: MAX_DISTANCE_FROM_COAST * 1000, // Convert km to meters
            color: '#0e346a',
            fillColor: '#0e346a',
            fillOpacity: 0.1,
            weight: 1
        }).addTo(map);
    });

    // Handle map clicks with validation
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        const coastalCheck = isNearCoast(lat, lng);
        if (!coastalCheck.isValid) {
            Swal.fire({
                title: 'Invalid Location',
                html: `This location is too far from the coast.<br>
                      Please select a location within ${MAX_DISTANCE_FROM_COAST}km of the coastline.<br>
                      Current distance: ${coastalCheck.distance.toFixed(2)}km`,
                icon: 'error'
            });
            return;
        }
        
        updateMarkerPosition(lat, lng);
    });

    // Update location validation in the existing click handler
    document.getElementById('useLocationBtn').addEventListener('click', function() {
        if (!navigator.geolocation) {
            Swal.fire({
                title: 'Error',
                text: 'Geolocation is not supported by your browser',
                icon: 'error'
            });
            return;
        }

        // Show loading state
        const button = this;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-arrow-repeat"></i> Getting Location...';

        navigator.geolocation.getCurrentPosition(
            // Success callback
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                if (updateMarkerPosition(lat, lng)) {
                    // Show success message only if location is valid
                    Swal.fire({
                        title: 'Success',
                        text: 'Your location has been found',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
                
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            },
            // Error callback
            function(error) {
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;

                let errorMessage = 'An error occurred while getting your location';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Please allow location access to use this feature';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Location information is unavailable';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Location request timed out';
                        break;
                }

                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error'
                });
            },
            // Options
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // Update location validation button handler
    document.getElementById('validateLocationBtn').addEventListener('click', function() {
        const locationInput = document.getElementById('location').value;
        if (!locationInput) {
            Swal.fire({
                title: 'Error',
                text: 'Please enter a location to validate',
                icon: 'error'
            });
            return;
        }

        // Show loading state
        const button = this;
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-arrow-repeat"></i> Validating...';

        // Use Nominatim to geocode the location
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(locationInput)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const location = data[0];
                    if (updateMarkerPosition(location.lat, location.lon)) {
                        // Show success message only if location is valid
                        Swal.fire({
                            title: 'Success',
                            text: 'Location validated successfully',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                } else {
                    throw new Error('Location not found');
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error',
                    text: 'Could not find the specified location',
                    icon: 'error'
                });
            })
            .finally(() => {
                // Reset button
                button.disabled = false;
                button.innerHTML = originalText;
            });
    });

    // Media Upload Functionality
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const mediaContainer = document.getElementById('mediaContainer');

    // Make sure the file input is properly initialized
    if (uploadArea && fileInput) {
        // Click handler for the upload area
        uploadArea.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });

        // File input change handler
        fileInput.addEventListener('change', function(e) {
            handleFiles(e);
        });

        // Drag and drop handlers
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles({ target: { files } });
        });

        function handleFiles(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    Swal.fire({
                        title: 'Error',
                        text: 'File size should not exceed 10MB',
                        icon: 'error'
                    });
                    return;
                }

                if (!file.type.match('image.*') && !file.type.match('video.*')) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Only image and video files are allowed',
                        icon: 'error'
                    });
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    createPreviewItem(e.target.result, file.type);
                };
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
            media.className = 'media-preview';
            if (type.startsWith('video/')) {
                media.controls = true;
            }
            previewItem.appendChild(media);

            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.onclick = function() {
                col.remove();
            };

            previewItem.appendChild(removeBtn);
            col.appendChild(previewItem);
            mediaContainer.appendChild(col);
        }
    }

    // Handle removal of existing media
    document.querySelectorAll('.remove-btn[data-media-id]').forEach(btn => {
        btn.addEventListener('click', function() {
            const mediaId = this.dataset.mediaId;
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/signal/media/${mediaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.col-md-4').remove();
                            Swal.fire(
                                'Deleted!',
                                'Your media has been deleted.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete media.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the media.',
                            'error'
                        );
                    });
                }
            });
        });
    });
});
</script>
@endpush
@endsection 