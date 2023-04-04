<template>
    <el-card class="box-card">
        <div slot="header" class="clearfix">
            <h5>Categories</h5>
        </div>
        <el-table :data="optionsFilters" style="width: 100%">
            <el-table-column label="Name" sortable prop="name"></el-table-column>
            <el-table-column label="Image">
                <template slot-scope="scope">
                    <img width="48" :src="`${spacePrefix}/${scope.row.image}`" alt="">
                </template>
            </el-table-column>
            <el-table-column label="Active">
                <template slot-scope="scope">
                    <div v-if="scope.row.profiles.length > 0">
                        <div v-if="scope.row.profiles[0].pivot.active">
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
                    <el-button :loading="actionLoading.includes(scope.row.id)"
                        v-if="scope.row.profiles.length == 0 || (scope.row.profiles.length > 0 && !scope.row.profiles[0].pivot.active)"
                        size="mini" @click="handleAction(scope.$index, scope.row.id, true)">Active</el-button>
                    <el-button :loading="actionLoading.includes(scope.row.id)"
                        v-if="scope.row.profiles.length > 0 && scope.row.profiles[0].pivot.active" size="mini" type="danger"
                        @click="handleAction(scope.$index, scope.row.id, false)">Deactivate</el-button>
                </template>
            </el-table-column>
        </el-table>
    </el-card>
</template>

<script>
    export default {
        props: ['options'],
        data() {
            return {
                search: "",
                actionLoading: [],
            }
        },
        methods: {
            async handleAction(index, id, status) {
                this.actionLoading.push(id)
                try {
                    let {data} = await axios.post("/api/projects/active/category", {
                        status: status,
                        id: id
                    })
                    this.actionLoading.splice(this.actionLoading.indexOf(id), 1)
                    this.options.splice(index, 1, data);
                } catch (error) {
                    this.actionLoading.splice(this.actionLoading.indexOf(id), 1)
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
        margin-bottom: 24px;
    }
</style>