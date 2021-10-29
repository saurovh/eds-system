<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class DutyScheduleChanged implements ShouldBroadcast
{
    public function broadcastOn()
    {
        return new Channel('user.1');
    }
}
