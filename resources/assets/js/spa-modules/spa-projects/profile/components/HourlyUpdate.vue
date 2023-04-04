<template>
	<div>
		<el-dialog :visible.sync="hourlyActive" :before-close="close" class="profile-desc-dialog">
			<span slot="title" class="display-title">
				<div class="title-icon">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						width="24"
						height="24"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="1.5"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="feather feather-dollar-sign"
					>
						<line x1="12" y1="1" x2="12" y2="23" />
						<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
					</svg>
				</div>
				<span class="title">Update hourly rate</span>
			</span>
			<el-form
				@submit.native.prevent="saveHourlyRate('ruleForm')"
				:model="ruleForm"
				:rules="rules"
				ref="ruleForm"
				label-width="120px"
				class="demo-ruleForm"
			>
				<el-form-item label="Hourly Rate" prop="hourly_rate">
					<el-input type="number" v-model="ruleForm.hourly_rate"></el-input>
				</el-form-item>
			</el-form>
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">
					<div>
						<el-button
							class="canacel-creative"
							:loading="loading"
							type="primary"
							@click.prevent="saveHourlyRate('ruleForm')"
						>Save</el-button>
						<el-button class="canacel-creative" :disabled="loading" @click="close">Close</el-button>
					</div>
				</div>
			</span>
		</el-dialog>
	</div>
</template>

<script>
	export default {
		props: ["hourlyActive", "data"],
		data() {
			return {
				loading: false,
				ruleForm: {
					hourly_rate: ""
				},
				rules: {
					hourly_rate: [
						{
							required: true,
							message: "Please input hourly rate",
							trigger: "blur"
						}
					]
				}
			};
		},
		watch: {
			hourlyActive(val) {
				if (!val) return;
				if (!this.data) return;
				this.getData();
			}
		},
		methods: {
			getData() {
				if (this.data && this.data.hourly_rate) {
					this.ruleForm.hourly_rate = this.data.hourly_rate;
				} else {
					this.ruleForm.hourly_rate = 0;
				}
			},
			close() {
				this.loading = false;
				this.$emit("close");
			},
			saveHourlyRate(formName) {
				this.$refs[formName].validate(valid => {
					if (valid) {
						this.loading = true;
						let payload = {
							hourly_rate: this.ruleForm.hourly_rate,
							username: this.data.profile_id,
						}
						this.$store
							.dispatch("updateHourlyRate", payload)
							.then(() => this.close());
					} else {
						return false;
					}
				});
			}
		}
	};
</script>