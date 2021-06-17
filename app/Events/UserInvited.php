<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserInvited
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $inviteDetails;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($inviteDetails)
    {
        $this->inviteDetails=$inviteDetails;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user_notifications.'.$this->inviteDetails['notifiedUser']->id);
    }
}
