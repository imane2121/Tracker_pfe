@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chat Rooms</h5>
        </div>
        <div class="card-body p-0">
            @if($chatRooms->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 text-muted">No chat rooms available.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($chatRooms as $chatRoom)
                        <a href="{{ route('messaging.show', $chatRoom) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                            <div>
                                <h6 class="mb-1">Collection: {{ $chatRoom->collecte->location }}</h6>
                                <small class="text-muted">
                                    Created by: {{ $chatRoom->creator->first_name }} {{ $chatRoom->creator->last_name }} | 
                                    Participants: {{ $chatRoom->participants->count() }}
                                </small>
                            </div>
                            <span class="badge bg-primary rounded-pill">
                                {{ $chatRoom->messages->count() }} messages
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $chatRooms->links() }}
    </div>
</div>
@endsection
