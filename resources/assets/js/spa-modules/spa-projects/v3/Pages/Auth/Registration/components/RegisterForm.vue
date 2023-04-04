<template>
	<div class="row login-card" v-if="page == 0">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 login-form">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
					<div class="logo">
						<!-- Branding Image -->
						<img
							src="https://app-prooflo.nyc3.digitaloceanspaces.com/assets/img/prooflo.png"
							class
							style="height: 48px;"
						/>
					</div>

					<div class="login-title">Why we use Prooflo?</div>

					<div class="login-content">It allows the creative process to be simplified.</div>
					<!-- Generic Error Message -->
					<!-- <div
							class="alert alert-danger"
							v-if="registerForm.errors.has('form')"
					>{{ registerForm.errors.get('form') }}</div>-->

					<!-- Invitation Code Error -->
					<!-- <div
							class="alert alert-danger"
							v-if="registerForm.errors.has('invitation')"
					>{{ registerForm.errors.get('invitation') }}</div>-->

					<form class="form-horizontal" role="form">
						<!-- Name -->
						<div class="form-group">
							<label class="col-md-12 label-form">Name</label>

							<div class="col-md-12">
								<input
									type="text"
									class="form-control prooflo-input"
									required
									name="name"
									v-model="registerForm.name"
									autofocus
								/>

								<span
									class="color-danger tw-text-sm"
									v-show="serverError.name"
									v-for="(error, key) in serverError.name"
									:key="key + 'name'"
								>{{ error }}</span>
							</div>
						</div>

						<!-- E-Mail Address -->
						<div class="form-group">
							<label class="col-md-12 label-form">Email</label>

							<div class="col-md-12">
								<input
									type="email"
									class="form-control prooflo-input"
									name="email"
									v-model="registerForm.email"
									required
								/>

								<span
									class="color-danger tw-text-sm"
									v-show="serverError.email"
									v-for="(error, key) in serverError.email"
									:key="key + 'email'"
								>{{ error }}</span>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<!-- Password -->
								<div class="form-group">
									<label class="col-md-12 label-form">Password</label>

									<div class="col-md-12">
										<input
											type="password"
											class="form-control prooflo-input"
											name="password"
											v-model="registerForm.password"
											required
										/>

										<span
											class="color-danger tw-text-sm"
											v-show="serverError.password"
											v-for="(error, key) in serverError.password"
											:key="key + 'password'"
										>{{ error }}</span>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<!-- Password Confirmation -->
								<div class="form-group">
									<label class="col-md-12 label-form">Confirm Password</label>

									<div class="col-md-12">
										<input
											type="password"
											required
											class="form-control prooflo-input"
											name="password_confirmation"
											v-model="registerForm.password_confirmation"
										/>

										<span
											class="color-danger tw-text-sm"
											v-show="serverError.password_confirmation"
											v-for="(error, key) in serverError.password_confirmation"
											:key="key + 'password_confirmation'"
										>{{ error }}</span>
									</div>
								</div>
							</div>
						</div>

						<!-- Terms And Conditions -->
						<div>
							<div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="terms" v-model="registerForm.terms" />
										I accept the
										<a class="prooflo-text" href="/terms" target="_blank">
											<b>
												Terms of
												Service &
												Privacy
												Policy
											</b>
										</a>
									</label>

									<span
										class="tw-block color-danger tw-text-sm"
										v-show="serverError.terms"
										v-for="(error, key) in serverError.terms"
										:key="key + 'terms'"
									>{{ error }}</span>
								</div>
							</div>

							<div class="form-group">
								<div class="col-md-12">
									<button class="btn btn-primary btn-block btn-login" @click.prevent="register">
										<span>
											Continue
											<ArrowSvg />
										</span>
									</button>

									<div class="stepper">
										<div class="step" :class="{'active': page == 0}"></div>
										<div class="step" :class="{'active': page == 1}"></div>
									</div>
									<div class="login-link">
										<a href="/login" class="prooflo-text">I already have an account</a>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="hidden-xs hidden-sm col-md-6 col-lg-6 login-image">
			<div class="image">
				<DashboardSvg />
			</div>
			<div class="content">
				<div class="content-head">Prooflo is the world’s simplest creative project management tool.</div>
				<div class="content-body">It’s so easy your grandmother can use it.</div>
				<div class="content-foot">Easy to manage creative projects, client review and teams.</div>
			</div>
		</div>
	</div>
</template>

<script>
	import DashboardSvg from "./svg/Dashboard.vue";
	import ArrowSvg from "./svg/Arrow";
	import axios from "axios";

	export default {
		props: ["page", "registerForm"],
		components: {
			DashboardSvg,
			ArrowSvg
		},
		data() {
			return {
				serverError: {}
			};
		},
		methods: {
			/*
			 * After obtaining the Stripe token, send the registration to Spark.
			 */
			async register() {
				try {
					const { user } = await axios.post(
						"/register",
						this.registerForm
					);
					this.$emit("nextPage", 1);
				} catch (error) {
					this.serverError = error.response.data.errors;
					console.log(error);
				}
			},

			/**
			 * Get the invitation specified in the query string.
			 */
			async getInvitation() {
				try {
					const { data } = await axios.get(
						`/invitations/${this.query.invitation}`
					);
					this.invitation = data;
				} catch (error) {
					this.invalidInvitation = true;
				}
			}
		},
		created() {
			if (this.$route.query.invitation) {
				this.getInvitation();

				this.registerForm.invitation = this.$route.query.invitation;
			}
		}
	};
</script>

<style>
</style>