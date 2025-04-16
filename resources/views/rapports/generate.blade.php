@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .title{
        color: #ffffff !important; 
    }

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

    #map {
        height: 300px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

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
    }

    .wsf-btn-option.active {
        background-color: #364e9c;
        color: white;
    }

    .toggle-icon {
        transition: transform 0.3s ease;
    }

    .wsf-btn-option.expanded .toggle-icon {
        transform: rotate(180deg);
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
        animation: slideDown 0.3s ease;
    }

    .wsf-subtype-item {
        margin: 0.5rem 0;
    }

    .wsf-specific-type {
        background-color: white !important;
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
        padding: 0.5rem 1rem !important;
    }

    .wsf-specific-type:hover {
        background-color: #f8f9fa !important;
        border-color: #364e9c !important;
    }

    .wsf-specific-type.active {
        background-color: #364e9c !important;
        color: white !important;
        border-color: #364e9c !important;
    }

    .wsf-btn-option.has-selected {
        background-color: #364e9c;
        color: white;
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

    .contributors-list {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .contributors-list::-webkit-scrollbar {
        width: 6px;
    }

    .contributors-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .contributors-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    .contributors-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .form-check-input:checked {
        background-color: #364e9c;
        border-color: #364e9c;
    }
</style>
@endpush

@section('content')
<div class="container py-4 mb-7">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <form action="{{ isset($rapport) ? route('rapport.update', $collecte) : route('rapport.store', $collecte) }}" method="POST" class="waste-signal-form">
                @csrf
                @if(isset($rapport))
                    @method('PUT')
                @endif
                <input type="hidden" name="collecte_id" value="{{ $collecte->id }}">
                <input type="hidden" name="supervisor_id" value="{{ auth()->id() }}">

                <!-- Description Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="title">Description</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea name="description" id="description" rows="4" class="form-control" required>{{ old('description', isset($rapport) ? $rapport->description : $collecte->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Collection Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="title">Collection Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="volume" class="form-label">Total Volume (m³)</label>
                                    <input type="number" step="0.01" name="volume" id="volume" class="form-control" value="{{ old('volume', isset($rapport) ? $rapport->volume : $collecte->actual_volume) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nbrContributors" class="form-label">Number of Contributors</label>
                                    <input type="number" name="nbrContributors" id="nbrContributors" class="form-control" value="{{ old('nbrContributors', isset($rapport) ? $rapport->nbrContributors : $collecte->current_contributors) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contributors Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="title">Mark Present Contributors</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="contributors-list">
                                    @foreach($collecte->contributors as $contributor)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="participants[]" 
                                                   value="{{ $contributor->id }}"
                                                   id="contributor_{{ $contributor->id }}"
                                                   @if(isset($rapport) && in_array($contributor->id, $rapport->participants ?? [])) 
                                                       checked 
                                                   @elseif(!isset($rapport) && $contributor->pivot->attended)
                                                       checked
                                                   @endif>
                                            <label class="form-check-label" for="contributor_{{ $contributor->id }}">
                                                {{ $contributor->first_name }} {{ $contributor->last_name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Waste Types Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="title">Please Select Waste Type</h5>
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
                                                           disabled>
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
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="resetWasteTypes">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Selection
                        </button>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="title">Location Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="location" class="form-label">Location Name</label>
                                <input type="text" name="location" id="location" class="form-control" value="{{ old('location', isset($rapport) ? $rapport->location : $collecte->location) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', isset($rapport) ? $rapport->latitude : $collecte->latitude) }}" required>
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', isset($rapport) ? $rapport->longitude : $collecte->longitude) }}" required>
                                </div>
                            </div>
                        </div>
                        <div id="map" class="mb-3"></div>
                        <button type="button" class="btn btn-outline-secondary" id="resetMapPin">
                            <i class="bi bi-geo-alt"></i> Reset Pin Location
                        </button>
                    </div>
                </div>

                <!-- Dates Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="title">Collection Dates</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="starting_date" class="form-label">Start Date</label>
                                    <input type="datetime-local" name="starting_date" id="starting_date" class="form-control" value="{{ old('starting_date', isset($rapport) ? $rapport->starting_date : $collecte->starting_date->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', isset($rapport) ? $rapport->end_date : $collecte->end_date->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> 
                        {{ isset($rapport) ? 'Update Report' : 'Generate Report' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map
    const lat = {{ old('latitude', isset($rapport) ? $rapport->latitude : $collecte->latitude) }};
    const lng = {{ old('longitude', isset($rapport) ? $rapport->longitude : $collecte->longitude) }};
    
    const map = L.map('map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Create draggable marker
    let marker = L.marker([lat, lng], {
        draggable: true
    }).addTo(map);

    // Update form inputs when marker is dragged
    marker.on('dragend', function(event) {
        const position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
    });

    // Reset Map Pin Handler
    document.getElementById('resetMapPin').addEventListener('click', function() {
        const initialLat = {{ isset($rapport) ? $rapport->latitude : $collecte->latitude }};
        const initialLng = {{ isset($rapport) ? $rapport->longitude : $collecte->longitude }};
        
        marker.setLatLng([initialLat, initialLng]);
        map.setView([initialLat, initialLng], 13);
        
        document.getElementById('latitude').value = initialLat;
        document.getElementById('longitude').value = initialLng;
    });

    // Set minimum date for starting_date to today
    document.getElementById('starting_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
    });

    // Waste Types Selection Handling
    const generalTypes = document.querySelectorAll('.wsf-general-type');
    let selectedTypes = new Set();

    // Pre-select waste types from either rapport (if editing) or collecte (if generating)
    @if(isset($rapport))
        const existingWasteTypes = @json($rapport->waste_types);
    @else
        const existingWasteTypes = @json($collecte->actual_waste_types ?? []);
    @endif

    // Pre-select waste types
    existingWasteTypes.forEach(typeId => {
        const specificTypeBtn = document.querySelector(`.wsf-specific-type[data-specific-id="${typeId}"]`);
        if (specificTypeBtn) {
            const parentId = specificTypeBtn.dataset.parentId;
            const parentBtn = document.querySelector(`.wsf-general-type[data-waste-type="${parentId}"]`);
            const subtypesContainer = document.getElementById(`subTypes_${parentId}`);
            const input = specificTypeBtn.previousElementSibling;

            if (subtypesContainer) {
                subtypesContainer.classList.add('show');
                parentBtn.classList.add('expanded');
            }

            specificTypeBtn.classList.add('active');
            if (input) {
                input.disabled = false;
            }

            if (parentBtn) {
                parentBtn.classList.add('has-selected');
            }

            selectedTypes.add(typeId.toString());
        }
    });

    // Handle parent type clicks
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

    // Close other expanded items when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.wsf-type-group')) {
            document.querySelectorAll('.wsf-subtypes.show').forEach(subtypes => {
                subtypes.classList.remove('show');
                subtypes.previousElementSibling.classList.remove('expanded');
            });
        }
    });

    // Reset Waste Types Handler
    document.getElementById('resetWasteTypes').addEventListener('click', function() {
        document.querySelectorAll('.wsf-specific-type.active').forEach(button => {
            button.classList.remove('active');
            const input = button.previousElementSibling;
            if (input) {
                input.disabled = true;
            }
        });

        document.querySelectorAll('.wsf-general-type.has-selected').forEach(button => {
            button.classList.remove('has-selected');
        });

        selectedTypes.clear();

        document.querySelectorAll('.wsf-subtypes.show').forEach(container => {
            container.classList.remove('show');
        });

        document.querySelectorAll('.wsf-general-type.expanded').forEach(button => {
            button.classList.remove('expanded');
        });
    });
});

document.querySelector('.waste-signal-form').addEventListener('submit', function(e) {
    // Get all checked participants
    const checkedParticipants = document.querySelectorAll('input[name="participants[]"]:checked');
    
    if (checkedParticipants.length === 0) {
        e.preventDefault();
        alert('Please select at least one participant who attended the collection.');
        return false;
    }
});
</script>
@endpush
@endsection
