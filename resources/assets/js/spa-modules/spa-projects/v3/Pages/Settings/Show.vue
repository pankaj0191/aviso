<template>
	<el-row id="settings" class="tw-py-4" :gutter="12">
		<SettingLinks />
		<el-col :xs="24" :sm="18" :md="16" :lg="14" :xl="12">
			<transition :name="transitionName">
				<router-view></router-view>
			</transition>
		</el-col>
	</el-row>
</template>

<script>
	import SettingLinks from "./components/partials/SettingLinks";
	import { mapActions, mapGetters } from "vuex";

	export default {
		components: {
			SettingLinks
		},
		data() {
			return {
				transitionName: null
			};
		},
		methods: {
			...mapActions(["getPaymentMethods"])
		},
		computed: {
			...mapGetters(["user"])
		},
		watch: {
			$route(to, from) {
				const toDepth = to.path.split("/").length;
				const fromDepth = from.path.split("/").length;
				this.transitionName =
					toDepth < fromDepth ? "slide-right" : "slide-left";
			},
			user(val) {
				this.getPaymentMethods(val);
			}
		},
		mounted() {
			this.user.id && this.getPaymentMethods(this.user);
		}
	};
</script>