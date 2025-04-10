@extends('layouts.app')
@section('content')
<main class="main">
    <section id="starter-section" class="starter-section">
        <div class="signContainer">
            <h1> Sign In </h1>

            <!-- Display success message -->
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if (session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px;">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <div class="form-group">
                    <input id="email" name="email" type="email" class="input" required autocomplete="email" autofocus placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                

                    <input id="password" name="password" type="password" class="input" required placeholder="Password">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-check">
                    <input class="form-check-input" name="remember" type="checkbox" id="remember">
                    <label class="form-check-label" for="remember">Remember Me</label>
                </div>

                <button type="submit" class="SignInBut">Sign In</button>

                <div class="agreement">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                    <br>
                    <a href="{{ route('register') }}">Don't have an account? Sign Up</a>
                </div>
            </form>
        </div>
    </section>
</main>
@endsection