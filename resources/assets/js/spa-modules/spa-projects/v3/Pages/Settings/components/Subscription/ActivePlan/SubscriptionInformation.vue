<template>
	<ProofloCard :footer="false" v-if="payment && payment.last4">
		<template #header>
			<div class="tw-flex tw-justify-between">
				<div class="tw-flex tw-justify-between">
					<h2 class="tw-px-6 tw-text-xl tw-font-semibold tw-text-gray-700">Payment Information</h2>
				</div>
			</div>
		</template>
		<template #body>
			<div
				class="tw-max-w-2xl tw-text-normal tw-text-gray-600"
			>Your default payment method is a credit card ending in {{payment.last4}} that expires on {{payment.exp_month}}/{{payment.exp_year}}.</div>
			<div class="tw-mt-4">
				<el-button
					class="tw-btn-default"
					type="primary"
					@click="changePaymentMethodDefault"
				>Change / Add Default Payment Method</el-button>
			</div>
		</template>
	</ProofloCard>
</template>

<script>
	import { mapGetters } from "vuex";
	export default {
		methods: {
			changePaymentMethodDefault() {
				return this.$router.push({
					name: "setting-payment-methods"
				});
			}
		},
		computed: {
			payment() {
				return this.paymentMethods.find(
					payment => payment.id == this.defaultMethod
				);
			},
			...mapGetters(["paymentMethods", "defaultMethod"])
		}
	};
</script>

<style>
</style>