@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Account Under Review') }}</div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <h4 class="alert-heading">Account Under Review</h4>
                        <p>Thank you for verifying your email. Your supervisor account is currently under review by our administrators.</p>
                        <p>You will receive an email notification once your account has been activated.</p>
                        <p>This process typically takes 1-2 business days.</p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('logout') }}" class="btn btn-primary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 