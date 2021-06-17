<?php


namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserTopicRank
{

    public function calculateUserTopicRank($usersTopicRank)
    {

//        echo $usersTopicRank;

        foreach ($usersTopicRank as $user_id => $topics) {
            $user = User::find($user_id);
            foreach ($topics as $topic_id => $values) {

                $userTopic = $user->topics()->where('topic_id', $topic_id)->first();

                if ($userTopic == null) {
                    $rank = $values['newVal'];
                    $answer_count = $values['count'];
                    $rank /= $answer_count;
                    $user->topics()->attach($topic_id, ['rank' => $rank, 'answers_count' => $answer_count]);
                } else {
                    $userTopicDetails=DB::table('topicables')->select(['rank','answers_count'])
                        ->where([['topicable_id',$user_id],['topicable_type','App\User'],['topic_id',$topic_id]])
                        ->first();

                    $rank = $userTopicDetails->rank;
                    $answer_count = $userTopicDetails->answers_count;
                    $rank = ($rank * $answer_count) + $values['newVal'] - $values['oldVal'];
                    $answer_count += $values['count'];
                    $rank /= $answer_count;
                    $user->topics()->updateExistingPivot($topic_id, ['rank' => $rank, 'answers_count' => $answer_count]);
                }

            }
        }

    }


}
