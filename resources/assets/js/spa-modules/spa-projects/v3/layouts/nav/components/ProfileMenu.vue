<template>
	<el-dropdown trigger="click" @command="handleCommand">
		<span class="el-dropdown-link">
			<div class="navbar-user">
				<div class="user-details" v-if="user.name">
					<div class="hidden-sm-and-down">
						<div class="user-name">{{user.current_profile.name}}</div>
						<small v-if="isFreelancer && isSubscribed">Freelancer (subscribed)</small>
						<small v-if="isFreelancer && !isSubscribed">
							Freelancer
							<router-link
								tag="a"
								:to="{name: 'setting-billing'}"
								class="dropdown-link tw-text-primary tw-hover-text-primary"
							>upgrade</router-link>
						</small>
						<small v-if="currentRole == 'Collaborator'">Collaborator</small>
						<small v-if="currentRole == 'Client'">Client</small>
						<small v-if="currentRole == 'Agency' && isSubscribed">Agency</small>
						<small v-if="currentRole == 'Agency' && !isSubscribed">
							Agency
							<router-link
								:to="{name: 'setting-billing'}"
								tag="a"
								class="dropdown-link tw-text-primary tw-hover-text-primary"
							>upgrade</router-link>
						</small>
					</div>
				</div>
				<el-avatar
					v-if="!user.photo_url"
					src="https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png"
				></el-avatar>
				<el-avatar v-if="user.photo_url" :src="user.photo_url"></el-avatar>
			</div>
		</span>
		<el-dropdown-menu slot="dropdown" style="min-width:240px;max-width:300px;">
			<el-dropdown-item command="profile" v-if="isBothRole" icon="el-icon-user">Profile</el-dropdown-item>
			<el-dropdown-item command="settings" icon="fa fa-fw fa-btn fa-cog">Settings</el-dropdown-item>
			<el-dropdown-item command="nova" v-if="nova" icon="el-icon-menu">Prooflo Admin</el-dropdown-item>
			<!-- <el-dropdown-item
				command="impersonator"
				v-if="impersonator"
				icon="fa fa-fw fa-btn fa-user-secret"
			>
				Back To
				My Account
			</el-dropdown-item> -->
			<!-- <el-dropdown-item command="add-profile" divided icon="el-icon-circle-plus-outline">
                Add Account
            </el-dropdown-item> -->
			<el-dropdown-item :command="{action: 'add-profile'}" disabled divided style="color:#9fa6b2;font-size:12px">
                <div class="tw-flex tw-justify-between tw-items-center">
                    <div>Accounts</div>
                    <router-link :to="{name: 'create_account'}" tag="div" class="tw-flex tw-py-1 tw-px-1 tw-items-center tw-cursor-pointer add-button tw-text-primary tw-text-xs">Add Account <i class="tw-ml-2 el-icon-circle-plus-outline tw-text-sm" style="margin-right:0px"></i></router-link>
                </div>
            </el-dropdown-item>
			<el-dropdown-item
				:command="{action: 'switch-profile', profile_id: profile.id}"
				:icon="user.current_profile_id == profile.id ? 'el-icon-circle-check' : 'el-icon-user'"
				:class="user.current_profile_id == profile.id ? 'profile-active' : ''"
				v-for="(profile, key) in sortedProfiles"
				:key="key"
			>
				{{profile.name | truncate(12, '...')}}
				<span class="profile-style">{{profile.profile_type.name}}</span>
			</el-dropdown-item>
			<el-dropdown-item
                :command="{action: 'add-team'}"
				divided
				disabled
				v-if="teams.length > 0"
				style="color:#9fa6b2;font-size:12px"
			>
            <div class="tw-flex tw-justify-between tw-items-center">
                    <div>Teams</div>
                    <router-link :to="{name: 'teams'}" tag="div" class="tw-flex tw-py-1 tw-px-1 tw-items-center tw-cursor-pointer add-button tw-text-primary tw-text-xs">Add Team <i class="tw-ml-2 el-icon-circle-plus-outline tw-text-sm" style="margin-right:0px"></i></router-link>
                </div>
        </el-dropdown-item>
			<el-dropdown-item
				:command="{action: 'switch-team', team_id: team.id}"
				:icon="user.current_team_id == team.id ? 'el-icon-circle-check' : 'el-icon-user'"
				:class="user.current_team_id == team.id ? 'profile-active' : ''"
				v-for="(team, key) in teams"
				:key="'team-' + key"
			>{{team.name | truncate(18, '...')}}</el-dropdown-item>
			<el-dropdown-item command="logout" divided icon="el-icon-circle-close">Logout</el-dropdown-item>
		</el-dropdown-menu>
	</el-dropdown>
