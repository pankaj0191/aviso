<template>
	<div>
		<div class="text-center">
			<i v-if="stateLoading" class="tw-py-6 el-icon-loading"></i>
		</div>
		<ActivePlan v-if="activePlan && activePlan.id" :plan="activePlan" />
		<PlanList v-else-if="!stateLoading" />
	</div>
</template>

<script>
	import PlanList from "./PlanList";
	import ActivePlan from "./ActivePlan/ActivePlan";
	import { mapGetters } from "vuex";

	export default {
		components: {
			PlanList,
			ActivePlan
		},
		computed: {
			...mapGetters(["activePlan", "stateLoading"])
		}
	};
</script>