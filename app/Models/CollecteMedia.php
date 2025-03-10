<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollecteMedia extends Model
{
    protected $table = 'collecte_media';

    protected $fillable = [
        'collecte_id',
        'file_path',
        'media_type'
    ];

    public function collecte()
    {
        return $this->belongsTo(Collecte::class);
    }
} 