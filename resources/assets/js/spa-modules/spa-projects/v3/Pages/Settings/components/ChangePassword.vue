<template>
	<div>
		<div class="tw-text-lg tw-font-semibold tw-text-gray-900">Change Password</div>
		<el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" class="demo-ruleForm">
			<ProofloCard>
				<template #body>
					<el-form-item prop="currentPassword">
						<label for="current_password" class="tw-pb-0 tw-mb-0 tw-block tw-mx-0">Current Password</label>
						<el-input
							type="current_password"
							show-password
                            size="medium"
							id="current_password"
							name="current_password"
							v-model="ruleForm.currentPassword"
							autocomplete="off"
						></el-input>
						<ServerErorrs
							v-if="serverErrors.current_password"
							:serverErrors="serverErrors.current_password"
						/>
					</el-form-item>

					<el-form-item prop="password">
						<label for="password" class="tw-pb-0 tw-mb-0 tw-block tw-mx-0">New Password</label>
						<el-input
							type="password"
							show-password
                            size="medium"
							id="password"
							name="password"
							v-model="ruleForm.password"
							autocomplete="off"
						></el-input>
						<ServerErorrs v-if="serverErrors.password" :serverErrors="serverErrors.password" />
					</el-form-item>

					<el-form-item prop="password_confirmation">
						<label for="confirm_passsword" class="tw-pb-0 tw-mb-0 tw-block tw-mx-0">Confirm Password</label>
						<el-input
							type="password_confirmation"
							show-password
							id="password_confirmation"
                            size="medium"
							name="password_confirmation"
							v-model="ruleForm.password_confirmation"
							autocomplete="off"
						></el-input>
						<ServerErorrs
							v-if="serverErrors.password_confirmation"
							:serverErrors="serverErrors.password_confirmation"
						/>
					</el-form-item>
				</template>
				<template #footer>
					<el-button
						class="tw-btn"
						:loading="loading"
						type="primary"
						@click="update('ruleForm')"
					>Update Password</el-button>
				</template>
			</ProofloCard>
		</el-form>
	</div>
</template>

<script>
	import ServerErorrs from "./partials/ServerErorrs.vue";

	export default {
		components: {
			ServerErorrs
		},
		data() {
			var validatePass = (rule, value, callback) => {
				if (value === "") {
					callback(new Error("Please input the password"));
				} else {
					if (this.ruleForm.password_confirmation !== "") {
						this.$refs.ruleForm.validateField("password_confirmation");
					}
					callback();
				}
			};
			var validatePass2 = (rule, value, callback) => {
				if (value === "") {
					callback(new Error("Please input the password again"));
				} else if (value !== this.ruleForm.password) {
					callback(new Error("Two inputs don't match!"));
				} else if (this.serverErrors.length > 0) {
					callback(new Error(this.serverErrors["confirm_passsword"][0]));
				} else {
					callback();
				}
			};
			return {
				ruleForm: {
					currentPassword: "",
					password: "",
					password_confirmation: ""
				},
				rules: {
					password: [{ validator: validatePass, trigger: "blur" }],
					password_confirmation: [
						{ validator: validatePass2, trigger: "blur" }
					],
					currentPassword: [{ required: true }]
				},
				serverErrors: [],
				loading: false
			};
		},
		methods: {
			update(formName) {
				this.loading = true;
				const payload = {
					current_password: this.ruleForm.currentPassword,
					password: this.ruleForm.password,
					password_confirmation: this.ruleForm.password_confirmation
				};
				this.$refs[formName].validate(async valid => {
					if (valid) {
						try {
							let { data } = await axios.post(
								`/api/profile/update/password`,
								payload
							);
							toastr["success"](data.message, "Success");
							this.loading = false;
						} catch (error) {
							this.serverErrors = error.response.data.errors;
							toastr["warning"](
								error.response.data.message,
								"Success"
							);
							this.loading = false;
						}
					} else {
						this.loading = false;
						return false;
					}
				});
			}
		}
	};
</script>

<style>
</style>