@extends('layouts.app')

@section('content')
<main class="main">
    <section id="starter-section" class="starter-section">
        <div class="signContainer" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
            <div class="verification-card" style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 2rem;">
                <div class="verification-header" style="text-align: center; margin-bottom: 2rem;">
                    <h1 style="color: #2c3e50; font-size: 1.8rem; margin-bottom: 1rem;">{{ __('Verify Your Email Address') }}</h1>
                    <div class="verification-icon" style="font-size: 3rem; color: #3498db; margin-bottom: 1rem;">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>

                <div class="verification-body" style="text-align: center;">
                    @if (session('resent'))
                        <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                            <i class="fas fa-check-circle"></i>
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    <p style="color: #34495e; margin-bottom: 1.5rem; line-height: 1.6;">
                        {{ __('Before proceeding, please check your email for a verification link.') }}
                    </p>

                    <div class="verification-actions" style="margin-top: 2rem;">
                        <p style="color: #7f8c8d; margin-bottom: 1rem;">
                            {{ __('If you did not receive the email') }},
                        </p>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="resend-button" style="
                                background-color: #3498db;
                                color: white;
                                border: none;
                                padding: 0.8rem 1.5rem;
                                border-radius: 4px;
                                cursor: pointer;
                                font-weight: 500;
                                transition: background-color 0.3s;
                            ">
                                <i class="fas fa-paper-plane"></i>
                                {{ __('click here to request another') }}
                            </button>
                        </form>
                    </div>

                    <div class="verification-help" style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #ecf0f1;">
                        <p style="color: #7f8c8d; font-size: 0.9rem;">
                            <i class="fas fa-info-circle"></i>
                            {{ __('Make sure to check your spam folder if you can\'t find the email.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
    .resend-button:hover {
        background-color: #2980b9;
    }
</style>
@endsection 