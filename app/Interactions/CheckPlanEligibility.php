<?php

namespace App\Interactions;

use App\Contracts\Interactions\CheckPlanEligibility as Contract;

class CheckPlanEligibility implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function handle($user, $plan)
    {
        return true;
    }
}
