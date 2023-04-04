<template>
	<ProofloCard :footer="false">
		<template #body>
			<div class="text-center">
				<i v-if="stateLoading" class="tw-py-6 el-icon-loading"></i>
				<span
					class="tw-py-6 text-center tw-text-gray-500"
					v-if="!stateLoading && paymentMethods.length === 0"
				>There is no Credit card, please add one.</span>
			</div>
			<transition-group name="list">
				<div v-for="(card, key) in paymentMethods" :key="card.id">
					<PaymentMethodItem :user="user" :defaultMethod="defaultMethod" :card="card" :index="key" />
				</div>
			</transition-group>
		</template>
	</ProofloCard>
</template>

<script>
	import { mapGetters } from "vuex";
	import PaymentMethodItem from "./PaymentMethodItem.vue";
	export default {
		components: {
			PaymentMethodItem
		},
		computed: {
			...mapGetters([
				"paymentMethods",
				"defaultMethod",
				"user",
				"stateLoading"
			])
		}
	};
</script>