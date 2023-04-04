<?php

namespace App\Contracts\Interactions;

interface SubscribeTeam
{
    /**
     * Subscribe the team to a subscription plan.
     *
     * @param  \App\Team  $team
     * @param  \App\Plan  $plan
     * @param  bool  $fromRegistration
     * @param  array  $data
     * @return \App\Team
     */
    public function handle($team, $plan, $fromRegistration, array $data);
}
