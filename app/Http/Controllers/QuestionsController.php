<?php

namespace App\Http\Controllers;


use App\Question;
use App\Topic;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unAnsweredQuestions()
    {
        $UAQ = cache()->remember('unAnsweredQuestions', now()->addDay(), function () {
            return Question::doesntHave('answers')->take(10)->get();
        });
        return response($UAQ, 200);
    }

    public function index(Request $request)
    {
        $minId = $request->minId;
        $maxId = $request->maxId;

        $data = Question::getRecentlyQuestions($minId, $maxId, 5);
        $questions = $data['questions'];
        $newMinId = $data['minId'];
        $newMaxId = $data['maxId'];
        Question::questionAndBestAnswer($questions);

        return response([
            'questions' => $questions,
            'minId' => $newMinId,
            'maxId' => $newMaxId,
        ], 200);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (auth('api')->guest())
            return response('you must log in first', 401);

        $request->validate([
            'body'=>'required|min:5'
        ]);

        $userId = auth('api')->user()->id;
        $body = $request->body;
        $topics = $request->topics;

        $question = Question::create([
            'body' => $body,
            'user_id' => $userId
        ]);
        $topicsId = [];
        foreach ($topics as $topic) {
            $topicsId[] = $topic['id'];
        }
        $question->topics()->attach($topicsId);
        Cache::forget('unAnsweredQuestions');
        return response($question, 200);
    }

    /**
     * Display the specified resource.
     * show the answers of question with id=$id
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $question = Question::findOrFail($id);

        $answers = $question->getAnswers();

        return response([
            'question' => $question,
            'answers' => $answers
        ], 200);
    }

    /**
     * Update the question in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'body'=>'required|min:5'
        ]);
        $question = Question::findOrFail($id);
        $this->authorize('update', $question);

        $body = $request->body;
        Question::whereId($id)->update(['body' => $body]);
        return response(Question::findOrFail($id), 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $this->authorize('delete', $question);
        Cache::forget('unAnsweredQuestions');
//        $question->reports()->detach();
        $question = Question::destroy($id);
        return response("Successfully Deleted", 200);
    }

    public function getQuestionsByTopic(Request $request)
    {
        $topicId = $request->topicId;

        $minId = $request->minId;
        $maxId = $request->maxId;

        $data = Question::getRecentlyQuestions($minId, $maxId, 5, $topicId);
        $questions = $data['questions'];
        $newMinId = $data['minId'];
        $newMaxId = $data['maxId'];
        Question::questionAndBestAnswer($questions);

        return response([
            'questions' => $questions,
            'minId' => $newMinId,
            'maxId' => $newMaxId,
        ], 200);

    }

    public function predictTopics(Request $request)
    {
        $toSend = [];
        $text = $request->text;

        $naivebayesClient = new Client(['base_uri' => 'http://127.0.0.1:8005/']);
        $svmClient = new Client(['base_uri' => 'http://127.0.0.1:5555/']);
        // Initiate each request but do not block
        $promises = [
            'naivebayes' => $naivebayesClient->requestAsync('POST', 'api/classify', [
                'form_params' => [
                    'text' => $text,
                ]
            ]),
            'svm' => $svmClient->requestAsync('POST', 'api/predict',
                $options = [
                    'json' => ['text' => json_encode($text)],
                    'headers' => ['Content-Type' => 'application/json'],
                ])
        ];
        // Wait for the requests to complete, even if some of them fail
        $responses = Promise\settle($promises)->wait();
        if ($responses['naivebayes']['state'] == 'rejected' && $responses['svm']['state'] == 'rejected') {
            return response('failed request dani try another one', 500);
        }
        if ($responses['naivebayes']['state'] != 'rejected') {
            $predictedCategories = (string)$responses['naivebayes']['value']->getBody();
            $toSend = [];
            if ($predictedCategories) $toSend[] = $predictedCategories;
        }
        if ($responses['svm']['state'] != 'rejected') {
            $predictedCategories = (string)$responses['svm']['value']->getBody();
            $predictedCategories = json_decode($predictedCategories, true);
            $temp = (string)$predictedCategories;
            $temp = substr($temp, 2, strlen($temp) - 4);
            if ($toSend != []) {
                if ($temp != $toSend[0]) {
                    $toSend[] = $temp;
                }
            } else {
                $toSend[] = $temp;
            }


        }
//        return $toSend;
        $res = [];
        foreach ($toSend as $category) {
            $tmp = Topic::where('title', $category)->first();
            $res[] = $tmp;
        }
        return response($res, 200);
    }
}

