<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    //
    protected $fillable = [
      'user_id',
      'body',
      'type'
    ];

    public function getBodyAttribute(){
        return unserialize($this->attributes['body']);
    }

    public function setBodyAttribute($value){
        $this->attributes['body'] = serialize($value);
    }

    public function user(){
        return $this->belongsTo('App\User');
    }
}
