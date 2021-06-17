<?php


namespace App;


use Illuminate\Support\Facades\Log;

class BayesianRating
{

    /**
     * @param $question_id
     * @param $order_by
     * @param $order
     *
     * @return mixed
     */
    private $userTopicRank;

    public function getAnswers($question_id, $order_by = "id", $order = "asc")
    {

        $answers = Question::find($question_id)->answers()
            ->orderBy($order_by, $order)->get();

        return $answers;
    }

    /**
     * Total up votes
     * @param $id integer question_id
     * @return mixed
     */
    public function getUpvotes($id)
    {
        $data = 0;
        $answer = Answer::find($id);
        if ($answer && count($answer->votes)) {
            $data = $answer->votes()->where('value', 1)->count();
        }
        return $data;
    }

    /**
     * Total down votes
     * @param $id integer question_id
     * @return mixed
     */
    public function getDownvotes($id)
    {
        $data = 0;
        $answer = Answer::find($id);
        if ($answer && count($answer->votes)) {
            $data = $answer->votes()->where('value', -1)->count();
        }

        return $data;
    }

    /**
     * Sum of up votes and down votes
     * @param $id
     *
     * @return float|int
     */
    public function getVotes($id)
    {
        $total = $this->getUpvotes($id) + $this->getDownvotes($id);

        return $total;
    }

    /**
     * Average total votes
     * @param $answers object
     *
     * @return float|int
     */
    public function getAverageVotes($answers)
    {
        $average = 0;
        $sum = 0;
        $count = 0;
        foreach ($answers as $answer) {
            $sum += $this->getVotes($answer->id);
            $count++;
        }
        if ($sum > 0) {
            $average = $sum / $count;
        }
        return $average;
    }


    /**
     * Upvotes divided by the sum of upvotes and downvotes
     * @param $id
     *
     * @return float|int
     */
    public function getRating($id)
    {
        $value = 0;
        $total = $this->getVotes($id);
        if ($total > 0) {
            $value = $this->getUpvotes($id) / $total;
        }
        return $value;
    }

    /**
     * Average rating
     * @param $definitions object
     *
     * @return float|int
     */
    public function getAverageRating($definitions)
    {
        $average = 0;
        $sum = 0;
        $count = 0;
        foreach ($definitions as $definition) {
            $sum += $this->getRating($definition->id);
            $count++;
        }
        if ($sum > 0) {
            $average = $sum / $count;
        }

        return $average;
    }

    public function getTopics($answer)
    {
        $question = Question::find($answer->question_id);
        return $question->topics()->get();
    }

    /**
     * Get Bayesian rating
     *
     * @param $answers object
     *
     * @return array
     */
    public function getBayesianRating($answers)
    {
        $insertOrUpdate=[];

        foreach ($answers as $answer) {

            if(is_null($answer->bayesian_rating))
                $insertOrUpdate[$answer->id] = 1;
            else
                $insertOrUpdate[$answer->id] = 0;

            $votes = $this->getVotes($answer->id);
            $average_votes = $this->getAverageVotes($answers);
            $numerator = ($average_votes * $this->getAverageRating($answers)) +
                ($votes * $this->getRating($answer->id));
            $denominator = $average_votes + $votes;
            $bayesian_rating = ($denominator > 0) ? $numerator / $denominator : 0;
            $answer->bayesian_rating = $bayesian_rating;
            $answer->must_update = 0;
            $answer->save();
        }
        if ($answers) {
            $count = 1;
            $answers_1 = $this->getAnswers($answers[0]->question_id, 'bayesian_rating', 'desc');

            foreach ($answers_1 as $answer) {

                $newVal = 0;$oldVal = 0;$insertedCount = 0;
                //insert case

                if($insertOrUpdate[$answer->id] == 1){
                    $insertedCount = 1;
                    $newVal = $count;
                }
                //update case
                else {
                    $oldVal = $answer->rank;
                    $newVal = $count;
                }

                $answer->rank = $count;
                $answer->save();
                $count++;

                $user_id = $answer->user_id;
                $topics = $this->getTopics($answer);
                foreach ($topics as $topic) {
                    if(!isset($this->userTopicRank[$user_id][$topic->id])){
                        $this->userTopicRank[$user_id][$topic->id]['count']=0;
                        $this->userTopicRank[$user_id][$topic->id]['newVal']=0;
                        $this->userTopicRank[$user_id][$topic->id]['oldVal']=0;
                    }

                    $this->userTopicRank[$user_id][$topic->id]['count']+=$insertedCount;
                    $this->userTopicRank[$user_id][$topic->id]['newVal']+=$newVal;
                    $this->userTopicRank[$user_id][$topic->id]['oldVal']+=$oldVal;

                }

            }
        }
    }

    /**
     * @return string
     */
    public function triggerRanking()
    {
        Log::info('ranking begin');

        $question_ids = Answer::select('question_id')->where('must_update', 1)
            ->distinct('question_id')->get();
        foreach ($question_ids as $question_id) {
            $answers = $this->getAnswers($question_id->question_id);
            if ($answers) {
                $this->getBayesianRating($answers);
            }
        }
        if($this->userTopicRank != null)
            (new UserTopicRank())->calculateUserTopicRank($this->userTopicRank);

        Log::info('ranking success');
        return "success";
    }

}
