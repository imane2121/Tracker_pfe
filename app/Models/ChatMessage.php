<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'chat_room_id',
        'user_id',
        'message_content',
        'message_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'message_hash'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Message types constants
    public const TYPE_TEXT = 'text';
    public const TYPE_IMAGE = 'image';
    public const TYPE_FILE = 'file';

    // Relationships
    public function chatRoom(): BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isFile(): bool
    {
        return in_array($this->message_type, [self::TYPE_IMAGE, self::TYPE_FILE]);
    }

    public function getFileUrl(): ?string
    {
        if (!$this->isFile()) {
            return null;
        }

        return asset('storage/' . $this->file_path);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            $message->created_at = now();
            $message->message_hash = hash('sha256', $message->message_content . time());
        });
    }
}
