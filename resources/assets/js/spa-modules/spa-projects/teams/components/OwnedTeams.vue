<template>
	<div>
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<div style="display:inline-block">
					<div style="font-weight: 700;font-size:16px">Team Owner</div>
                    <a @click="$router.back()" href.prevent="#" class="tw-text-normal tw-text-primary tw-hover-text-primary tw-cursor-pointer">Go Back</a>
				</div>
				<div style="float:right" class="tw-flex tw-items-center">
                    <h5 class="upgrade-background tw-shadow">
                        <span style="color: #fcbd01; font-weight: bold">
                            {{this.exitingEmails.length + this.pendingEmails.length}}
                            of {{activePlan.teams_members_count - 1}} member invited.
                        </span>
                        <span>Upgrade to add more</span>
                        <router-link :to="{name: 'setting-billing'}"><el-tag class="upgrade-button">Upgrade</el-tag></router-link>
                    </h5>
				</div>
			</div>
			<el-table :data="teamsFilter" style="width: 100%" v-loading="loading" >
				<el-table-column label="Name" sortable>
					<template slot-scope="scope">
						<div class="name-avatar">
							<el-avatar style="margin-right:6px" :src="scope.row.photo_url" :size="32"></el-avatar>
							{{scope.row.name}}
						</div>
					</template>
				</el-table-column>
				<el-table-column label="Email" sortable prop="owner.email"></el-table-column>
				<el-table-column label="Owner" sortable>
					<template slot-scope="scope">
						<div v-if="scope.row.owner.id == user.id">You</div>
						<div v-else>{{scope.row.owner.name}}</div>
					</template>
				</el-table-column>
				<el-table-column label="Members" sortable>
					<template slot-scope="scope">
						<div class="name-avatar">
							<el-avatar v-for="(user,key) in scope.row.users" :key="key" :src="user.photo_url" :size="24"></el-avatar>
						</div>
					</template>
				</el-table-column>
				<el-table-column label="Invite" sortable>
					<template slot-scope="scope">
						<el-button
							v-if="scope.row.owner.id == user.id"
							type="primary"
							size="small"
							icon="el-icon-plus"
							@click.native="showMembersDialog(scope.row)"
						>Invite</el-button>
						<div v-else>You Not owner</div>
					</template>
				</el-table-column>
				<el-table-column align="right">
					<template slot="header" slot-scope="scope">
                        <div v-if="canCreateMoreTeams">
                            <el-button
                                type="primary"
                                size="small"
                                @click="openCreateTeamDialog"
                                icon="el-icon-plus"
                            >Reminding Teams ({{ remainingTeams }})</el-button>
                        </div>
					</template>
					<template slot-scope="scope">
						<span>
							<el-tooltip class="item" effect="dark" content="Team Information" placement="bottom">
								<el-button
									size="mini"
									circle
									icon="el-icon-info"
									@click="currentTeamTable(scope.row)"
								></el-button>
							</el-tooltip>
						</span>
						<el-tooltip class="item" effect="dark" content="Edit Team" placement="bottom">
							<el-button
								size="mini"
								circle
								v-if="scope.row.owner.id == user.id"
								icon="el-icon-edit"
								@click="openDialog(scope.$index, scope.row)"
							></el-button>
						</el-tooltip>
						<el-tooltip class="item" effect="dark" content="Delete Team" placement="bottom">
							<el-popconfirm
								v-if="scope.row.owner.id == user.id"
								confirmButtonText="OK"
								@confirm="handleDelete(scope.$index, scope.row)"
								cancelButtonText="No, Thanks"
								icon="el-icon-info"
								iconColor="red"
								title="Are you sure to delete team?"
							>
								<el-button slot="reference" size="mini" circle icon="el-icon-delete"></el-button>
							</el-popconfirm>
						</el-tooltip>
						<el-tooltip class="item" effect="dark" content="Leave Team" placement="bottom">
							<el-popconfirm
								confirmButtonText="OK"
								v-if="scope.row.owner.id !== user.id && isBothRole"
								@confirm="handleLeave(scope.$index, scope.row)"
								cancelButtonText="No, Thanks"
								icon="el-icon-info"
								iconColor="red"
								title="Are you sure to leave team?"
							>
								<el-button slot="reference" size="mini" circle icon="fa fa-sign-out"></el-button>
							</el-popconfirm>
						</el-tooltip>
					</template>
				</el-table-column>
			</el-table>
		</el-card>
		<el-dialog
			:title="'Edit Team ' + dialogData.name"
			:visible.sync="dialogVisible"
			width="30%"
		>
			<div class="text-center">
				<el-avatar :src="dialogData.photo_url" :size="128"></el-avatar>
				<div class="form-group">
					<label type="button" class="btn btn-primary btn-upload">
						<span>Select New Photo</span>

						<input ref="photo" type="file" class="form-control" name="photo" @change="updatePhoto" />
					</label>
				</div>
			</div>
			<el-input type="textarea" autosize placeholder="Team Name" v-model="dialogData.name"></el-input>
			<span slot="footer" class="dialog-footer">
				<el-button @click="dialogVisible = false">Cancel</el-button>
				<el-button type="primary" :loading="dialogLoading" @click="update()">Confirm</el-button>
			</span>
		</el-dialog>
		<el-dialog :title="'Create a Team'" :visible.sync="createTeamDialog" width="30%">
			<el-input type="textarea" autosize placeholder="Team Name" v-model="teamName"></el-input>
			<span
				class="help-teams"
				v-if="hasTeamLimit"
			>You currently have {{ remainingTeams }} teams remaining.</span>
			<span slot="footer" class="dialog-footer">
				<el-button @click="createTeamDialog = false">Cancel</el-button>
				<el-button
					type="primary"
					:loading="dialogLoading || !canCreateMoreTeams"
					@click="createTeam()"
				>Confirm</el-button>
			</span>
		</el-dialog>
		<el-dialog
			v-if="activePlan"
			:title="currentTeam.name"
			:show-close="true"
			:visible.sync="showDialogInviteTeamMembers"
			class="team-dialog"
			@open="onOpenDialogNewTeamMembers"
			@close="onCloseDialogNewTeamMembers"
			center
		>
			<invite-team-second
				v-if="showDialogInviteTeamMembers"
				@closeDialog="hideMembersDialog"
				:currentTeam="currentTeam"
			></invite-team-second>
		</el-dialog>
	</div>
