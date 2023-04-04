<?php

namespace App\Http\Requests\Settings\Teams\Subscription;

use App\Spark;
use App\Http\Requests\ValidatesBillingAddresses;
use App\Contracts\Http\Requests\Settings\Teams\Subscription\CreateSubscriptionRequest as Contract;

class CreateStripeSubscriptionRequest extends CreateSubscriptionRequest implements Contract
{
    use ValidatesBillingAddresses;

    /**
     * Get the validator for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        $validator = $this->baseValidator([
            'stripe_token' => 'required_if:existing_card,0',
            'vat_id' => 'nullable|max:50|vat_id',
        ]);

        if (Spark::collectsBillingAddress()) {
            $this->validateBillingAddress($validator);
        }

        return $validator;
    }
}
