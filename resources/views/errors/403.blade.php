@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <h1 class="display-1 text-danger mb-4">403</h1>
                    <h2 class="h4 mb-4">Access Denied</h2>
                    <p class="text-muted mb-4">
                        You don't have permission to access this page.
                        Only administrators and supervisors can manage collections.
                    </p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-house"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 