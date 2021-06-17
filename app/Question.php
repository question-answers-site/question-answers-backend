<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Question extends Model
{
    //
    protected $fillable = [
        'user_id',
        'body'
    ];

    protected $appends = [
        'my_answer'
    ];

    protected $with = ['topics', 'user'];
    protected $withCount = ['answers'];

    public function getMyAnswerAttribute()
    {
        if (!auth('api')->check()) return null;
        return $this->answers()->where('answers.user_id', auth()->user()->id)->first();
    }


    public function scopeTopicFilter(Builder $builder, $topic_id)
    {
        return $builder->when(!!$topic_id, function ($query) use ($topic_id) {
            return $query->whereHas('topics', function ($query) use ($topic_id) {
                return $query->where('topics.id', $topic_id);
            });
        });
    }

    public static function getRecentlyQuestions($minId, $maxId, $length, $topic_id = null)
    {
        if ($minId == 0 && $maxId == 0) {
            $questions = Question::topicFilter($topic_id)->orderBy('id','desc')
                ->take($length)->get();
        } else {
            $questionsAboveMax = Question::topicFilter($topic_id)
                ->where('id', '>', $maxId)
                ->orderBy('id')->take($length)->get();



            $questionsAboveMaxSize = $questionsAboveMax->count();
            if ($questionsAboveMaxSize < $length) {
                $questionsUnderMin = Question::topicFilter($topic_id)
                    ->where('id', '<', $minId)
                    ->orderBy('id', 'desc')
                    ->take($length - $questionsAboveMaxSize)->get();
                $questions = $questionsAboveMax->merge($questionsUnderMin);
            }
        }

        $minId = ($questions->count() !== 0) ? $questions[0]->id : $minId;
        foreach ($questions as $question) {
            $minId = min($minId, $question->id);
            $maxId = max($maxId, $question->id);
        }
        return
            [
                'minId' => $minId,
                'maxId' => $maxId,
                'questions' => $questions
            ];

    }

    /**
     * @param Question $questions
     */
    public static function questionAndBestAnswer($questions)
    {
        foreach ($questions as $question) {
            $question->answers = $question->getBestRankAnswer();
        }
    }

    public function votes()
    {
        return $this->hasManyThrough('App\Vote', 'App\Answer');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * return relation
     */
    public function orderedAnswers()
    {
        return $this->answers()->orderBy('rank');
    }

    /**
     * @return mixed
     * return collection
     */
    public function getAnswers()
    {
        return $this->orderedAnswers()->excludeMyAnswer()->upDownVotesCount()->get();
    }

    /**
     * @return mixed
     * return collection
     */
    public function getBestRankAnswer()
    {
        return $this->orderedAnswers()->excludeMyAnswer()->upDownVotesCount()
            ->get()->take(1);
    }

    public function topics()
    {
        return $this->morphToMany('App\Topic', 'topicable')
            ->withPivot(['rank', 'answers_count']);
    }

    public function reports()
    {
        return $this->morphMany('App\Report', 'reportable');
    }

    public static function orderedReports()
    {
        $questionsReports = self::wherehas('reports', function ($q) {
            $q->where('is_read', 0);
        })->withCount('reports')->get();

        $questionsReportss = $questionsReports->sortByDesc(function ($item, $key) {
            return $item->reports_count;
        })->values();;

        return $questionsReportss;
    }
}
