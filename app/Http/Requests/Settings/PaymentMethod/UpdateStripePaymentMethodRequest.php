<?php

namespace App\Http\Requests\Settings\PaymentMethod;

use App\Spark;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\ValidatesBillingAddresses;
use App\Contracts\Http\Requests\Settings\PaymentMethod\UpdatePaymentMethodRequest;

class UpdateStripePaymentMethodRequest extends FormRequest implements UpdatePaymentMethodRequest
{
    use ValidatesBillingAddresses;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validator for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        $validator = Validator::make($this->all(), [
            'stripe_token' => 'required',
        ]);

        if (Spark::collectsBillingAddress()) {
            $this->validateBillingAddress($validator);
        }

        return $validator;
    }
}
