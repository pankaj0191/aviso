<template>
	<el-row :gutter="24">
		<el-col :span="24" v-if="index !== 0">
			<hr class="tw-py-2 tw-my-4 tw-mx-8" />
		</el-col>
		<el-col class="text-center" :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
			<div class="tw-flex tw-items-center tw-justify-start tw-pl-6">
				<CardBrand :brand="card.brand" />
				{{card.name}}
			</div>
		</el-col>
		<el-col class="text-center" :xs="6" :sm="6" :md="6" :lg="6" :xl="6">{{card.brand}}({{card.last4}})</el-col>
		<el-col
			class="text-center"
			:xs="6"
			:sm="6"
			:md="6"
			:lg="6"
			:xl="6"
			@click="setAsDefaultCard"
		>{{card.exp_month}}/{{card.exp_year}}</el-col>
		<el-col class="text-center" :xs="6" :sm="6" :md="6" :lg="6" :xl="6">
			<i v-if="removeProcess || settingProcess" class="tw-py-6 el-icon-loading"></i>
			<el-dropdown trigger="click" @command="handleCommand" v-else>
				<span class="el-dropdown-link">
					<i :id="'cardIcon' + index" class="el-icon-more" style="font-size: 24px; color: #949292"></i>
				</span>
				<el-dropdown-menu slot="dropdown">
					<el-dropdown-item
						class="tw-text-primary"
						icon="el-icon-circle-check tw-text-primary"
						v-if="card.id === defaultMethod"
					>Default</el-dropdown-item>
					<el-dropdown-item v-else icon="el-icon-circle-check" command="makeDefault">Make As Default</el-dropdown-item>
					<el-dropdown-item
						command="remove"
						class="color-danger"
						icon="el-icon-delete color-danger"
					>Remove</el-dropdown-item>
				</el-dropdown-menu>
			</el-dropdown>
		</el-col>
	</el-row>
</template>

<script>
	import { mapActions } from "vuex";
	import CardBrand from "../partials/CardBrand";
	export default {
		components: {
			CardBrand
		},
		props: ["card", "index", "defaultMethod", "user"],
		data() {
			return {
				removeProcess: false,
				paymentLoading: false,
				settingProcess: false
			};
		},
		methods: {
			handleCommand(command) {
				command == "makeDefault" && this.setAsDefaultCard();
				command == "remove" && this.removePaymentMethod();
			},
			removePaymentMethod() {
				this.$confirm(
					"Are you sure you want to remove this payment method?",
					"Remove Payment method",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning",
						lockScroll: false
					}
				)
					.then(() => {
						let self = this;
						this.removeProcess = true;
						axios
							.post("/api/settings/remove-payment-method", this.card)
							.then(response => {
								self.removeProcess = false;
								if (response.data.status) {
									toastr["success"](
										response.data.message,
										"Success"
									);
									self.paymentLoading = true;
									this.$store.commit(
										"removePaymentMethod",
										this.card
									);
								} else {
									toastr["error"](response.data.errors, "Error");
								}
							})
							.catch(error => {
								self.removeProcess = false;
							});
					})
					.catch(() => {
						toastr["error"]("Delete canceled");
					});
			},
			setAsDefaultCard() {
				this.$confirm(
					"Are you sure you want to set this payment method as default?",
					"Set as default payment method",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning",
						lockScroll: false
					}
				)
					.then(() => {
						let self = this;
						this.settingProcess = true;
						axios
							.post("/api/settings/set-as-default-payment", this.card)
							.then(response => {
								self.settingProcess = false;
								if (response.data.status) {
									toastr["success"](
										response.data.message,
										"Success"
									);
									self.paymentLoading = true;
									self.getPaymentMethods(self.user);
								} else {
									toastr["error"](response.data.errors, "Error");
								}
							})
							.catch(error => {
								self.settingProcess = false;
								toastr["error"]("Internal server error");
							});
					})
					.catch(() => {
						this.$message({
							type: "info",
							message: "Delete canceled"
						});
					});
			},

			...mapActions(["getPaymentMethods"])
		}
	};
</script>

<style>
</style>