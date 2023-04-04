<template>
	<div>
		<div class="tw-text-lg tw-font-semibold tw-text-gray-900">Change Subscription Plan</div>

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
		</button>

		<PlanList :activePlan="activePlan" />
	</div>
</template>

<script>
	import PlanList from "../PlanList";
	export default {
		props: ["activePlan"],
		components: {
			PlanList
		},
		methods: {
			backToPlan() {
				this.$store.commit("CHANGE_PLAN", false);
				return this.$router.push({
					name: "setting-billing"
				});
			}
		}
	};
</script>

<style>
</style>