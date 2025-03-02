<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'type', // 'general' or 'specific'
        'parent_id', // Parent waste type if it's a specific type
        'created_at', 
        'updated_at',
    ];

    // Relationship to its specific waste types (for general waste types)
    public function specificWasteTypes()
    {
        return $this->hasMany(WasteTypes::class, 'parent_id');
    }
    public function signals()
    {
        return $this->belongsToMany(Signal::class, 'signal_waste_types');
    }
    // Relationship to parent waste type (for specific waste types)
    public function parentWasteType()
    {
        return $this->belongsTo(WasteTypes::class, 'parent_id');
    }
}
