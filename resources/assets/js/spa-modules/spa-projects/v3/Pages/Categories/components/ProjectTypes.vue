<template>
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <div class="card-header">
                <div>
                    <h5>Project Type Samples</h5>
                </div>
                <el-button size="mini" type="primary" @click="addDialog = true">Create Project Type</el-button>
            </div>
        </div>
        <el-table :data="optionsFilters" style="width: 100%">
            <el-table-column label="Name" sortable prop="name"></el-table-column>
            <el-table-column label="Category" sortable prop="category.name"></el-table-column>
            <el-table-column label="Image">
                <template slot-scope="scope">
                    <img width="48" :src="`${spacePrefix}/${scope.row.image}`" alt="">
                </template>
            </el-table-column>
            <el-table-column label="Dimantions" sortable>
                <template slot-scope="scope">
                    <div>{{scope.row.width}} * {{scope.row.height}}</div>
                </template>
            </el-table-column>
            <el-table-column label="Files" sortable>
                <template slot-scope="scope">
                    <div v-if="scope.row.files.length > 0" class="capitalize">{{scope.row.files.length}}</div>
                    <div v-else>N/A</div>
                </template>
            </el-table-column>
            <el-table-column label="Size Type" sortable>
                <template slot-scope="scope">
                    <el-tag v-if="scope.row.size_type" class="capitalize">{{scope.row.size_type}}</el-tag>
                    <el-tag v-else>N/A</el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Active">
                <template slot-scope="scope">
                    <div v-if="scope.row.active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            aria-labelledby="check-circle" role="presentation" class="fill-current text-success">
                            <path
                                d="M12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-2.3-8.7l1.3 1.29 3.3-3.3a1 1 0 0 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-2-2a1 1 0 0 1 1.4-1.42z">
                            </path>
                        </svg>
                    </div>
                    <div v-else>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            aria-labelledby="x-circle" role="presentation" class="fill-current text-danger">
                            <path
                                d="M4.93 19.07A10 10 0 1 1 19.07 4.93 10 10 0 0 1 4.93 19.07zm1.41-1.41A8 8 0 1 0 17.66 6.34 8 8 0 0 0 6.34 17.66zM13.41 12l1.42 1.41a1 1 0 1 1-1.42 1.42L12 13.4l-1.41 1.42a1 1 0 1 1-1.42-1.42L10.6 12l-1.42-1.41a1 1 0 1 1 1.42-1.42L12 10.6l1.41-1.42a1 1 0 1 1 1.42 1.42L13.4 12z">
                            </path>
                        </svg>
                    </div>
                </template>
            </el-table-column>
            <el-table-column align="right">
                <template slot="header" slot-scope="scope">
                    <el-input v-model="search" size="mini" placeholder="Type to search" />
                </template>
                <template slot-scope="scope">
                    <el-tooltip v-if="!scope.row.active" class="item" effect="dark" content="Active" placement="bottom">
                        <el-button size="mini" circle icon="el-icon-check" class="text-success" :loading="actionLoading.includes(scope.row.id)" @click="handleAction(scope.$index, scope.row, true)">
                        </el-button>
                    </el-tooltip>
                    <el-tooltip v-else class="item" effect="dark" content="Deactivate" placement="bottom">
                        <el-button size="mini" circle icon="el-icon-close" class="text-danger" :loading="actionLoading.includes(scope.row.id)" @click="handleAction(scope.$index, scope.row, false)">
                        </el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="Edit Team" placement="bottom">
                        <el-button size="mini" circle icon="el-icon-edit" @click="openDialog(scope.$index, scope.row)">
                        </el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="Upload Template" placement="bottom">
                        <el-button size="mini" :type="scope.row.files.length > 0 ? 'primary': 'default'" circle icon="el-icon-paperclip" @click="openFileDialog(scope.$index, scope.row)">
                        </el-button>
                    </el-tooltip>
                    <el-tooltip class="item" effect="dark" content="Delete Team" placement="bottom">
                        <el-popconfirm confirmButtonText="OK" @confirm="handleDelete(scope.$index, scope.row)"
                            cancelButtonText="No, Thanks" icon="el-icon-info" iconColor="red"
                            title="Are you sure to delete team?">
                            <el-button slot="reference" :loading="actionLoading.includes(scope.row.id)" size="mini" circle icon="el-icon-delete"></el-button>
                        </el-popconfirm>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
        <create-project-type @addProjectType="addProjectType" @addDialog="addDialogStatus" :addDialog="addDialog"
            :categories="categories" />
        <edit-project-type @editProjectType="editProjectType" @editDialog="editDialogStatus" :editDialog="editDialog"
            :dialogData="dialogData" :categories="categories" />
            <upload-file @fileDialog="fileDialogStatus" :fileDialog="fileDialog"
            :dialogData="dialogData" :categories="categories" />
    </el-card>
</template>

<script>
    import CreateProjectType from "./components/CreateProjectType"
    import EditProjectType from "./components/EditProjectType"
    import UploadFile from "./components/UploadFile"

    export default {
        props: ['options', 'categories'],
        components: {
            CreateProjectType,
            EditProjectType,
            UploadFile
        },
        data() {
            return {
                search: "",
                actionLoading: [],
                addDialog: false,
                editDialog: false,
                fileDialog: false,
                dialogData: {}
            }
        },
        methods: {
            addDialogStatus(status) {
                return this.addDialog = status
            },
            editDialogStatus(status) {
                return this.editDialog = status
            },
            fileDialogStatus(status) {
                return this.fileDialog = status
            },
            openDialog(index, row) {
                this.dialogData = row;
                this.editDialog = true;
            },
            openFileDialog(index, row) {
                this.dialogData = row;
                this.fileDialog = true;
            },
            async handleDelete(index, row) {
                this.actionLoading.push(row.id)
                try {
                    let {
                        data
                    } = await axios.delete(`/api/projects/project-type/${row.id}`)
                    this.options.splice(index, 1);
                    this.actionLoading.splice(this.actionLoading.indexOf(row.id), 1)
                    toastr["success"](data.message, "Success");
                } catch (error) {
                    this.actionLoading.splice(this.actionLoading.indexOf(row.id), 1)
                    console.log(error)
                }
            },
            editProjectType(data) {
                let index = this.options.findIndex(option => option.id == data.id);
                console.log(index)
                this.options.splice(index, 1, data);
            },
            addProjectType(data) {
                this.options.unshift(data);
            },
            async handleAction(index, row, status) {
                this.actionLoading.push(row)
                try {
                    let {
                        data
                    } = await axios.post("/api/projects/active/project-type", {
                        status: status,
                        id: row.id
                    })
                    this.actionLoading.splice(this.actionLoading.indexOf(row), 1)
                    this.options.splice(index, 1, data);
                } catch (error) {
                    this.actionLoading.splice(this.actionLoading.indexOf(row), 1)
                    console.log(error)
                }
            },
        },
        computed: {
            optionsFilters() {
                if (this.options && this.options.length > 0) {
                    return this.options.filter(
                        data =>
                        !this.search ||
                        data.name
                        .toLowerCase()
                        .includes(this.search.toLowerCase())
                    );
                } else {
                    return this.options;
                }
            },
            spacePrefix() {
                return spacePrefix
            },
        }
    }
</script>

<style lang="scss" scoped>
    .text-success {
        color: #21b978;
    }

    .fill-current {
        fill: currentColor;
    }

    .text-danger {
        color: #e74444;
    }

    .box-card {
        margin-bottom: 54px;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .capitalize {
        text-transform: capitalize;
    }
</style>