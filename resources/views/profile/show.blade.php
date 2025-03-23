@extends('layouts.app')

@section('styles')
<style>
.profile-container {
    padding-top: 80px !important;
    background-color: #f8f9fa !important;
    min-height: 100vh !important;
}

.profile-header {
    background: linear-gradient(135deg, #0e346a 0%, #1a5f9e 100%) !important;
    padding: 4rem 0 !important;
    margin-bottom: 3rem !important;
    color: white !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
    position: relative !important;
    overflow: hidden !important;
}

.profile-header::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E") !important;
    opacity: 0.1 !important;
}

.profile-picture {
    width: 150px !important;
    height: 150px !important;
    border-radius: 50% !important;
    border: 4px solid white !important;
    object-fit: cover !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
    transition: transform 0.3s ease !important;
}

.profile-picture:hover {
    transform: scale(1.05) !important;
}

.profile-name {
    font-size: 2.5rem !important;
    font-weight: 700 !important;
    margin: 1.5rem 0 0.75rem 0 !important;
    color: white !important;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
}

.profile-role {
    font-size: 1.2rem !important;
    opacity: 0.95 !important;
    color: rgba(255, 255, 255, 0.95) !important;
    margin-bottom: 1.5rem !important;
}

.profile-card {
    background: white !important;
    border-radius: 20px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    margin-bottom: 2rem !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.profile-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1) !important;
}

.profile-card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
    padding: 1.5rem !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    font-weight: 600 !important;
    color: #0e346a !important;
    font-size: 1.2rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}

.profile-card-header i {
    color: #1a5f9e !important;
    font-size: 1.1rem !important;
}

.profile-card-body {
    padding: 2rem !important;
}

