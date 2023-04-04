<template>
	<div class="spark-screen container" style="margin-top: -100px">
		<div class="row">
			<div
				class="col-xs-10 col-xs-offset-1 col-sm-12 col-sm-offset-0 col-md-12 col-md-offset-0 col-lg-12 col-lg-offset-0 login"
			>
				<RegisterForm v-if="page === 0" :page="page" @nextPage="nextPage" :registerForm="registerForm" />

				<RegisterPlans
					v-else-if="page === 1"
					:page="page"
					@nextPage="nextPage"
					:registerForm="registerForm"
				/>

				<Verfication v-else :page="page" :registerForm="registerForm" />
			</div>
		</div>
	</div>
</template>

<script>
	import RegisterForm from "./components/RegisterForm";
	import RegisterPlans from "./components/RegisterPlans";
	import Verfication from "./components/Verfication";
	export default {
		components: {
			RegisterForm,
			RegisterPlans,
			Verfication
		},
		data() {
			return {
				page: 0,
				registerForm: {
					plan: "",
					team: "",
					type: "",
					team_slug: "",
					name: "",
					email: "",
					password: "",
					password_confirmation: "",
					terms: false,
					invitation: null
				}
			};
		},
		methods: {
			nextPage(page) {
				this.page = page;
			}
		}
	};
</script>

<style>
</style>