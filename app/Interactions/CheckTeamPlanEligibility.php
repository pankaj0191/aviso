<?php

namespace App\Interactions;

use App\Contracts\Interactions\CheckTeamPlanEligibility as Contract;

class CheckTeamPlanEligibility implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function handle($team, $plan)
    {
        return true;
    }
}