</template>

<script>
	import { mapGetters } from "vuex";
	export default {
		data() {
			return {
				// impersonator: null
			};
		},
		computed: {
			...mapGetters([
				"user",
				"nova",
				"spark",
				"support",
				"isFreelancer",
				"teams",
				"isBothRole",
				"isSubscribed",
				"currentRole"
            ]),
            sortedProfiles() {
                // Sort profiles by role first agency next freelancer next client last collaborator
                if (!this.user.profiles || this.user.profiles.length === 0) {
                    return [];
                }
                let profiles = this.user.profiles;
                profiles.sort((a, b) => {
                    if (a.profile_type.value == 'agency') {
                        return -1;
                    } else if (b.profile_type.value == 'agency') {
                        return 1;
                    } else if (a.profile_type.value == 'freelancer') {
                        return -1;
                    } else if (b.profile_type.value == 'freelancer') {
                        return 1;
                    } else if (a.profile_type.value == 'client') {
                        return -1;
                    } else if (b.profile_type.value == 'client') {
                        return 1;
                    } else if (a.profile_type.value == 'collaborator') {
                        return -1;
                    } else if (b.profile_type.value == 'collaborator') {
                        return 1;
                    }
                    return 0;
                });
                return profiles;
            }
		},
		methods: {
			handleCommand(command) {
				switch (command) {
					case "profile":
						return this.$router.push({
							name: "profile",
							params: {
								username: this.user.current_profile_id
							}
						});
						break;
					case "settings":
						return this.$router.push({
							name: "setting-profile"
						});
						break;
					case "nova":
						return (window.location.href = "/admin");
						break;
					case "add-profile":
						return this.$router.push({ name: "create_account" });
						break;
					case "logout":
						return (window.location.href = "/logout");
						break;
                }
                if (!command.action) {
                    return;
                }
				if (command.action == "switch-profile") {
					return this.switchProfile(command.profile_id);
				}
				if (command.action == "switch-team") {
					return this.switchTeam(command.team_id);
				}
			},
			switchProfile(profile_id) {
				axios
					.post("/api/profile/switch-profile", { profile_id: profile_id })
					.then(response => {
						if (response.data.status == 1) {
							window.location.reload();
							toastr["success"](response.data.message, "Success");
							this.$store.commit(
								"UPDATE_USER_CURRENT_PROFILE",
								response.data
							);
						} else {
						}
					})

					.catch(error => {
						// this.handle_error({});
					});
			},
			switchTeam(team_id) {
				axios
					.post("/api/profile/switch-team", { team_id: team_id })
					.then(response => {
						if (response.data.status == 1) {
							window.location.reload();
							toastr["success"](response.data.message, "Success");
							this.$store.commit(
								"UPDATE_USER_CURRENT_TEAM",
								response.data
							);
						} else {
						}
					})

					.catch(error => {
						// this.handle_error({});
					});
			},
			// impersonatorCheck() {
			// 	axios
			// 		.get("/impersonator-check")
			// 		.then(response => {
			// 			if (response.data > 0) {
			// 				this.impersonator = true;
			// 			} else {
			// 				this.impersonator = false;
			// 			}
			// 		})
			// 		.catch(error => {
			// 			console.log(error);
			// 			// this.handle_error(error);
			// 		});
			// }
		},
		filters: {
			truncate: function(text, length, suffix) {
				if (text.length > length) {
					return text.substring(0, length) + suffix;
				} else {
					return text;
				}
			}
		},
		created() {
			// this.impersonatorCheck();
		}
	};
</script>

<style lang="scss" scoped>
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;

.navbar-user {
	display: flex;
	align-items: center;

	.user-details {
		text-align: right;
		display: block !important;
		line-height: 1.25 !important;

		.user-name {
			font-weight: 600 !important;
		}
	}

	.el-avatar {
		margin-left: 0.75rem !important;
		box-shadow: $box-shadow;
	}
}
.profile-style {
	font-size: 10px;
	padding: 2px 4px;
	background-color: #edf2f7;
	margin-left: 4px;
	border-radius: 6px;
}
.add-button {
    pointer-events: auto;
    // border-radius: 4px;
    // background-color: #80B4A0
}
</style>