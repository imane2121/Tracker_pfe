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
        <div class="notification-list">
            @forelse($notifications as $notification)
                <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                    <div class="d-flex align-items-start">
                        <div class="notification-icon">
                            @if($notification->type === 'App\Notifications\NewCollectionInRegion')
                                <i class="bi bi-calendar-event text-primary"></i>
                            @else
                                <i class="bi bi-info-circle text-info"></i>
                            @endif
                        </div>
                        <div class="notification-content">
                            <div class="notification-header">
                                <div class="notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                <div class="notification-actions">
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-danger p-0">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="notification-text">{{ $notification->data['message'] ?? '' }}</div>
                            @if(isset($notification->data['details']))
                                <div class="notification-details">
                                    <div class="detail-item"><i class="bi bi-geo-alt"></i> {{ $notification->data['details']['location'] }}</div>
                                    <div class="detail-item"><i class="bi bi-calendar"></i> {{ $notification->data['details']['date'] }}</div>
                                    <div class="detail-item"><i class="bi bi-people"></i> {{ $notification->data['details']['current_contributors'] }}/{{ $notification->data['details']['contributors_needed'] }} contributors</div>
                                </div>
                            @endif
                            <div class="notification-footer">
                                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}" class="btn btn-sm btn-primary">
                                        {{ $notification->data['action_text'] ?? 'Join Collection' }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
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

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
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
    flex-shrink: 0;
    margin-right: 1rem;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notification-title {
    font-weight: 500;
    color: #212529;
    margin-right: 1rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-details {
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.detail-item i {
    margin-right: 0.5rem;
    width: 16px;
}

.notification-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.notification-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.dropdown-divider {
    margin: 0;
    border-top: 1px solid #dee2e6;
}

/* Mobile Styles */
@media (max-width: 991px) {
    .notification-dropdown {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        z-index: 1050;
    }

    .notification-list {
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .notification-item {
        padding: 0.75rem;
    }

    .notification-icon {
        width: 32px;
        height: 32px;
        margin-right: 0.75rem;
    }

    .notification-content {
        width: 100%;
    }

    .notification-header {
        flex-wrap: wrap;
    }

    .notification-title {
        width: 100%;
        margin-bottom: 0.25rem;
    }

    .notification-actions {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
    }

    .notification-details {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.25rem;
    }

    .detail-item {
        margin-bottom: 0;
    }

    .notification-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .notification-footer .btn {
        width: 100%;
        text-align: center;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .notification-dropdown {
        background-color: #212529;
        border-color: #343a40;
    }

    .notification-dropdown .dropdown-header {
        background-color: #343a40;
        border-color: #495057;
    }

    .notification-item {
        border-color: #343a40;
    }

    .notification-item:hover {
        background-color: #343a40;
    }

    .notification-item.unread {
        background-color: #1a1d20;
    }

    .notification-icon {
        background-color: #343a40;
    }

    .notification-title {
        color: #f8f9fa;
    }

    .notification-text {
        color: #adb5bd;
    }

    .notification-details {
        color: #adb5bd;
    }

    .notification-time {
        color: #adb5bd;
    }

    .dropdown-divider {
        border-color: #343a40;
    }
}
</style> 