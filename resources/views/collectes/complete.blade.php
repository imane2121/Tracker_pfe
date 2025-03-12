@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="collecte-header mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Complete Collection</h1>
                    <p class="mb-0 mt-2">Record actual data and attendance for the collection event</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Back to Collection
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
                    <form action="{{ route('collecte.complete', $collecte) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Actual Waste Types -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Actual Waste Types Found</h5>
                            <div class="row g-3">
                                @foreach($wasteTypes as $type)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="actual_waste_types[]" value="{{ $type->id }}"
                                                   id="waste_{{ $type->id }}"
                                                   @if(is_array(old('actual_waste_types')) && in_array($type->id, old('actual_waste_types'))) checked @endif>
                                            <label class="form-check-label" for="waste_{{ $type->id }}">
                                                {{ $type->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('actual_waste_types')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actual Volume -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Actual Volume</h5>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="number" name="actual_volume" step="0.01" min="0"
                                           class="form-control @error('actual_volume') is-invalid @enderror"
                                           value="{{ old('actual_volume') }}" required>
                                    <span class="input-group-text">m³</span>
                                </div>
                                @error('actual_volume')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Completion Notes -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Completion Notes</h5>
                            <div class="form-group">
                                <textarea name="completion_notes" rows="3" 
                                          class="form-control @error('completion_notes') is-invalid @enderror"
                                          placeholder="Add any additional notes about the collection...">{{ old('completion_notes') }}</textarea>
                                @error('completion_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Attendance -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Attendance</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Contributor</th>
                                            <th>Present</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($collecte->contributors as $contributor)
                                            <tr>
                                                <td>
                                                    <input type="hidden" 
                                                           name="attendance_data[{{ $loop->index }}][user_id]" 
                                                           value="{{ $contributor->id }}">
                                                    {{ $contributor->first_name }} {{ $contributor->last_name }}
                                                </td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="attendance_data[{{ $loop->index }}][attended]"
                                                               value="1"
                                                               @if(old("attendance_data.{$loop->index}.attended", true)) checked @endif>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                           name="attendance_data[{{ $loop->index }}][notes]"
                                                           value="{{ old("attendance_data.{$loop->index}.notes") }}"
                                                           placeholder="Optional notes...">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @error('attendance_data')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Completion Media -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3">Completion Photos/Videos</h5>
                            <div class="form-group">
                                <input type="file" name="completion_media[]" multiple 
                                       class="form-control @error('completion_media.*') is-invalid @enderror"
                                       accept="image/*,video/*">
                                <small class="text-muted">Upload photos or videos showing the completed collection</small>
                                @error('completion_media.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Complete Collection
                            </button>
                        </div>
                    </form>

                    @if($collecte->report_generated && $collecte->report_path)
                        <div class="mt-4">
                            <a href="{{ route('collecte.report.download', $collecte) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download Report
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Info Column -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Collection Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Location</dt>
                        <dd class="col-sm-8">{{ $collecte->location }}</dd>

                        <dt class="col-sm-4">Region</dt>
                        <dd class="col-sm-8">{{ $collecte->region }}</dd>

                        <dt class="col-sm-4">Started</dt>
                        <dd class="col-sm-8">{{ $collecte->starting_date->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-4">Ended</dt>
                        <dd class="col-sm-8">{{ $collecte->end_date->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-4">Contributors</dt>
                        <dd class="col-sm-8">{{ $collecte->current_contributors }} / {{ $collecte->nbrContributors }}</dd>

                        <dt class="col-sm-4">Expected Types</dt>
                        <dd class="col-sm-8">
                            @foreach($collecte->waste_types as $typeId)
                                @php $type = $wasteTypes->find($typeId); @endphp
                                @if($type)
                                    <span class="badge bg-secondary">{{ $type->name }}</span>
                                @endif
                            @endforeach
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-check-input:checked + .form-check-label {
        color: var(--bs-primary);
        font-weight: 500;
    }
    
    .table td {
        vertical-align: middle;
    }
</style>
@endpush
@endsection 