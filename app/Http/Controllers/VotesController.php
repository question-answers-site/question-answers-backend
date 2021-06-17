<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $user = auth('api')->user();
        $answerId = $request->answerId;
        Answer::where('id',$answerId)->update(['must_update'=>1]);
        $value = $request->value;
        $userVote = Vote::where('user_id',$user->id)
            ->where('answer_id',$answerId)->first();

        if(!$userVote){
            Vote::create([
                'answer_id'=>$answerId,
                'user_id'=>$user->id,
                'value'=>$value
            ]);
        }else {

            $userVoteValue = $userVote->value;

            if ($userVoteValue === $value) {
                $userVote->delete();
            }
            else {
                $userVote->value = $value;
                $userVote->save();
            }
        }
        $answer = Answer::whereId($answerId)->upDownVotesCount()->first();
        return response([
            'upVotesCount'=>$answer->upVotesCount,
            'downVotesCount'=>$answer->downVotesCount
            ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
