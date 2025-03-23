@php
    // Only process if this is the first message in the group
    $imageGroup = $chatRoom->messages()
        ->where('message_type', 'image')
        ->where('created_at', '>=', $message->created_at->subSecond())
        ->where('created_at', '<=', $message->created_at->addSecond())
        ->orderBy('id', 'asc')
        ->get();
    
    $isFirstInGroup = $imageGroup->first()->id === $message->id;
    $totalImages = $imageGroup->count();
    $isSender = $message->user_id === auth()->id();
@endphp

{{-- Only show the image grid for the first message in the group --}}
@if($isFirstInGroup)
<div class="d-flex {{ $isSender ? 'justify-content-end' : 'justify-content-start' }} mb-3">
    @if(!$isSender)
        <div class="message-user-avatar me-2">
            @if($message->user->profile_picture)
                <img src="{{ Storage::url($message->user->profile_picture) }}" 
                     alt="{{ $message->user->first_name }}" 
                     class="rounded-circle">
            @else
                <div class="default-avatar">
                    {{ strtoupper(substr($message->user->first_name, 0, 1)) }}
                </div>
            @endif
        </div>
    @endif

    <div class="message-wrapper {{ $isSender ? 'sent' : 'received' }}">
        @if(!$isSender)
            <div class="message-sender-name">
                {{ $message->user->first_name }} {{ $message->user->last_name }}
            </div>
        @endif

        <div class="message-bubble">
            @if($message->message_content)
                <div class="message-text mb-2">
                    {{ $message->message_content }}
                </div>
            @endif

            <div class="image-grid image-grid-{{ min($totalImages, 4) }}">
                @foreach($imageGroup->take(4) as $index => $imageMessage)
                    <div class="grid-item {{ $totalImages > 4 && $index === 3 ? 'has-more' : '' }}">
                        <img src="{{ Storage::url($imageMessage->file_path) }}" 
                             alt="Image" 
                             class="img-fluid rounded" 
                             onclick="openImageViewer({{ json_encode($imageGroup->pluck('file_path')->map(function($path) { return Storage::url($path); })->toArray()) }}, {{ $index }})">
                        @if($totalImages > 4 && $index === 3)
                            <div class="more-overlay rounded" 
                                 onclick="openImageViewer({{ json_encode($imageGroup->pluck('file_path')->map(function($path) { return Storage::url($path); })->toArray()) }}, {{ $index }})">
                                <span>+{{ $totalImages - 4 }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="message-time">
                {{ $message->created_at->format('H:i') }}
            </div>
        </div>
    </div>
</div>
@endif

<style>
.message-container {
    width: 100%;
    display: flex;
    margin-bottom: 1rem;
}

.message-container-sent {
    justify-content: flex-end;
}

.message-container-received {
    justify-content: flex-start;
}

.message-wrapper {
    max-width: 60%;
}

.message-wrapper.sent {
    align-items: flex-end;
}

.message-wrapper.received {
    align-items: flex-start;
}

.message-user-avatar {
    width: 35px;
    height: 35px;
    flex-shrink: 0;
}

.message-user-avatar img {
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

.message-sender-name {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 2px;
    padding-left: 12px;
}

.message-bubble {
    padding: 8px 12px;
    border-radius: 12px;
    position: relative;
}

.sent .message-bubble {
    background-color: #0d6efd;
    color: white;
}

.received .message-bubble {
    background-color: #f8f9fa;
    color: #212529;
}

.message-time {
    font-size: 0.75rem;
    margin-top: 2px;
    text-align: right;
}

.sent .message-time {
    color: rgba(255, 255, 255, 0.7);
}

.received .message-time {
    color: #6c757d;
}

.image-grid {
    display: grid;
    gap: 2px;
    max-width: 300px;
    border-radius: 8px;
    overflow: hidden;
}

.image-grid-1 {
    grid-template-columns: 1fr;
}

.image-grid-2 {
    grid-template-columns: 1fr 1fr;
}

.image-grid-3 {
    grid-template-columns: 1fr 1fr;
}

.image-grid-3 .grid-item:first-child {
    grid-column: 1 / -1;
}

.image-grid-4 {
    grid-template-columns: 1fr 1fr;
}

.grid-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
}

.grid-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.2s;
}

