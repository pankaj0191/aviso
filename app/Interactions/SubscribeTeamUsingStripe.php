<?php

namespace App\Interactions;

class SubscribeTeamUsingStripe extends SubscribeTeam
{
    /**
     * The token field to be used during subscription.
     *
     * @var string
     */
    protected $token = 'stripe_token';
}
