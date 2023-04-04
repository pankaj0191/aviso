<template>
	<div>
		<div v-if="!changePlanStatus">
			<div class="tw-text-lg tw-font-semibold tw-text-gray-900">Current Subscription Plan</div>
			<ProofloCard>
				<template #header>
					<div class="tw-flex tw-justify-between">
						<PlanTitle :plan="plan" />
					</div>
				</template>
				<template #body>
					<PlanDetails :plan="plan" />
				</template>
				<template #footer>
					<el-button class="tw-btn" type="primary" @click="changePlan">Change Subscription Plan</el-button>
				</template>
			</ProofloCard>
			<!-- Payment Information -->
			<SubscriptionInformation />
		</div>
		<ChangePlan v-else :activePlan="plan" />
	</div>
</template>

<script>
	import PlanTitle from "../components/PlanTitle";
	import PlanDetails from "../components/PlanDetails";
	import ChangePlan from "./ChangePlan.vue";
	import SubscriptionInformation from "./SubscriptionInformation";
    import { mapGetters } from "vuex";

	export default {
		props: ["plan"],
		components: { PlanTitle, PlanDetails, ChangePlan, SubscriptionInformation },
		methods: {
			changePlan() {
				this.$store.commit("CHANGE_PLAN", true);
			}
		},
        computed: {
           ...mapGetters(['changePlanStatus']) 
        }
	};
</script>
    
<style>
</style>