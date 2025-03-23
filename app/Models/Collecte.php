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

    protected $guarded = []; // Allow mass assignment of all fields

    protected $casts = [
        'signal_ids' => 'array',
        'actual_waste_types' => 'array',
        'starting_date' => 'datetime',
        'end_date' => 'datetime'
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

    public function signals()
    {
        return $this->belongsToMany(Signal::class)
            ->whereIn('signals.id', $this->signal_ids ?? []);
    }

    // Add this relationship for chat functionality
    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class);
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
    }

    // Add mutator to ensure waste type IDs are integers
    public function setActualWasteTypesAttribute($value)
    {
        $this->attributes['actual_waste_types'] = json_encode(
            is_array($value) ? array_map('intval', $value) : []
        );
    }
} 