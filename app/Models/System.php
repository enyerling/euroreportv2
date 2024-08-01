<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $fillable = ['name', 'score'];

    public function hotel_systems()
    {
        return $this->hasMany(HotelSystem::class);
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }
    
}