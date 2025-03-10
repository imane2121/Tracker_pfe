<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\CollecteService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Collecte extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'signal_id',
        'user_id',
        'region',
        'location',
        'image',
        'description',
        'latitude',
        'longitude',
        'nbrContributors',
        'current_contributors',
        'status',
        'starting_date',
        'end_date',
        'waste_types'
    ];

    protected $casts = [
        'starting_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'waste_types' => 'array',
    ];

    // Relationships
    public function signal(): BelongsTo
    {
        return $this->belongsTo(Signal::class)->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contributors()
    {
        return $this->belongsToMany(User::class, 'collecte_contributor')
            ->withPivot('status', 'joined_at');
    }

    public function media()
    {
        return $this->hasMany(CollecteMedia::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('starting_date', '>', Carbon::now())
                    ->where('status', 'planned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'validated']);
    }

    // Mutators & Accessors
    protected function currentContributors(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: function ($value) {
                if ($value > $this->nbrContributors) {
                    $value = $this->nbrContributors;
                }
                return max(0, $value); // Ensure it's never negative
            }
        );
    }

    protected function nbrContributors(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: function ($value) {
                $value = max(0, $value); // Ensure it's never negative
                if ($value < $this->current_contributors) {
                    $this->attributes['current_contributors'] = $value;
                }
                return $value;
            }
        );
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        return $this->nbrContributors > 0 
            ? ($this->current_contributors / $this->nbrContributors) * 100 
            : 0;
    }

    public function getIsFullAttribute()
    {
        return $this->current_contributors >= $this->nbrContributors;
    }

    // Model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collecte) {
            $collecteService = app(CollecteService::class);
            
            if (!$collecteService->canCreateCollecte(
                $collecte->creator, 
                $collecte->signal,
                $collecte->latitude,
                $collecte->longitude
            )) {
                throw new \Exception('Insufficient signals in the area to create a collection.');
            }

            // Ensure current_contributors never exceeds nbrContributors
            if ($collecte->current_contributors > $collecte->nbrContributors) {
                $collecte->current_contributors = $collecte->nbrContributors;
            }
        });

        static::saving(function ($collecte) {
            // Ensure current_contributors never exceeds nbrContributors
            if ($collecte->current_contributors > $collecte->nbrContributors) {
                $collecte->current_contributors = $collecte->nbrContributors;
            }
        });
    }
} 