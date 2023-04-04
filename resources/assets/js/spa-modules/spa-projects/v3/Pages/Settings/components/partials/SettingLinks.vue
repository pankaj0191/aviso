<template>
	<el-col id="setting-links" class="hidden-sm-and-down tw-py-2 tw-px-4" :md="8" :lg="6" :xl="4">
		<router-link
			:to="{ name: 'setting-profile'}"
			class="tw-my-2 tw-rounded-md tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium"
			:class="$route.name == 'setting-profile' ?  'tw-bg-gray-50 tw-text-primary hover:tw-bg-white' : 'tw-text-gray-900 hover:tw-text-gray-900 hover:tw-bg-gray-50'"
		>
			<ProfileIcon
				:class="$route.name == 'setting-profile' ?  'tw-text-primary' : 'tw-text-gray-400'"
			/>
			<span class="tw-truncate">Profile</span>
		</router-link>
		<router-link
			:to="{ name: 'setting-change-password'}"
			class="tw-my-2 tw-rounded-md tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium"
			:class="$route.name == 'setting-change-password' ?  'tw-bg-gray-50 tw-text-primary hover:tw-bg-white' : 'tw-text-gray-900 hover:tw-text-gray-900 hover:tw-bg-gray-50'"
		>
			<PasswordIcon
				:class="$route.name == 'setting-change-password' ?  'tw-text-primary' : 'tw-text-gray-400'"
			/>
			<span class="tw-truncate">Password</span>
		</router-link>
		<router-link
			:to="{ name: 'setting-notification'}"
			class="tw-my-2 tw-rounded-md tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium"
			:class="$route.name == 'setting-notification' ?  'tw-bg-gray-50 tw-text-primary hover:tw-bg-white' : 'tw-text-gray-900 hover:tw-text-gray-900 hover:tw-bg-gray-50'"
		>
			<BillIcon
				:class="$route.name == 'setting-notification' ?  'tw-text-primary' : 'tw-text-gray-400'"
			/>
			<span class="tw-truncate">Notifications</span>
		</router-link>
		<router-link
			:to="{ name: 'setting-billing'}"
			@click.native="billing"
			class="tw-my-2 tw-rounded-md tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium"
			:class="$route.name == 'setting-billing' ?  'tw-bg-gray-50 tw-text-primary hover:tw-bg-white' : 'tw-text-gray-900 hover:tw-text-gray-900 hover:tw-bg-gray-50'"
		>
			<BillingIcon
				:class="$route.name == 'setting-billing' ?  'tw-text-primary' : 'tw-text-gray-400'"
			/>
			<span class="tw-truncate">Subscription</span>
		</router-link>
		<router-link
			:to="{ name: 'setting-payment-methods'}"
			class="tw-my-2 tw-rounded-md tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium"
			:class="$route.name == 'setting-payment-methods' ?  'tw-bg-gray-50 tw-text-primary hover:tw-bg-white' : 'tw-text-gray-900 hover:tw-text-gray-900 hover:tw-bg-gray-50'"
		>
			<BillingIcon
				:class="$route.name == 'setting-payment-methods' ?  'tw-text-primary' : 'tw-text-gray-400'"
			/>
			<span class="tw-truncate">Payment Methods</span>
		</router-link>
	</el-col>
</template>

<script>
	import ProfileIcon from "../svg/ProfileIcon";
	import PasswordIcon from "../svg/PasswordIcon";
	import BillingIcon from "../svg/BillingIcon";
	import BillIcon from "../svg/BillIcon";

	export default {
		components: {
			BillingIcon,
			ProfileIcon,
			PasswordIcon,
			BillIcon
		},
		methods: {
			billing() {
				this.$store.commit("CHANGE_PLAN", false);
			}
		}
	};
</script>


<style lang="scss" scoped>
#setting-links {
	a {
		&:hover,
		&:focus,
		&:active {
			color: #5e9882 !important;
			cursor: pointer;
			text-decoration: none;
		}
	}
}
</style>