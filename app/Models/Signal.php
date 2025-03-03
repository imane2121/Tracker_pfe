<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Signal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'volume',
        'location',
        'customType',
        'latitude',
        'longitude',
        'anomalyFlag',
        'signalDate',
        'status',
        'description'
    ];

    protected $casts = [
        'anomalyFlag' => 'boolean',
        'signalDate' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'volume' => 'integer'
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
}
