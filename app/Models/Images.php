<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $fillable = ['record_evaluation_id', 'path'];

    public function recordEvaluation()
    {
        return $this->belongsTo(('App\Models\RecordEvaluation'));
    }
}
