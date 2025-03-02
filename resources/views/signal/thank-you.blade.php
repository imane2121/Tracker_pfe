@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-body text-center">
                    <h1 class="display-4 text-success mb-4">Thank You!</h1>
                    <p class="lead">Your signal has been successfully submitted.</p>
                    <p>Our team will review your submission shortly.</p>
                    <div class="mt-4">
                        <a href="{{ route('signal.create') }}" class="btn btn-primary me-3">Submit Another Signal</a>
                        <a href="{{ route('signal.index') }}" class="btn btn-secondary">View All Signals</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 