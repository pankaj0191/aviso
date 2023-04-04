<template>
	<div>
		<h3
			class="hidden-sm-and-up text-center"
			style="text-transform:uppercase;font-weight:bold"
		>Time Tracker</h3>
		<el-card class="box-card">
			<div slot="header" class="clearfix">
				<div style="display:inline-block">
					Total Time:
					<span class="total-time">{{totalDuration}}</span>
				</div>
				<!-- <el-link icon="el-icon-delete" type="danger" style="margin-left:4px">Bulk Delete</el-link> -->

				<el-select style="float: right;" size="mini" v-model="filter" placeholder="Select" clearable>
					<el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.value"></el-option>
				</el-select>
			</div>
			<el-table
				:data="filterTable.filter(data => !search || data.client.toLowerCase().includes(search.toLowerCase()))"
				:default-sort="{prop: 'project', order: 'descending'}"
				style="width: 100%"
				v-loading="loading"
			>
				<el-table-column label="Project" sortable prop="project.name"></el-table-column>
				<el-table-column label="Client" sortable>
					<template
						slot-scope="scope"
						v-if="scope.row.project && scope.row.project.user_role_client.length > 0"
					>{{scope.row.project.user_role_client[0].name}}</template>
				</el-table-column>
				<el-table-column label="Team" sortable>
					<template
						slot-scope="scope"
						v-if="scope.row.project && scope.row.project.client && scope.row.project.client.owned_teams.length > 0"
					>{{scope.row.project.client.owned_teams[0].name}}</template>
				</el-table-column>
				<el-table-column label="Team Members" sortable>
					<template
						slot-scope="scope"
						v-if="scope.row.project && scope.row.project.user_role_freelancer && scope.row.project.user_role_freelancer.length > 0"
					>
						<div class="name-avatar">
							<el-avatar
								v-for="(user,key) in scope.row.project.user_role_freelancer"
								:key="key"
								:src="user.photo_url"
								:size="24"
							></el-avatar>
						</div>
					</template>
				</el-table-column>
				<el-table-column label="Duration" sortable>
					<template slot-scope="scope" v-if="tableData.length > 0">{{getDuration(scope.row)}}</template>
				</el-table-column>
				<el-table-column align="right">
					<template slot="header" slot-scope="scope">
						<el-input v-model="search" size="mini" placeholder="Type to search" />
					</template>
					<template slot-scope="scope">
						<span>
							<el-tooltip class="item" effect="dark" content="Create invoice" placement="bottom">
								<router-link :to="{name: 'invoice', params: {id: scope.row.id}}">
									<el-button size="mini" circle icon="el-icon-document"></el-button>
								</router-link>
							</el-tooltip>
						</span>
						<el-button
							size="mini"
							circle
							icon="el-icon-edit"
							@click="openDialog(scope.$index, scope.row)"
						></el-button>
						<el-popconfirm
							confirmButtonText="OK"
							@confirm="handleDelete(scope.$index, scope.row)"
							cancelButtonText="No, Thanks"
							icon="el-icon-info"
							iconColor="red"
							title="Are you sure to delete this?"
						>
							<el-button slot="reference" size="mini" circle icon="el-icon-delete"></el-button>
						</el-popconfirm>
					</template>
				</el-table-column>
			</el-table>
		</el-card>
		<el-dialog :title="'Edit '" :visible.sync="dialogVisible" width="30%" :before-close="handleClose">
			<el-input
				type="textarea"
				autosize
				placeholder="Write description"
				v-model="dialogData.description"
			></el-input>
			<span slot="footer" class="dialog-footer">
				<el-button @click="dialogVisible = false">Cancel</el-button>
				<el-button type="primary" :loading="dialogLoading" @click="updateDialog()">Confirm</el-button>
			</span>
		</el-dialog>
	</div>
</template>

<script>
	import moment from "moment";
	export default {
		data() {
			return {
				tableData: [],
				search: "",
				filterTimeTracker: "",
				filter: "",
				dialogVisible: false,
				dialogLoading: false,
				loading: false,
				dialogData: {},
				options: [
					{
						value: "days",
						label: "Day"
					},
					{
						value: "weeks",
						label: "Week"
					},
					{
						value: "months",
						label: "Month"
					},
					{
						value: "years",
						label: "Year"
					}
				]
			};
		},
		methods: {
			handle_error(errors) {
				this.$notify.error({
					title: "Error",
					message: "An error occurs!"
				});
			},
			fetch() {
				this.loading = true;
				axios
					.get("/api/projects/project-timers")
					.then(response => {
						this.loading = false;
						this.tableData = response.data;
					})
					.catch(error => {
						this.loading = false;
						console.log(error);
						// this.handle_error(error);
					});
			},
			updateDialog() {
				this.dialogLoading = true;
				axios
					.put(`/api/projects/project-timers/${this.dialogData.id}`, {
						description: this.dialogData.description
					})
					.then(response => {
						this.dialogLoading = false;
						if (response.data.status) {
							this.dialogVisible = false;
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success"
							});
						} else {
							this.dialogLoading = false;
							this.$notify({
								title: "Error",
								message: response.data.errors,
								type: "error"
							});
						}
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
			getDuration(row) {
				return moment.utc(row.duration * 1000).format("HH:mm:ss");
			},
			handleDelete(index, row) {
				this.dialogLoading = true;
				axios
					.delete(`/api/projects/project-timers/${row.id}`)
					.then(response => {
						this.dialogLoading = false;
						if (response.data.status) {
							this.dialogVisible = false;
							this.tableData.splice(this.tableData.indexOf(row), 1);
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success"
							});
						} else {
							this.dialogLoading = false;
							this.$notify({
								title: "Error",
								message: response.data.errors,
								type: "error"
							});
						}
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
			handleCommandFilter(command) {
				this.filterTimeTracker = command;
			},
			handleClose(done) {
				this.$confirm("Are you sure you want to close this?")
					.then(_ => {
						done();
					})
					.catch(_ => {});
			},
			openDialog(index, row) {
				this.dialogData = row;
				this.dialogVisible = true;
			},
			handleClose(done) {
				this.$confirm("Are you sure to close this dialog?")
					.then(_ => {
						done();
					})
					.catch(_ => {});
			}
		},
		computed: {
			isFreelancerRole() {
				if (!this.$store.state.users.isFreelancer) {
					return this.$router.push({name: "projects"});
				} else {
					return true;
				}
			},
			totalDuration() {
				let total = 0;
				this.tableData.forEach(data => {
					return (total += data.duration);
				});
				return moment.utc(total * 1000).format("HH:mm:ss");
			},
			filterTable() {
				if (this.filter) {
					return this.tableData.filter(data => {
						return moment(data.start).isSameOrAfter(
							moment()
								.subtract(1, this.filter)
								.format("YYYY-MM-DD HH:mm:ss")
						) && (!this.$route.query.issue || data.issue_id == this.$route.query.issue)
					});
				} else {
					return this.tableData.filter(data => !this.$route.query.issue || data.issue_id == this.$route.query.issue);
				}
			}
		},
		mounted() {
			this.fetch();
		}
	};
</script>
<style lang="scss" scoped>
.total-time {
	font-size: 18px;
	color: #6a6c71;
	font-weight: 700;
	display: block;
}
.name-avatar {
	display: flex;
	align-items: center;
	.el-avatar {
		margin-right: -8px;
	}
}
</style>
