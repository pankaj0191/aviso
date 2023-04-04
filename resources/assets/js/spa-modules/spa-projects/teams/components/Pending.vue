<template>
	<el-card class="box-card">
		<div slot="header" class="clearfix">
			<h4 style="font-weight: 700;">Pending Members</h4>
		</div>
		<el-table v-loading="loading" :data="pendingsFilters" style="width: 100%">
			<el-table-column label="Email" sortable prop="email"></el-table-column>
			<el-table-column label="Status" sortable>
				<template slot-scope="scope">
					<el-tag size="small" type="warning">Pending</el-tag>
				</template>
			</el-table-column>
            <el-table-column align="right">
                <template slot="header" slot-scope="scope">
					<el-input v-model="search" size="mini" placeholder="Type to search" />
				</template>
                <template slot-scope="scope">
                    <el-popconfirm
                        confirmButtonText="OK"
                        @confirm="removeTeamMemberInvitation(scope.row)"
                        cancelButtonText="No, Thanks"
                        icon="el-icon-info"
                        iconColor="red"
                        title="Are you sure to delete this member?"
                    >
                        <el-tooltip slot="reference" content="Remove this member">
                            <el-button size="mini" icon="fa fa-times"></el-button>
                        </el-tooltip>
                    </el-popconfirm>
                </template>
            </el-table-column>
		</el-table>
	</el-card>
</template>

<script>
	import { mapActions } from "vuex";
	export default {
		props: ["team"],
		data() {
			return {
				search: "",
				loading: false,
				invitations: []
			};
		},
    methods: {
        removeTeamMemberInvitation(invitation) {
            axios
                .delete(`/settings/invitations/${invitation.id}`)
                .then(response => {
                    if (response.status) {
                        toastr["success"](
                            "Invitation removed successfully",
                            "Success"
                        );
                        this.invitations.splice(this.invitations.indexOf(invitation), 1);
                        this.deleteInvitation(invitation);
                    } else {
                        toastr["error"](
                            "Error removing member, try again later",
                            "Error"
                        );
                    }
                })
                .catch(error => {
                    console.log(error);
                    toastr["error"](
                        "Something went wrong, try again later",
                        "Error"
                    );
                });
			},
			fetchInvitations() {
				this.loading = true;
				axios
					.get(`/api/teams/invitations/${this.team.id}`)
					.then(response => {
						this.loading = false;

						this.invitations = response.data.data;
					})
					.catch(error => {
						this.loading = false;
					});
        },
        ...mapActions([
            "deleteInvitation",
        ])
		},
		computed: {
			pendingsFilters() {
				if (this.invitations && this.invitations.length > 0) {
					return this.invitations.filter(
						data =>
							!this.search ||
							data.email
								.toLowerCase()
								.includes(this.search.toLowerCase())
					);
				} else {
					if (this.invitations && this.invitations.length > 0) {
						return this.invitations;
					} else {
						var invitations = [];
						return invitations;
					}
				}
			}
		},
		created() {
			this.fetchInvitations();
		}
	};
</script>