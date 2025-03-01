<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'signal_media'; // Correct table name

    protected $fillable = [
        'signal_id',
        'media_type',
        'file_path',
    ];

    public function signal()
    {
        return $this->belongsTo(Signal::class);
    }
}
