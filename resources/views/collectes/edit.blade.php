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
                        @if($collecte->is_urgent)
                            Edit Urgent Collection
                        @else
                            Edit Collection
                        @endif
                    </span>
                </h2>
                <p class="message">
                    Update collection details and make adjustments
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('collecte.update', $collecte) }}" method="POST" enctype="multipart/form-data" class="waste-signal-form">
        @csrf
        @method('PUT')
        
        @if(!$collecte->is_urgent)
            <input type="hidden" name="signal_ids" value="{{ json_encode($collecte->signal_ids) }}">
        @endif
        <input type="hidden" name="is_urgent" value="{{ $collecte->is_urgent ? '1' : '0' }}">

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
                                   id="location" name="location" value="{{ old('location', $collecte->location) }}" required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="region" class="form-label">Region</label>
                            <input type="text" class="form-control @error('region') is-invalid @enderror" 
                                   id="region" name="region" value="{{ old('region', $collecte->region) }}" required>
                            @error('region')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                       id="latitude" name="latitude" value="{{ old('latitude', $collecte->latitude) }}" required>
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                       id="longitude" name="longitude" value="{{ old('longitude', $collecte->longitude) }}" required>
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
                                   id="actual_volume" name="actual_volume" 
                                   value="{{ old('actual_volume', $collecte->actual_volume) }}" required>
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
                               id="nbrContributors" name="nbrContributors" 
                               value="{{ old('nbrContributors', $collecte->nbrContributors) }}" required>
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
                               id="starting_date" name="starting_date" 
                               value="{{ old('starting_date', $collecte->starting_date->format('Y-m-d\TH:i')) }}" required>
                        @error('starting_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="datetime-local" 
                               class="form-control @error('end_date') is-invalid @enderror"
                               id="end_date" name="end_date" 
                               value="{{ old('end_date', $collecte->end_date->format('Y-m-d\TH:i')) }}" required>
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
                <div class="wsf-buttons-container d-flex flex-wrap gap-3">
                    @foreach($wasteTypes->where('parent_id', null) as $wasteType)
                        <div class="wsf-type-group">
                            <button type="button" class="wsf-btn-option wsf-general-type" 
                                    data-waste-type="{{ $wasteType->id }}">
                                {{ $wasteType->name }}
                                @if($wasteTypes->where('parent_id', $wasteType->id)->isNotEmpty())
                                    <i class="bi bi-chevron-down ms-2 toggle-icon"></i>
                                @endif
                            </button>
                            @if($wasteTypes->where('parent_id', $wasteType->id)->isNotEmpty())
                                <div class="wsf-subtypes" id="subTypes_{{ $wasteType->id }}">
                                    @foreach($wasteTypes->where('parent_id', $wasteType->id) as $specificType)
                                        <div class="wsf-subtype-item">
                                            <input type="hidden" name="waste_types[]" 
                                                   value="{{ $specificType->id }}" 
                                                   data-parent-id="{{ $wasteType->id }}" 
                                                   {{ in_array($specificType->id, $collecte->waste_types ?? []) ? '' : 'disabled' }}>
                                            <button type="button" class="wsf-btn-option wsf-specific-type {{ in_array($specificType->id, $collecte->waste_types ?? []) ? 'active' : '' }}" 
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
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="resetWasteTypes">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Selection
                </button>
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
                              placeholder="Add any additional information about the collection...">{{ old('description', $collecte->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="d-flex gap-3 justify-content-end">
            <a href="{{ route('collecte.index') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-check-circle"></i> Update Collection
            </button>
        </div>
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    /* Copy all styles from create.blade.php here */
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

    /* Add all other styles from create.blade.php */
    
    /* Waste Types Section */
    .wsf-buttons-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .wsf-type-group {
        flex: 0 0 calc(33.333% - 1rem);
        min-width: 250px;
        margin-bottom: 0.5rem;
    }

    .wsf-btn-option {
        width: 100%;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        background-color: #f8f9fa;
        color: #495057;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .wsf-btn-option:hover {
        background-color: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .wsf-btn-option.active {
        background-color: #364e9c;
        color: white;
    }

    .wsf-btn-option.has-selected {
        background-color: #364e9c;
        color: white;
    }

    .wsf-subtypes {
        display: none;
        padding: 0.75rem;
        margin-top: 0.5rem;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #dee2e6;
    }

    .wsf-subtypes.show {
        display: block;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    const initialLat = {{ $collecte->latitude }};
    const initialLng = {{ $collecte->longitude }};
    
    const locationMap = L.map('map').setView([initialLat, initialLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(locationMap);

    // Create draggable marker
    let locationMarker = L.marker([initialLat, initialLng], {
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
        locationMarker.setLatLng([initialLat, initialLng]);
        locationMap.setView([initialLat, initialLng], 13);
        document.getElementById('latitude').value = initialLat;
        document.getElementById('longitude').value = initialLng;
    });

    // Initialize waste types with the collecte's existing selections
    const existingWasteTypes = @json($collecte->actual_waste_types ?? []);
    console.log('Existing waste types:', existingWasteTypes); // Debug log

    // Pre-select waste types and show relevant sections
    existingWasteTypes.forEach(typeId => {
        // Find and activate the specific type button
        const specificTypeBtn = document.querySelector(`.wsf-specific-type[data-specific-id="${typeId}"]`);
        if (specificTypeBtn) {
            // Activate the specific type
            specificTypeBtn.classList.add('active');
            
            // Enable the hidden input
            const input = specificTypeBtn.previousElementSibling;
            if (input) {
                input.disabled = false;
            }

            // Show and mark the parent section as active
            const parentId = specificTypeBtn.dataset.parentId;
            const parentBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
            const subtypesContainer = document.getElementById(`subTypes_${parentId}`);

            if (parentBtn) {
                parentBtn.classList.add('has-selected');
                parentBtn.classList.add('expanded');
            }

            if (subtypesContainer) {
                subtypesContainer.classList.add('show');
            }
        }
    });

    // Initialize selectedTypes Set with existing selections
    let selectedTypes = new Set(existingWasteTypes.map(String));

    // Handle parent type clicks
    const generalTypes = document.querySelectorAll('.wsf-general-type');
    generalTypes.forEach(button => {
        button.addEventListener('click', function() {
            const wasteTypeId = this.dataset.wasteType;
            const subtypes = document.getElementById(`subTypes_${wasteTypeId}`);
            
            if (subtypes) {
                subtypes.classList.toggle('show');
                this.classList.toggle('expanded');
            }
        });
    });

    // Handle specific type selection
    const specificTypes = document.querySelectorAll('.wsf-specific-type');
    specificTypes.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const parentId = this.dataset.parentId;
            const input = this.previousElementSibling;
            const parentButton = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
            
            this.classList.toggle('active');
            input.disabled = !this.classList.contains('active');

            if (this.classList.contains('active')) {
                selectedTypes.add(this.dataset.specificId);
            } else {
                selectedTypes.delete(this.dataset.specificId);
            }

            const hasSelected = document.querySelectorAll(
                `.wsf-specific-type[data-parent-id="${parentId}"].active`
            ).length > 0;
            parentButton.classList.toggle('has-selected', hasSelected);
        });
    });

    // Update Reset handler to use existingWasteTypes
    document.getElementById('resetWasteTypes').addEventListener('click', function() {
        // Reset all selections first
        document.querySelectorAll('.wsf-specific-type').forEach(button => {
            button.classList.remove('active');
            const input = button.previousElementSibling;
            if (input) {
                input.disabled = true;
            }
        });

        document.querySelectorAll('.wsf-general-type').forEach(button => {
            button.classList.remove('has-selected', 'expanded');
        });

        document.querySelectorAll('.wsf-subtypes').forEach(container => {
            container.classList.remove('show');
        });

        selectedTypes.clear();

        // Re-apply original selections
        existingWasteTypes.forEach(typeId => {
            const specificTypeBtn = document.querySelector(`.wsf-specific-type[data-specific-id="${typeId}"]`);
            if (specificTypeBtn) {
                // Activate the specific type
                specificTypeBtn.classList.add('active');
                const input = specificTypeBtn.previousElementSibling;
                if (input) {
                    input.disabled = false;
                }

                // Show and mark the parent section as active
                const parentId = specificTypeBtn.dataset.parentId;
                const parentBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
                const subtypesContainer = document.getElementById(`subTypes_${parentId}`);

                if (parentBtn) {
                    parentBtn.classList.add('has-selected');
                    parentBtn.classList.add('expanded');
                }

                if (subtypesContainer) {
                    subtypesContainer.classList.add('show');
                }

                selectedTypes.add(typeId.toString());
            }
        });
    });

    // Set minimum date for starting_date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('starting_date').min = today;

    // Update end_date minimum when starting_date changes
    document.getElementById('starting_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });
});
</script>
@endpush
@endsection 