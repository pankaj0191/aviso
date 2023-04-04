<template>
	<ProofloCard :footer="false">
		<template #header>
			<div class="tw-flex tw-justify-between">
				<div class="tw-flex tw-justify-between">
					<h2 class="tw-px-6 tw-text-xl tw-font-semibold tw-text-gray-700">Cancel Subscription</h2>
				</div>
			</div>
		</template>
		<template #body>
			<div
				class="tw-max-w-2xl tw-text-normal tw-text-gray-600"
			>You may cancel your subscription at any time. Once your subscription has been cancelled, you will have the option to resume the subscription until the end of your current billing cycle.</div>
			<div class="tw-mt-4">
				<el-button
					class="tw-btn-default"
					:loading="cancelingProcess"
					@click="cancelSubscription"
				>Cancel Subscription</el-button>
			</div>
		</template>
	</ProofloCard>
</template>

<script>
	import { mapActions } from "vuex";
	export default {
		props: ["plan"],
		data() {
			return {
				cancelingProcess: false
			};
		},
		methods: {
			// Cancel subscription
			async cancelSubscription() {
				this.$confirm(
					`Are you sure you want to cancel your subscription from ${this.plan.name}?`,
					"Cancel Subscription",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning",
						lockScroll: false
					}
				).then(async () => {
					this.cancelingProcess = true;
					try {
						const {
							data
						} = await axios.post("/api/settings/cancel-subscription", {
							plan: this.plan.id
						});
						this.cancelingProcess = false;
						if (data.status) {
							toastr["success"](data.message, "Success");
							// Bus.$emit("updateUser");
							this.getActiveSubscription();
						} else {
							toastr["error"](data.errors, "Error");
						}
					} catch (error) {
						this.cancelingProcess = false;
						toastr["error"]("Internal server error");
					}
				});
			},
			...mapActions(["getActiveSubscription"])
		}
	};
</script>

<style>
</style>