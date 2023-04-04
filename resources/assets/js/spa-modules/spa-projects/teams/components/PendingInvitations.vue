<template>
	<div>
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<h4 style="font-weight: 700;">Invitations</h4>
			</div>
			<el-table v-loading="loading" :data="invitations" style="width: 100%">
				<el-table-column label="Team Owner" sortable prop="team.name"></el-table-column>
				<el-table-column label="Status" sortable>
					<template slot-scope="scope">
						<el-tag size="small" type="warning">Pending</el-tag>
					</template>
				</el-table-column>
				<el-table-column align="right">
					<template slot-scope="scope">
						<span>
							<el-button size="mini" icon="el-icon-check" @click="accept(scope.row)">Accept</el-button>
						</span>
						<el-popconfirm
							confirmButtonText="OK"
							@confirm="reject(scope.row)"
							cancelButtonText="No, Thanks"
							icon="el-icon-info"
							iconColor="red"
							title="Are you sure to delete this?"
						>
							<el-button slot="reference" size="mini" icon="fa fa-times">Reject</el-button>
						</el-popconfirm>
					</template>
				</el-table-column>
			</el-table>
		</el-card>
	</div>
</template>

<script>
	export default {
		props: ["invitations", "loading"],

		methods: {
			getPendingInvitations() {
				this.$emit("fetchPendingInvitations");
			},
			/**
			 * Accept the given invitation.
			 */
			accept(invitation) {
				axios
					.post(`/settings/invitations/${invitation.id}/accept`)
					.then(() => {
						Bus.$emit("updateTeams");

						this.getPendingInvitations();
					});

				this.removeInvitation(invitation);
			},

			/**
			 * Reject the given invitation.
			 */
			reject(invitation) {
				axios
					.post(`/settings/invitations/${invitation.id}/reject`)
					.then(() => {
						this.getPendingInvitations();
					});

				this.removeInvitation(invitation);
			},

			/**
			 * Remove the given invitation from the list.
			 */
			removeInvitation(invitation) {
				this.invitations = _.reject(
					this.invitations,
					i => i.id === invitation.id
				);
			}
		}
	};
</script>

<style>
</style>