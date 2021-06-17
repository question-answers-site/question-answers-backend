<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class Report extends Notification implements ShouldQueue
{
    use Queueable;
    public $reporterId;
    public $id;
    public $type;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reporterId,$id,$type)
    {
        $this->reporterId = $reporterId;
        $this->id = $id;
        $this->type=$type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        if($this->type=='question'){
            $array=[
                'reporterId' =>$this->reporterId,
                'id' => $this->id
            ];
        }
        else if($this->type=='answer'){
            $array=[
                'type'=>$this->type,
                'reporterId' =>$this->reporterId,
                'id' => $this->id
            ];
        }
        return $array;
    }
}
