<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $fillable=['url'];

    public function imageable(){
        return $this->morphTo();
    }

    public function getUrlAttribute($value){
        return url($value);
    }

}
