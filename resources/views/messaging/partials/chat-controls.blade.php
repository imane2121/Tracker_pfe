<div class="chat-controls bg-light border-top p-2">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="text-muted me-3">
                <i class="bi bi-people"></i> 
                {{ $participants->count() }} participants
            </span>
            <span class="text-muted">
                <i class="bi bi-chat-dots"></i> 
                {{ $messages->total() }} messages
            </span>
        </div>
        
        <div class="btn-group">
            @if(auth()->user()->role === 'admin' || $chatRoom->created_by === auth()->id())
                <button type="button" 
                        class="btn btn-outline-primary btn-sm" 
                        data-bs-toggle="modal" 
                        data-bs-target="#inviteParticipantsModal">
                    <i class="bi bi-person-plus"></i> Invite
                </button>
            @endif
            
            <a href="{{ route('collecte.show', $chatRoom->collecte) }}" 
               class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right"></i> View Collection
            </a>
        </div>
    </div>
</div>
