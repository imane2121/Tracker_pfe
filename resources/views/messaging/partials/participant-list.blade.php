<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Participants</h5>
    </div>
    <div class="card-body">
        <div class="list-group list-group-flush">
            @foreach($participants as $participant)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">{{ $participant->name }}</h6>
                        <small class="text-muted">
                            {{ $participant->pivot->role === 'admin' ? 'Administrator' : 'Participant' }}
                        </small>
                    </div>
                    
                    @if(auth()->user()->role === 'admin' || 
                        ($chatRoom->created_by === auth()->id() && $participant->id !== auth()->id()))
                        <form action="{{ route('messaging.participants.remove', [$chatRoom, $participant]) }}" 
                              method="POST" 
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-person-x"></i>
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        </div>

        @if($chatRoom->created_by !== auth()->id() && auth()->user()->role !== 'admin')
            <div class="mt-3">
                <form action="{{ route('messaging.participants.leave', $chatRoom) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                        <i class="bi bi-box-arrow-right"></i> Leave Chat
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
