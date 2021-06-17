<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    //

    protected $guarded = [];

    public function answer(){
        return $this->belongsTo('App\Answer');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
}
