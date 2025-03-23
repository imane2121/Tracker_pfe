<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ChatParticipant extends Pivot
{
    public $timestamps = false;

    protected $table = 'chat_participants';

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'joined_at',
        'left_at',
        'created_at',
        'updated_at'
    ];

    // Role constants
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PARTICIPANT = 'participant';

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isActive(): bool
    {
        return is_null($this->left_at);
    }
}
