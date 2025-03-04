@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Encouraging Message -->
    <div class="text-center mb-5">
        <div class="encouragement-container position-relative">
            <div class="ocean-wave"></div>
            <h2 class="display-4 fw-bold text-primary mb-3">
                <span class="d-block">Few Steps to</span>
                <span class="d-block text-success">Save Our Ocean</span>
            </h2>
            <p class="lead text-muted mb-0">
                <i class="bi bi-heart-pulse-fill text-danger me-2"></i>
                Your report helps us protect marine life
                <br class="d-none d-md-block">
                and keep our waters clean
            </p>
        </div>
    </div>

    <form action="{{ route('signal.store') }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
        @csrf
        
        <!-- Waste Types Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
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
                            @if($wasteType->specificWasteTypes->isNotEmpty())
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
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Location Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="volume" class="form-label">Volume (m³)</label>
                        <input type="number" class="form-control" id="volume" name="volume" min="0" step="0.1" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="number" class="form-control" id="latitude" name="latitude" step="any" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="number" class="form-control" id="longitude" name="longitude" step="any" required>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-lg flex-grow-1" id="useLocationBtn">
                        <i class="bi bi-geo-alt"></i> Use My Location
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-lg" id="validateLocationBtn">
                        <i class="bi bi-check-circle"></i> Validate Location
                    </button>
                </div>
            </div>
        </div>

        <!-- Media Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
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
            <div class="card-header bg-warning text-dark">
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
<style>
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
    }

    /* New styles for the encouragement section */
    .encouragement-container {
        padding: 2rem 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }

    .encouragement-container h2 {
        font-size: calc(1.5rem + 1.5vw);
        line-height: 1.2;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
    }

    .encouragement-container .lead {
        font-size: calc(1rem + 0.3vw);
        line-height: 1.6;
        position: relative;
        z-index: 1;
    }

    .ocean-wave {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: linear-gradient(45deg, #0d6efd 25%, transparent 25%),
                    linear-gradient(-45deg, #0d6efd 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, #0d6efd 75%),
                    linear-gradient(-45deg, transparent 75%, #0d6efd 75%);
        background-size: 20px 20px;
        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        opacity: 0.1;
        animation: wave 20s linear infinite;
    }

    @keyframes wave {
        0% {
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }
        100% {
            background-position: 40px 0, 40px 10px, 50px -10px, 30px 0px;
        }
    }

    /* Mobile-specific adjustments */
    @media (max-width: 768px) {
        .encouragement-container {
            padding: 1.5rem 1rem;
            margin: 0 -1rem;
            border-radius: 0;
        }

        .encouragement-container h2 {
            font-size: calc(1.25rem + 0.5vw);
            margin-bottom: 0.75rem;
        }

        .encouragement-container .lead {
            font-size: 1rem;
            line-height: 1.5;
        }

        /* Adjust card spacing for mobile */
        .waste-signal-form .card {
            margin-left: -1rem;
            margin-right: -1rem;
            border-radius: 0;
        }

        .waste-signal-form .card-header {
            border-radius: 0;
        }

        /* Make buttons more touch-friendly */
        .btn-lg {
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
        }

        /* Adjust waste type buttons for better mobile layout */
        .wsf-buttons-container {
            gap: 0.5rem;
        }

        .wsf-btn-option {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }

        /* Improve media upload container on mobile */
        .image-container {
            width: 100%;
            height: 120px;
            margin-bottom: 1rem;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .encouragement-container {
            background: linear-gradient(135deg, #2d3436 0%, #1e272e 100%);
        }

        .encouragement-container .lead {
            color: #a0aec0 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Location handling
        const useLocationBtn = document.getElementById('useLocationBtn');
        const validateLocationBtn = document.getElementById('validateLocationBtn');
        const locationInput = document.getElementById('location');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');

        useLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                useLocationBtn.disabled = true;
                useLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Getting Location...';
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        latitudeInput.value = position.coords.latitude;
                        longitudeInput.value = position.coords.longitude;
                        
                        // Reverse geocode to get location name
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                            .then(response => response.json())
                            .then(data => {
                                locationInput.value = data.display_name;
                                useLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i> Use My Location';
                                useLocationBtn.disabled = false;
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                useLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i> Use My Location';
                                useLocationBtn.disabled = false;
                            });
                    },
                    function(error) {
                        console.error('Error:', error);
                        alert('Unable to get your location. Please enter it manually.');
                        useLocationBtn.innerHTML = '<i class="bi bi-geo-alt"></i> Use My Location';
                        useLocationBtn.disabled = false;
                    }
                );
            } else {
                alert('Geolocation is not supported by your browser. Please enter location manually.');
            }
        });

        validateLocationBtn.addEventListener('click', function() {
            const location = locationInput.value.trim();
            if (!location) {
                alert('Please enter a location name.');
                return;
            }

            validateLocationBtn.disabled = true;
            validateLocationBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Validating...';

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        latitudeInput.value = data[0].lat;
                        longitudeInput.value = data[0].lon;
                        locationInput.value = data[0].display_name;
                        validateLocationBtn.innerHTML = '<i class="bi bi-check-circle"></i> Location Validated';
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
                    if (!validateLocationBtn.classList.contains('btn-success')) {
                        validateLocationBtn.innerHTML = '<i class="bi bi-check-circle"></i> Validate Location';
                    }
                });
        });

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
        
        wasteTypeButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                const isAutreBtn = btn.id === 'autreBtn';
                const wasteTypeId = btn.dataset.wasteType;
                const currentGroup = btn.closest('.wsf-type-group');
                const subTypesContainer = isAutreBtn ? null : document.getElementById('subTypes_' + wasteTypeId);
                const generalTypeInput = currentGroup.querySelector('.wsf-type-input');
                const isActive = btn.classList.contains('active');
                
                // Handle Autre button differently
                if (isAutreBtn) {
                    const autreContainer = document.getElementById('autreInputContainer');
                    const isHidden = autreContainer.classList.contains('wsf-hidden');
                    
                    // Reset all other buttons and show all groups
                    resetAllTypes();
                    showAllGroups();
                    
                    // Toggle autre button and input
                    btn.classList.toggle('active', isHidden);
                    autreContainer.classList.toggle('wsf-hidden');
                    return;
                }

                if (isActive) {
                    // If already active, deactivate and show all groups
                    btn.classList.remove('active');
                    if (subTypesContainer) {
                        subTypesContainer.classList.remove('show');
                        // Clear specific type selections
                        subTypesContainer.querySelectorAll('.wsf-specific-input').forEach(input => {
                            input.disabled = true;
                        });
                        subTypesContainer.querySelectorAll('.wsf-btn-option.wsf-specific-type').forEach(btn => {
                            btn.classList.remove('active');
                        });
                    }
                    generalTypeInput.value = '';
                    showAllGroups();
                } else {
                    // Reset all types first
                    resetAllTypes();
                    
                    // Activate current button and hide other groups
                    btn.classList.add('active');
                    generalTypeInput.value = wasteTypeId;
                    hideOtherGroups(currentGroup);
                    
                    // Show subtypes if they exist
                    if (subTypesContainer) {
                        subTypesContainer.classList.add('show');
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
                const isActive = btn.classList.contains('active');
                
                btn.classList.toggle('active');
                input.disabled = !btn.classList.contains('active');
            });
        });

        function resetAllTypes() {
            // Reset all general type buttons and inputs
            wasteTypeButtons.forEach(btn => {
                btn.classList.remove('active');
                const group = btn.closest('.wsf-type-group');
                const input = group.querySelector('.wsf-type-input');
                if (input) input.value = '';
            });
            
            // Reset all specific type buttons and inputs
            document.querySelectorAll('.wsf-btn-option.wsf-specific-type').forEach(btn => {
                btn.classList.remove('active');
                const input = btn.parentElement.querySelector('.wsf-specific-input');
                if (input) input.disabled = true;
            });
            
            // Hide all subtypes
            document.querySelectorAll('.wsf-subtypes').forEach(container => {
                container.classList.remove('show');
            });
            
            // Reset autre
            document.getElementById('autreInputContainer').classList.add('wsf-hidden');
            document.getElementById('autreInput').value = '';
        }

        function hideOtherGroups(currentGroup) {
            allWasteTypeGroups.forEach(group => {
                if (group !== currentGroup) {
                    group.style.display = 'none';
                }
            });
        }

        function showAllGroups() {
            allWasteTypeGroups.forEach(group => {
                group.style.display = '';
            });
        }

        // Form validation before submit
        document.querySelector('.waste-signal-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            
            // Validate location
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            
            if (!latitude || !longitude) {
                alert('Please select a location.');
                return;
            }
            
            // Get all selected general types
            const selectedGeneralTypes = Array.from(document.querySelectorAll('.wsf-general-type.active'))
                .map(btn => btn.dataset.wasteType)
                .filter(id => id);
            
            // Get all checked specific types
            const selectedSpecificTypes = Array.from(document.querySelectorAll('.wsf-specific-type:checked'))
                .map(cb => cb.value);
            
            // Get custom type if entered
            const autreInput = document.getElementById('autreInput');
            const customType = autreInput.value.trim();
            
            // Validate waste types
            if (selectedGeneralTypes.length === 0 && selectedSpecificTypes.length === 0 && !customType) {
                alert('Please select at least one waste type or enter a custom type.');
                return;
            }
            
            // Clear existing general waste types from FormData
            formData.delete('general_waste_type[]');
            
            // Add selected general types
            selectedGeneralTypes.forEach(typeId => {
                formData.append('general_waste_type[]', typeId);
            });
            
            // If all validations pass, submit the form
            fetch(this.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin' // Include cookies (for CSRF token)
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response;
            })
            .then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json().then(data => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '{{ route("signal.thank-you") }}';
                        }
                    });
                } else {
                    window.location.href = '{{ route("signal.thank-you") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the form. Please try again.');
            });
        });
    });
</script>
@endpush
@endsection