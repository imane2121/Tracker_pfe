@extends('layouts.app')

@section('content')
<main class="main">
    <section id="dashboard-section" class="dashboard-section section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header accent-background">
                            <h4>{{ __('Dashboard') }}</h4>
                        </div>

                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <p>{{ __('You are logged in!') }}</p>

                            <!-- Role-Specific Content -->
                            @if (Auth::user()->role === 'admin')
                                <div class="admin-dashboard">
                                    <h5>Admin Dashboard</h5>
                                    <p>Welcome, Admin! You have access to all features.</p>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Manage Users</a>
                                </div>
                            @elseif (Auth::user()->role === 'supervisor')
                                <div class="supervisor-dashboard">
                                    <h5>Supervisor Dashboard</h5>
                                    <p>Welcome, Supervisor! Your account is currently 
                                        <strong>{{ Auth::user()->account_status }}</strong>.
                                    </p>
                                    @if (Auth::user()->account_status === 'under_review')
                                        <div class="alert alert-info">
                                            Your account is under review. Please wait for admin approval.
                                        </div>
                                    @else
                                        <a href="{{ route('supervisor.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                                    @endif
                                </div>
                            @elseif (Auth::user()->role === 'contributor')
                                <div class="contributor-dashboard">
                                    <h5>Contributor Dashboard</h5>
                                    <p>Welcome, Contributor! Start contributing to the platform.</p>
                                    <a href="{{ route('contributor.dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection