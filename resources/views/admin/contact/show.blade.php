@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Message Details</h3>
                    <a href="{{ route('admin.contact.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Messages
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="message-details mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label>From:</label>
                                    <div>{{ $message->name }}</div>
                                    <div class="text-muted">{{ $message->email }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label>Date:</label>
                                    <div>{{ $message->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Subject:</label>
                            <div>{{ $message->subject }}</div>
                        </div>
                        <div class="detail-item">
                            <label>Message:</label>
                            <div class="message-content">{{ $message->message }}</div>
                        </div>
                    </div>

                    @if($message->admin_reply)
                        <div class="admin-reply mb-4">
                            <h4>Your Reply</h4>
                            <div class="reply-content">{{ $message->admin_reply }}</div>
                            <small class="text-muted">Replied on: {{ $message->replied_at->format('M d, Y H:i') }}</small>
                        </div>
                    @else
                        <form action="{{ route('admin.contact.reply', $message) }}" method="POST" class="reply-form">
                            @csrf
                            <div class="form-group">
                                <label for="admin_reply">Your Reply</label>
                                <textarea class="form-control @error('admin_reply') is-invalid @enderror" 
                                          id="admin_reply" name="admin_reply" rows="5" required></textarea>
                                @error('admin_reply')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Send Reply
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #fff;
    border-bottom: 1px solid #eee;
    padding: 20px 30px;
}

.card-title {
    color: #2c3e50;
    font-size: 1.5rem;
    margin: 0;
}

.detail-item {
    margin-bottom: 20px;
}

.detail-item label {
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    margin-bottom: 5px;
    display: block;
}

.message-content {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    white-space: pre-wrap;
}

.admin-reply {
    background-color: #e3f2fd;
    padding: 20px;
    border-radius: 8px;
}

.reply-content {
    margin: 10px 0;
    white-space: pre-wrap;
}

.reply-form {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.form-control {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px;
}

.form-control:focus {
    border-color: #0ea2bd;
    box-shadow: 0 0 0 0.2rem rgba(14, 162, 189, 0.25);
}

.btn {
    padding: 8px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background-color: #0ea2bd;
    border-color: #0ea2bd;
}

.btn-primary:hover {
    background-color: #0d8fa8;
    border-color: #0d8fa8;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .card-header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 10px;
    }
    
    .detail-item {
        margin-bottom: 15px;
    }
    
    .message-content, .admin-reply, .reply-form {
        padding: 15px;
    }
}
</style>
@endsection 