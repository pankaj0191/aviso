<template>
	<div>
		<el-card class="box-card">
			<div slot="header">
				<span class="header">Create new account</span>
			</div>
			<el-form
				:model="ruleForm"
				@submit.native.prevent="submitForm('ruleForm')"
				status-icon
				:rules="rules"
				label-width="120px"
				ref="ruleForm"
				class="demo-ruleForm"
				v-if="findRoles()"
			>
				<div class="profile-types">
					<el-form-item prop="profile">
						<el-radio-group v-model="ruleForm.profile">
							<el-radio-button v-if="hasRole('collaborator')" label="Collaborator"></el-radio-button>
							<el-radio-button v-if="hasRole('client')" label="Client"></el-radio-button>
							<el-radio-button v-if="hasRole('freelancer')" label="Freelancer"></el-radio-button>
							<el-radio-button v-if="hasRole('agency')" label="Agency"></el-radio-button>
						</el-radio-group>
					</el-form-item>
				</div>
				<el-row class="profile-name">
					<el-col :xs="24" :sm="16" :md="12" :lg="8" :xl="6">
						<el-form-item prop="name">
							<label>Company Name</label>
							<el-input class="profile-input" placeholder="Type your name" v-model="ruleForm.name"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<div class="profile-button">
					<el-form-item>
						<el-button
							@click="submitForm('ruleForm')"
							type="primary"
							:loading="loading"
						>Create New Account</el-button>
					</el-form-item>
				</div>
			</el-form>
			<div v-else-if="cardLoad" class="text-center">
				<h5>Loading...</h5>
			</div>
			<div class v-else>
				<el-alert
					title="Accounts Limit"
					type="info"
					description="You already have all types of accounts"
					show-icon
				></el-alert>
			</div>
		</el-card>
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<h4 style="font-weight: 700;">Profiles</h4>
			</div>
			<el-alert
				title="You can not remove your current profile"
				style="margin-bottom:12px"
				type="info"
				show-icon
			></el-alert>
			<el-tooltip
				:content="profile.profile_type.name"
				placement="bottom"
				:key="profile.id"
				v-for="profile in user.profiles"
			>
				<el-tag
					:disable-transitions="false"
					v-if="user.current_profile_id != profile.id"
					class="tw-cursor-pointer"
					@click="openDialog(profile)"
				>{{profile.name}}</el-tag>
			</el-tooltip>
			<el-dialog
				:title="`Edit ${dialogData.profile_type ? dialogData.profile_type.name : ''} ${dialogData.name}`"
				:visible.sync="dialogVisible"
				width="30%"
			>
				<el-input type="text" placeholder="Profile Name" v-model="dialogData.name"></el-input>
				<span slot="footer" class="dialog-footer">
					<el-button @click="dialogVisible = false">Cancel</el-button>
					<el-button type="primary" :loading="dialogLoading" @click="update()">Confirm</el-button>
				</span>
			</el-dialog>
		</el-card>
	</div>
</template>

<script>
	import { mapGetters } from "vuex";
	export default {
		data() {
			return {
				ruleForm: {
					name: "",
					profile: ""
				},
				rules: {
					name: [
						{
							required: true,
							message: "Please type name",
							trigger: "blur"
						}
					],
					profile: [
						{
							required: true,
							message: "Please select account type",
							trigger: "change"
						}
					]
				},
				cardLoad: true,
				findRole2: [],
				loading: false,
				inputVisible: false,
				inputValue: "",
				dialogData: {},
				dialogVisible: false,
				dialogLoading: false
			};
		},
		computed: {
			...mapGetters(["user"])
		},
		methods: {
			async handleClose(profile) {
				this.$confirm(
					"This will permanently delete the profile. Continue?",
					"Warning",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning"
					}
				).then(async () => {
					let { data } = await axios.post(
						`/api/profile/delete-profile/${profile.id}`
					);
					if (data.status == 1) {
						this.user.profiles.splice(
							this.user.profiles.indexOf(profile),
							1
						);
						toastr["success"](data.message, "Success");
					}
				});
				toastr["error"](data.errors[0], "Error");
			},
			hasRole(role) {
				if (this.user.profiles) {
					let findRole = this.user.profiles.find(
						profile => profile.profile_type.value == role
					);
					if (findRole) {
						return false;
					} else {
						return true;
					}
				}
			},
			findRoles() {
				if (this.user.profiles) {
					this.cardLoad = false;
					let roleList = [
						"Collaborator",
						"Client",
						"Freelancer",
						"Agency"
					];
					var roles = [];
					roles = _.difference(
						roleList,
						this.user.profiles.map(profile => {
							return profile.profile_type.name;
						})
					);
					return roles.length;
				} else {
					return 0;
				}
			},
			openDialog(row) {
				this.dialogData = row;
				this.dialogVisible = true;
			},
			update() {
				this.dialogLoading = true;
				axios
					.put(
						`/api/profile/update/${this.dialogData.id}`,
						this.dialogData
					)
					.then(() => {
						this.dialogVisible = false;
						this.dialogLoading = false;
						toastr["success"](response.data.message, "Success");
						this.$store.commit("UPDATE_USER_PROFILE", response.data);
					})
					.catch(() => {
						this.dialogLoading = false;
						this.dialogVisible = false;
					});
			},
			submitForm(formName) {
				this.$refs[formName].validate(valid => {
					if (valid) {
						this.loading = true;
						axios
							.post("/api/profile/create-profile", this.ruleForm)
							.then(response => {
								if (response.data.status == 1) {
									this.loading = false;
									this.ruleForm.name = "";
									this.ruleForm.profile = "";
									toastr["success"](
										response.data.message,
										"Success"
									);
									this.$store.commit(
										"UPDATE_USER_PROFILE",
										response.data
									);
								} else {
									this.loading = false;
								}
							})

							.catch(error => {
								this.loading = false;
								// this.handle_error({});
							});
					} else {
						console.log("error submit!!");
						return false;
					}
				});
			}
		}
	};
</script>

<style lang="scss" scoped>
.header {
	font-weight: 600;
	line-height: 30px;
	font-size: 24px;
}

.profile-types {
	margin-bottom: 12px;
}

.profile-name {
	margin-bottom: 24px;

	.profile-input {
		widows: 300px;
	}
}

.profile-button {
	text-align: right;
}

.box-card {
	margin-bottom: 24px;
}
</style>
<style>
.el-tag + .el-tag {
	margin-left: 12px;
}

.button-new-tag {
	margin-left: 12px;
	height: 32px;
	line-height: 30px;
	padding-top: 0;
	padding-bottom: 0;
}

.input-new-tag {
	width: 180px;
	margin-left: 12px;
	vertical-align: bottom;
}
</style>