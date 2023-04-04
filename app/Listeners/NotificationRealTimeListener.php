<?php

namespace App\Listeners;

use App\Events\NotificationRealTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationRealTimeListener
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
     * @param  NotificationRealTime  $event
     * @return void
     */
    public function handle(NotificationRealTime $event)
    {
        //
    }
}