</template>

<script>
	import { mapGetters, mapActions } from "vuex";
	import InviteTeamSecond from "../../partials/InviteTeamSecond";

	export default {
		props: ["teams", "loading"],
		components: {
			InviteTeamSecond
		},
		data() {
			return {
				search: "",
				teamName: "",
				dialogVisible: false,
				dialogLoading: false,
				showDialogInviteTeamMembers: false,
				dialogData: {},
				currentTeam: {},
				createTeamDialog: false
			};
		},
    methods: {
			openCreateTeamDialog() {
				this.createTeamDialog = true;
			},
            showMembersDialog(row) {
				if (!this.activePlan || this.activePlan.teams_members_count <= 1) {
					return this.$notify.error({
						title: "Error",
						message: "Upgrade you subscribtion pleas!"
					});
				}
				this.currentTeam = row;
				this.showDialogInviteTeamMembers = true;
			},
			hideMembersDialog() {
				this.showDialogInviteTeamMembers = false;
			},
			onOpenDialogNewTeamMembers() {},
			onCloseDialogNewTeamMembers() {
				this.newTeamMembers = [];
			},
			currentTeamTable(row) {
				this.$emit("currentTeam", row);
			},
			updatePhoto(e) {
				e.preventDefault();

				if (!this.$refs.photo.files.length) {
					return;
				}

				var self = this;

				// We need to gather a fresh FormData instance with the profile photo appended to
				// the data so we can POST it up to the server. This will allow us to do async
				// uploads of the profile photos. We will update the user after this action.
				axios
					.post(
						`/settings/teams/${this.dialogData.id}/photo`,
						this.gatherFormData()
					)
					.then(
                        (response) => {
                            // Success Message toaster
                            this.$notify({
                                title: "Success",
                                message: "Team photo updated successfully.",
                                type: "success"
                            });
                            this.dialogData.photo_url = response.data.photo_url;
							// self.form.finishProcessing();
							this.$store.dispatch("loadTeams");
						},
						error => {
							// self.form.setErrors(error.response.data.errors);
						}
					);
			},
			update() {
				this.dialogLoading = true;
				axios
					.put(
						`/settings/teams/${this.dialogData.id}/name`,
						this.dialogData
					)
					.then(() => {
						this.$store.dispatch("loadTeams");
						this.bootstrap();
						this.dialogVisible = false;
						this.dialogLoading = false;
					})
					.catch(() => {
						this.dialogLoading = false;
						this.dialogVisible = false;
					});
			},
			gatherFormData() {
				const data = new FormData();

				data.append("photo", this.$refs.photo.files[0]);

				return data;
			},
			createTeam() {
				this.dialogLoading = true;
				axios
					.post(`/settings/teams`, {
						name: this.teamName
					})
					.then(() => {
						this.createTeamDialog = false;
						this.dialogLoading = false;
						this.teamName = "";
						this.getActiveSubscription();
						this.bootstrap();
						this.$store.dispatch("loadTeams");
					})
					.catch(() => {
						this.dialogLoading = false;
						this.createTeamDialog = false;
					});
			},
			openDialog(index, row) {
				this.dialogData = row;
				this.dialogVisible = true;
			},
			handleDelete(index, row) {
				this.dialogLoading = true;
				axios
					.delete(`/settings/teams/${row.id}`)
					.then(response => {
						this.dialogLoading = false;
						this.dialogVisible = false;
						this.teams.splice(this.teams.indexOf(row), 1);
						this.getActiveSubscription();
						this.$notify({
							title: "Success",
							message: response.data.message,
							type: "success"
						});
					})
					.catch(error => {
						this.dialogLoading = false;
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error"
						});
					});
			},
			handleLeave(index, row) {
				this.dialogLoading = true;
				axios
					.delete(`/settings/teams/${row.id}/members/${this.user.id}`)
					.then(response => {
						this.dialogLoading = false;
						this.dialogVisible = false;
						this.teams.splice(this.teams.indexOf(row), 1);
						this.$notify({
							title: "Success",
							message: response.data.message,
							type: "success"
						});
					})
					.catch(error => {
						this.dialogLoading = false;
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error"
						});
					});
			},
			...mapActions(["getActiveSubscription", "bootstrap"])
    },
		computed: {
			teamsFilter() {
				if (this.teams && this.teams.length > 0) {
					return this.teams.filter(
						data =>
							!this.search ||
							data.name
								.toLowerCase()
								.includes(this.search.toLowerCase()) ||
							data.owner.email
								.toLowerCase()
								.includes(this.search.toLowerCase())
					);
				} else {
					if (this.teams && this.teams.length > 0) {
						return this.teams;
					} else {
						var teams = [];
						return teams;
					}
				}
			},
			hasTeamLimit() {
				if (!this.activePlan) {
					return;
				}
				return this.activePlan.teams_count;
			},
			remainingTeams() {
				return this.activePlan
					? this.activePlan.teams_count - this.ownedTeams.length
					: 0;
			},

			canCreateMoreTeams() {
				return this.remainingTeams > 0;
			},
			...mapGetters(["user", "isBothRole", "activePlan", "ownedTeams", "pendingEmails", "exitingEmails", "membersLimit"])
    },
    watch: {
        user(val) {
            // console.log(val);
            if (this.user.current_team_id) {
                const team = this.teams.find(team => team.id === this.user.current_team_id)
                console.log(team);
                this.currentTeamTable(team);
            }
        },
        teams(val) {
            if (this.teams.length > 0 && this.user.current_team_id) {
                const team = this.teams.find(team => team.id === this.user.current_team_id)
                this.currentTeamTable(team);
            }
        }
    },
    mounted() {
        if (this.teams.length > 0 && this.user) {
            const team = this.teams.find(team => team.id === this.user.current_team_id)
            this.currentTeamTable(team);
        }
    }
	};
</script>

<style lang="scss" scoped>
.help-teams {
	display: block;
	color: #a4aaae;
	font-size: 12px;
}

.upgrade-background {
    background: rgb(255, 253, 222);
    padding: 8px 24px;
    border-radius: 6px;

}
</style>