<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Topic;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->only('update','getAuthUser');
    }

    public function getAuthUser(){
            $user = auth('api')->user();
            return response($user,200);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getUserByTopic(Request $request)
    {
        $topicId = $request->topicId;
        $users = Topic::findOrFail($topicId)->users()
            ->when(auth('api')->check(),function (Builder $builder){
                return $builder->where('id','!=',auth('api')->user()->id);
            })
            ->orderBy('rank', 'desc')->take(10)->get();
        return response($users,200);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function userQuestions($userId)
    {
        $questions = User::findOrFail($userId)->questions;
        Question::questionAndBestAnswer($questions);
        return response(['userQuestions'=>$questions], 200);
    }


    public function userAnswers($userId)
    {
        $answers =
            Question::whereHas('answers',function(Builder $query) use ($userId){
                $query->where('user_id',$userId);
            })->with(['answers'=>function($query) use ($userId){
                $query->where('user_id',$userId)->excludeMyAnswer();
            }])->get();
        return response(['userAnswers'=>$answers],200);
    }

    /**
     * Display the information about user has $id
     * @param  int  $id
     * @return \Illuminate\Http\Response $response[] = [credentials]
     */

    public function show($userId)
    {
        $user = User::whereId($userId)
            ->with('credentials')
            ->withCount(['questions','answers'])
            ->first();
        $user->topics = $user->topicsDetails();
        $user->points = $user->points();

        return response($user,200);
    }


    /**
     * @param Request $request
     * $request[] => [description]
     * @return  \Illuminate\Http\Response response[] =>[modified keys]
     */
    public function update(Request $request)
    {
        $user = auth('api')->user();
        $response = [];
        if ($request->has('description')) {
            $request->validate([
                'description'=>'required'
            ]);
            $user->update(['description' => $request->description]);
            $response['description'] = $user->description;
        }
        return response($response, 200);
    }
    public function changeMyPassword(Request $request)
    {
        $user = auth('api')->user();
        $oldPassword = $request->oldPassword;
        if (!(Hash::check($oldPassword, $user->getAuthPassword()))) {
            return response('you entered wrong password', 403);
        }
        $newPassword = $request->newPassword;
//        if($oldPassword!=$user->getAuthPassword()){
//            return response('you entered wrong password',403);
//        }
        $user->password = Hash::make($newPassword);
        $user->update();
        return response('successfully changed', 200);
    }

}
