<?php

namespace App\Listeners\Prooflo\Teams;

use App\Spark;
use App\Events\Prooflo\Teams\UserInvitedToTeam;
use App\Contracts\Repositories\NotificationRepository;

class CreateInvitationNotification
{
    /**
     * The notification repository instance.
     *
     * @var \Laravel\Spark\Contracts\Repositories\NotificationRepository
     */
    protected $notifications;

    /**
     * Create a new listener instance.
     *
     * @param NotificationRepository $notifications
     */
    public function __construct(NotificationRepository $notifications)
    {
        $this->notifications = $notifications;
    }


    public function handle(UserInvitedToTeam $event)
    {
        $this->notifications->create($event->user, [
            'icon' => 'fa fa-users',
            'body' => 'Hello ' . $event->user->name . ' you have been invited to the ' . $event->team->name  . '.',
            'project' => 'Invite to Join Team',
            'action_text' => 'View Team',
            'action_url' => 'dashboard/teams',
            'proofer' => '',
            'company' => currentRole(auth()->user()),
        ]);
    }
}
