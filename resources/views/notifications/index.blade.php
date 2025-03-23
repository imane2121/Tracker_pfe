@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    @if($notifications->count() > 0)
                        <a href="{{ route('notifications.markAllAsRead') }}" class="btn btn-sm btn-primary">
                            Mark all as read
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <a href="{{ route('notifications.show', $notification->id) }}" 
                                   class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'active' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ $notification->data['message'] ?? '' }}</p>
                                </a>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <p>No notifications found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-group-item {
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item.active {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.list-group-item.active h6,
.list-group-item.active small {
    color: #212529;
}
</style>
@endsection 