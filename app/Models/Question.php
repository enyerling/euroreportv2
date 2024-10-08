<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['id', 'name', 'type', 'answer', 'system_id' ,'accessorie_id','expected_value'];
    
    public function accessorie()
    {
        return $this->belongsTo('App\Models\Accessories', 'accessorie_id');
    }

    public function system()
    {
        return $this->belongsTo('App\Models\System');
    }

    public function question_hotel()
    {
        return $this->hasMany('App\Models\QuestionsHotel');
    }
}
