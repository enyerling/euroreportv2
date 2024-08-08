<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionsHotel extends Model
{
    protected $fillable = ['id', 'hotel_id', 'question_id','cantidad'];

    public function hotel()
    {
        return $this->hasMany('App\Models\Hotel');
    }

    public function question()
{
    return $this->belongsTo('App\Models\Question', 'question_id');
}
    
}

