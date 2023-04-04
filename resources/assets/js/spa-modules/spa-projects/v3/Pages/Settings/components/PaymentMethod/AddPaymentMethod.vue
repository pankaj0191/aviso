<template>
	<div id="add-payment-method">
		<ProofloCard>
			<template #body>
				<div class="tw-text-md tw-font-semibold tw-text-gray-700">
					<div class="tw-py-8">
						<label class="tw-text-gray-400 tw-uppercase tw-text-sm">Card Name:</label>
						<input v-model="name" type="text" class="stripe-card-name" placeholder="ex: John Doe" />
						<div class="el-form-item__error tw-relative" v-if="nameError">{{nameError}}</div>
					</div>
					<label class="tw-text-gray-400 tw-uppercase tw-text-sm">Card Details:</label>

					<stripe-element-card
						ref="elementRef"
						@element-ready="elementReady"
						:classes="{ 'focus': 'stripe-element--focus', 'base': 'stripe-element'}"
						:tokenData="{
                                name: name,
                            }"
						:pk="publishableKey"
						@token="tokenCreated"
					/>
				</div>
			</template>
			<template #footer>
				<el-button
					@click="submit"
					:loading="loading"
					:disabled="!cardReady"
					type="primary"
					class="tw-btn"
					size="medium"
				>Add credit card</el-button>
			</template>
		</ProofloCard>
	</div>
</template>

<script>
	import { mapActions } from "vuex";
	import { StripeElementCard } from "@vue-stripe/vue-stripe";
	export default {
		components: {
			StripeElementCard
		},
		data() {
			this.publishableKey = "pk_test_QblZAc4qD5SD77QpoF7cA2hL";
			return {
				name: "",
				form: {
					stripe_token: ""
				},
				loading: false,
				cardReady: false,
				nameError: ""
			};
		},
		methods: {
			submit() {
				if (this.name.length < 3) {
					return (this.nameError = "The card name is required.");
				}
				// this will trigger the process
				this.$refs.elementRef.submit();
			},

			createPaymentMethod(token) {
				this.loading = true;
				let self = this;
				this.form.stripe_token = token;
				axios
					.post("/api/settings/add-payment-method", this.form)
					.then(response => {
						self.loading = false;
						if (response.data.status) {
							toastr["success"](response.data.message, "Success");
							self.updatePaymentMethods({
								card: response.data.data,
								type: "add"
							});
						} else {
							toastr["error"](response.data.errors, "Error");
						}
					})
					.catch(error => {
						self.loading = false;
						toastr["error"]("Internal server error");
					});
			},

			elementReady(card) {
				if (card) {
					this.cardReady = true;
				}
			},

			tokenCreated(token) {
				this.createPaymentMethod(token.id);
			},

			...mapActions(["updatePaymentMethods"])
		}
	};
</script>

<style lang="scss" >
#add-payment-method {
	.stripe-element--focus {
		border: 1px solid #80b4a0;
	}
	.stripe-element {
		box-sizing: border-box;
		height: 40px;
		padding: 10px 12px;
		border: 1px solid #f3f4f6;
		border-radius: 4px;
		-webkit-transition: box-shadow 150ms ease;
		transition: box-shadow 150ms ease;
	}
	.stripe-card-name {
		display: block;
		width: 100%;
		height: 36px;
		padding: 10px 12px;
		font-size: 14px;
		border-radius: 4px;
		line-height: 1.6;
		border: 1px solid #f3f4f6;
		-webkit-transition: box-shadow 150ms ease;
		transition: box-shadow 150ms ease;
		&:focus,
		&:active {
			border: 1px solid #80b4a0;
			outline: 0;
		}
		&::placeholder {
			/* Chrome, Firefox, Opera, Safari 10.1+ */
			color: #e1e6ea;
			opacity: 1; /* Firefox */
		}
	}
}
</style>