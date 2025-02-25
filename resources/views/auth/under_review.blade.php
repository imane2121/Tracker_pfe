@extends('layouts.app')

@section('content')
<main class="main">
    <section id="starter-section" class="starter-section section">
        <div class="signContainer">
            <div class="heading">Account Under Review</div>
            <p>Your account is currently under review. An admin will verify your details and activate your account soon.</p>
            <p>Thank you for your patience!</p>
            <a href="{{ route('login') }}" class="login-button">Back to Login</a>
        </div>
    </section>
</main>
@endsection