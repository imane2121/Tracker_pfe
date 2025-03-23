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
<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 text-center text-md-start">
                    <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('images/default-avatar.png') }}" 
                         alt="Profile Picture" 
                         class="profile-picture">
                </div>
                <div class="col-md-8 text-center text-md-start mt-4 mt-md-0">
                    <h1 class="profile-name">{{ $user->first_name }} {{ $user->last_name }}</h1>
                    <p class="profile-role mb-0">
                        @switch($user->role)
                            @case('admin')
                                <i class="fas fa-shield-alt me-2"></i>Administrator
                                @break
                            @case('supervisor')
                                <i class="fas fa-user-tie me-2"></i>Supervisor
                                @break
                            @case('contributor')
                                <i class="fas fa-user me-2"></i>Contributor
                                @break
                        @endswitch
                    </p>
                    <div class="profile-actions mt-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-light">
                            <i class="fas fa-edit me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Profile Information -->
            <div class="col-md-8">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-user-circle"></i>
                        Personal Information
                    </div>
                    <div class="profile-card-body">
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </div>
                            <div class="profile-info-value">{{ $user->email }}</div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Location
                            </div>
                            <div class="profile-info-value">{{ $user->city ? $user->city->name : 'Not specified' }}</div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-phone"></i>
                                Phone Number
                            </div>
                            <div class="profile-info-value">{{ $user->phone_number ?? 'Not specified' }}</div>
                        </div>
                        <div class="profile-info-item">
                            <div class="profile-info-label">
                                <i class="fas fa-building"></i>
                                Organization
                            </div>
                            <div class="profile-info-value">{{ $user->organisation ?? 'Not specified' }}</div>
                        </div>
                    </div>
                </div>

                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-chart-bar"></i>
                        Statistics
                    </div>
                    <div class="profile-card-body">
                        <div class="profile-stats">
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->signals ? $user->signals->count() : 0 }}</div>
                                <div class="stat-label">Signals Created</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->contributors ? $user->contributors->count() : 0 }}</div>
                                <div class="stat-label">Collections Joined</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value">{{ $user->credibility_score ?? 0 }}</div>
                                <div class="stat-label">Credibility Score</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Region Subscriptions -->
            <div class="col-md-4">
                <div class="region-card">
                    <div class="region-header">
                        <h3 class="region-title">
                            <i class="fas fa-map-marker-alt"></i>
                            Region Subscriptions
                        </h3>
                    </div>
                    <div class="region-content">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="region-grid">
                            @foreach($availableRegions as $region)
                                @if(in_array($region, $subscribedRegions))
                                    <div class="region-item">
                                        <span class="region-name">{{ $region }}</span>
                                        <div class="region-actions">
                                            <form action="{{ route('subscriptions.destroy', $region) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="region-btn region-btn-unsubscribe" onclick="return confirm('Are you sure you want to unsubscribe from this region?')">
                                                    <i class="fas fa-times"></i>
                                                    Unsubscribe
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="region-item">
                                        <span class="region-name">{{ $region }}</span>
                                        <div class="region-actions">
                                            <form action="{{ route('subscriptions.store') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="region" value="{{ $region }}">
                                                <button type="submit" class="region-btn region-btn-subscribe">
                                                    <i class="fas fa-plus"></i>
                                                    Subscribe
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @if(!empty($subscriptions))
                            <div class="mt-4">
                                <h4 class="text-muted mb-3">Notification Preferences</h4>
                                @foreach($subscriptions as $subscription)
                                    <div class="region-item mb-2">
                                        <div>
                                            <span class="region-name">{{ $subscription->region }}</span>
                                            <div class="notification-preferences">
                                                <form action="{{ route('subscriptions.update', $subscription->region) }}" method="POST" class="d-flex gap-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <label class="notification-preference">
                                                        <input type="checkbox" name="email_notifications" value="1" {{ $subscription->email_notifications ? 'checked' : '' }} class="form-check-input">
                                                        <span>Email</span>
                                                    </label>
                                                    <label class="notification-preference">
                                                        <input type="checkbox" name="push_notifications" value="1" {{ $subscription->push_notifications ? 'checked' : '' }} class="form-check-input">
                                                        <span>Push</span>
                                                    </label>
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 