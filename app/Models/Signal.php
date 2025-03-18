<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\SignalService;
use App\Models\WasteTypes;

class Signal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'volume',
        'waste_types',
        'location',
        'custom_type',
        'latitude',
        'longitude',
        'anomaly_flag',
        'signal_date',
        'status',
        'description',
        'viewed'
    ];

    protected $casts = [
        'anomaly_flag' => 'boolean',
        'signal_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'volume' => 'integer',
        'waste_types' => 'array',
        'viewed' => 'boolean'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function wasteTypes()
    {
        return $this->belongsToMany(WasteTypes::class, 'signal_waste_types', 'signal_id', 'waste_type_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function collectes()
    {
        // Don't use belongsToMany since we're using a JSON column
        return $this->hasMany(Collecte::class, 'signal_ids')
            ->where(function ($query) {
                $query->whereRaw('JSON_CONTAINS(signal_ids, ?)', [$this->id]);
            });
    }

    public function waste_types()
    {
        return $this->belongsToMany(WasteTypes::class, 'signal_waste_types', 'signal_id', 'waste_type_id');
    }

    /**
     * Calculate distance between two points in kilometers using Haversine formula
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    public function getWasteTypeNames()
    {
        return $this->waste_types->pluck('name')->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($signal) {
            $signalService = app(SignalService::class);
            $signal->status = $signalService->determineSignalStatus($signal, $signal->creator);
        });

        static::saved(function ($signal) {
            if ($signal->isDirty('anomaly_flag') && $signal->anomaly_flag) {
                $signalService = app(SignalService::class);
                $signalService->handleAnomalyDetection($signal);
            }
        });
    }
}
