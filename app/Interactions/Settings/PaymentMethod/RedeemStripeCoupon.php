<?php

namespace App\Interactions\Settings\PaymentMethod;

use App\Contracts\Interactions\Settings\PaymentMethod\RedeemCoupon;

class RedeemStripeCoupon implements RedeemCoupon
{
    /**
     * {@inheritdoc}
     */
    public function handle($billable, $coupon)
    {
        $billable->applyCoupon($coupon);
    }
}
