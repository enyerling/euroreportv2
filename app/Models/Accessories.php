<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Accessories extends Model
{
    //
    protected $fillable = ['id', 'name'];

    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'accessorie_id');
    }
}
