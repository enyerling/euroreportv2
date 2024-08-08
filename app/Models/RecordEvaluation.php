<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RecordEvaluation extends Model
{
    //
    protected $fillable = ['id', 'hotel_id', 'status'];

    public function hotel()
    {
        return $this->belongsTo('App\Model\Hotel');
    }

    public function evaluations()
    {
        return $this->hasMany('App\Models\Evaluation');
    }

    public function images()
    {
        return $this->hasMany('App\EvaluationImage');
    }
}

