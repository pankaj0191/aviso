<template>
	<div id="notification">
		<div class="tw-text-lg tw-font-semibold tw-text-gray-900">Notification Settings</div>

		<el-form :model="form" status-icon ref="form" class="demo-form">
			<ProofloCard>
				<template #body>
					<div v-if="stateLoading" class="tw-text-center">
						<i class="tw-py-6 el-icon-loading"></i>
					</div>
					<div v-else>
						<el-form-item label="New project is created">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.new_project"></el-switch>
						</el-form-item>
						<el-form-item label="New issue is posted">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.new_issue"></el-switch>
						</el-form-item>
						<el-form-item label="Issue status changed">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.issue_status"></el-switch>
						</el-form-item>
						<el-form-item label="New comment is posted">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.new_comment"></el-switch>
						</el-form-item>
						<el-form-item label="Corrections are submitted">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.new_correction"></el-switch>
						</el-form-item>
						<el-form-item label="New revision round is uploaded">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.new_revision"></el-switch>
						</el-form-item>
						<el-form-item label="Project is approved">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.approved_project"></el-switch>
						</el-form-item>
						<el-form-item label="Project is completed">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.completed_project"></el-switch>
						</el-form-item>
						<el-form-item label="Project is review">
							<el-switch :active-value="1" :inactive-value="0" v-model="form.review_project"></el-switch>
						</el-form-item>
					</div>
				</template>
				<template #footer>
					<el-button class="tw-btn" :loading="loading" type="primary" @click="update('form')">Update Notifications</el-button>
				</template>
			</ProofloCard>
		</el-form>
	</div>
</template>

<script>
	import { mapGetters } from "vuex";
	export default {
		data() {
			return {
				form: {
					new_project: 0,
					new_issue: 0,
					issue_status: 0,
					new_comment: 0,
					new_correction: 0,
					new_revision: 0,
					approved_project: 0,
					completed_project: 0,
					review_project: 0
				},
				loading: false
			};
		},
		computed: {
			...mapGetters(["user", "stateLoading"])
		},
		watch: {
			user(val) {
				this.fetchNotifcations(val);
			}
		},
		methods: {
			fetchNotifcations(val) {
				this.form.new_project = val.email_notifications
					? val.email_notifications.new_project
					: 0;
				this.form.new_issue = val.email_notifications
					? val.email_notifications.new_issue
					: 0;
				this.form.issue_status = val.email_notifications
					? val.email_notifications.issue_status
					: 0;
				this.form.new_comment = val.email_notifications
					? val.email_notifications.new_comment
					: 0;
				this.form.new_correction = val.email_notifications
					? val.email_notifications.new_correction
					: 0;
				this.form.new_revision = val.email_notifications
					? val.email_notifications.new_revision
					: 0;
				this.form.approved_project = val.email_notifications
					? val.email_notifications.approved_project
					: 0;
				this.form.completed_project = val.email_notifications
					? val.email_notifications.completed_project
					: 0;
				this.form.review_project = val.email_notifications
					? val.email_notifications.review_project
					: 0;
			},
			update(formName) {
				this.loading = true;
				this.$refs[formName].validate(async valid => {
					if (valid) {
						let { data } = await axios.post(
							`/api/profile/update/notifcation`,
							this.form
						);
						toastr["success"](data.message, "Success");
						this.$store.commit("UPDATE_NOTIFICATION", data);
						this.loading = false;
					} else {
						this.loading = false;
						return false;
					}
				});
			}
		},
		mounted() {
			this.fetchNotifcations(this.user);
		}
	};
</script>

<style lang="scss">
#notification {
	.el-form-item__label {
		width: 250px;
		text-align: start;
	}
    .el-form-item {
        margin-bottom: 6px;
    }
}
</style>