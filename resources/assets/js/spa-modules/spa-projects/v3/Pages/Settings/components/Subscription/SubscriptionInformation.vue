<template>
	<div>
		<ProofloCard>
			<template #header>
				<div class="tw-flex tw-justify-between">
					<h2 class="tw-px-6 tw-text-xl tw-font-semibold tw-text-gray-700">Subscription Information</h2>
				</div>
			</template>
			<template #body>
				<div>
					<div class="text-center">
						<i v-if="stateLoading" class="tw-py-6 el-icon-loading"></i>
						<el-button
							v-if="!stateLoading && paymentMethods.length === 0"
							class="tw-btn"
							type="primary"
							@click="createPaymentMethod"
						>Create New Payment Method</el-button>
					</div>
					<el-radio-group v-model="cardItem" class="tw-w-full">
						<transition-group name="list">
							<el-radio
								border
								class="tw-w-full tw-mx-0 tw-my-1"
								v-for="card in paymentMethods"
								:key="card.id"
								:label="card.id"
							>
								<CardItem :card="card" :defaultMethod="defaultMethod" />
							</el-radio>
						</transition-group>
					</el-radio-group>
				</div>
			</template>
			<template #footer>
				<div class="tw-flex tw-items-center tw-justify-between" v-if="!activeSubscription">
					<div>Total: ${{plan.price}}</div>
					<el-button
						class="tw-btn"
						:disabled="paymentMethods.length === 0 || stateLoading"
						type="primary"
						:loading="subscribeProccess"
						@click="subscribeWithExitingCard"
					>(${{plan.price}}) Pay</el-button>
				</div>
                <div v-else class="tw-flex tw-items-center tw-justify-between">
                    <span class="tw-text-sm color-danger" >You are goin to change your subscription plan!</span>
                    <el-button
                        class="tw-btn"
                        :disabled="paymentMethods.length === 0 || stateLoading"
                        type="primary"
                        :loading="subscribeProccess"
                        @click="subscribeWithExitingCard"
                    >Pay Now</el-button>
                </div>
			</template>
		</ProofloCard>
	</div>
</template>

<script>
	import CardItem from "./components/CardItem";
	import { mapGetters, mapActions } from "vuex";
	export default {
		props: ["plan"],
		components: {
			CardItem
		},
		data() {
			return {
				cardItem: null,
				subscribeProccess: false
			};
		},
		methods: {
			createPaymentMethod() {
				return this.$router.push({
					name: "setting-payment-methods"
				});
			},
			// Subscribe with exiting payment method
			subscribeWithExitingCard() {
				self = this;
				this.$confirm(
					`Are you sure you want to subscribe Now?`,
					"Subscribe",
					{
						confirmButtonText: `${(this.activeSubscription &&
							"Swap Plan") ||
							"Pay $(" + this.plan.price + ")"}`,
						cancelButtonText: "Cancel",
						type: "success",
						lockScroll: false
					}
				).then(async () => {
					this.subscribeProccess = true;

					try {
						const payload = {
							card: this.cardItem,
							plan: this.plan
						};
						this.subscribeProccess = true;
						const { data } = await axios.post(
							this.urlForSubscribeWithExitingCard,
							payload
						);
						this.subscribeProccess = false;
						if (data.status) {
							self.selectedPlan = null;
							toastr["success"](data.message, "Success");
							// Bus.$emit("updateUser");
							this.getPaymentMethods(this.user);
							this.getActiveSubscription();
							Bus.$emit("get-receipts");
						} else {
							toastr["error"](data.errors, "Error");
						}
					} catch (error) {
						this.subscribeProccess = false;
						if (error.exception) {
							toastr["error"](
								"Something went wrong, please try again later",
								"Error"
							);
						} else {
							toastr["error"](error.message, "Error");
						}
					}
				});
			},
			...mapActions(["getActiveSubscription", "getPaymentMethods"])
		},
		computed: {
			urlForSubscribeWithExitingCard() {
				return this.activeSubscription
					? "/api/settings/swap-subscription-with-exiting-card"
					: "/api/settings/subscribe-with-exiting-card";
			},
			...mapGetters([
				"paymentMethods",
				"activeSubscription",
				"defaultMethod",
				"user",
				"stateLoading"
			])
		},
		watch: {
			defaultMethod(val) {
				this.cardItem = val;
			}
		},
		mounted() {
			this.user.id && (this.cardItem = this.defaultMethod);
		}
	};
</script>

<style lang="scss">
.el-radio__label {
	vertical-align: middle;
}
.el-radio.is-bordered {
	margin-left: 0px !important;
	margin-right: 0px !important;
}
.el-radio-group {
	border: 1px solid #dcdfe6;
	border-radius: 6px;
	.el-radio {
		margin: 0;
		&.is-checked {
			background-color: #80b4a04d;
		}

		.el-radio__label {
			color: initial !important;
		}
		border: none;
		border-radius: 0;
		&:not(:first-child) {
			border-top: 1px solid #dcdfe6;
		}
	}
}
</style>