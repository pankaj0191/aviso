<?php

namespace App\Contracts\Interactions;

interface CheckPlanEligibility
{
    /**
     * Determine if the user is eligible to switch to the given plan.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Plan  $plan
     * @return bool
     */
    public function handle($user, $plan);
}
