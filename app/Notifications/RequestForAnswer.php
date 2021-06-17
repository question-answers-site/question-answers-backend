<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestForAnswer extends Notification implements ShouldQueue
{
    use Queueable;

    public $requesterId;
    public $requester;
    public $questionId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($requesterId, $questionId)
    {
        $this->requesterId = $requesterId;
        $this->questionId = $questionId;
//        $this->requester = User::findOrFail($requesterId);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $questionUrl = url('question/' . $this->questionId);
        return (new MailMessage)
            ->line('Hello' . $notifiable->first_name)
            ->line($this->requester->first_name . ' ' . $this->requester->last_name .
                'request you to answer on a questions')
            ->line('To See The Question Click On Down Button')
            ->action('See Question',$questionUrl);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
            'requesterId' => $this->requesterId,
            'questionId' => $this->questionId
        ];
    }
}
