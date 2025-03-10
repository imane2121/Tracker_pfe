@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/collecte.css') }}">
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
                    <a href="{{ route('collecte.create') }}" class="btn btn-light btn-lg">
                        <i class="bi bi-plus-circle"></i> Create New Collecte
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filters Section -->
        <div class="collecte-filters">
            <form action="{{ route('collecte.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search collectes..." value="{{ request('search') }}">
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
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Collectes List -->
        @if($collectes->isEmpty())
            <div class="collecte-empty-state">
                <i class="bi bi-people"></i>
                <h3>No Collectes Found</h3>
                <p>There are no collectes matching your criteria. Create a new one to get started!</p>
                <a href="{{ route('collecte.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Collecte
                </a>
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
                                    
                                    @if($collecte->user_id === auth()->id())
                                        <a href="{{ route('collecte.edit', $collecte) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    @elseif(!$collecte->isFull && !$collecte->contributors->contains(auth()->id()))
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