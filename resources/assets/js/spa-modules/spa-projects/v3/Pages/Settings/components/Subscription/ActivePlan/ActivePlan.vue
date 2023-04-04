<template>
	<div>
		<div v-if="activeSubscription.id">
			<div v-if="activeSubscription.ends_at">
				<!-- Cancel Subscription -->
				<div class="tw-text-lg tw-font-semibold tw-text-gray-900">Resume Subscription</div>
				<ResumeSubscription :plan="plan" />
			</div>
			<div>
				<!-- Plan Details -->
				<Plan :plan="plan" />

				<!-- Cancel Subscription -->
				<CancelSubscription :plan="plan" />
			</div>
		</div>
		<div v-else class="tw-text-center">
			<i class="tw-py-6 el-icon-loading"></i>
		</div>

		<!-- Receipts -->
		<Receipts />
	</div>
</template>

<script>
	import CancelSubscription from "./CancelSubscription";
	import ResumeSubscription from "./ResumeSubscription";
	import Receipts from "./Receipts";
	import Plan from "./Plan";
	import { mapGetters } from "vuex";

	export default {
		props: ["plan"],
		components: {
			Plan,
			CancelSubscription,
			ResumeSubscription,
			Receipts
		},
		methods: {},
		computed: {
			...mapGetters(["activeSubscription", "activePlan"])
		}
	};
</script>