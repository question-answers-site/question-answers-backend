<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    //
    protected $fillable = [
        'title,description',
    ];
    protected $visible=[
        'title','id','description'
    ];

    public function image(){
        return $this->morphOne('App\Image','imageable');
    }

    public function users(){
        return $this->morphedByMany('App\User','topicable')->withPivot(['rank','answers_count']);
    }

    public function questions(){
        return $this->morphedByMany('App\Question','topicable')->withPivot(['rank','answers_count']);
    }

}
