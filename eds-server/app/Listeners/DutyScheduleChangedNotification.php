<?php

namespace App\Listeners;

use App\Events\DutyScheduleChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class DutyScheduleChangedNotification implements ShouldQueue
{
    public function handle( $event)
    {
        dd($event);
        return $event;
    }
}
