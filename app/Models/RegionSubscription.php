<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegionSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'region',
        'email_notifications',
        'push_notifications',
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getAvailableRegions()
    {
        return [
            'Casablanca-Settat',
            'Rabat-Salé-Kénitra',
            'Marrakech-Safi',
            'Fès-Meknès',
            'Tanger-Tétouan-Al Hoceïma',
            'Souss-Massa',
            'Béni Mellal-Khénifra',
            'Oriental',
            'Drâa-Tafilalet',
            'Laâyoune-Sakia El Hamra',
            'Guelmim-Oued Noun',
            'Dakhla-Oued Ed-Dahab',
        ];
    }
} 