<?php

namespace App\Http\Controllers;

use App\Events\UserInvited;
use App\Notifications\RequestForAnswer;
use App\Question;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class UserRequestsController extends Controller
{
    public function getUsers(){
        return User::all();
    }

    public function inviteUsers(Request $request){

        $request->validate([
            'questionId'=>'required',
            'selectedUsersIds'=>'required'
        ]);

        $requester = auth('api')->user();
        $questionId = $request->questionId;
        $notifiedUsers = User::find($request->selectedUsersIds);
        Notification::send($notifiedUsers,new RequestForAnswer($requester->id,$questionId));

        foreach ($notifiedUsers as $user){
            $inviteDetails=['questionId'=>$questionId,'notifiedUser'=>$user];
            event(new UserInvited($inviteDetails));
        }

        return response('users invited successfully',200);
    }

    public function getNotifications(){
        $user = auth('api')->user();

        $notifications =  $user->notifications()->get();

        foreach($notifications as $notification){
            $notification->requester = User::find($notification->data['requesterId']);
            $notification->question = Question::find($notification->data['questionId']);
            if(!$notification->question)$notification->delete();
        }
        $newNotification = $notifications->reject(function($element){
           return !$element->question || !$element->requester;
        });
        return response($newNotification,200);
    }

    public function markAsRead(Request $request){
        $notificationId = $request->notificationId;

        auth('api')->user()->unreadNotifications()
            ->whereId($notificationId)
            ->update(['read_at'=>Carbon::now()]);

        return response('marked as read',200);
    }

}
