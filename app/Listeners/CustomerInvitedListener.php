<?php

namespace App\Listeners;

use App\Events\CustomerInvited;
use App\Mail\PasswordChange;
use Illuminate\Support\Facades\Mail;

class CustomerInvitedListener
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CustomerInvited  $event
     * @return void
     */
    public function handle(CustomerInvited $event)
    {
        Mail::to($event->customer->email)->send(new PasswordChange($event->customer, $event->project, $event->token));
    }
}
