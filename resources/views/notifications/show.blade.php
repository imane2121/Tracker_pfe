@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notification Details</h5>
                    <div>
                        <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this notification?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="notification-details">
                        <div class="d-flex align-items-center mb-3">
                            <div class="notification-icon me-3">
                                @if($notification->type === 'App\Notifications\NewCollectionInRegion')
                                    <i class="bi bi-calendar-event text-primary fa-2x"></i>
                                @else
                                    <i class="bi bi-info-circle text-info fa-2x"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                                <small class="text-muted">Received {{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="notification-content">
                            <p class="mb-3">{{ $notification->data['message'] ?? '' }}</p>
                            
                            @if(isset($notification->data['action_url']))
                                <a href="{{ $notification->data['action_url'] }}" class="btn btn-primary">
                                    {{ $notification->data['action_text'] ?? 'View Details' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #f8f9fa;
}

.notification-content {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
}
</style>
@endsection 