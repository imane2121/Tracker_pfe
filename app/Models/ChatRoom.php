<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ChatRoom extends Model
{
    protected $fillable = [
        'collecte_id',
        'created_by'
    ];

    // Relationships
    public function collecte(): BelongsTo
    {
        return $this->belongsTo(Collecte::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants')
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }

    // Helper methods
    public function addParticipant(User $user, string $role = 'participant'): void
    {
        $this->participants()->attach($user->id, [
            'role' => $role,
            'joined_at' => now()
        ]);
    }

    public function removeParticipant(User $user): void
    {
        $this->participants()->detach($user->id);
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }
}
