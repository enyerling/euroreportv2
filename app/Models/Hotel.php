<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = ['id', 'name', 'manager', 'img'];

    public function hotel_systems()
    {
        return $this->hasMany('App\Models\HotelSystem');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User');
    }

    public function evaluations()
    {
        return $this->hasMany('App\Models\Evaluation');
    }

    public function questions()
    {
        return $this->belongsToMany('App\Models\Question', 'question_hotel');
    }
}