.profile-info-item {
    display: flex !important;
    align-items: center !important;
    margin-bottom: 1.5rem !important;
    padding-bottom: 1.5rem !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.profile-info-item:last-child {
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
    border-bottom: none !important;
}

.profile-info-label {
    width: 160px !important;
    color: #666 !important;
    font-weight: 500 !important;
    font-size: 1rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.profile-info-value {
    flex: 1 !important;
    color: #333 !important;
    font-size: 1rem !important;
    font-weight: 500 !important;
}

.profile-stats {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
    gap: 1.5rem !important;
    margin-top: 2rem !important;
}

.stat-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
    padding: 1.75rem !important;
    border-radius: 15px !important;
    text-align: center !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.stat-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}

.stat-value {
    font-size: 2.5rem !important;
    font-weight: 700 !important;
    color: #0e346a !important;
    margin-bottom: 0.75rem !important;
    line-height: 1 !important;
}

.stat-label {
    color: #666 !important;
    font-size: 1.1rem !important;
    font-weight: 500 !important;
}

.profile-actions {
    display: flex !important;
    gap: 1rem !important;
    margin-top: 2rem !important;
}

.profile-actions .btn {
    padding: 0.875rem 2rem !important;
    border-radius: 12px !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.profile-actions .btn-light {
    background: rgba(255, 255, 255, 0.95) !important;
    border: none !important;
    color: #0e346a !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
}

.profile-actions .btn-light:hover {
    background: white !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

/* Region Subscription Styles */
.region-card {
    background: white !important;
    border-radius: 20px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05) !important;
    margin-bottom: 2rem !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.region-card:hover {
    transform: translateY(-5px) !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1) !important;
}

.region-header {
    background: linear-gradient(135deg, #0e346a 0%, #1a5f9e 100%) !important;
    padding: 1.75rem !important;
    color: white !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    position: relative !important;
    overflow: hidden !important;
}

.region-title {
    font-size: 1.4rem !important;
    font-weight: 600 !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}

.region-content {
    padding: 2rem !important;
}

.region-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    gap: 1.25rem !important;
    margin-top: 1.5rem !important;
}

.region-item {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
    border-radius: 15px !important;
    padding: 1.5rem !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    transition: all 0.3s ease !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.region-item:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
}

.region-name {
    font-weight: 600 !important;
    color: #333 !important;
    font-size: 1.1rem !important;
}

.region-actions {
    display: flex !important;
    gap: 0.75rem !important;
}

.region-btn {
    padding: 0.625rem 1.25rem !important;
    border-radius: 10px !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.region-btn-subscribe {
    background: #0e346a !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(14, 52, 106, 0.2) !important;
}

.region-btn-subscribe:hover {
    background: #1a5f9e !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(14, 52, 106, 0.3) !important;
}

.region-btn-unsubscribe {
    background: #dc3545 !important;
    color: white !important;
    border: none !important;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2) !important;
}

.region-btn-unsubscribe:hover {
    background: #c82333 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3) !important;
}

.notification-preferences {
    display: flex !important;
    gap: 1.5rem !important;
    margin-top: 1rem !important;
    padding-top: 1rem !important;
    border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.notification-preference {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    font-size: 0.95rem !important;
    color: #666 !important;
}

.form-check-input:checked {
    background-color: #0e346a !important;
    border-color: #0e346a !important;
}

.form-select {
    border-radius: 10px !important;
    border: 1px solid #dee2e6 !important;
    padding: 0.75rem !important;
    transition: all 0.3s ease !important;
}

.form-select:focus {
    border-color: #0e346a !important;
    box-shadow: 0 0 0 0.2rem rgba(14, 52, 106, 0.25) !important;
}

@media (max-width: 768px) {
    .profile-header {
        padding: 3rem 0 !important;
    }

    .profile-picture {
        width: 120px !important;
        height: 120px !important;
    }

    .profile-name {
        font-size: 2rem !important;
    }

    .profile-info-item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.75rem !important;
    }

    .profile-info-label {
        width: 100% !important;
    }

    .profile-stats {
        grid-template-columns: 1fr !important;
    }

    .profile-actions {
        flex-direction: column !important;
    }

    .profile-actions .btn {
        width: 100% !important;
        justify-content: center !important;
    }

    .profile-card-body {
        padding: 1.5rem !important;
    }

    .region-grid {
        grid-template-columns: 1fr !important;
    }

    .region-actions {
        flex-direction: column !important;
    }

    .region-btn {
        width: 100% !important;
        justify-content: center !important;
    }
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-4 mb-4">
            <div class="card profile-card">
                <div class="card-body text-center">
                    <div class="profile-picture-container mb-4">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('assets/images/default-avatar.png') }}" 
                             alt="{{ $user->first_name }}'s profile picture" 
                             class="profile-picture">
                        @if(auth()->id() === $user->id)
                            <form action="{{ route('profile.update-picture') }}" method="POST" enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="profile_picture" class="form-label">Change Profile Picture</label>
                                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Update Picture</button>
                            </form>
                        @endif
                    </div>
                    <h4 class="mb-1">{{ $user->first_name }} {{ $user->last_name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <div class="profile-stats d-flex justify-content-center gap-4 mb-4">
                        <div class="stat-item">
                            <div class="stat-value">{{ $user->collections ? $user->collections->count() : 0 }}</div>
                            <div class="stat-label">Collections</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $user->contributions ? $user->contributions->count() : 0 }}</div>
                            <div class="stat-label">Contributions</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $user->regionSubscriptions ? $user->regionSubscriptions->count() : 0 }}</div>
                            <div class="stat-label">Regions</div>
                        </div>
                    </div>
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-pencil-square me-2"></i>Edit Profile
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Profile Information -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Name</label>
                            <p class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="mb-0">{{ $user->email }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Phone</label>
                            <p class="mb-0">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Member Since</label>
                            <p class="mb-0">{{ $user->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Region Subscriptions -->
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Region Subscriptions</h5>
                    @if(auth()->id() === $user->id)
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-gear me-1"></i>Manage
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($user->regionSubscriptions->count() > 0)
                        <div class="row g-3">
                            @foreach($user->regionSubscriptions as $subscription)
                                <div class="col-md-6">
                                    <div class="region-card">
                                        <div class="region-header">
                                            <h6 class="mb-1">{{ $subscription->region }}</h6>
                                            <div class="notification-badges">
                                                @if($subscription->email_notifications)
                                                    <span class="badge bg-info" title="Email notifications enabled">
                                                        <i class="bi bi-envelope"></i>
                                                    </span>
                                                @endif
                                                @if($subscription->push_notifications)
                                                    <span class="badge bg-success" title="Push notifications enabled">
                                                        <i class="bi bi-bell"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="mb-2 text-muted small">{{ $subscription->region }} Region</p>
                                        <div class="region-stats">
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ $subscription->region }} collections
                                            </span>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-people me-1"></i>
                                                {{ $subscription->region }} subscribers
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No region subscriptions yet.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Collections -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Recent Collections</h5>
                </div>
                <div class="card-body">
                    @if($user->collections && $user->collections->count() > 0)
                        <div class="list-group">
                            @foreach($user->collections->take(5) as $collection)
                                <a href="{{ route('collections.show', $collection) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $collection->location }}</h6>
                                            <p class="mb-1 text-muted small">{{ $collection->description ?? 'No description provided' }}</p>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $collection->starting_date->format('M d, Y') }}
                                                </span>
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    {{ $collection->region }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">
                                                {{ $collection->current_contributors }}/{{ $collection->nbrContributors }} contributors
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No collections created yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 