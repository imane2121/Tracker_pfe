@extends('layouts.app')

@section('styles')
<style>
    body .profile-container {
        padding-top: 100px !important;
        background-color: #f8f9fa !important;
        min-height: 100vh !important;
    }

    body .profile-header {
        background: linear-gradient(135deg, #0e346a 0%, #1a5f9e 100%) !important;
        padding: 3rem 0 !important;
        margin-bottom: 2rem !important;
        color: white !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }

    body .profile-picture {
        width: 120px !important;
        height: 120px !important;
        border-radius: 50% !important;
        border: 4px solid white !important;
        object-fit: cover !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }

    body .profile-name {
        font-size: 2rem !important;
        font-weight: 600 !important;
        margin: 1rem 0 0.5rem 0 !important;
        color: white !important;
    }

    body .profile-role {
        font-size: 1.1rem !important;
        opacity: 0.9 !important;
        color: rgba(255, 255, 255, 0.9) !important;
    }

    body .profile-card {
        background: white !important;
        border-radius: 15px !important;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05) !important;
        margin-bottom: 2rem !important;
        overflow: hidden !important;
        transition: transform 0.3s ease !important;
    }

    body .profile-card:hover {
        transform: translateY(-5px) !important;
    }

    body .profile-card-header {
        background-color: #f8f9fa !important;
        padding: 1.25rem !important;
        border-bottom: 1px solid #eee !important;
        font-weight: 600 !important;
        color: #0e346a !important;
        font-size: 1.1rem !important;
    }

    body .profile-card-body {
        padding: 2rem !important;
    }

    body .profile-info-item {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 1.5rem !important;
        padding-bottom: 1.5rem !important;
        border-bottom: 1px solid #eee !important;
    }

    body .profile-info-item:last-child {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        border-bottom: none !important;
    }

    body .profile-info-label {
        width: 140px !important;
        color: #666 !important;
        font-weight: 500 !important;
        font-size: 1rem !important;
    }

    body .profile-info-value {
        flex: 1 !important;
        color: #333 !important;
        font-size: 1rem !important;
    }

    body .profile-stats {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important;
        gap: 1.5rem !important;
        margin-top: 2rem !important;
    }

    body .stat-card {
        background: #f8f9fa !important;
        padding: 1.5rem !important;
        border-radius: 12px !important;
        text-align: center !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
        transition: transform 0.3s ease !important;
    }

    body .stat-card:hover {
        transform: translateY(-5px) !important;
    }

    body .stat-value {
        font-size: 2rem !important;
        font-weight: 600 !important;
        color: #0e346a !important;
        margin-bottom: 0.5rem !important;
    }

    body .stat-label {
        color: #666 !important;
        font-size: 1rem !important;
        font-weight: 500 !important;
    }

    body .profile-actions {
        display: flex !important;
        gap: 1rem !important;
        margin-top: 1.5rem !important;
    }

    body .profile-actions .btn {
        padding: 0.75rem 1.5rem !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
    }

    body .profile-actions .btn-light {
        background: rgba(255, 255, 255, 0.9) !important;
        border: none !important;
        color: #0e346a !important;
    }

    body .profile-actions .btn-light:hover {
        background: white !important;
        transform: translateY(-2px) !important;
    }

    body .form-check-input:checked {
        background-color: #0e346a !important;
        border-color: #0e346a !important;
    }

    body .form-select {
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.75rem !important;
    }

    body .form-select:focus {
        border-color: #0e346a !important;
        box-shadow: 0 0 0 0.2rem rgba(14, 52, 106, 0.25) !important;
    }

    @media (max-width: 768px) {
        body .profile-header {
            padding: 2rem 0 !important;
        }

        body .profile-picture {
            width: 100px !important;
            height: 100px !important;
        }

        body .profile-name {
            font-size: 1.5rem !important;
        }

        body .profile-info-item {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }

        body .profile-info-label {
            width: 100% !important;
        }

        body .profile-stats {
            grid-template-columns: 1fr !important;
        }

        body .profile-actions {
            flex-direction: column !important;
        }

        body .profile-actions .btn {
            width: 100% !important;
        }

        body .profile-card-body {
            padding: 1.5rem !important;
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
                        <i class="fas fa-user-circle me-2"></i>Profile Information
                    </div>
                    <div class="profile-card-body">
                        <div class="profile-info-item">
                            <span class="profile-info-label">Username</span>
                            <span class="profile-info-value">{{ $user->username }}</span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label">Email</span>
                            <span class="profile-info-value">{{ $user->email }}</span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label">Phone</span>
                            <span class="profile-info-value">{{ $user->phone ?? 'Not provided' }}</span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label">Bio</span>
                            <span class="profile-info-value">{{ $user->bio ?? 'No bio yet' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences -->
            <div class="col-md-4">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-cog me-2"></i>Preferences
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('profile.preferences') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="email_notifications" 
                                           id="email_notifications" {{ $user->email_notifications ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">Email Notifications</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="sms_notifications" 
                                           id="sms_notifications" {{ $user->sms_notifications ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_notifications">SMS Notifications</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Language</label>
                                <select name="language" class="form-select">
                                    <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="fr" {{ $user->language === 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="ar" {{ $user->language === 'ar' ? 'selected' : '' }}>Arabic</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-select">
                                    <option value="UTC" {{ $user->timezone === 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="Africa/Casablanca" {{ $user->timezone === 'Africa/Casablanca' ? 'selected' : '' }}>Casablanca</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 