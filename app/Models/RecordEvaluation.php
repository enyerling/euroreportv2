<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RecordEvaluation extends Model
{
    //
    protected $fillable = ['id', 'hotel_id', 'status'];

    public function hotel()
    {
        return $this->belongsTo('App\Models\Hotel');
    }

    public function evaluations()
    {
        return $this->hasMany('App\Models\Evaluation');
    }

    public function images()
    {
        return $this->hasMany('App\Models\Images', 'record_evaluation_id'); // Especifica la clave foránea
    }
}

