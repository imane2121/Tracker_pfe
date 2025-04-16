@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/collecte.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .collecte-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }

    /* Back Button */
    .back-button {
        margin-bottom: 2rem;
    }

    .back-button .btn {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Cards Styling */
    .collecte-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .collecte-card .card-header {
        background: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem;
    }

    .card-header h5 {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Status Badge */
    .collecte-status {
        padding: 0.5rem 1.25rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .status-planned {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-in_progress {
        background: #fff3e0;
        color: #f57c00;
    }

    .status-completed {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-cancelled {
        background: #ffebee;
        color: #c62828;
    }

    /* Card Content */
    .card-body {
        padding: 1.5rem;
    }

    .card-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
    }

    /* Info Items */
    .collecte-info {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        color: #596575;
    }

    .collecte-info i {
        font-size: 1.2rem;
        margin-right: 1rem;
        color: #1e56b0;
        width: 24px;
        text-align: center;
    }

    /* Progress Bar */
    .collecte-progress {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin: 1.5rem 0;
    }

    .collecte-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #1e56b0 0%, #3498db 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    /* Media Section */
    .media-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .media-item {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .media-item:hover {
        transform: scale(1.02);
    }

    .media-item img,
    .media-item video {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    /* Map */
    #map {
        height: 400px;
        border-radius: 0 0 12px 12px;
    }

    /* Contributors List */
    .list-group {
        margin: -0.5rem;
    }

    .list-group-item {
        border: none;
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 8px !important;
        transition: background-color 0.2s;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    .contributor-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* Action Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-success {
        background: #2ecc71;
        border-color: #2ecc71;
    }

    .btn-success:hover {
        background: #27ae60;
        border-color: #27ae60;
        transform: translateY(-1px);
    }

    .btn-danger {
        background: #e74c3c;
        border-color: #e74c3c;
    }

    .btn-danger:hover {
        background: #c0392b;
        border-color: #c0392b;
        transform: translateY(-1px);
    }

    .form-select {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .form-select:focus {
        border-color: #1e56b0;
        box-shadow: 0 0 0 0.2rem rgba(30, 86, 176, 0.15);
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .collecte-container {
            padding: 1rem 0;
        }

        .card-title {
            font-size: 1.5rem;
        }

        .media-container {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }

        .media-item {
            height: 150px;
        }
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.1rem;
        color: #2c3e50;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .waste-types-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .waste-type-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .volume-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
    }

    .volume-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .info-label {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .info-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .signals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .signal-item {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .signal-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .signal-header {
        background: #f8f9fa;
        padding: 0.75rem;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        border-bottom: 1px solid #e9ecef;
    }

    .signal-header:has(.signal-id) {
        justify-content: space-between;
    }

    .signal-id {
        font-weight: 600;
        color: #2c3e50;
    }

    .signal-body {
        padding: 0.75rem;
    }

    .signal-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        color: #596575;
    }

    .signal-info i {
        color: #1e56b0;
        width: 16px;
    }

    .signal-status {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-validated {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-pending {
        background: #fff3e0;
        color: #f57c00;
    }

    @media (max-width: 768px) {
        .volume-info-grid {
            grid-template-columns: 1fr;
        }

        .signals-grid {
            grid-template-columns: 1fr;
        }
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .detail-label {
        color: #6c757d;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-label i {
        color: #1e56b0;
    }

    .detail-value {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .waste-types {
        grid-column: 1 / -1;
    }

    .waste-types-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .waste-type-badge {
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="collecte-container">
    <div class="container">
        <!-- Back Button -->
        <div class="back-button mb-4">
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

                        <!-- Single Details Section -->
                        <div class="mb-4">
                            <h5 class="section-title">
                                <i class="bi bi-info-circle"></i> Collection Details
                            </h5>
                            <div class="details-grid">
                                <!-- Volume Information -->
                                <div class="detail-item">
                                    <div class="detail-label">
                                        <i class="bi bi-box"></i> Total Volume
                                    </div>
                                    <div class="detail-value">{{ $collecte->actual_volume }} m³</div>
                                </div>

                                <!-- Signals Count -->
                                @if($collecte->signal_ids)
                                    <div class="detail-item">
                                        <div class="detail-label">
                                            <i class="bi bi-exclamation-triangle"></i> Based on
                                        </div>
                                        <div class="detail-value">
                                            {{ count($collecte->signal_ids) }} {{ Str::plural('signal', count($collecte->signal_ids)) }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Single Waste Types Section -->
                                @if($collecte->actual_waste_types)
                                    <div class="detail-item waste-types">
                                        <div class="detail-label">
                                            <i class="bi bi-recycle"></i> Waste Types
                                        </div>
                                        <div class="waste-types-container">
                                            @foreach($collecte->actual_waste_types as $wasteTypeId)
                                                @php
                                                    $wasteType = \App\Models\WasteTypes::find($wasteTypeId);
                                                @endphp
                                                @if($wasteType)
                                                    <span class="waste-type-badge">{{ $wasteType->name }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($collecte->media->isNotEmpty())
                            <div class="mb-4">
                                <h5>Media</h5>
                                <div class="media-container">
                                    @foreach($collecte->media as $media)
                                        <div class="media-item">
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
                                    <select name="status" class="form-select" id="statusSelect">
                                        <option value="planned" {{ $collecte->status === 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="in_progress" {{ $collecte->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $collecte->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $collecte->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" id="updateStatusBtn">
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
                        @if(($collecte->status === 'completed') && 
                            (auth()->user()->isAdmin() || (auth()->user()->isSupervisor() && $collecte->user_id === auth()->id())))
                            @php
                                $rapport = \App\Models\Rapport::where('collecte_id', $collecte->id)->first();
                            @endphp
                            
                            @if($rapport)
                                <a href="{{ route('rapport.edit', $collecte) }}" class="btn btn-primary w-100 mt-3">
                                    <i class="bi bi-pencil"></i> Edit Rapport
                                </a>
                                
                                <!-- Export Rapport Dropdown Button -->
                                <div class="dropdown w-100 mt-3">
                                    <button class="btn btn-outline-primary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-download"></i> Export Rapport
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('rapport.export', ['collecte' => $collecte, 'format' => 'pdf']) }}">
                                                <i class="bi bi-file-pdf"></i> Export as PDF
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('rapport.export', ['collecte' => $collecte, 'format' => 'csv']) }}">
                                                <i class="bi bi-file-excel"></i> Export as CSV
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $contributor->profile_photo_url }}" 
                                             alt="{{ $contributor->first_name }}" 
                                             class="rounded-circle me-2" 
                                             width="32" 
                                             height="32">
                                        <div>
                                            <h6 class="mb-0">{{ $contributor->first_name }} {{ $contributor->last_name }}</h6>
                                            <small class="text-muted">
                                                @if($contributor->pivot && $contributor->pivot->status === 'accepted')
                                                    Joined {{ \Carbon\Carbon::parse($contributor->pivot->joined_at)->diffForHumans() }}
                                                @elseif($contributor->pivot && $contributor->pivot->status === 'pending')
                                                    <span class="text-warning">Pending approval</span>
                                                @elseif($contributor->pivot && $contributor->pivot->status === 'rejected')
                                                    <span class="text-danger">Request rejected</span>
                                                @else
                                                    Joined recently
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                    @if($contributor->pivot && $contributor->pivot->status === 'pending' && 
                                        (auth()->user()->isAdmin() || (auth()->user()->isSupervisor() && $collecte->user_id === auth()->id())))
                                        <div class="btn-group">
                                            <form action="{{ route('collecte.approve-request', ['collecte' => $collecte, 'contributor' => $contributor]) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-lg"></i> Accept
                                                </button>
                                            </form>
                                            <form action="{{ route('collecte.reject-request', ['collecte' => $collecte, 'contributor' => $contributor]) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @endif
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

        // Status change handling
        const statusSelect = document.getElementById('statusSelect');
        const statusForm = statusSelect?.closest('form');

        if (statusSelect && statusForm) {
            statusForm.addEventListener('submit', function(e) {
                if (statusSelect.value === 'completed') {
                    const currentStatus = '{{ $collecte->status }}';
                    if (currentStatus === 'in_progress') {
                        e.preventDefault();
                        window.location.href = '{{ route('rapport.generate', $collecte) }}';
                    } else if (currentStatus !== 'completed') {
                        e.preventDefault();
                        // First update status to in_progress
                        fetch('{{ route('collecte.update-status', $collecte) }}', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                status: 'in_progress'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '{{ route('rapport.generate', $collecte) }}';
                            } else {
                                alert('Failed to update status. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred. Please try again.');
                        });
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection 