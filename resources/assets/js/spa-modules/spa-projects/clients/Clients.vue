<template>
	<div>
		<h3
			class="hidden-sm-and-up text-center"
			style="text-transform:uppercase;font-weight:bold"
		>My Users</h3>
		<el-card class="box-card">
			<el-table :data="tableDataFilter" style="width: 100%">
				<el-table-column label="Name" sortable>
					<template slot-scope="scope">
						<div class="name-avatar">
							<el-avatar style="margin-right:6px" :src="scope.row.photo_url" :size="32"></el-avatar>
							{{scope.row.name}}
						</div>
					</template>
				</el-table-column>
				<el-table-column label="Email" sortable prop="email"></el-table-column>
				<!-- <el-table-column label="Company" sortable prop="company"></el-table-column> -->
				<!-- <el-table-column label="Website" sortable prop="website" v-if="isFreelancer"></el-table-column> -->
				<el-table-column label="Phone" sortable prop="phone"></el-table-column>
				<el-table-column align="right">
					<template slot="header" slot-scope="scope">
						<el-input v-model="search" size="mini" placeholder="Type to search" />
					</template>
				</el-table-column>
			</el-table>
		</el-card>
	</div>
</template>

<script>
	import moment from "moment";
	import { mapGetters } from "vuex";

	export default {
		data() {
			return {
				tableData: [],
				search: "",
				dialogVisible: false,
				dialogLoading: false,
				dialogData: {}
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
				axios
					.get("/api/my-users")
					.then(response => {
                        this.tableData = _.uniqBy(response.data, "email");
					})
					.catch(error => {
						console.log(error);
						// this.handle_error(error);
					});
			}
		},
		computed: {
			tableDataFilter() {
				if (this.tableData && this.tableData.length > 0) {
					return this.tableData.filter(
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
					return this.tableData;
				}
			},
			...mapGetters(["isFreelancer"])
		},
		mounted() {
			this.fetch();
		}
	};
</script>
<style lang="scss" scoped>
.name-avatar {
	display: flex;
	align-items: center;
}
</style>
