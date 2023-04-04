<template>
	<ProofloCard :footer="false">
		<template #body>
			<div
				class="tw-max-w-2xl tw-text-normal tw-text-gray-600"
			>Having second thoughts about cancelling your subscription? You can instantly reactive your subscription at any time until the end of your current billing cycle. After your current billing cycle ends, you may choose an entirely new subscription plan.</div>
			<div class="tw-mt-4">
				<el-button
					class="tw-btn-default"
					:loading="resumingProcess"
					type="primary"
					@click="resumeSubscription"
				>Resume Subscription</el-button>
			</div>
		</template>
	</ProofloCard>
</template>

<script>
	import { mapActions, mapGetters } from "vuex";
	export default {
		props: ["plan"],
		data() {
			return {
				resumingProcess: false
			};
		},
		methods: {
			// Resume subscription
			async resumeSubscription() {
				this.$confirm(
					`Are you sure you want to cancel your subscription from ${this.plan.name}?`,
					"Resume Subscription",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning",
						lockScroll: false
					}
				).then(async () => {
					this.resumingProcess = true;
					try {
						const { data } = await axios.post(
							"/api/settings/resume-subscription",
							{
								plan: this.plan.id,
								subscription: this.activeSubscription.stripe_id
							}
						);
						this.resumingProcess = false;
						if (data.status) {
							toastr["success"](data.message, "Success");
							// Bus.$emit("updateUser");
							this.getActiveSubscription();
						} else {
							toastr["error"](data.errors, "Error");
						}
					} catch (error) {
						this.resumingProcess = false;
						toastr["error"]("Internal server error");
					}
				});
			},
			...mapActions(["getActiveSubscription", ""])
		},
		computed: {
			...mapGetters(["activeSubscription"])
		}
	};
</script>

<style>
</style>