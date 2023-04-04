<template>
	<div v-loading.fullscreen.lock="fullscreenLoading">
		<el-row type="flex" justify="center">
			<el-col>
				<el-row style="text-align: center">
					<h3>Invite Team Member</h3>
					<i style="font-style: italic;">Please add new team Member here by email</i>
				</el-row>

				<!--Team members-->
				<el-row style="margin-top: 20px" type="flex" justify="center">
					<div>
						<!--Exiting Members-->
						<div
							v-if="currentTeam.users.length > 0"
							v-for="member in currentTeam.users"
							:key="member.id"
							style="margin-top: 10px"
						>
							<el-row type="flex" align="middle" justify="center" v-if="member.id != user.id">
								<el-col :xs="3" :sm="3" :md="3">
									<img :src="member.photo_url" class="img-circle special-img team-circle" />
								</el-col>
								<el-col :xs="17" :sm="18" :md="18" style="padding: 0 20px;white-space: nowrap;">
									<el-popover
										placement="top-start"
										width="300"
										trigger="hover"
										:content="member.email"
										:disabled="member.email.length < 25"
									>
										<span slot="reference">{{member.email | truncate(25, '...')}}</span>
									</el-popover>
								</el-col>
								<el-col :xs="4" :sm="3" :md="3">
									<i
										@click="removeTeamMember(currentTeam.id, member.id)"
										class="el-icon-close"
										style="font-weight: bold; cursor:pointer"
									></i>
								</el-col>
							</el-row>
						</div>

						<!--Pending Members-->
						<div
							v-if="invitations.length > 0"
							v-for="invitation in invitations"
							:key="invitation.id"
							style="margin-top: 10px"
						>
							<el-row type="flex" align="middle">
								<el-col :xs="3" :sm="3" :md="3">
									<div
										class="img-circle special-img team-circle"
										style="float: left;"
									>{{invitation.email | pendingFilter}}</div>
								</el-col>
								<el-col :xs="17" :sm="18" :md="18" style="padding: 0 20px;white-space: nowrap;">
									<el-popover
										placement="top-start"
										width="300"
										trigger="hover"
										:content="invitation.email"
										:disabled="invitation.email.length < 25"
									>
										<span slot="reference">{{invitation.email | truncate(25, '...')}}</span>
									</el-popover>
									<span class="pending-invite">pending invite</span>
								</el-col>
								<el-col :xs="4" :sm="3" :md="3">
									<i
										@click="removeTeamMemberInvitation(invitation)"
										class="el-icon-close"
										style="font-weight: bold; cursor:pointer"
									></i>
								</el-col>
							</el-row>
						</div>
					</div>
				</el-row>

				<!--New members-->
				<el-row style="text-align: center; margin-top: 30px" type="flex" justify="center">
					<el-col :md="12">
						<el-tag
							v-for="newTeamMember in newTeamMembers"
							:key="newTeamMember"
							:disable-transitions="false"
							:closable="true"
							@close="handleClose(newTeamMember)"
							style="margin: 5px"
						>{{newTeamMember}}</el-tag>
						<el-row type="flex" justify="center">
							<el-input
								class="input-new-tag"
								v-if="inputVisible"
								v-model="inputValue"
								ref="saveTagInput"
								size="mini"
								@keyup.enter.native="handleInputNewMember"
								@blur="handleInputNewMember"
							></el-input>
							<el-button v-else class="button-new-tag" size="small" @click="showInput">
								+ New Team
								Member
							</el-button>
						</el-row>
					</el-col>
				</el-row>
			</el-col>
		</el-row>
		<el-row style="text-align: center">
			<el-col style="margin-top: 20px">
				<el-button
					type="primary"
					:disabled="newTeamMembers.length == 0 && !inviteDisabled"
					@click="inviteTeamNewMembers"
				>Invite</el-button>
				<el-button @click="closeDialog">Close</el-button>
			</el-col>
		</el-row>
		<el-row class="invite-info">
			<h5>
				<span style="color: #fcbd01; font-weight: bold">
					{{this.exitingEmails.length + this.pendingEmails.length}}
					of {{activePlan.teams_members_count - 1}} member invited.
				</span>
				<span>Upgrade to add more</span>
				<el-tag class="upgrade-button">Upgrade</el-tag>
			</h5>
		</el-row>
	</div>
</template>

