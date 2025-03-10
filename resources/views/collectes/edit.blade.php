@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/collecte.css') }}">
@endpush

@section('content')
<div class="collecte-container">
    <div class="container">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Back to Collecte
            </a>
        </div>

        <!-- Edit Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="collecte-card card">
                    <div class="card-header">
                        <h2 class="mb-0">Edit Collecte</h2>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('collecte.update', $collecte) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Location Details -->
                            <div class="mb-4">
                                <h5 class="mb-3">Location Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Region</label>
                                        <input type="text" name="region" class="form-control @error('region') is-invalid @enderror" 
                                               value="{{ old('region', $collecte->region) }}" required>
                                        @error('region')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" 
                                               value="{{ old('location', $collecte->location) }}" required>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Latitude</label>
                                        <input type="number" step="any" name="latitude" class="form-control @error('latitude') is-invalid @enderror" 
                                               value="{{ old('latitude', $collecte->latitude) }}" required>
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Longitude</label>
                                        <input type="number" step="any" name="longitude" class="form-control @error('longitude') is-invalid @enderror" 
                                               value="{{ old('longitude', $collecte->longitude) }}" required>
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Date and Participants -->
                            <div class="mb-4">
                                <h5 class="mb-3">Schedule & Participants</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Starting Date</label>
                                        <input type="date" name="starting_date" class="form-control @error('starting_date') is-invalid @enderror" 
                                               value="{{ old('starting_date', $collecte->starting_date->format('Y-m-d')) }}" required>
                                        @error('starting_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" 
                                               value="{{ old('end_date', $collecte->end_date->format('Y-m-d')) }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Participants</label>
                                        <input type="number" name="nbrContributors" class="form-control @error('nbrContributors') is-invalid @enderror" 
                                               value="{{ old('nbrContributors', $collecte->nbrContributors) }}" min="1" required>
                                        @error('nbrContributors')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Waste Types -->
                            <div class="mb-4">
                                <h5 class="mb-3">Waste Types</h5>
                                <div class="row g-3">
                                    @foreach($wasteTypes as $type)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="waste_types[]" value="{{ $type->id }}" 
                                                       class="form-check-input @error('waste_types') is-invalid @enderror"
                                                       {{ in_array($type->id, old('waste_types', $collecte->waste_types)) ? 'checked' : '' }}>
                                                <label class="form-check-label">{{ $type->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('waste_types')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <h5 class="mb-3">Description</h5>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                          rows="4">{{ old('description', $collecte->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Media -->
                            <div class="mb-4">
                                <h5 class="mb-3">Media</h5>
                                <div class="row g-3">
                                    @foreach($collecte->media as $media)
                                        <div class="col-md-4">
                                            <div class="position-relative">
                                                @if(str_starts_with($media->media_type, 'image/'))
                                                    <img src="{{ Storage::url($media->file_path) }}" class="img-fluid rounded" alt="Collecte media">
                                                @elseif(str_starts_with($media->media_type, 'video/'))
                                                    <video class="img-fluid rounded" controls>
                                                        <source src="{{ Storage::url($media->file_path) }}" type="{{ $media->media_type }}">
                                                    </video>
                                                @endif
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                        onclick="deleteMedia({{ $media->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <label class="form-label">Add New Media</label>
                                    <input type="file" name="media[]" class="form-control @error('media.*') is-invalid @enderror" 
                                           multiple accept="image/*,video/*">
                                    @error('media.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">You can upload multiple images or videos. Maximum file size: 2MB</small>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Update Collecte
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteMedia(mediaId) {
    if (confirm('Are you sure you want to delete this media?')) {
        // Add AJAX call to delete media
        fetch(`/collectes/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete media');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the media');
        });
    }
}
</script>
@endpush
@endsection 