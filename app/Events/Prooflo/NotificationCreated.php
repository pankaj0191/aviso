<?php

namespace App\Events\Prooflo;

class NotificationCreated
{
    /**
     * The notification instance.
     *
     * @var \Laravel\Spark\Notification
     */
    public $notification;

    /**
     * Create a new notification instance.
     *
     * @param  \Laravel\Spark\Notification  $notification
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }
}
