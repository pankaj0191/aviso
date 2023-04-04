<template>
	<ProofloCard :footer="footer">
		<template #header>
			<PlanTitle :plan="plan" />
		</template>
		<template #body>
			<PlanDetails :plan="plan" />
		</template>
		<template #footer v-if="footer">
			<div v-if="activePlan && activePlan.id === plan.id" class="text-gray-400">Currently Subscribed</div>
			<el-button class="tw-btn" type="primary" v-else @click="subscribe">Subscribe</el-button>
		</template>
	</ProofloCard>
</template>

<script>
	import PlanTitle from "./components/PlanTitle";
	import PlanDetails from "./components/PlanDetails";
	export default {
		components: {
			PlanTitle,
			PlanDetails
		},
		props: {
			activePlan: {
				type: Object
			},
			plan: {
				type: Object
			},
			footer: {
				default: true,
				type: Boolean
			}
		},
		methods: {
			subscribe() {
				return this.$router.push({
					query: {
						subscribe: this.plan.id
					}
				});
			}
		}
	};
</script>