@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/collecte.css') }}">
<style>
    .collecte-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding-bottom: 3rem;
    }

    .collecte-header {
        background: linear-gradient(135deg, #0e346a 0%, #1e56b0 100%);
        padding: 2.5rem 0;
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .collecte-header h1 {
        font-weight: 600;
        font-size: 2.2rem;
    }

    .collecte-header p {
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .collecte-filters {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-top: -1.5rem; /* Pull up the filters to remove gap */
        position: relative; /* Ensure it stays above content */
        z-index: 1;
    }

    .collecte-card {
        background: white;
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .collecte-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .collecte-card .card-header {
        background: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1rem 1.25rem;
    }

    .collecte-status {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
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

    .collecte-card .card-body {
        padding: 1.5rem;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.25rem;
    }

    .collecte-info {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        color: #596575;
    }

    .collecte-info i {
        font-size: 1.1rem;
        margin-right: 0.75rem;
        color: #1e56b0;
    }

    .collecte-progress {
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        margin: 1rem 0;
        overflow: hidden;
    }

    .collecte-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #1e56b0 0%, #3498db 100%);
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    .collecte-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .collecte-actions .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-outline-primary {
        border-color: #1e56b0;
        color: #1e56b0;
    }

    .btn-outline-primary:hover {
        background: #1e56b0;
        color: white;
    }

    .btn-success {
        background: #2ecc71;
        border-color: #2ecc71;
    }

    .btn-success:hover {
        background: #27ae60;
        border-color: #27ae60;
    }

    .btn-danger {
        background: #e74c3c;
        border-color: #e74c3c;
    }

    .btn-danger:hover {
        background: #c0392b;
        border-color: #c0392b;
    }

    .collecte-empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .collecte-empty-state i {
        font-size: 3rem;
        color: #1e56b0;
        margin-bottom: 1.5rem;
    }

    .collecte-empty-state h3 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .collecte-empty-state p {
        color: #596575;
        margin-bottom: 1.5rem;
    }

    /* Form Controls Styling */
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #1e56b0;
        box-shadow: 0 0 0 0.2rem rgba(30, 86, 176, 0.15);
    }

    /* Pagination Styling */
    .pagination {
        gap: 0.25rem;
    }

    .page-link {
        border-radius: 6px;
        border: none;
        color: #1e56b0;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        min-width: 32px;
        text-align: center;
        height: 32px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .page-item.active .page-link {
        background: #1e56b0;
        color: white;
    }

    .page-item.disabled .page-link {
        background: #f8f9fa;
        color: #6c757d;
    }

    .page-link[rel="prev"],
    .page-link[rel="next"] {
        font-size: 0.8rem;
        padding: 0.5rem;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .collecte-header {
            padding: 2rem 0;
        }

        .collecte-card {
            margin-bottom: 1rem;
        }

        .collecte-actions {
            flex-direction: column;
        }

        .collecte-actions .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="collecte-container">
    <!-- Header Section -->
    <div class="collecte-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Collectes</h1>
                    <p class="mb-0 mt-2">Manage and participate in waste collection events</p>
                </div>
                <div class="col-md-4 text-md-end">
                    @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                        <a href="{{ route('collecte.cluster') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-plus-circle"></i> Create New Collecte
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filters Section -->
        <div class="collecte-filters">
            <form action="{{ route('collecte.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by location or region..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="participants" {{ request('sort') == 'participants' ? 'selected' : '' }}>Most Participants</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'sort']))
                            <a href="{{ route('collecte.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Collectes List -->
        @if($collectes->isEmpty())
            <div class="collecte-empty-state">
                <i class="bi bi-people"></i>
                <h3>No Collectes Found</h3>
                <p>There are no collectes matching your criteria.</p>
                @if(auth()->user()->isAdmin() || auth()->user()->isSupervisor())
                    <a href="{{ route('collecte.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Collecte
                    </a>
                @endif
            </div>
        @else
            <div class="row">
                @foreach($collectes as $collecte)
                    <div class="col-md-6 col-lg-4">
                        <div class="collecte-card card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="collecte-status status-{{ $collecte->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $collecte->status)) }}
                                </span>
                                <small class="text-muted">{{ $collecte->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $collecte->location }}</h5>
                                
                                <div class="collecte-info">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $collecte->region }}</span>
                                </div>

                                <div class="collecte-info">
                                    <i class="bi bi-calendar"></i>
                                    <span>{{ $collecte->starting_date->format('M d, Y') }}</span>
                                </div>

                                <div class="collecte-info">
                                    <i class="bi bi-people"></i>
                                    <span>{{ $collecte->current_contributors }}/{{ $collecte->nbrContributors }} Participants</span>
                                </div>

                                <div class="collecte-progress">
                                    <div class="collecte-progress-bar" style="width: {{ $collecte->progressPercentage }}%"></div>
                                </div>

                                <div class="collecte-actions">
                                    <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                    
                                    @if(auth()->user()->isAdmin() || (auth()->user()->isSupervisor() && $collecte->user_id === auth()->id()))
                                        <a href="{{ route('collecte.edit', $collecte) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('collecte.destroy', $collecte) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this collection?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    @elseif(!$collecte->isFull && !$collecte->contributors->contains(auth()->id()) && $collecte->status === 'planned')
                                        <form action="{{ route('collecte.join', $collecte) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-person-plus"></i> Join
                                            </button>
                                        </form>
                                    @elseif($collecte->contributors->contains(auth()->id()))
                                        <form action="{{ route('collecte.leave', $collecte) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">
                                                <i class="bi bi-person-x"></i> Leave
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $collectes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 