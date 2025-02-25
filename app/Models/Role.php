<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['title']; // Fields that can be mass-assigned

    public function users()
    {
        return $this->belongsToMany(User::class); // Define the many-to-many relationship with users
    }
}