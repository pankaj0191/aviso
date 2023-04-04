@if (Spark::billsUsingStripe())
    @include('settings.subscription.subscribe-stripe')
@else
    @include('settings.subscription.subscribe-braintree')
@endif
