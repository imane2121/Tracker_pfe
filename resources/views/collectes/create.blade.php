@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="collecte-header mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Create Collection</h1>
                    <p class="mb-0 mt-2">Create a new collection event based on validated reports</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('collecte.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Back to Collections
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('collecte.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Signal Selection -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Select Base Report</h5>
                            <div class="signal-grid row g-3">
                                @foreach($signals as $signal)
                                    <div class="col-md-6">
                                        <div class="signal-card card h-100 @if(old('signal_id') == $signal->id) border-primary @endif">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="signal_id" 
                                                           id="signal_{{ $signal->id }}" value="{{ $signal->id }}"
                                                           @if(old('signal_id') == $signal->id) checked @endif
                                                           data-lat="{{ $signal->latitude }}"
                                                           data-lng="{{ $signal->longitude }}"
                                                           data-location="{{ $signal->location }}"
                                                           data-region="{{ $signal->region }}">
                                                    <label class="form-check-label w-100" for="signal_{{ $signal->id }}">
                                                        <div class="d-flex align-items-start">
                                                            @if($signal->media->isNotEmpty())
                                                                <img src="{{ Storage::url($signal->media->first()->file_path) }}" 
                                                                     class="rounded me-2" alt="Signal image"
                                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                                            @endif
                                                            <div>
                                                                <h6 class="mb-1">{{ $signal->location }}</h6>
                                                                <p class="text-muted small mb-1">
                                                                    <i class="bi bi-geo-alt"></i> {{ $signal->region }}
                                                                </p>
                                                                <p class="text-muted small mb-0">
                                                                    <i class="bi bi-calendar"></i> {{ $signal->created_at->format('M d, Y') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('signal_id')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Collection Details -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Collection Details</h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                               value="{{ old('location') }}" required>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Region</label>
                                        <input type="text" name="region" class="form-control @error('region') is-invalid @enderror" 
                                               value="{{ old('region') }}" required>
                                        @error('region')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Starting Date</label>
                                        <input type="datetime-local" name="starting_date" 
                                               class="form-control @error('starting_date') is-invalid @enderror" 
                                               value="{{ old('starting_date') }}" required>
                                        @error('starting_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">End Date</label>
                                        <input type="datetime-local" name="end_date" 
                                               class="form-control @error('end_date') is-invalid @enderror" 
                                               value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Number of Contributors</label>
                                        <input type="number" name="nbrContributors" min="1" 
                                               class="form-control @error('nbrContributors') is-invalid @enderror" 
                                               value="{{ old('nbrContributors') }}" required>
                                        @error('nbrContributors')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" rows="3" 
                                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Waste Types</label>
                                        <div class="row g-2">
                                            @foreach($wasteTypes as $type)
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="waste_types[]" value="{{ $type->id }}"
                                                               id="waste_{{ $type->id }}"
                                                               @if(is_array(old('waste_types')) && in_array($type->id, old('waste_types'))) checked @endif>
                                                        <label class="form-check-label" for="waste_{{ $type->id }}">
                                                            {{ $type->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('waste_types')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Media (Optional)</label>
                                        <input type="file" name="media[]" multiple 
                                               class="form-control @error('media.*') is-invalid @enderror"
                                               accept="image/*,video/*">
                                        <small class="text-muted">You can upload multiple images or videos</small>
                                        @error('media.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Create Collection
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map Column -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-0">
                    <div id="map" style="height: 400px;" class="rounded-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .signal-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .signal-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08);
    }
    
    .signal-card.border-primary {
        border-width: 2px;
    }
    
    .form-check-input:checked + .form-check-label .signal-card {
        border-color: var(--bs-primary);
    }
    
    @media (max-width: 768px) {
        .signal-grid {
            margin: -0.5rem;
        }
        
        .signal-grid > div {
            padding: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map
    var map = L.map('map').setView([31.7917, -7.0926], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker;
    
    // Function to update map marker
    function updateMapMarker(lat, lng) {
        if (marker) {
            map.removeLayer(marker);
        }
        
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 13);
        
        // Update hidden inputs
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }
    
    // Handle signal selection
    document.querySelectorAll('input[name="signal_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                const location = this.dataset.location;
                const region = this.dataset.region;
                
                updateMapMarker(lat, lng);
                
                // Update form fields
                document.querySelector('input[name="location"]').value = location;
                document.querySelector('input[name="region"]').value = region;
            }
        });
    });
    
    // Select first signal by default if none selected
    const selectedSignal = document.querySelector('input[name="signal_id"]:checked');
    if (!selectedSignal && document.querySelector('input[name="signal_id"]')) {
        document.querySelector('input[name="signal_id"]').click();
    }
});
</script>
@endpush
@endsection 