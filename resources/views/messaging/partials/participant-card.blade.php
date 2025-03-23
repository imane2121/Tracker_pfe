@props(['participant'])

<div class="participant-item d-flex align-items-center mb-2">
    <div class="participant-avatar me-2">
        @if($participant->profile_picture)
            <img src="{{ Storage::url($participant->profile_picture) }}" 
                 alt="{{ $participant->first_name }}" 
                 class="rounded-circle">
        @else
            <div class="default-avatar">
                {{ strtoupper(substr($participant->first_name, 0, 1)) }}
            </div>
        @endif
    </div>
    <div class="participant-info">
        <div class="participant-name">
            {{ $participant->first_name }} {{ $participant->last_name }}
        </div>
        <small class="text-muted text-capitalize">
            {{ $participant->pivot->role }}
        </small>
    </div>
</div>

<style>
.participant-avatar {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
}

.participant-avatar img {
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
    font-size: 1.2rem;
}

.participant-name {
    font-weight: 500;
    line-height: 1.2;
}

.participant-info {
    overflow: hidden;
}
</style>
