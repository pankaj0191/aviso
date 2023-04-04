@if (Spark::billsUsingStripe())
    @include('settings.payment-method-stripe')
@else
    @include('settings.payment-method-braintree')
@endif
