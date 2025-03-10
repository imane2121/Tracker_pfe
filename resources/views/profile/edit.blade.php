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

    body .profile-picture-upload {
        position: relative !important;
        display: inline-block !important;
    }

    body .profile-picture-upload .btn {
        position: absolute !important;
        bottom: 0 !important;
        right: 0 !important;
        background: rgba(255, 255, 255, 0.9) !important;
        border-radius: 50% !important;
        width: 35px !important;
        height: 35px !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        transition: all 0.3s ease !important;
    }

    body .profile-picture-upload .btn:hover {
        background: white !important;
        transform: scale(1.1) !important;
    }

    body .profile-picture-upload input[type="file"] {
        display: none !important;
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

    body .form-label {
        font-weight: 500 !important;
        color: #444 !important;
        margin-bottom: 0.5rem !important;
    }

    body .form-control {
        border-radius: 8px !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.75rem !important;
        transition: all 0.3s ease !important;
    }

    body .form-control:focus {
        border-color: #0e346a !important;
        box-shadow: 0 0 0 0.2rem rgba(14, 52, 106, 0.25) !important;
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
        background: #f8f9fa !important;
        border: 1px solid #dee2e6 !important;
        color: #0e346a !important;
    }

    body .profile-actions .btn-light:hover {
        background: #e9ecef !important;
        transform: translateY(-2px) !important;
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
                    <div class="profile-picture-upload">
                        <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('images/default-avatar.png') }}" 
                             alt="Profile Picture" 
                             class="profile-picture"
                             id="profile-picture-preview">
                        <label for="profile-picture" class="btn">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" 
                               id="profile-picture" 
                               name="profile_picture" 
                               accept="image/*" 
                               class="form-control">
                    </div>
                </div>
                <div class="col-md-8 text-center text-md-start mt-4 mt-md-0">
                    <h1 class="profile-name">Edit Profile</h1>
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
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-user-circle me-2"></i>Profile Information
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name', $user->first_name) }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name', $user->last_name) }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $user->username) }}" 
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control @error('bio') is-invalid @enderror" 
                                          id="bio" 
                                          name="bio" 
                                          rows="3">{{ old('bio', $user->bio) }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="profile-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                                <a href="{{ route('profile.show') }}" class="btn btn-light">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="profile-card">
                    <div class="profile-card-header">
                        <i class="fas fa-key me-2"></i>Change Password
                    </div>
                    <div class="profile-card-body">
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Profile picture preview
    document.getElementById('profile-picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-picture-preview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection 