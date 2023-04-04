<template>
	<div id="teams-member">
		<h3
			class="hidden-sm-and-up text-center"
			style="text-transform:uppercase;font-weight:bold"
		>My Team members</h3>
		<transition name="el-zoom-in-top">
			<owned-teams
				:teams="teams"
				:loading="teamLoading"
				@currentTeam="currentTeamTable"
			/>
		</transition>

		<transition name="el-zoom-in-top">
			<pending-invitations
				v-if="invitations.length>0"
				:invitations="invitations"
				@fetchPendingInvitations="getPendingInvitations"
				:loading="tableLoading"
			/>
		</transition>

		<transition name="el-zoom-in-top">
			<el-row :gutter="12">
				<el-col :span="12">
					<team-members v-if="currentTeam.users" :team="currentTeam" />
				</el-col>
				<el-col :span="12">
					<pending v-if="currentTeam.users" :team="currentTeam" />
				</el-col>
			</el-row>
		</transition>
	</div>
</template>

<script>
	import moment from "moment";
	import TeamMembers from "./components/TeamMembers";
	import Pending from "./components/Pending";
	import OwnedTeams from "./components/OwnedTeams";
	import PendingInvitations from "./components/PendingInvitations";
	import { mapGetters } from "vuex";

	export default {
		components: {
			TeamMembers,
			Pending,
			OwnedTeams,
			PendingInvitations
		},
		data() {
			return {
				tableData: [],
				currentTeam: {},
				tableLoading: false,
				invitations: []
			};
		},
		methods: {
			currentTeamTable(row) {
				if (row.users && !this.currentTeam.users) {
					this.currentTeam = row;
				} else {
					this.currentTeam = {};
				}
			},
			getPendingInvitations() {
				this.tableLoading = true;
				axios.get("/settings/invitations/pending").then(response => {
					this.tableLoading = false;
					this.invitations = response.data;
				});
			},
			handle_error(errors) {
				this.$notify.error({
					title: "Error",
					message: "An error occurs!"
				});
			}
		},
		computed: {
			...mapGetters(["teams", "teamLoading"])
		},
		mounted() {
			this.getPendingInvitations();
		}
	};
</script>
<style lang="scss">
#teams-member {
	$white: #fafafa;
	$black: #545c64;
	$red: #ef5b5b;
	$green: #80b4a0;
	$grey: #c0c4cc;
	$border-color: rgba(0, 0, 0, 0.1);
	$box-shadow: 0 2px 12px 0 $border-color;
	.name-avatar {
		display: flex;
		align-items: center;
		.el-avatar {
			box-shadow: $box-shadow;
			margin-right: -8px;
		}
	}
	.box-card {
		margin-bottom: 24px;
	}
}
</style>
