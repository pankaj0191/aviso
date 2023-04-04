<?php

namespace App\Observers;

use App\Plan;
use Stripe\Stripe;
use Stripe\Plan as StripePlan;
use App\Contracts\IPlanService;
use App\Contracts\IStripeService;

class PlanObserver
{
    protected $planService;
    protected $stripeService;

    public function __construct(IPlanService $planService, IStripeService $stripeService)
    {
        $this->planService = $planService;
        $this->stripeService = $stripeService;
    }
    
    /**
     * Handle the plan "created" event.
     *
     * @param  \App\Plan  $plan
     * @return void
     */
    public function saving(Plan $plan)
    {
        if ($plan->stripe_plan_id) {
            $this->stripeService->deletePlan([
                'id' => $plan->stripe_plan_id,
                'product' => $plan->stripe_product_id
            ]);
        }
        Stripe::setApiKey(config('stripe.secret'));
        $stripePlan = StripePlan::create([
            'amount' => $plan->price * 100,
            'interval' => 'month',
            'product' => [
                "name" => $plan->name
            ],
            'currency' => 'usd'
        ]);

        $plan->stripe_plan_id = $stripePlan['id'];
        $plan->stripe_product_id = $stripePlan['product'];
    }
}
