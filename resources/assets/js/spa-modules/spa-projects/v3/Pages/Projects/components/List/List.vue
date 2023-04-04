
<template>
  <!--LIST TYPE-->
		<el-row :gutter="10">
			<div class="project-list" id="list">
				<table class="vs-table vs-table--tbody-table table-responsive" v-loading="loading">
					<thead class="vs-table--thead">
						<tr style="text-align: center">
							<th scope="col">Project</th>
							<th scope="col">Team</th>
							<th scope="col">Collaborators</th>
							<th scope="col">Revision Round</th>
							<th scope="col">Progress</th>
						</tr>
					</thead>
					<tbody v-for="(project, index, key) in projects" :key="project.id" >
                            <list-item @openInProgressDialog="openInProgressDialog" @openAddTeamMemberDialog="openAddTeamMemberDialog" @openCreativeBriefModal="openCreativeBriefModal" @loadingMethod="loadingMethod"  :project="project" :index="index" />
					</tbody>
				</table>
			</div>
		</el-row>
</template>

<script>
import ListItem from './components/ListItem.vue'
export default {
    props: ['projects'],
    data() {
        return {
            loading: false
        }
    },
    components: { ListItem },
    methods: {
        loadingMethod(status) {
            if (status == true) {
                this.loading = true
            } else {
                this.loading = false
            }
        },
        openInProgressDialog(project_id) {
                this.$emit('openInProgressDialog', project_id)
        },
        openAddTeamMemberDialog(project) {
            this.$emit('openAddTeamMemberDialog', project)
        },
        openCreativeBriefModal(project) {
            this.$emit('openCreativeBriefModal', project)
        },
    }

}
</script>

<style lang="scss">

th,
td {
	text-align: center;
	vertical-align: middle !important;
}
th {
	font-weight: bold !important;
}

tbody tr:hover {
	background: rgba(255, 255, 255, 0.22) !important;
}

tr td:first-child {
	border-top-left-radius: 5px;
	border-bottom-left-radius: 5px;
}
tr td:last-child {
	border-top-right-radius: 5px;
	border-bottom-right-radius: 5px;
}
.has-new-actions-card {
	border: 3px solid rgb(250, 115, 115) !important;
}
.has-new-actions-list {
	box-shadow: inset 0 0 0 1px rgb(250, 115, 115);
	border-radius: 5px;
}
tr,
td {
	border: none !important;
}

.vs-table--header {
	display: flex;
	flex-wrap: wrap;
	margin-left: 1.5rem;
	margin-right: 1.5rem;

	> span {
		display: flex;
		flex-grow: 1;
	}

	.vs-table--search {
		padding-top: 0;

		.vs-table--search-input {
			font-size: 1rem;

			& + i {
				left: 1rem;
			}

			&:focus + i {
				left: 1rem;
			}
		}
	}
}

.vs-table {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0 1.3rem;
	padding: 0 1rem;

	tr {
		background-color: #fff;
		box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);

		td {
			padding: 10px;

			&:first-child {
				border-top-left-radius: 0.5rem;
				border-bottom-left-radius: 0.5rem;
			}

			&:last-child {
				border-top-right-radius: 0.5rem;
				border-bottom-right-radius: 0.5rem;
			}
		}

		td.td-check {
			padding: 20px !important;
		}
	}
}

.vs-table--thead {
	th {
		padding-top: 0;
		padding-bottom: 0;

		.vs-table-text {
			text-transform: uppercase;
			font-weight: 600;
		}
	}

	tr {
		background: none;
		box-shadow: none;
	}
}

.project-list .el-progress-bar__outer {
	background-color: #545c64 !important;
}
.project-list .add-project-block {
	text-decoration: none;
	border: 2px dashed #bdbbbb;
	border-radius: 10px;
	height: 100%;
}
.show {
	display: block;
}
</style>