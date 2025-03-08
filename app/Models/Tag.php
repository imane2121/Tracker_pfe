<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug'
    ];

    // Relationships
    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    // Scopes
    public function scopePopular($query, $limit = 10)
    {
        return $query->withCount('articles')
                    ->orderBy('articles_count', 'desc')
                    ->limit($limit);
    }

    // Methods
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }
} 