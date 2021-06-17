<?php

namespace App\Listeners;

use App\Events\QuestionDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionDeletedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(QuestionDeleted $event)
    {
        Log::info('Question deleted Listener access');
        $question=$event->question;
//        Log::info($question);
        $y=DB::table('notifications')->where('type','App\Notifications\RequestForAnswer')
            ->where('data->questionId',$question->id)->get();
//        Log::info($y);
    }
}
