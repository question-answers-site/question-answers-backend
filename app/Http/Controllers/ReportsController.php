<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function store(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $reporter = auth('api')->user();
        $message = '';
        if ($type == 'question') {
            Question::find($id)->reports()->create(['reporter_id' => $reporter->id]);
//            $admin->notify(new Report($reporter->id,$id,$type));
//            $message='question reported successfully';
        } else if ($type == 'answer') {
            Answer::find($id)->reports()->create(['reporter_id' => $reporter->id]);
//            $admin->notify(new Report($reporter->id,$id,$type));
//            $message='answer reported successfully';
        }
        return response($message, 200);
    }

    public function index()
    {
        $user = auth('api')->user();
//        $reportedQuestions = Report::where('reportable_type', 'App\Question')->where('is_read',0)->get();
//        $reportedAnswers = Report::where('reportable_type', 'App\Answer')->where('is_read',0)->get();
//        $questions = []; $answers = [];
//
//        foreach ($reportedQuestions as $reportedQuestion) {
//            $question=$reportedQuestion->reportable;
//            $question->report_id=$reportedQuestion->id;
//            $questions[] = $question;
//        }
//
//        foreach ($reportedAnswers as $reportedAnswer){
//            $answer = $reportedAnswer->reportable;
//            $question = Question::find($answer->question_id);
//            $question->report_id =$reportedAnswer->id ;
//            $answers[] = $question;
//        }
//        return response(['reportedQuestions'=>$questions,'reportedAnswers'=>$answers],200);

        $answersReports = Answer::orderedReports();
        $questionsReports = Question::orderedReports();
        return response(['reportedQuestions' => $questionsReports, 'reportedAnswers' => $answersReports], 200);
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
//        $this->authorize('delete', $question);
        Report::destroy($id);
        return response("Successfully Deleted", 200);
    }

    public function ignoreQuestion(Request $request){
        Question::find($request->id)->reports()->where('is_read',0)->update(['is_read' => 1]);
        return response('ignored successfully',200);
    }
    public function ignoreAnswer(Request $request){
        Answer::find($request->id)->reports()->where('is_read',0)->update(['is_read' => 1]);
        return response('ignored successfully',200);
    }
    public function markAsRead($id)
    {
        $report = Report::where('id', $id)->update(['is_read' => 1]);
//        $this->authorize('delete', $question);
        return response("marked as read", 200);
    }
}
