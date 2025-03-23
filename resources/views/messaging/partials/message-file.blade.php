<div class="d-flex {{ $message->user_id === auth()->id() ? 'justify-content-end' : 'justify-content-start' }} mb-3">
    @if($message->user_id !== auth()->id())
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

    <div class="message-wrapper {{ $message->user_id === auth()->id() ? 'sent' : 'received' }}">
        @if($message->user_id !== auth()->id())
            <div class="message-sender-name">
                {{ $message->user->first_name }} {{ $message->user->last_name }}
            </div>
        @endif

        <div class="message-bubble">
            <a href="{{ Storage::url($message->file_path) }}" 
               class="file-attachment" 
               target="_blank"
               download>
                <i class="bi bi-file-earmark"></i>
                <span class="file-name">{{ $message->file_name }}</span>
                <span class="file-size">({{ number_format($message->file_size / 1024, 1) }} KB)</span>
            </a>
            <div class="message-time">
                {{ $message->created_at->format('H:i') }}
            </div>
        </div>
    </div>
</div>

<style>
.file-attachment {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    gap: 8px;
}

.file-attachment i {
    font-size: 1.5rem;
}

.file-name {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-size {
    font-size: 0.8rem;
    opacity: 0.7;
}
</style>

<div class="message {{ $message->user_id === auth()->id() ? 'own-message' : 'other-message' }}">
    @unless($message->user_id === auth()->id())
        <small class="text-muted">{{ $message->user->name }}</small>
    @endunless
    <div class="message-content">
        <a href="{{ route('messaging.attachments.download', $message) }}" 
           class="d-flex align-items-center text-decoration-none">
            <i class="bi bi-file-earmark-text me-2"></i>
            {{ $message->file_name }}
        </a>
        @if($message->message_content)
            <p class="mt-2 mb-0">{{ $message->message_content }}</p>
        @endif
    </div>
    <small class="message-time">
        {{ $message->created_at->format('H:i') }}
        @if($message->user_id === auth()->id() || auth()->user()->role === 'admin')
            <button class="btn btn-link btn-sm text-danger p-0 ms-2" 
                    onclick="event.preventDefault(); document.getElementById('delete-message-{{ $message->id }}').submit();">
                <i class="bi bi-trash"></i>
            </button>
            <form id="delete-message-{{ $message->id }}" 
                  action="{{ route('messaging.messages.destroy', $message) }}" 
                  method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </small>
</div>
