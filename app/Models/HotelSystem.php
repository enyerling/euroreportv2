<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelSystem extends Model
{
    //
    protected $fillable = ['hotel_id', 'system_id','cant'];

    public function hotel()
    {
        return $this->belongsTo('App\Models\Hotel', 'hotel_id');
    }

    public function system()
    {
        return $this->belongsTo('App\Models\System', 'system_id');
    }
   
}
