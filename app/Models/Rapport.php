<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rapport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'collecte_id',
        'supervisor_id',
        'description',
        'volume',
        'waste_types',
        'participants',
        'nbrContributors',
        'location',
        'latitude',
        'longitude',
        'starting_date',
        'end_date'
    ];

    protected $casts = [
        'waste_types' => 'array',
        'participants' => 'array',
        'starting_date' => 'datetime',
        'end_date' => 'datetime',
        'volume' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Get the collecte that owns the rapport.
     */
    public function collecte(): BelongsTo
    {
        return $this->belongsTo(Collecte::class);
    }

    /**
     * Get the supervisor who created the rapport.
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}