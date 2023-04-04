<template>
	<div>
		<el-dialog
			:visible.sync="descriptionEditActive"
			:before-close="close"
			class="profile-desc-dialog"
		>
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
						class="feather feather-file-text"
					>
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
						<polyline points="14 2 14 8 20 8" />
						<line x1="16" y1="13" x2="8" y2="13" />
						<line x1="16" y1="17" x2="8" y2="17" />
						<polyline points="10 9 9 9 8 9" />
					</svg>
				</div>
				<span class="title">Profile Description</span>
			</span>
			<el-form
				@submit.native.prevent="saveDescription('ruleForm')"
				:model="ruleForm"
				:rules="rules"
				ref="ruleForm"
				label-width="120px"
				class="demo-ruleForm"
			>
				<el-form-item label="Title" prop="title">
					<el-input v-model="ruleForm.title"></el-input>
				</el-form-item>
				<el-form-item label="Description" prop="desc">
					<el-input type="textarea" :autosize="{ minRows: 8, maxRows: 12}" v-model="ruleForm.desc"></el-input>
				</el-form-item>
			</el-form>
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">
					<div>
						<el-button
							class="canacel-creative"
							:loading="loading"
							type="primary"
							@click.prevent="saveDescription('ruleForm')"
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
		props: ["descriptionEditActive", "data"],
		data() {
			return {
				loading: false,
				ruleForm: {
					id: "",
					title: "",
					desc: ""
				},
				rules: {
					title: [
						{
							required: true,
							message: "Please input title",
							trigger: "blur"
						}
					],
					desc: [
						{
							required: true,
							message: "Please input description form",
							trigger: "blur"
						}
					]
				}
			};
		},
		watch: {
			descriptionEditActive(val) {
				if (!val) return;
				if (!this.data) return;
				this.getData();
			}
		},
		methods: {
			getData() {
				this.ruleForm.id = this.data.id;
				this.ruleForm.title = this.data.title;
				this.ruleForm.desc = this.data.body;
			},
			close() {
				this.loading = false;
				this.$emit("close");
			},
			saveDescription(formName) {
				this.$refs[formName].validate(valid => {
					if (valid) {
						this.loading = true;
						let payload = {
							id: this.ruleForm.id,
							title: this.ruleForm.title,
							desc: this.ruleForm.desc,
							username: this.data.profile_id,
						}
						this.$store
							.dispatch("updateProfileDescription", payload)
							.then(() => this.close());
					} else {
						return false;
					}
				});
			}
		}
	};
</script>

<style>
</style>