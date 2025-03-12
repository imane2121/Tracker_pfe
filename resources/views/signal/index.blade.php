@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>My Reports
        </h2>
        <a href="{{ route('signal.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>New Report
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="validated">Validated</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Waste Type</label>
                    <select class="form-select" name="waste_type">
                        <option value="">All Types</option>
                        @foreach($wasteTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Date Range</label>
                    <input type="date" class="form-control" name="date">
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="bi bi-funnel me-1"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Signals Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Waste Types</th>
                            <th>Volume</th>
                            <th>Status</th>
                            <th>Media</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($signals as $signal)
                            <tr>
                                <td>{{ $signal->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                        {{ $signal->location }}
                                    </div>
                                </td>
                                <td>
                                    @if($signal->wasteTypes && $signal->wasteTypes->count() > 0)
                                        @foreach($signal->wasteTypes as $type)
                                            <span class="badge bg-info me-1">{{ $type->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No waste types</span>
                                    @endif
                                </td>
                                <td>{{ $signal->volume }} m³</td>
                                <td>
                                    <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($signal->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($signal->media->count() > 0)
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#mediaModal{{ $signal->id }}">
                                            <i class="bi bi-images"></i> View ({{ $signal->media->count() }})
                                        </button>

                                        <!-- Media Modal -->
                                        <div class="modal fade" id="mediaModal{{ $signal->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Media Files</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            @foreach($signal->media as $media)
                                                                <div class="col-6 col-md-4">
                                                                    @if(str_contains($media->media_type, 'image'))
                                                                        <img src="{{ asset('storage/' . $media->file_path) }}" class="img-fluid rounded" alt="Signal media">
                                                                    @else
                                                                        <video class="img-fluid rounded" controls>
                                                                            <source src="{{ asset('storage/' . $media->file_path) }}" type="{{ $media->media_type }}">
                                                                        </video>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No media</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $signal->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($signal->status === 'pending')
                                            <button type="button" class="btn btn-outline-danger" onclick="deleteSignal({{ $signal->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Details Modal -->
                                    <div class="modal fade" id="detailsModal{{ $signal->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Signal Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h6>Location</h6>
                                                        <p>{{ $signal->location }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Description</h6>
                                                        <p>{{ $signal->description ?: 'No description provided' }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Waste Types</h6>
                                                        <div>
                                                            @foreach($signal->wasteTypes as $type)
                                                                <span class="badge bg-info me-1">{{ $type->name }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Custom Type</h6>
                                                        <p>{{ $signal->customType ?: 'None' }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Volume</h6>
                                                        <p>{{ $signal->volume }} m³</p>
                                                    </div>
                                                    <div>
                                                        <h6>Status History</h6>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-{{ $signal->status === 'validated' ? 'success' : ($signal->status === 'rejected' ? 'danger' : 'warning') }}">
                                                                {{ ucfirst($signal->status) }}
                                                            </span>
                                                            <span class="ms-2 text-muted small">
                                                                {{ $signal->updated_at->diffForHumans() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-4"></i>
                                        <p class="mt-2">No reports found</p>
                                        <a href="{{ route('signal.create') }}" class="btn btn-primary mt-2">
                                            Create your first report
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteSignal(signalId) {
        if (confirm('Are you sure you want to delete this signal?')) {
            // Send delete request
            fetch(`/signals/${signalId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated list
                    window.location.reload();
                } else {
                    alert('Error deleting signal');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting signal');
            });
        }
    }

    // Filter form handling
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        window.location.href = `${window.location.pathname}?${params.toString()}`;
    });
</script>
@endpush

@push('styles')
<style>
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .modal-body h6 {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .modal-body p {
        margin-bottom: 1rem;
    }
    .btn btn-secondary btn-sm dropdown-toggle{
        width: 100%;
    }
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        
        .btn-group {
            display: flex;
            margin-top: 0.5rem;
        }
        
        .btn-group .btn {
            flex: 1;
        }
    }
</style>
@endpush
@endsection 