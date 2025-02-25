<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',       // City name
        'region',     // Region name (optional)
    ];

    /**
     * Get the users associated with the city.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}