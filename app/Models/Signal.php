<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signal extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'volume',
        'wasteTypes',
        'location',
        'customType',
        'latitude',
        'longitude',
        'anomalyFlag',
        'signalDate',
        'status',
        'description',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function wasteTypes()
    {
        return $this->belongsToMany(WasteTypes::class, 'signal_waste_types', 'signal_id', 'waste_type_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
