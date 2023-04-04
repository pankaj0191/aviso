<template>
	<div>
		<!-- Loading and not found data  -->
		<div class="tw-text-center">
			<i v-if="planProccess" class="tw-py-6 el-icon-loading"></i>
			<span
				class="tw-py-6 text-center tw-text-gray-500"
				v-if="!planProccess && !plan"
			>There is no Credit card, please add one.</span>
		</div>

		<!-- Select Plan details  -->
		<PlanItem v-if="plan && plan.id" :plan="plan" :footer="false" />

		<SubscriptionInformation :plan="plan" />

		<!-- Back to plans -->
		<button
			class="tw-border-0 tw-flex tw-items-center tw-mt-5 tw-p-0 tw-bg-transparent"
			@click="backToPlan"
		>
			<svg viewBox="0 0 20 20" fill="currentColor" class="tw-text-gray-400 tw-w-4 tw-h-4">
				<path
					fill-rule="evenodd"
					d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
					clip-rule="evenodd"
				/>
			</svg>
			<div
				class="tw-ml-3 tw-tw-text-sm tw-text-gray-600 tw-underline"
				v-if="activePlan"
			>Nevermind, I'll keep my old plan</div>
			<div class="tw-ml-3 tw-tw-text-sm tw-text-gray-600 tw-underline" v-else>Select a different plan</div>
		</button>
	</div>
</template>

<script>
	import PlanItem from "./PlanItem.vue";
	import SubscriptionInformation from "./SubscriptionInformation";
	export default {
		props: ["activePlan"],
		components: {
			PlanItem,
			SubscriptionInformation
		},
		data() {
			return {
				plan: {},
				planProccess: false
			};
		},
		methods: {
			backToPlan() {
				this.$store.commit("CHANGE_PLAN", false);
				return this.$router.push({
					name: "setting-billing"
				});
			},
			async getPlan() {
				try {
					this.planProccess = true;
					let { data } = await axios.get(
						`/plans/${this.$route.query.subscribe}`
					);
					if (data.status) {
						this.plan = data.data;
					} else {
						toastr["error"](data.errors, "Error");
					}
					this.planProccess = false;
				} catch (error) {
					this.planProccess = false;
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
		watch: {
			activePlan(val) {
				if (this.plan && this.plan.id == val.id) {
					this.$router.push({
						name: "setting-billing"
					});
				}
			}
		},
		async created() {
			await this.getPlan();
			if (this.plan && this.plan.id == this.activePlan.id) {
				this.$router.push({
					name: "setting-billing"
				});
			}
		}
	};
</script>