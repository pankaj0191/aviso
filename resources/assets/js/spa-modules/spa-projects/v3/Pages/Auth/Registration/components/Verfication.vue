<template>
	<div class="row login-card" v-if="page == 2">
		<div style="height: 100%">
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

						<div class="register-confirmation">
							<div class="email-icon">
								<EmailSentSvg />
							</div>
							<div class="thanks">Thanks!</div>
							<div class="content">We have sent a confirmation email to</div>
							<div class="conf-mail">{{registerForm.email}}</div>
						</div>

						<div class="text-center re-send-email">
							<div v-if="show" style="font-weight: 500;color:#58585A">
								Didn't receive an email?
								<a
									class="prooflo-text"
									href
									@click.prevent="resend"
									style="cursor: pointer"
								>
									Click to
									resend
								</a>
							</div>
							<div style="font-weight: 500;" v-if="!show">Resend - {{count}}</div>
							<small style="font-style: italic;color:#A0AEC0">"Check your spam mail"</small>
						</div>
					</div>
				</div>
			</div>
			<div class="hidden-xs hidden-sm col-md-6 col-lg-6 login-image">
				<div>
					<div class="image">
						<DashboardSvg />
					</div>
					<div class="content">
						<div class="content-head">Prooflo is the world’s simplest creative project management tool.</div>
						<div class="content-body">It’s so easy your grandmother can use it.</div>
						<div class="content-foot">Easy to manage creative projects, client review, and teams.</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import DashboardSvg from "./svg/Dashboard.vue";
	import EmailSentSvg from "./svg/EmailSent.vue";

	export default {
		props: ["page", "registerForm"],
		components: {
			DashboardSvg,
			EmailSentSvg
		},
		data() {
			return {
				show: true,
				count: "",
				countAttemp: 0,
				timer: null
			};
		},
		methods: {
			/**
			 * Resend email versifcation.
			 */
			async resend() {
				try {
					const { user } = await axios.post("/resend", this.registerForm);
					this.countDownTimer();
					convertfox.identify(user.id, {
						email: user.email,
						name: user.name
					});
				} catch (error) {
					console.log(error);
				}
			},

			/**
			 * CountDown time to resend mail
			 */
			countDownTimer() {
				const TIME_COUNT = 30;
				if (!this.timer) {
					this.count = TIME_COUNT;
					this.show = false;
					this.timer = setInterval(() => {
						if (this.count > 0 && this.count <= TIME_COUNT) {
							this.count--;
						} else {
							this.show = true;
							clearInterval(this.timer);
							this.timer = null;
						}
					}, 1000);
				}
			}
		}
	};
</script>