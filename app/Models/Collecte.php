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
        'title',
        'description',
        'location',
        'region',
        'starting_date',
        'ending_date',
        'nbrContributors',
        'current_contributors',
        'waste_types',
        'actual_waste_types',
        'actual_volume',
        'completion_date',
        'completion_notes',
        'attendance_data',
        'report_generated',
        'report_path',
        'status',
        'user_id',
        'signal_id'
    ];

    protected $casts = [
        'starting_date' => 'datetime',
        'ending_date' => 'datetime',
        'completion_date' => 'datetime',
        'waste_types' => 'array',
        'actual_waste_types' => 'array',
        'attendance_data' => 'array',
        'report_generated' => 'boolean'
    ];

    // Status constants
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_VALIDATED = 'validated';
    const STATUS_CANCELLED = 'cancelled';

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
            ->withPivot(['status', 'joined_at', 'attended', 'attendance_notes']);
    }

    public function media()
    {
        return $this->hasMany(CollecteMedia::class);
    }

    public function completionMedia()
    {
        return $this->hasMany(CollecteMedia::class)->where('type', 'completion');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('starting_date', '>', Carbon::now())
                    ->where('status', self::STATUS_PLANNED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', [self::STATUS_COMPLETED, self::STATUS_VALIDATED]);
    }

    public function scopeNeedsReport($query)
    {
        return $query->where('status', self::STATUS_COMPLETED)
                    ->where('report_generated', false);
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
                return max(0, $value);
            }
        );
    }

    protected function nbrContributors(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: function ($value) {
                $value = max(0, $value);
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

    public function getCanBeCompletedAttribute()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function getCanBeValidatedAttribute()
    {
        return $this->status === self::STATUS_COMPLETED && 
               $this->actual_waste_types !== null && 
               $this->actual_volume !== null;
    }

    /**
     * Get the attendance percentage for the collection.
     *
     * @return float
     */
    public function getAttendancePercentageAttribute()
    {
        if (!$this->attendance_data || !$this->current_contributors) {
            return 0;
        }

        $attended = collect($this->attendance_data)
            ->filter(fn($record) => $record['attended'])
            ->count();

        return ($attended / $this->current_contributors) * 100;
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

            if ($collecte->current_contributors > $collecte->nbrContributors) {
                $collecte->current_contributors = $collecte->nbrContributors;
            }
        });

        static::saving(function ($collecte) {
            if ($collecte->current_contributors > $collecte->nbrContributors) {
                $collecte->current_contributors = $collecte->nbrContributors;
            }
        });
    }
} 