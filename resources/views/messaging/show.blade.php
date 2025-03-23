@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Chat Messages Section -->
        <div class="col-md-9">
            <div class="card shadow-sm" style="height: calc(100vh - 150px);">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Collection: {{ $chatRoom->collecte->location }}
                    </h5>
                    <a href="{{ route('messaging.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Chats
                    </a>
                </div>
                
                <!-- Messages Container -->
                <div class="card-body p-0 d-flex flex-column">
                    @include('messaging.partials.chat-controls')
                    <div class="messages-container flex-grow-1 overflow-auto p-3" id="messagesContainer">
                        @foreach($messages->reverse() as $message)
                            @include('messaging.partials.message-' . $message->message_type, ['message' => $message])
                        @endforeach
                    </div>

                    <!-- Message Input Form -->
                    @include('messaging.components.message-input', ['chatRoom' => $chatRoom])
                </div>
            </div>
        </div>

        <!-- Participants Sidebar -->
        <div class="col-md-3">
            <div class="chat-container">
                <div class="chat-sidebar">
                    <div class="participants-header">
                        <h5 class="mb-3">Participants</h5>
                    </div>
                    <div class="participants-list">
                        @foreach($chatRoom->participants as $participant)
                            @include('messaging.components.participant-card', ['participant' => $participant])
                        @endforeach
                    </div>
                </div>

                <div class="chat-main">
                    {{-- Rest of your chat content --}}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Updated Image Viewer -->
<div id="imageViewer" class="image-viewer">
    <div class="viewer-content">
        <img id="viewerImage" src="" alt="Full size image">
        <button class="nav-btn prev-btn" onclick="navigateImages(-1)">❮</button>
        <button class="nav-btn next-btn" onclick="navigateImages(1)">❯</button>
        <button class="close-btn" onclick="closeImageViewer()">×</button>
        <div class="image-counter">
            <span id="currentImageIndex">1</span>/<span id="totalImages">1</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .messages-container {
        height: calc(100vh - 280px);
    }

    .message {
        max-width: 80%;
        margin-bottom: 1rem;
    }

    .message.own-message {
        margin-left: auto;
    }

    .message-content {
        border-radius: 15px;
        padding: 0.75rem 1rem;
    }

    .own-message .message-content {
        background-color: #007bff;
        color: white;
    }

    .other-message .message-content {
        background-color: #f8f9fa;
    }

    .message-time {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .file-preview {
        max-width: 200px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }

    /* Chat Layout */
    .chat-container {
        display: flex;
        height: calc(100vh - 60px); /* Adjust based on your navbar height */
        background: #fff;
    }

    .chat-sidebar {
        width: 280px;
        border-right: 1px solid #e5e5e5;
        padding: 20px;
        background: #f8f9fa;
        overflow-y: auto;
    }

    .chat-main {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    /* Participants Styling */
    .participant-avatar {
        width: 40px;
        height: 40px;
    }

    .participant-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .default-avatar {
        width: 100%;
        height: 100%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border-radius: 50%;
    }

    .participant-name {
        font-weight: 500;
        line-height: 1.2;
    }

    /* Image Viewer Styling */
    .image-viewer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1050;
    }

    .viewer-content {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    #viewerImage {
        max-width: 90%;
        max-height: 90vh;
        object-fit: contain;
    }

    .viewer-controls {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }

    .nav-btn, .close-btn {
        pointer-events: auto;
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.3s;
        position: absolute;
    }

    .nav-btn:hover, .close-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .prev-btn {
        left: 20px;
    }

    .next-btn {
        right: 20px;
    }

    .close-btn {
        top: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
    }

    .image-counter {
        position: absolute;
        bottom: 20px;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 5px 15px;
        border-radius: 15px;
        pointer-events: auto;
    }

    .participants-header {
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 15px;
    }

    .participants-list {
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // File upload preview
    const fileInput = document.getElementById('file');
    const previewContainer = document.getElementById('previewContainer');

    fileInput?.addEventListener('change', function() {
        previewContainer.innerHTML = '';
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'img-thumbnail mt-2';
                img.style.maxHeight = '100px';
                img.src = URL.createObjectURL(file);
                previewContainer.appendChild(img);
            } else {
                const fileInfo = document.createElement('div');
                fileInfo.className = 'alert alert-info mt-2';
                fileInfo.textContent = `Selected file: ${file.name}`;
                previewContainer.appendChild(fileInfo);
            }
        }
    });
});
</script>
@endpush
