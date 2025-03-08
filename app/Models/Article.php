<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'image',
        'thumbnail',
        'author_id',
        'category',
        'metadata',
        'is_featured',
        'view_count',
        'published_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer'
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getReadingTimeAttribute()
    {
        return $this->metadata['reading_time'] ?? '5 minutes';
    }

    public function getRelatedLinksAttribute()
    {
        return $this->metadata['related_links'] ?? [];
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    public function toggleFeatured()
    {
        $this->update(['is_featured' => !$this->is_featured]);
    }
} 