<?php

namespace App\Listeners\Prooflo\Teams;

use App\Spark;
use App\Events\Teams\TeamCreated;

class UpdateOwnerSubscriptionQuantity
{
    /**
     * Handle the event.
     *
     * @param  mixed $event
     * @return void
     */
    public function handle($event)
    {
        if (! Spark::chargesUsersPerTeam() || ! $event->team->owner->subscription()) {
            return;
        }

        if ($event instanceof TeamCreated) {
            $this->incrementQuantity($event->team->owner);
        } else {
            $this->decrementQuantity($event->team->owner);
        }
    }


    /**
     * Increment the subscription quantity.
     *
     * @param  mixed  $owner
     * @return void
     */
    private function incrementQuantity($owner)
    {
        if ($owner->ownedTeams()->count() > 1) {
            $owner->addSeat();
        }
    }

    /**
     * Decrement the subscription quantity.
     *
     * @param  mixed  $owner
     * @return void
     */
    private function decrementQuantity($owner)
    {
        if ($owner->subscription()->quantity > 1) {
            $owner->removeSeat();
        }
    }
}
