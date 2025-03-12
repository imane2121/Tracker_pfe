@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/collecte.css') }}">
@endpush

@section('content')
<div class="collecte-container">
    <div class="container">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('collecte.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Back to Collectes
            </a>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Left Column -->
            <div class="col-lg-8">
                <!-- Collecte Details Card -->
                <div class="collecte-card card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="collecte-status status-{{ $collecte->status }}">
                            {{ ucfirst(str_replace('_', ' ', $collecte->status)) }}
                        </span>
                        <div>
                            @if(auth()->user()->isAdmin() || (auth()->user()->isSupervisor() && $collecte->user_id === auth()->id()))
                                <a href="{{ route('collecte.edit', $collecte) }}" class="btn btn-sm btn-outline-secondary me-2">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('collecte.destroy', $collecte) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this collection?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title mb-4">{{ $collecte->location }}</h2>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="collecte-info">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $collecte->region }}</span>
                                </div>
                                <div class="collecte-info">
                                    <i class="bi bi-calendar"></i>
                                    <span>Starting: {{ $collecte->starting_date->format('M d, Y') }}</span>
                                </div>
                                <div class="collecte-info">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Ending: {{ $collecte->end_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="collecte-info">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $collecte->current_contributors }}/{{ $collecte->nbrContributors }} Participants</span>
                                </div>
                                <div class="collecte-info">
                                    <i class="bi bi-person"></i>
                                    <span>Created by: {{ $collecte->creator->first_name }} {{ $collecte->creator->last_name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="collecte-progress mb-4">
                            <div class="collecte-progress-bar" style="width: {{ $collecte->progressPercentage }}%"></div>
                        </div>

                        <div class="mb-4">
                            <h5>Description</h5>
                            <p>{{ $collecte->description ?? 'No description provided.' }}</p>
                        </div>

                        @if($collecte->media->isNotEmpty())
                            <div class="mb-4">
                                <h5>Media</h5>
                                <div class="row g-3">
                                    @foreach($collecte->media as $media)
                                        <div class="col-md-4">
                                            @if(str_starts_with($media->media_type, 'image/'))
                                                <img src="{{ Storage::url($media->file_path) }}" class="img-fluid rounded" alt="Collecte media">
                                            @elseif(str_starts_with($media->media_type, 'video/'))
                                                <video class="img-fluid rounded" controls>
                                                    <source src="{{ Storage::url($media->file_path) }}" type="{{ $media->media_type }}">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Map Section -->
                <div class="collecte-card card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Location</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <!-- Action Card -->
                <div class="collecte-card card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        @if(auth()->user()->isAdmin() || (auth()->user()->isSupervisor() && $collecte->user_id === auth()->id()))
                            <form action="{{ route('collecte.update-status', $collecte) }}" method="POST" class="mb-3">
                                @csrf
                                @method('PATCH')
                                <div class="mb-3">
                                    <label class="form-label">Update Status</label>
                                    <select name="status" class="form-select">
                                        <option value="planned" {{ $collecte->status === 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="in_progress" {{ $collecte->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $collecte->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $collecte->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle"></i> Update Status
                                </button>
                            </form>
                        @else
                            @if(!$collecte->isFull && !$collecte->contributors->contains(auth()->id()))
                                <form action="{{ route('collecte.join', $collecte) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-person-plus"></i> Join Collection
                                    </button>
                                </form>
                            @elseif($collecte->contributors->contains(auth()->id()))
                                <form action="{{ route('collecte.leave', $collecte) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-person-x"></i> Leave Collection
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Contributors Card -->
                <div class="collecte-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Contributors</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @forelse($collecte->contributors as $contributor)
                                <div class="list-group-item d-flex align-items-center">
                                    <img src="{{ $contributor->profile_photo_url }}" alt="{{ $contributor->name }}" class="rounded-circle me-2" width="32" height="32">
                                    <div>
                                        <h6 class="mb-0">{{ $contributor->first_name }} {{ $contributor->last_name }}</h6>
                                        <small class="text-muted">{{ $contributor->pivot->joined_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No contributors yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        var map = L.map('map').setView([{{ $collecte->latitude }}, {{ $collecte->longitude }}], 13);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add marker
        L.marker([{{ $collecte->latitude }}, {{ $collecte->longitude }}])
            .addTo(map)
            .bindPopup('{{ $collecte->location }}')
            .openPopup();
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush
@endsection 