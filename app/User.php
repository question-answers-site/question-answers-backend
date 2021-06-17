<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'description',
        'confirmation_code','confirmed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','is_admin','confirmation_code'
    ];

    protected $with = ['image'];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub
        self::addGlobalScope('OnlyConfirmedEmail',function (Builder $builder){
            return $builder->where('confirmed',true);
        });
    }

    public function credentials()
    {
        return $this->hasMany('App\Credential');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    public function image()
    {
        return $this->morphOne('App\Image', 'imageable');
    }

    public function topics()
    {
        return $this->morphToMany('App\Topic', 'topicable');
    }

    public static function admin()
    {
        return self::where('is_admin', 1)->first();
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    public function points()
    {
        $avgRank = $this->topics()->avg('rank');
        if ($avgRank == 0) return 0;
        return round(1 / ($avgRank) * 100);
    }

    public function topicsDetails()
    {
        return DB::table('topicables')
            ->join('topics', 'topicables.topic_id', '=', 'topics.id')
            ->select(['id','title','answers_count','rank'])
            ->where([['topicable_type', 'App\User'], ['topicable_id', $this->id]])
            ->get();
    }

}
