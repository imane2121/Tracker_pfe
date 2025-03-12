@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/collecte.css') }}">
@endpush

@section('content')
<div class="collecte-container">
    <div class="container">
        <!-- Header Section -->
        <div class="collecte-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-0">Collection Dashboard</h1>
                        <p class="mb-0 mt-2">Overview of your collections activities</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('collecte.create') }}" class="btn btn-light btn-lg">
                            <i class="bi bi-plus-circle"></i> Create New Collection
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="collecte-card card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill text-primary fs-1 mb-3"></i>
                        <h3 class="mb-0">{{ $userCollectes }}</h3>
                        <p class="text-muted mb-0">Your Collections</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="collecte-card card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-check-fill text-success fs-1 mb-3"></i>
                        <h3 class="mb-0">{{ $userContributions }}</h3>
                        <p class="text-muted mb-0">Contributions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="collecte-card card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check-fill text-info fs-1 mb-3"></i>
                        <h3 class="mb-0">{{ $upcomingCollectes->count() }}</h3>
                        <p class="text-muted mb-0">Upcoming</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="collecte-card card h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
                        <h3 class="mb-0">{{ $completedCollectes->count() }}</h3>
                        <p class="text-muted mb-0">Completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Upcoming Collectes -->
            <div class="col-lg-6 mb-4">
                <div class="collecte-card card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-calendar"></i> Upcoming Collectes</h5>
                        <a href="{{ route('collecte.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @forelse($upcomingCollectes as $collecte)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <span class="collecte-status status-{{ $collecte->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $collecte->status)) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $collecte->location }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> {{ $collecte->starting_date->format('M d, Y') }}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted fs-2"></i>
                                <p class="text-muted mt-2">No upcoming collections</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- In Progress Collectes -->
            <div class="col-lg-6 mb-4">
                <div class="collecte-card card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> In Progress</h5>
                        <a href="{{ route('collecte.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @forelse($inProgressCollectes as $collecte)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <span class="collecte-status status-{{ $collecte->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $collecte->status)) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $collecte->location }}</h6>
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $collecte->progressPercentage }}%"
                                             aria-valuenow="{{ $collecte->progressPercentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $collecte->current_contributors }}/{{ $collecte->nbrContributors }} participants
                                    </small>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-arrow-repeat text-muted fs-2"></i>
                                <p class="text-muted mt-2">No collections in progress</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Completed Collectes -->
            <div class="col-12">
                <div class="collecte-card card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-check-circle"></i> Recently Completed</h5>
                        <a href="{{ route('collecte.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($completedCollectes as $collecte)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $collecte->location }}</h6>
                                            <p class="card-text text-muted small">
                                                <i class="bi bi-calendar-check"></i> Completed on {{ $collecte->updated_at->format('M d, Y') }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    {{ $collecte->current_contributors }} participants
                                                </small>
                                                <a href="{{ route('collecte.show', $collecte) }}" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-4">
                                    <i class="bi bi-check-circle text-muted fs-2"></i>
                                    <p class="text-muted mt-2">No completed collections</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 