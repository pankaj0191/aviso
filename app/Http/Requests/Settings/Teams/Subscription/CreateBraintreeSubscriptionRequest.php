<?php

namespace App\Http\Requests\Settings\Teams\Subscription;

use App\Contracts\Http\Requests\Settings\Teams\Subscription\CreateSubscriptionRequest as Contract;

class CreateBraintreeSubscriptionRequest extends CreateSubscriptionRequest implements Contract
{
    /**
     * Get the validator for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        return $this->baseValidator([
            'braintree_type' => 'required',
            'braintree_token' => 'required',
        ]);
    }
}
