<template>
    <el-autocomplete :autofocus="true" popper-class="my-autocomplete" style="width:100%" v-if="searchAble"
        placeholder="Type something" :fetch-suggestions="querySearch" :trigger-on-focus="false"
        @select="handleSearchSelect" prefix-icon="el-icon-search" v-model="search" size="small">
        <template slot-scope="{ item }">
            <div class="autocomplete-item">
                <el-avatar v-if="item.active_revision.proofs.length > 0" shape="square" size="large"
                    :src="`${spacePrefix}/` + item.active_revision.proofs[0].project_files.path">
                </el-avatar>
                <el-avatar v-else shape="square" size="large"
                    src="https://cube.elemecdn.com/9/c2/f0ee8a3c7c9638a54940382568c9dpng.png">
                </el-avatar>
                <div class="autocomplete-content">
                    <div class="project-name" v-html="$options.filters.highlight(item.name, search)">{{ item.name }}
                    </div>
                    <div class="footer-content">
                        <div class="project-company">{{ item.company }}</div>
                        <div class="project-company">{{projectDate(item.created_at)}}</div>
                    </div>
                </div>
            </div>
        </template>
    </el-autocomplete>
</template>

<script>
    export default {
        props: ['projects', 'searchAble'],
        data() {
            return {
                search: "",
            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix;
            }
        },
        filters: {
            highlight(words, query) {
                var iQuery = new RegExp(query, "ig");
                return words.toString().replace(iQuery, function (matchedTxt, a, b) {
                    return ('<span class=\'highlight\'>' + matchedTxt + '</span>');
                });
            }
        },
        methods: {
            handleSearchSelect(project) {
                this.openProofer(project.id, project.active_revision.id, project);
            },
            openProofer(project_id, revision_id, project) {
                this.$router.push({
                    name: "proofer",
                    params: {
                        project_id: project_id,
                        revision_id: revision_id,
                        project: project
                    }
                });
            },
            projectDate(date) {
                if (!date) return "";
                var utc = moment.utc(date).toDate();
                return moment(utc)
                    .local()
                    .format("MMMM DD, YYYY");
            },
            querySearch(queryString, cb) {
                if (Object.entries(this.projects).length > 0) {
                    var results = queryString ?
                        _.filter(this.projects, this.createFilter(queryString)) :
                        this.projects;
                    // call callback function to return suggestions
                    console.log(results);
                    cb(results);
                }
            },
            createFilter(queryString) {
                return project => {
                    return (
                        project.name
                        .toLowerCase()
                        .includes(queryString.toLowerCase())
                    );
                };
            },
        },
    }
</script>

<style lang="scss">
    .highlight {
        background-color: #c1ffe7;
        font-weight: 700;
    }

    .my-autocomplete {
        li {
            line-height: normal;
            padding: 7px;

            .autocomplete-item {
                display: flex;
                align-items: center;

                .autocomplete-content {
                    width: 90%;
                    margin-left: 8px;

                    .project-name {
                        font-weight: 500;
                        font-size: 16px;
                        color: #1a202c;
                    }

                    .footer-content {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;

                        .project-company {
                            font-size: 12px;
                            color: #a0aec0;
                        }
                    }
                }
            }
        }
    }
</style>