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
            <div class="message-text">
                {{ $message->message_content }}
            </div>
            <div class="message-time">
                {{ $message->created_at->format('H:i') }}
            </div>
        </div>
    </div>
</div>
