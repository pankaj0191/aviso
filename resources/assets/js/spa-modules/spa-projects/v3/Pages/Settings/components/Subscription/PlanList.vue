<template>
	<div>
		<div class="tw-text-lg tw-font-semibold tw-text-gray-900" v-if="!activePlan">Subscribe</div>
		<div v-if="plans.some(plan => plan.id == $route.query.subscribe)">
			<SelectedPlan :activePlan="activePlan" />
		</div>
		<div v-else>
			<div
				class="tw-mt-6 tw-mb-8 tw-px-7 tw-py-5 tw-bg-gray-200 tw-border tw-border-gray-300 tw-rounded-lg tw-mb-7"
				v-if="!activePlan"
			>
				<div
					class="tw-max-w-2xl tw-text-normal tw-text-gray-600"
				>It looks like you do not have an active subscription. You may choose one of the subscription plans below to get started. Subscription plans may be changed or cancelled at your convenience.</div>
			</div>
			<transition-group name="list">
				<div v-for="plan in plans" :key="plan.id">
					<PlanItem :plan="plan" :activePlan="activePlan" />
				</div>
			</transition-group>
		</div>
	</div>
</template>

<script>
	import PlanItem from "./PlanItem";
	import SelectedPlan from "./SelectedPlan";
	export default {
		props: ["activePlan"],
		components: {
			PlanItem,
			SelectedPlan
		},
		data() {
			return {
				plansProccess: false,
				plans: []
			};
		},
		methods: {
			async getPlans() {
				try {
					this.plansProccess = true;
					let { data } = await axios.get("/plans/list");
					if (data.status) {
						this.plans = data.data;
					} else {
						toastr["error"](response.data.errors, "Error");
					}
					this.plansProccess = false;
				} catch (error) {
					this.plansProccess = false;
					if (error.exception) {
						toastr["error"](
							"Something went wrong, please try again later",
							"Error"
						);
					} else {
						toastr["error"](error.message, "Error");
					}
				}
			}
		},
		created() {
			this.getPlans();
		}
	};
</script>

<style>
</style>