<?php

namespace App\Listeners;

use App\Mail\ProjectByLink;
use App\Events\InvitePepoleByLink;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitePepoleByLinkListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(InvitePepoleByLink $event)
    {
        if ($event->inv->user_id) {
            if (!$event->inv->user->emailNotifications) {
                $event->inv->user->emailNotifications()->create();
                
                Mail::to($event->inv->user)->queue(new ProjectByLink(auth()->user(), $event->inv->link, $event->project, $event->revision));
            } elseif ($event->inv->user->emailNotifications->new_project) {
                Mail::to($event->inv->user->email)->queue(new ProjectByLink(auth()->user(), $event->inv->link, $event->project, $event->revision));
            }
        } else {
            Mail::to($event->inv->email)->queue(new ProjectByLink(auth()->user(), $event->inv->link, $event->project, $event->revision));
        }
    }
}
