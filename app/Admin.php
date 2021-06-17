<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Self_;
use function foo\func;

class Admin
{
    public static $options = [
        ['id'=>1,'title'=>'today','keysCount'=>24,'labels'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24]],
        ['id'=>2,'title'=>'Last Day','keysCount'=>24,'labels'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24]],
        ['id'=>3,'title'=>'Last Month','keysCount'=>31,'labels'=>[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31]],
        ['id'=>4,'title'=>'Last Year','keysCount'=>12,'labels'=> ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December']],
//        ['id'=>5,'title'=>'last week','keysCount'=>7,
//            'labels'=>['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']]
    ];

    public static function getKey($optionId,$date){
        if($optionId == 1 || $optionId == 2){
           return (new Carbon($date))->hour;
        }

        if($optionId == 3){
            return (new Carbon($date))->day;
        }

        if($optionId == 4){
            return  (new Carbon($date))->month;
        }
    }

    public static function getComparisionDate($optionId){

        if($optionId == 1){
            return Carbon::now();
        }

        if($optionId == 2){
            return Carbon::now()->subDay();
        }

        if($optionId == 3){
            return Carbon::now()->subMonth();
        }

        if($optionId == 4){
            return Carbon::now()->subYear();
        }
    }
    public static function getData($model,$optionId){
        $data = $model::whereYear('created_at',self::getComparisionDate($optionId)->year);

        if($optionId <= 3){
            $data = $data->whereMonth('created_at',self::getComparisionDate($optionId)->month);
        }

        if($optionId <= 2){
            $data = $data->whereDay('created_at',self::getComparisionDate($optionId)->day);
        }

        $data = $data->get();

        $res = [];

        for ($i=0;$i<=self::$options[$optionId-1]['keysCount'];$i++){
            $res[$i] = 0;
        }

        foreach ($data as $singleData) {
            $key = self::getKey($optionId,new Carbon($singleData->created_at));
            $res[$key]++;
        }

        return response(array_values($res), 200);
    }

    public function newRegisteredUsers($time)
    {
        $users = User::where('created_at', Carbon::now()->subHours($time->hours))->count();
    }

    public static function topTenActiveUsers()
    {
        $users = User::withCount(['votes', 'questions', 'answers'])->get();
        $sortedUsers = $users->sortByDesc(function ($item, $key) {
            return $item->votes_count + $item->answers_count + $item->questions_count;
        });
        return $sortedUsers->take(10)->values()->all();
    }

    public static function topTenContributors(){
       $topRankUsers =   DB::table('topicables')
            ->select('topicable_id',DB::raw('min(rank) as min_rank'))
            ->where('topicable_type','App\User')
            ->groupBy('topicable_id')
            ->orderBy('min_rank')
            ->get()->take(10);

       $users = [];
       foreach ($topRankUsers as $topRankUser){
          $user = User::findOrFail($topRankUser->topicable_id);
          $user->points = (1/$topRankUser->min_rank)*100;
          $users[] = $user;
       }
       return $users;
    }
}
