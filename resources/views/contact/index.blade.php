@extends('layouts.app')

@section('content')
<section class="contact-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-card">
                    <div class="section-title text-center mb-5">
                        <h2>Contact Us</h2>
                        <p>Have questions or need assistance? We're here to help!</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="contact-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Your Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

.contact-card {
    background: #fff;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.section-title h2 {
    color: #2c3e50;
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.section-title p {
    color: #6c757d;
    font-size: 1.1rem;
}

.contact-form .form-group {
    margin-bottom: 25px;
}

.contact-form label {
    color: #2c3e50;
    font-weight: 500;
    margin-bottom: 8px;
}

.contact-form .form-control {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
    height: auto;
    transition: all 0.3s ease;
}

.contact-form .form-control:focus {
    border-color: #0ea2bd;
    box-shadow: 0 0 0 0.2rem rgba(14, 162, 189, 0.25);
}

.contact-form textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.btn-primary {
    background-color: #0ea2bd;
    border-color: #0ea2bd;
    padding: 12px 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #0d8fa8;
    border-color: #0d8fa8;
    transform: translateY(-2px);
}

.alert {
    border-radius: 8px;
    margin-bottom: 25px;
}

@media (max-width: 768px) {
    .contact-section {
        padding: 60px 0;
    }
    
    .contact-card {
        padding: 30px;
    }
    
    .section-title h2 {
        font-size: 2rem;
    }
}
</style>
@endsection 