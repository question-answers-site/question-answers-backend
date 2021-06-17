<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'user_id',
        'question_id',
        'body'
    ];

    protected $with= ['user'];

    protected $appends= [
        'is_user_reacted'
    ];

    /**
     * @param Builder $builder
     * local scope that add upVotesCount and DownVotesCount To Each Answer
     */
    public function scopeUpDownVotesCount(Builder $builder){
        $builder->withCount([
                'votes as upVotesCount' => function (Builder $query) {
                    $query->where('value',1);
                },
                'votes as downVotesCount' => function (Builder $query) {
                        $query->where('value', -1);
                }
            ]
        );
    }

    public function scopeExcludeMyAnswer(Builder $builder){
        $builder->when(auth('api')->check(), function ($query) {
            return $query->where('user_id', '!=', auth('api')->user()->id);
        });
    }

    /**
     * @return int
     * check if authenticated user react with this answer
     */
    public function getIsUserReactedAttribute()
    {
        if (!auth('api')->check()) {
            return 0;
        } else {
            $user_id = auth('api')->user()->id;
            $reactionValue = Vote::where('user_id', $user_id)
                ->where('answer_id', $this->id)->first();
            if ($reactionValue) {
                return $reactionValue->value;
            } else {
                return 0;
            }

        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * return user that has this answer
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * return question that this answer belongs to
     */
    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     * return images that belongs to this answer
     */
    public function images()
    {
        return $this->morphMany('App\Image', 'imageable');
    }

    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    public function reports()
    {
        return $this->morphMany('App\Report', 'reportable');
    }

    public static function orderedReports()
    {
        $answersReports = self::whereHas('reports', function ($q) {
            $q->where('is_read', 0);
        })->withCount('reports')->get();

        $answersReportss = $answersReports->sortByDesc(function ($item, $key) {
            $item->question;
            return $item->reports_count;
        })->values();

        return $answersReportss;
    }

}
