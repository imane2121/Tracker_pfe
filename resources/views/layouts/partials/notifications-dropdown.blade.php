@php
    $notifications = auth()->user()->notifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications->count();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-bell"></i>
        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $unreadCount }}
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationsDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Notifications</h6>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-primary small p-0">Mark all as read</button>
                </form>
            @endif
        </div>
        <div class="dropdown-divider"></div>
        <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.show', $notification->id) }}" 
                   class="dropdown-item notification-item {{ $notification->read_at ? '' : 'unread' }}">
                    <div class="d-flex align-items-center">
                        <div class="notification-icon me-3">
                            @if($notification->type === 'App\Notifications\NewCollectionInRegion')
                                <i class="bi bi-calendar-event text-primary"></i>
                            @else
                                <i class="bi bi-info-circle text-info"></i>
                            @endif
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                            <div class="notification-text">{{ $notification->data['message'] ?? '' }}</div>
                            @if(isset($notification->data['details']))
                                <div class="notification-details small text-muted">
                                    <div><i class="bi bi-geo-alt"></i> {{ $notification->data['details']['location'] }}</div>
                                    <div><i class="bi bi-calendar"></i> {{ $notification->data['details']['date'] }}</div>
                                    <div><i class="bi bi-people"></i> {{ $notification->data['details']['current_contributors'] }}/{{ $notification->data['details']['contributors_needed'] }} contributors</div>
                                </div>
                            @endif
                            <div class="notification-time text-muted small">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                            @if(isset($notification->data['action_url']))
                                <div class="mt-2">
                                    <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-primary">
                                        {{ $notification->data['action_text'] ?? 'Join Collection' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center text-muted">
                    No notifications
                </div>
            @endforelse
        </div>
        @if($notifications->count() > 0)
            <div class="dropdown-divider"></div>
            <a href="{{ route('notifications.index') }}" class="dropdown-item text-center">
                View all notifications
            </a>
        @endif
    </div>
</li>

<style>
.notification-dropdown {
    width: 350px;
    padding: 0;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.15);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
}

.notification-dropdown .dropdown-header {
    padding: 1rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.notification-dropdown .dropdown-header h6 {
    color: #212529;
    margin: 0;
}

.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.2s;
    color: #212529;
    text-decoration: none;
}

.notification-item:hover {
    background-color: #f8f9fa;
    color: #212529;
}

.notification-item.unread {
    background-color: #f0f7ff;
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: #f8f9fa;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 500;
    margin-bottom: 0.25rem;
    color: #212529;
}

.notification-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.notification-details {
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}

.notification-details i {
    margin-right: 0.25rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.dropdown-divider {
    margin: 0;
    border-top: 1px solid #dee2e6;
}

@media (max-width: 991px) {
    .notification-dropdown {
        width: 100%;
        position: static !important;
        transform: none !important;
        margin-top: 0;
        border: none;
        box-shadow: none;
    }
    
    .notification-item {
        padding: 0.75rem 1rem;
    }
}
</style> 