@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>Edit Report
        </h2>
        <a href="{{ route('signal.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Reports
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('signal.update', $signal->id) }}" method="POST" class="row g-4">
                @csrf
                @method('PUT')

                <!-- Volume -->
                <div class="col-12 col-md-6">
                    <label class="form-label">Volume (m³) <span class="text-danger">*</span></label>
                    <input type="number" name="volume" class="form-control @error('volume') is-invalid @enderror" 
                           value="{{ old('volume', $signal->volume) }}" step="0.01" min="0" required>
                    @error('volume')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Waste Types -->
                <div class="col-12">
                    <label class="form-label">Waste Types <span class="text-danger">*</span></label>
                    <div class="row g-3">
                        @foreach($wasteTypes as $type)
                            <div class="col-12 col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="waste_types[]" 
                                           value="{{ $type->id }}" id="type{{ $type->id }}"
                                           {{ in_array($type->id, old('waste_types', $signal->wasteTypes->pluck('id')->toArray())) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type{{ $type->id }}">
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

                <!-- Custom Type -->
                <div class="col-12 col-md-6">
                    <label class="form-label">Custom Type (Optional)</label>
                    <input type="text" name="custom_type" class="form-control @error('custom_type') is-invalid @enderror" 
                           value="{{ old('custom_type', $signal->custom_type) }}">
                    @error('custom_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="4">{{ old('description', $signal->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="col-12">
                    <hr>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('signal.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .form-check-label {
        cursor: pointer;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush
@endsection 