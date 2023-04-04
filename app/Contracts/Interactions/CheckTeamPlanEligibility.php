<?php

namespace App\Contracts\Interactions;

interface CheckTeamPlanEligibility
{
    /**
     * Determine if the team is eligible to switch to the given plan.
     *
     * @param  \App\Team  $team
     * @param  \App\Plan  $plan
     * @return bool
     */

    public function handle($team, $plan);
}
