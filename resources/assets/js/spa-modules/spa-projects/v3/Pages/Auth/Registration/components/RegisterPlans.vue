<template>
	<div class="row login-card" v-if="page == 1">
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 login-form">
			<div class="row">
				<div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-10 col-xs-offset-1">
					<div class="logo">
						<!-- Branding Image -->
						<img
							src="https://app-prooflo.nyc3.digitaloceanspaces.com/assets/img/prooflo.png"
							class
							style="height: 42px;"
						/>
					</div>

					<div class="login-title">Tell us who you are?</div>

					<ul class="login-content">
						<li>Clients submit and manage all creative projects to freelancers/agencies.</li>
						<li>Freelancers manage all creative projects in one place.</li>
						<li>Agencies manage all creative projects and teams in one place.</li>
					</ul>

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
						<label class="m-b-lg" style="width: 100%">
							<input
								type="radio"
								v-model="registerForm.type"
								class="card-input-element hidden"
								value="client"
							/>
							<div class="card card-body">
								<div class="card-detail">
									<div>Client</div>
									<div class="p-r-md price">Free</div>
								</div>
							</div>
						</label>

						<label class="m-b-lg" style="width: 100%">
							<input
								type="radio"
								v-model="registerForm.type"
								class="card-input-element hidden"
								value="collaborator"
							/>
							<div class="card card-body">
								<div class="card-detail">
									<div>Collaborator</div>
									<div class="p-r-md price">Free</div>
								</div>
							</div>
						</label>

						<label class="m-b-lg" style="width: 100%">
							<input
								type="radio"
								v-model="registerForm.type"
								class="card-input-element hidden"
								value="freelancer"
							/>
							<div class="card card-body">
								<div class="card-detail">
									<div>Freelancer</div>
									<div class="p-r-md price">
										$19 /
										<small>month</small>
									</div>
								</div>
							</div>
						</label>

						<label class="m-b-lg" style="width: 100%">
							<input
								type="radio"
								v-model="registerForm.type"
								class="card-input-element hidden"
								value="agency"
							/>
							<div class="card card-body">
								<div class="card-detail">
									<div>Agency</div>
									<div class="p-r-md price">
										$79 /
										<small>month</small>
									</div>
								</div>
							</div>
						</label>

						<div class="form-group">
							<div class="col-md-12">
								<button
									class="btn btn-primary btn-block btn-login"
									@click.prevent="register"
									:disabled="registerForm.busy || !registerForm.type"
								>
									<span v-if="registerForm.busy">
										Registering
										<i class="fa fa-btn fa-spinner fa-spin"></i>
									</span>

									<span v-else>
										Register
										<i class="fa fa-btn fa-check-circle"></i>
									</span>
								</button>
								<div class="stepper">
									<div class="step" :class="{'active' : page == 0}"></div>
									<div class="step" :class="{'active' : page == 1}"></div>
								</div>
								<div class="login-link">
									<a href="/login" class="prooflo-text">I already have an account</a>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div
			v-if="!registerForm.type || registerForm.type == 'client' || registerForm.type == 'collaborator'"
			class="hidden-xs hidden-sm col-md-6 col-lg-6 login-image"
		>
			<div class="image">
				<DashboardSvg />
			</div>
			<div class="content">
				<div class="content-head">Prooflo is the world’s simplest creative project management tool.</div>
				<div class="content-body">It’s so easy your grandmother can use it.</div>
				<div class="content-foot">Easy to manage creative projects, client review, and teams.</div>
			</div>
		</div>
		<div
			v-else-if="registerForm.type == 'freelancer'"
			class="hidden-xs hidden-sm col-md-6 col-lg-6 login-image"
			style="min-height: 644px;display: flex;
    flex-direction: column;
    justify-content: center;align-items:center"
		>
			<div class="head-content">No credit card required</div>
			<div class="price-card col-md-9 col-lg-7">
				<div class="price-header">
					<CheckCSvg />
					<div class="price-title">Freelancer Plan</div>
				</div>
				<ul class="list-unstyled price-features">
					<li>
						<div class="feature-title">Unlimited projects</div>
						<div>
							<CheckSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Unlimited Client</div>
						<div>
							<CheckSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Team</div>
						<div>
							<CloseSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Members</div>
						<div>
							<CloseSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Storage</div>
						<div>
							<b>5GB</b>
						</div>
					</li>
				</ul>
				<div class="price-cost">$19</div>
			</div>
			<div class="foot-content">Try it on us for Free</div>
		</div>
		<div
			v-else-if="registerForm.type == 'agency'"
			class="hidden-xs hidden-sm col-md-6 col-lg-6 login-image"
			style="min-height: 644px;display: flex;
    flex-direction: column;
    justify-content: center;align-items:center"
		>
			<div class="head-content">No credit card required</div>
			<div class="price-card col-md-9 col-lg-7">
				<div class="price-header">
					<CheckCSvg />
					<div class="price-title">Agency Plan</div>
				</div>
				<ul class="list-unstyled price-features">
					<li>
						<div class="feature-title">Unlimited projects</div>
						<div>
							<CheckSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Unlimited Client</div>
						<div>
							<CheckSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Team</div>
						<div>
							<CheckSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Members</div>
						<div>
							<CheckSvg />
						</div>
					</li>
					<li>
						<div class="feature-title">Storage</div>
						<div>
							<b>15GB</b>
						</div>
					</li>
				</ul>
				<div class="price-cost">$79</div>
			</div>
			<div class="foot-content">Try it on us for Free</div>
		</div>
	</div>
</template>

<script>
	import DashboardSvg from "./svg/Dashboard.vue";
	import CheckCSvg from "./svg/CheckC.vue";
	import CheckSvg from "./svg/Check.vue";
	import CloseSvg from "./svg/Close.vue";

	export default {
		props: ["page", "registerForm"],
		components: {
			DashboardSvg,
			CheckCSvg,
			CheckSvg,
			CloseSvg
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
					this.$emit("nextPage", 2);
				} catch (error) {
					console.log(error);
				}
			}
		}
	};
</script>