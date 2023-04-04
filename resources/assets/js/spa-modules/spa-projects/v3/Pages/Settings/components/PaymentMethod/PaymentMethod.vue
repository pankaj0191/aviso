<template>
	<div>
		<AddPaymentMethod />
		<PaymentMethodList />
	</div>
</template>


<script>
	import AddPaymentMethod from "./AddPaymentMethod";
	import PaymentMethodList from "./PaymentMethodList";

	export default {
		components: {
			AddPaymentMethod,
			PaymentMethodList
		}
	};
</script>
