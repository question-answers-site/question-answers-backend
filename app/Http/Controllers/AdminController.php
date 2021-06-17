<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Answer;
use App\Question;
use App\Topic;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function checkIsAdmin(){
        return response([],200);
    }
    public function getQuestionsDetails(Request $request){
        $optionId=$request->optionId;
        return (new Admin())->getData(Question::class,$optionId);
    }
    public function getAnswersDetails(Request $request){
        $optionId=$request->optionId;
        return (new Admin())->getData(Answer::class,$optionId);
    }
    public function deleteUser(Request $request){
        $user=User::where('id',$request->userId);
        $user->delete();
        return response('user deleted Successfully',200);
    }
    public function getOptions(){
        return Admin::$options;
    }

	public function getTopContributors(){
        // $res=[];
        $res= Admin::topTenContributors();
        // foreach ($a as $q){
        //     $res[]=$q;
        //     $res[]=$q;
        //     $res[]=$q;
        // }
        return $res;
    }

    public function getTopActiveUsers(){
        return Admin::topTenActiveUsers();
	}
	public function getAllTopics(){
        $topics=Topic::all();
        return response($topics,200);
    }
}
