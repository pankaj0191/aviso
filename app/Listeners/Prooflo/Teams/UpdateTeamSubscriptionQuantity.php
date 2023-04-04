<?php

namespace App\Listeners\Prooflo\Teams;

use App\Spark;
use App\Events\Teams\TeamMemberAdded;

class UpdateTeamSubscriptionQuantity
{
    /**
     * Handle the event.
     *
     * @param  mixed $event
     * @return void
     */
    public function handle($event)
    {
        if (! Spark::chargesTeamsPerMember() || ! $event->team->subscription()) {
            return;
        }

        if ($event instanceof TeamMemberAdded) {
            $this->incrementQuantity($event->team);
        } else {
            $this->decrementQuantity($event->team);
        }
    }


    /**
     * Increment the subscription quantity.
     *
     * @param  \Laravel\Spark\Team  $team
     * @return void
     */
    private function incrementQuantity($team)
    {
        if ($team->users()->count() > 1) {
            $team->addSeat();
        }
    }

    /**
     * Decrement the subscription quantity.
     *
     * @param  \Laravel\Spark\Team  $team
     * @return void
     */
    private function decrementQuantity($team)
    {
        if ($team->subscription()->quantity > 1) {
            $team->removeSeat();
        }
    }
}
