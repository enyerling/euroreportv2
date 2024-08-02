<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    //
    protected $fillable = [
        'id', 'record_evaluation_id', 'system_id', 'question_id', 'answer','date', 'correct', 'room'
    ];

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function observation_add()
    {
        return $this->belongsTo('App\Models\ObservationAdds');
    }
}

