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
                    <span class="highlight">Few Steps to</span>
                    <span class="highlight">A Cleaner Ocean</span>
                </h2>
                <p class="message">
                    Your report helps us protect marine life
                    and keep our waters clean
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('signal.store') }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
        @csrf
        
        <!-- Waste Types Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Please Select Waste Type</h4>
            </div>
            <div class="card-body">
                <div class="wsf-buttons-container d-flex flex-wrap gap-2">
                    @foreach($wasteTypes as $wasteType)
                        <div class="wsf-type-group">
                            <input type="hidden" name="general_waste_type[]" value="" class="wsf-type-input">
                            <button type="button" class="wsf-btn-option wsf-general-type" data-waste-type="{{ $wasteType->id }}">
                                {{ $wasteType->name }}
                            </button>
                            @if($wasteType->specificWasteTypes && $wasteType->specificWasteTypes->isNotEmpty())
                                <div class="wsf-subtypes" id="subTypes_{{ $wasteType->id }}">
                                    @foreach($wasteType->specificWasteTypes as $specificType)
                                        <div class="wsf-subtype-item">
                                            <input type="hidden" class="wsf-specific-input" 
                                                name="waste_types[]" 
                                                value="{{ $specificType->id }}" 
                                                data-parent-id="{{ $wasteType->id }}">
                                            <button type="button" class="wsf-btn-option wsf-specific-type" 
                                                data-specific-id="{{ $specificType->id }}"
                                                data-parent-id="{{ $wasteType->id }}">
                                                {{ $specificType->name }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                    
                    <!-- Custom Waste Type -->
                    <div class="wsf-type-group">
                        <button type="button" class="wsf-btn-option wsf-general-type" id="autreBtn">Other</button>
                        <div id="autreInputContainer" class="wsf-hidden">
                            <input type="text" name="customType" id="autreInput" 
                                class="form-control mt-2" placeholder="Enter waste type">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Location Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Location Name</label>
                            <div class="location-input-group">
                                <input type="text" class="form-control" id="location" name="location" required placeholder="Enter or validate a location">
                                <button type="button" class="btn" id="validateLocationBtn" title="Validate Location">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3 volume-input">
                            <label for="volume" class="form-label">Volume (m³)</label>
                            <input type="number" class="form-control" id="volume" name="volume" min="0" step="0.1" required placeholder="Enter waste volume">
                        </div>
                        <div class="row coordinate-inputs">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" class="form-control" id="latitude" name="latitude" step="any" required placeholder="Latitude">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" class="form-control" id="longitude" name="longitude" step="any" required placeholder="Longitude">
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
                <h4 class="mb-0">Add Photos/Videos</h4>
            </div>
            <div class="card-body">
                <div class="row g-3" id="mediaContainer">
                    <div class="col-md-4">
                        <div class="image-container position-relative">
                            <input type="file" class="d-none" name="media[]" accept="image/*,video/*">
                            <div class="image-preview d-flex align-items-center justify-content-center">
                                <i class="bi bi-plus-circle display-4 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Additional Details</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                        placeholder="Add any additional information about the waste..."></textarea>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex gap-3 justify-content-end">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check-circle"></i> Submit Report
            </button>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* Add spacing utility class */
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

    .wsf-btn-option {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        margin: 0.25rem;
        border: none;
        border-radius: 25px;
        background-color: #e9ecef;
        color: #495057;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .wsf-btn-option:hover {
        background-color: #dee2e6;
        transform: translateY(-1px);
    }

    .wsf-btn-option.active {
        background-color: #198754;
        color: white;
    }

    .wsf-btn-option.has-selected {
        background-color: #198754;
        color: white;
    }

    .wsf-type-group {
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .wsf-back-button {
        position: absolute;
        top: 0;
        left: -30px;
        padding: 5px 8px;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        color: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .wsf-back-button:hover {
        transform: scale(1.1);
    }

    .wsf-back-button.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .wsf-subtypes {
        display: none;
        margin-left: 1.5rem;
        margin-top: 0.5rem;
        padding-left: 1rem;
        border-left: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .wsf-subtypes.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    .wsf-subtype-item {
        margin: 0.5rem 0;
    }

    .wsf-specific-type {
        background-color: #f8f9fa !important;
        font-size: 0.95em;
        padding: 0.5rem 1rem;
    }

    .wsf-specific-type:hover {
        background-color: #e9ecef !important;
    }

    .wsf-specific-type.selected {
        background-color: #198754 !important;
        color: white;
    }

    .image-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-container:hover {
        border-color: #198754;
    }

    .image-preview {
        width: 100%;
        height: 100%;
        background-color: #f8f9fa;
    }

    .image-preview img,
    .image-preview video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .wsf-hidden {
        display: none !important;
    }

    @media (max-width: 768px) {
        .wsf-buttons-container {
            gap: 0.5rem;
        }

        .wsf-type-group {
            flex: 1 1 100%;
        }

        .wsf-btn-option {
            width: 100%;
            text-align: center;
        }

        .waste-signal-form .image-container {
            width: 120px;
            height: 120px;
        }

        .location-input-group .form-control {
            font-size: 0.95rem;
        }

        .use-location-btn {
            margin-top: 1rem;
        }
    }

    #locationMap {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    #locationMap:hover {
        border-color: var(--primary-color);
    }

    .location-marker {
        animation: markerPulse 1.5s infinite;
    }

    @keyframes markerPulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .map-tooltip {
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Location Input Styles */
    .location-input-group {
        position: relative;
    }

    .location-input-group .form-control {
        padding-right: 45px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .location-input-group .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    .location-input-group .btn {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 0 8px 8px 0;
    }

    .location-input-group .btn:hover {
        color: var(--primary-gradient-start);
        background-color: rgba(32, 84, 144, 0.1);
    }

    .location-input-group .btn.btn-success {
        color: #fff;
        background-color: #198754;
    }

    .location-input-group .btn.btn-success:hover {
        background-color: #157347;
    }

    .location-input-group .btn i {
        font-size: 1.1rem;
    }

    /* Volume Input Styles */
    .volume-input {
        position: relative;
    }

    .volume-input .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .volume-input .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    /* Coordinate Inputs */
    .coordinate-inputs .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .coordinate-inputs .form-control:focus {
        border-color: var(--primary-gradient-start);
        box-shadow: 0 0 0 0.2rem rgba(32, 84, 144, 0.1);
    }

    /* Use Location Button */
    .use-location-btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        background: linear-gradient(45deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
        border: none;
    }

    .use-location-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .use-location-btn:active {
        transform: translateY(0);
    }

    .use-location-btn i {
        margin-right: 0.5rem;
    }

    /* Map Container */
    #locationMap {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        const map = L.map('locationMap').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;

        function updateMarker(lat, lng) {
            if (marker) {
                marker.remove();
            }
            marker = L.marker([lat, lng], {
                title: 'Selected Location',
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = e.target.getLatLng();
                updateLocationInputs(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
            });

            map.setView([lat, lng], 15);
        }

        function updateLocationInputs(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        }

        function reverseGeocode(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('location').value = data.display_name;
                })
                .catch(error => console.error('Error:', error));
        }

        // Map click handler
        map.on('click', function(e) {
            updateMarker(e.latlng.lat, e.latlng.lng);
            updateLocationInputs(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        // Use My Location button
        const useLocationBtn = document.getElementById('useLocationBtn');
        useLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                useLocationBtn.disabled = true;
                useLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Getting Location...';
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        updateMarker(lat, lng);
                        updateLocationInputs(lat, lng);
                        reverseGeocode(lat, lng);
                        
                        useLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i> Use My Location';
                        useLocationBtn.disabled = false;
                    },
                    function(error) {
                        console.error('Error:', error);
                        alert('Unable to get your location. Please select on the map or enter manually.');
                        useLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i> Use My Location';
                        useLocationBtn.disabled = false;
                    }
                );
            } else {
                alert('Geolocation is not supported by your browser. Please select on the map or enter manually.');
            }
        });

        // Validate Location button
        const validateLocationBtn = document.getElementById('validateLocationBtn');
        validateLocationBtn.addEventListener('click', function() {
            const location = document.getElementById('location').value.trim();
            if (!location) {
                alert('Please enter a location name.');
                return;
            }

            validateLocationBtn.disabled = true;
            validateLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        
                        updateMarker(lat, lng);
                        updateLocationInputs(lat, lng);
                        document.getElementById('location').value = data[0].display_name;
                        
                        validateLocationBtn.classList.add('btn-success');
                        validateLocationBtn.classList.remove('btn-outline-primary');
                    } else {
                        alert('Location not found. Please try a different location name.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error validating location. Please try again.');
                })
                .finally(() => {
                    validateLocationBtn.disabled = false;
                    validateLocationBtn.innerHTML = '<i class="bi bi-check-circle"></i>';
                    setTimeout(() => {
                        validateLocationBtn.classList.remove('btn-success');
                        validateLocationBtn.classList.add('btn-outline-primary');
                    }, 2000);
                });
        });

        // Watch for manual input changes
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        function handleCoordinateChange() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                updateMarker(lat, lng);
                reverseGeocode(lat, lng);
            }
        }

        latInput.addEventListener('change', handleCoordinateChange);
        lngInput.addEventListener('change', handleCoordinateChange);

        // Media upload handling
        const mediaContainer = document.getElementById('mediaContainer');
        const maxFiles = 5;

        function createMediaInput() {
            const col = document.createElement('div');
            col.className = 'col-md-4';
            
            const container = document.createElement('div');
            container.className = 'image-container position-relative';
            
            const input = document.createElement('input');
            input.type = 'file';
            input.className = 'd-none';
            input.name = 'media[]';
            input.accept = 'image/*,video/*';
            
            const preview = document.createElement('div');
            preview.className = 'image-preview d-flex align-items-center justify-content-center';
            preview.innerHTML = '<i class="bi bi-plus-circle display-4 text-muted"></i>';
            
            container.appendChild(input);
            container.appendChild(preview);
            col.appendChild(container);
            
            container.addEventListener('click', function() {
                input.click();
            });
            
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = file.type.startsWith('image/') 
                            ? `<img src="${e.target.result}" alt="Preview">`
                            : `<video src="${e.target.result}" controls></video>`;
                        
                        // Add remove button
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-2';
                        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                        removeBtn.onclick = function(e) {
                            e.stopPropagation();
                            col.remove();
                        };
                        container.appendChild(removeBtn);
                        
                        // Add new input if under max files
                        if (mediaContainer.children.length < maxFiles) {
                            mediaContainer.appendChild(createMediaInput());
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            return col;
        }

        // Initialize first media input
        mediaContainer.appendChild(createMediaInput());
        
        // Waste type selection logic
        const wasteTypeButtons = document.querySelectorAll('.wsf-btn-option.wsf-general-type');
        const allWasteTypeGroups = document.querySelectorAll('.wsf-type-group');
        
        // Initially disable all specific type inputs
        document.querySelectorAll('.wsf-specific-input').forEach(input => {
            input.disabled = true;
        });

        function showAllGeneralTypes() {
            allWasteTypeGroups.forEach(group => {
                group.style.display = 'block';
            });
        }

        function hideOtherGeneralTypes(currentGroup) {
            allWasteTypeGroups.forEach(group => {
                if (group !== currentGroup) {
                    group.style.display = 'none';
                }
            });
        }
        
        wasteTypeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const isAutreBtn = btn.id === 'autreBtn';
                const wasteTypeId = btn.dataset.wasteType;
                const currentGroup = btn.closest('.wsf-type-group');
                const subTypesContainer = isAutreBtn ? null : document.getElementById('subTypes_' + wasteTypeId);
                const generalTypeInput = currentGroup.querySelector('.wsf-type-input');
                let backButton = currentGroup.querySelector('.wsf-back-button');
                const isActive = btn.classList.contains('active');
                
                // Create back button if it doesn't exist
                if (!backButton && !isActive) {
                    backButton = document.createElement('button');
                    backButton.type = 'button';
                    backButton.className = 'wsf-back-button';
                    backButton.innerHTML = '<i class="bi bi-arrow-left"></i>';
                    
                    // Add back button functionality
                    backButton.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const currentGroup = this.closest('.wsf-type-group');
                        const subTypesContainer = currentGroup.querySelector('.wsf-subtypes');
                        
                        // Hide subtypes but keep selections
                        if (subTypesContainer) {
                            subTypesContainer.classList.remove('show');
                        }
                        
                        // Show all general types
                        showAllGeneralTypes();
                        
                        // Remove back button
                        this.remove();

                        // If it's the "Other" button
                        if (btn.id === 'autreBtn') {
                            document.getElementById('autreInputContainer').classList.add('wsf-hidden');
                        }
                    });
                    
                    currentGroup.insertBefore(backButton, currentGroup.firstChild);
                }
                
                // Handle Autre button differently
                if (isAutreBtn) {
                    const autreContainer = document.getElementById('autreInputContainer');
                    const isHidden = autreContainer.classList.contains('wsf-hidden');
                    
                    if (!isActive) {
                        hideOtherGeneralTypes(currentGroup);
                        btn.classList.add('active');
                        autreContainer.classList.remove('wsf-hidden');
                        backButton.classList.add('show');
                    }
                    return;
                }

                if (!isActive) {
                    // Activate current button
                    btn.classList.add('active');
                    generalTypeInput.value = wasteTypeId;
                    backButton.classList.add('show');
                    
                    // Hide other general types
                    hideOtherGeneralTypes(currentGroup);
                    
                    // Show subtypes if they exist
                    if (subTypesContainer) {
                        subTypesContainer.classList.add('show');
                        // Enable specific type inputs for this group
                        subTypesContainer.querySelectorAll('.wsf-specific-input').forEach(input => {
                            input.disabled = false;
                        });
                    }
                }
            });
        });

        // Handle specific type selection
        document.querySelectorAll('.wsf-btn-option.wsf-specific-type').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const specificId = btn.dataset.specificId;
                const parentId = btn.dataset.parentId;
                const input = btn.parentElement.querySelector('.wsf-specific-input');
                
                btn.classList.toggle('active');
                input.disabled = !btn.classList.contains('active');
            });
        });
    });
</script>
@endpush
@endsection 