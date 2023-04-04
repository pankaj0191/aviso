<template>
	<el-card class="box-card">
		<div slot="header" class="clearfix">
			<h4 style="font-weight: 700;">Team Member ({{team.name}})</h4>
		</div>
		<el-table :data="membersFilters" style="width: 100%">
			<el-table-column label="Name" sortable>
				<template slot-scope="scope">
					<div class="name-avatar">
						<!-- <el-avatar style="margin-right:6px" :src="scope.row.photo_url" :size="32"></el-avatar> -->
                        <span class="tw-relative tw-inline-block">
                            <img class="tw-h-6 tw-w-6 tw-rounded-full" :src="scope.row.photo_url" alt="" />
                            <span v-if="scope.row.pivot.role == 'owner'" class="tw-absolute tw-top-0 tw-right-0 tw-block tw-h-1.5 tw-w-1.5 tw-rounded-full tw-ring-white-2 tw-ring-white" style="background-color: #4ade80;" />
                        </span>
                        <span class="tw-pl-2">
                            {{scope.row.name | truncate('15', '...')}}
                        </span>
					</div>
				</template>
			</el-table-column>
			<el-table-column label="Email" sortable prop="email"></el-table-column>
			<el-table-column label="Role" class-name="tw-capitalize" sortable prop="pivot.role">
                <template slot-scope="scope">
					<el-tag size="small" :type="scope.row.pivot.role =='owner' ? 'success' : 'info'">{{ scope.row.pivot.role }}</el-tag>
				</template>
            </el-table-column>
			<el-table-column align="right">
                <template slot-scope="scope">
						<el-tooltip class="item" effect="dark" content="Delete Member" placement="bottom">
							<el-popconfirm
								confirmButtonText="OK"
								v-if="team.owner.id == user.id && isBothRole && scope.row.pivot.role != 'owner'"
								@confirm="handleLeave(scope.$index, scope.row)"
								cancelButtonText="No, Thanks"
								icon="el-icon-info"
								iconColor="red"
								title="Are you sure to leave team?"
							>
								<el-button slot="reference" size="mini" circle icon="el-icon-delete"></el-button>
							</el-popconfirm>
						</el-tooltip>
						<el-tooltip class="item" effect="dark" content="Delete Member" placement="bottom">
							<el-popconfirm
								confirmButtonText="OK"
								v-if="team.owner.id != user.id && isBothRole && scope.row.pivot.role != 'owner' && scope.row.id == user.id"
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
</template>

<script>
import { mapGetters } from "vuex";
	export default {
        props: ["team"],
        data() {
            return {
                search: ''
            }
        },
        methods: {
			handleLeave(index, row) {
				this.dialogLoading = true;
				axios
					.delete(`/settings/teams/${this.team.id}/members/${row.id}`)
					.then(response => {
						this.dialogLoading = false;
						this.dialogVisible = false;
						this.$notify({
                            title: "Success",
							message: response.data.message,
							type: "success"
						});
						this.team.users.splice(index, 1);
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
        },
		computed: {
            ...mapGetters(["user", "isBothRole"]),
			membersFilters() {
				if (this.team.users && this.team.users.length > 0) {
					return this.team.users.filter(
						data =>
							!this.search ||
							data.name
								.toLowerCase()
								.includes(this.search.toLowerCase()) ||
							data.email
								.toLowerCase()
								.includes(this.search.toLowerCase())
					);
				} else {
					if (this.team.users && this.team.users.length > 0) {
						return this.team.users;
					} else {
						var members = [];
						return members;
					}
				}
			}
		}
	};
</script>