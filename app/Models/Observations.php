<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Observations extends Model
{
    protected $fillable = ['id', 'answer','record_evaluation_id'];

    public function record_evaluation()
    {
        return $this->hasMany('App\Models\RecordEvaluation');
    }


}