.grid-item img:hover {
    transform: scale(1.05);
}

.grid-item.has-more {
    position: relative;
}

.more-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

.more-overlay:hover {
    background: rgba(0, 0, 0, 0.6);
}

.message-meta {
    text-align: right;
    margin-top: 4px;
    font-size: 0.75rem;
}

/* Image viewer modal styles */
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
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

#viewerImage {
    max-width: 90%;
    max-height: 90vh;
    object-fit: contain;
}

/* Fixed navigation buttons */
.nav-btn {
    position: fixed !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    background: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
    border: none !important;
    width: 50px !important;
    height: 50px !important;
    border-radius: 50% !important;
    font-size: 24px !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    z-index: 1060 !important;
    transition: none !important;
    outline: none !important;
    padding: 0 !important;
    margin: 0 !important;
    user-select: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
}

.prev-btn {
    left: 20px !important;
}

.next-btn {
    right: 20px !important;
}

.close-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1060;
}

.image-counter {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    background: rgba(0, 0, 0, 0.5);
    padding: 5px 15px;
    border-radius: 15px;
    z-index: 1060;
}
</style>

{{-- Update the image viewer HTML structure --}}
<div id="imageViewer" class="image-viewer">
    <div class="viewer-content">
        <img id="viewerImage" src="" alt="Full size image">
        <button type="button" class="nav-btn prev-btn" onclick="navigateImages(-1)" style="pointer-events: auto;">❮</button>
        <button type="button" class="nav-btn next-btn" onclick="navigateImages(1)" style="pointer-events: auto;">❯</button>
        <button type="button" class="close-btn" onclick="closeImageViewer()">×</button>
        <div class="image-counter">
            <span id="currentImageIndex">1</span>/<span id="totalImages">1</span>
        </div>
    </div>
</div>

<script>
let currentGroupImages = [];
let currentImageIndex = 0;

function openImageViewer(images, startIndex = 0) {
    currentGroupImages = images;
    currentImageIndex = startIndex;

    const viewer = document.getElementById('imageViewer');
    const viewerImage = document.getElementById('viewerImage');
    
    updateImageViewer();
    
    viewer.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function updateImageViewer() {
    const viewerImage = document.getElementById('viewerImage');
    const currentIndexEl = document.getElementById('currentImageIndex');
    const totalImagesEl = document.getElementById('totalImages');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    // Update image
    viewerImage.src = currentGroupImages[currentImageIndex];
    
    // Update counter
    currentIndexEl.textContent = currentImageIndex + 1;
    totalImagesEl.textContent = currentGroupImages.length;

    // Show/hide navigation buttons
    prevBtn.style.display = currentImageIndex > 0 ? 'flex' : 'none';
    nextBtn.style.display = currentImageIndex < currentGroupImages.length - 1 ? 'flex' : 'none';
}

function navigateImages(direction) {
    const newIndex = currentImageIndex + direction;
    if (newIndex >= 0 && newIndex < currentGroupImages.length) {
        currentImageIndex = newIndex;
        updateImageViewer();
    }
}

function closeImageViewer() {
    const viewer = document.getElementById('imageViewer');
    viewer.style.display = 'none';
    document.body.style.overflow = '';
}

// Close viewer with escape key and navigate with arrow keys
document.addEventListener('keydown', function(e) {
    if (document.getElementById('imageViewer').style.display === 'block') {
        if (e.key === 'Escape') {
            closeImageViewer();
        } else if (e.key === 'ArrowLeft') {
            navigateImages(-1);
        } else if (e.key === 'ArrowRight') {
            navigateImages(1);
        }
    }
});

// Prevent click on image viewer from closing it when clicking the image
document.getElementById('viewerImage').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Close viewer when clicking outside the image
document.getElementById('imageViewer').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageViewer();
    }
});

// Update the event handlers
document.addEventListener('DOMContentLoaded', function() {
    const navButtons = document.querySelectorAll('.nav-btn');
    navButtons.forEach(btn => {
        btn.addEventListener('mouseover', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        btn.addEventListener('mouseout', function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });
});
</script>