<script>
	import { mapActions, mapGetters } from "vuex";
	export default {
		props: ["currentTeam"],
		data() {
			return {
				newTeamMembers: [],
				inputValue: "",
				inputVisible: false,
				inviteDisabled: false,
				fullscreenLoading: false,
				invitations: []
			};
		},
		filters: {
			pendingFilter(text) {
				if (!text) return "";
				text = text.toString();
				return text
					.charAt(0)
					.toUpperCase()
					.substring(0, 1);
			},
			truncate: function(text, length, suffix) {
				if (text.length > length) {
					return text.substring(0, length) + suffix;
				} else {
					return text;
				}
			}
		},
		methods: {
			fetchInvitations() {
				axios
					.get(`/api/teams/invitations/${this.currentTeam.id}`)
					.then(response => {
						this.invitations = response.data.data;
					})
					.catch(error => {});
			},
			closeDialog() {
				this.$emit("closeDialog");
			},
			showInput() {
				this.inputVisible = true;
				this.inviteDisabled = true;
				this.$nextTick(_ => {
					this.$refs.saveTagInput.$refs.input.focus();
				});
			},
			handleInputNewMember() {
				let inputValue = this.inputValue;
				if (inputValue) {
					if (
						this.exitingEmails.length +
							this.pendingEmails.length +
							this.newTeamMembers.length <
						this.activePlan.teams_members_count - 1
					) {
						if (inputValue == this.user.email) {
							toastr["error"](
								"You are the owner of this team",
								"Error"
							);
						} else if (
							this.newTeamMembers.indexOf(inputValue) == -1 &&
							this.exitingEmails.indexOf(inputValue) == -1 &&
							this.pendingEmails.indexOf(inputValue) == -1
						) {
							if (this.isEmail(inputValue)) {
								this.newTeamMembers.push(inputValue);
							} else {
								toastr["error"]("Invalid email", "Error");
							}
						} else {
							toastr["error"]("This email is already exist", "Error");
						}
					} else {
						toastr["error"](
							"Your current plan doesn't allow you to invite more than 5 members",
							"Error"
						);
					}
				}
				this.inputVisible = false;
				this.inviteDisabled = false;
				this.inputValue = "";
			},
			isEmail(string) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(String(string).toLowerCase());
			},
			handleClose(key, keyPath) {
				this.newTeamMembers.splice(this.newTeamMembers.indexOf(key), 1);
			},
			inviteTeamNewMembers() {
				var self = this;
				this.fullscreenLoading = true;
				if (this.newTeamMembers.length > 0) {
					let formData = {
						team_id: self.currentTeam.id,
						emails: self.newTeamMembers
					};
					axios
						.post("/api/teams/invite-members", formData)
						.then(resposne => {
							this.fullscreenLoading = false;
							if (resposne.data.status == 1) {
								self.newTeamMembers = [];
								toastr["success"](resposne.data.message, "Success");
                                this.addInvitation(resposne.data.data);
                                this.fetchInvitations()
							} else {
								toastr["error"](resposne.data.errors, "Error");
							}
						})
						.catch(error => {
							this.fullscreenLoading = false;
							toastr["error"](
								"Something went wrong, please try again later",
								"Error"
							);
						});
				} else {
					toastr["error"]("Please add member email", "Error");
					this.fullscreenLoading = false;
				}
			},
			removeTeamMemberInvitation(invitation) {
				this.$confirm(
					"Are you sure you want to remove this invitation?",
					"Warning",
					{
						confirmButtonText: "Confirm",
						cancelButtonText: "Cancel",
						type: "success",
						title: ""
					}
				).then(() => {
					this.fullscreenLoading = true;
					axios
						.delete(`/settings/invitations/${invitation.id}`)
						.then(response => {
							this.fullscreenLoading = false;
							if (response.status) {
								toastr["success"](
									"Invitation removed successfully",
									"Success"
								);
								this.deleteInvitation(invitation);
							} else {
								toastr["error"](
									"Error removing member, try again later",
									"Error"
								);
							}
						})
						.catch(error => {
							this.fullscreenLoading = false;
							toastr["error"](
								"Something went wrong, try again later",
								"Error"
							);
						});
				});
			},
			removeTeamMember(teamId, memberId) {
				this.$confirm(
					"Are you sure you want to remove this member from team?",
					"Warning",
					{
						confirmButtonText: "Confirm",
						cancelButtonText: "Cancel",
						type: "success",
						title: ""
					}
				).then(() => {
					this.fullscreenLoading = true;
					axios
						.delete(
							`/settings/${Spark.pluralTeamString}/${teamId}/members/${memberId}`
						)
						.then(response => {
							this.fullscreenLoading = false;
							if (response.status) {
								toastr["success"](
									"Member removed successfully",
									"Success"
								);
								this.deleteTeamMembers(memberId);
							} else {
								toastr["error"](
									"Error removing member, try again later",
									"Error"
								);
							}
						})
						.catch(error => {
							this.fullscreenLoading = false;
							toastr["error"](
								"Something went wrong, try again later",
								"Error"
							);
						});
				});
			},
			...mapActions([
				"addInvitation",
				"deleteInvitation",
				"deleteTeamMembers"
			])
		},
		computed: {
			...mapGetters([
				"user",
				"isFreelancer",
				"activeSubscription",
				"activePlan",
				"project_listing_type",
				"unread_comments",
				"ownedTeam",
				"teamMembers",
				"exitingEmails",
				"pendingEmails",
				"membersLimit",
				"is_freelancer",
				"is_collaborator",
				"projects",
				"in_progress",
				"on_revision",
				"approved",
				"completed",
				"on_draft",
				"on_hold",
				"member_projects",
				"current_member",
				"windowBreakPoint"
			])
		},
		created() {
			this.fetchInvitations();
		}
	};
</script>

<style>
</style>