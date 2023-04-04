<template>
    <div>
        <div class="tw-pl-8 tw-flex tw-items-center tw-my-4">
            <button type="button" @click="methodProjectNameClose(project.id)"
                class="tw-flex tw-items-center tw-px-2 tw-mr-2 tw-py-1 tw-font-semibold tw-rounded-md tw-border-0 tw-text-white button-icon">
                <svg class="svg-icon" focusable="false" viewBox="0 0 32 32"
                    v-if="!projectNameClose.includes(project.id)">
                    <path
                        d="M7.207,13.707L16.5,23l9.293-9.293c0.63-0.63,0.184-1.707-0.707-1.707H7.914C7.023,12,6.577,13.077,7.207,13.707z">
                    </path>
                </svg>
                <svg v-else class="svg-icon" focusable="false" viewBox="0 0 32 32">
                    <path
                        d="M13.707,6.707L23,16l-9.293,9.293C13.077,25.923,12,25.477,12,24.586V7.414C12,6.523,13.077,6.077,13.707,6.707z">
                    </path>
                </svg>
            </button>
            <div class="tw-font-semibold tw-text-gray-900 tw-text-base tw-cursor-pointer"
                @click="methodProjectNameClose(project.id)">{{project.name}}</div>
            <span class="tw-ml-2 tw-bg-gray-100 tw-rounded-md tw-shadow tw-px-2">{{projectDuration(project)}}</span>
            <span class="tw-ml-2 tw-text-sm tw-bg-gray-100 tw-rounded-md tw-shadow tw-px-4 tw-py-2" v-if="isBothRole">{{projectLabel(project)}}</span>
        </div>
        <transition-group name="el-zoom-in-top">
            <template v-for="revision in project.revisions">
                <template v-for="proof in revision.proofs">
                    <template v-for="issue in filterIssues(proof.issues)">
                        <div class="tw-flex tw-font-medium tw-cursor-pointer hover:tw-bg-gray-50 tw-text-gray-500 tw-items-center table-mytasks"
                            :key="issue.id" v-show="!projectNameClose.includes(project.id)">
                            <div
                                class="tw-text-gray-900 task-item tw-pl-20 tw-pr-4 tw-py-4 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-flex tw-items-center">
                                    <el-dropdown trigger="click" v-if="project.type == 'website'">
                                        <span class="el-dropdown-link">
                                            <i class="el-icon-user tw-p-2 tw-mr-2 issue-icons"
                                                v-if="!issue.assign_id"></i>
                                            <el-tooltip v-if="issue.assign" :content="issue.assign.name">
                                                <span class="tw-mr-2 tw-relative">
                                                    <el-avatar style="vertical-align:middle"
                                                        :src="issue.assign.photo_url" :size="28" />
                                                </span>
                                            </el-tooltip>
                                        </span>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item v-for="(user, key) in project.users" :key="key">
                                                <div class="tw-flex tw-items-center"
                                                    @click="assignTask(user, issue, proof, revision, project)">
                                                    <div class="tw-flex tw-items-center">
                                                        <el-avatar :src="user.photo_url" class="tw-mr-2" :size="24" />
                                                        {{user.name}}
                                                    </div>
                                                    <i class="el-icon-success tw-ml-6"
                                                        v-if="issue.assign_id && issue.assign_id == user.id"></i>
                                                </div>
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                    <el-tooltip v-else :content="displayGraphicDesignUser('name')">
                                        <span class="tw-mr-2 tw-relative">
                                            <el-avatar style="vertical-align:middle"
                                                :src="displayGraphicDesignUser('photo_url')" :size="28" />
                                        </span>
                                    </el-tooltip>
                                    <div class="tw-pr-2" @click="goToProof(project.id, revision.id, proof.id)">
                                        <i :class="issue.status == 'done' ? 'el-icon-success tw-text-primary tw-hover-text-primary' : 'el-icon-circle-check' "
                                            class="tw-text-base"></i>
                                    </div>
                                    <div @click="goToProof(project.id, revision.id, proof.id)" class="tw-text-normal">
                                        {{issue.description}}</div>
                                </div>
                                <i @click="goToProof(project.id, revision.id, proof.id)"
                                    class="el-icon-top-right tw-text-base"></i>
                            </div>
                            <div class="date-item tw-px-4 tw-py-4">
                                <div class="tw-text-gray-500"
                                    v-if="issue.end_date && project.type === 'website'"
                                    :class="issue.end_date | dateToFromNowDaily | dateColor(issue.end_date)">
                                    {{ issue.end_date | dateToFromNowDaily }}</div>
                                <i v-else-if="project.type === 'website'" @click="goToProof(project.id, revision.id, proof.id)" class="el-icon-date tw-p-2 tw-mr-2 issue-icons"></i>
                            </div>
                            <div class="tag-item tw-px-4 tw-py-4">
                                <el-tag size="mini" class="tw-capitalize" type="danger" effect="dark"
                                    v-if="issue.tag && issue.tag == 'high'">{{issue.tag}}</el-tag>
                                <el-tag size="mini" class="tw-capitalize" type="warning" effect="dark"
                                    v-else-if="issue.tag && issue.tag == 'medium'">{{issue.tag}}</el-tag>
                                <el-tag size="mini" class="tw-capitalize" type="success" effect="dark"
                                    v-else-if="issue.tag && issue.tag == 'low'">{{issue.tag}}</el-tag>
                                <i v-else @click="goToProof(project.id, revision.id, proof.id)" class="el-icon-collection-tag tw-p-2 tw-mr-2 issue-icons"></i>
                            </div>
                            <div class="track-item tw-px-4 tw-py-4 tw-flex tw-items-center">
                                <el-tooltip
                                    v-if="isBothRole && issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start"
                                    content="Stop Tracker" placement="bottom">
                                    <div class="stop-tracker" @click="stopTracker(project,issue)">
                                        <i class="fa fa-stop icon-time-tracker-stop"></i>
                                    </div>
                                </el-tooltip>
                                <el-tooltip v-else-if="isBothRole" content="Start Tracker" placement="bottom">
                                    <div class="play-tracker" @click="startTracker(project, issue)"><i
                                            class="fa fa-play icon-time-tracker"></i></div>
                                </el-tooltip>
                                <div class="tw-bg-gray-100 tw-rounded-md tw-shadow tw-px-2"
                                    :class="{'time-counts-active': (issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start)}">
                                    <span
                                        v-if="issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start">{{trackerTimer(issue.time_tracker)}}</span>
                                    <span v-else-if="isBothRole">{{projectDuration(issue)}}</span>
                                </div>
                            </div>
                            <router-link tag="a"
                                class="empty-item tw-px-4 tw-px-4 tw-text-primary tw-text-primary tw-hover-text-primary"
                                :to="{name: 'time-trackers', query: {issue: issue.id}}">
                                <i class="el-icon-top-right tw-text-base"></i>
                            </router-link>
                        </div>
                    </template>
                </template>
            </template>
        </transition-group>
    </div>
</template>

<script>
    import {
        mapGetters
    } from 'vuex'
    export default {
        props: ['project', 'filterCompleted', 'filterTags', 'collapse'],
        data() {
            return {
                projectNameClose: JSON.parse(localStorage.getItem('projectNameClose-' + this.project.id)) || [],
                now: null,
            }
        },
    methods: {
        // Return project label (InBox, Sent, Draft, Hold, Approved, Delivered)
        projectLabel(project) {
            // Find this project from multiple arrays (in progress, sent, draft, hold, approved, delivered)
            const inProgress = this.in_progress.find(p => p.id === project.id)
            const sent = this.on_revision.find(p => p.id === project.id)
            const draft = this.on_draft.find(p => p.id === project.id)
            const hold = this.on_hold.find(p => p.id === project.id)
            const approved = this.approved.find(p => p.id === project.id)
            const delivered = this.completed.find(p => p.id === project.id)

            // Return project label
            if (inProgress) {
                return 'INBOX'
            } else if (sent) {
                return 'SENT'
            } else if (draft) {
                return 'DRAFT'
            } else if (hold) {
                return 'HOLD'
            } else if (approved) {
                return 'APPROVED'
            } else if (delivered) {
                return 'DELIVERED'
            }
        },

        displayGraphicDesignUser(field) {
                const user = this.project.users.find(user => user.pivot.role === 'freelancer')
                if (user) {
                    return user[field]
                }
            },
            methodProjectNameClose(id) {
                if (this.projectNameClose.includes(id)) {
                    
                    let index = this.projectNameClose.indexOf(id)
                    this.projectNameClose.splice(index, 1)
                } else {
                    this.projectNameClose.push(id)
                }
                localStorage.setItem('projectNameClose-' + id, JSON.stringify(this.projectNameClose))
            },
            projectDuration(project) {
                return moment.utc(project.totalTracker * 1000).format("HH:mm:ss");
            },
            filterIssues(issues) {
                let issuesFilter = [];
                if (this.filterCompleted == 'Completed') {
                    issuesFilter = issues.filter(issue => issue.status == 'done')
                } else if (this.filterCompleted == 'Incompleted') {
                    issuesFilter = issues.filter(issue => issue.status != 'done')
                } else {
                    issuesFilter = issues
                }

                let filterTag = []

                if (this.filterTags != 'All') {
                    filterTag = issuesFilter.filter(issue => issue.tag == this.filterTags.toLowerCase())
                } else {
                    filterTag = issuesFilter
                }
                return filterTag
            },
            goToProof(project_id, revision_id, proof_id) {
                // this.$router.push({name: 'projects'})

                this.$router.push({
                    name: "proofer_proof",
                    params: {
                        project_id: project_id,
                        revision_id: revision_id,
                        proof_id: proof_id,
                    },
                });
            },
            async assignTask(user, issue, proof, revision, project) {
                try {
                    let payload = {
                        user_id: user.id,
                        issue_id: issue.id
                    }
                    let {
                        data
                    } = await axios.post('/api/proof/issue/assisng', payload);
                    let getProject = this.projects.find(pro => pro.id == project.id);
                    let getRevision = getProject.revisions.find(rev => rev.id == revision.id)
                    let getProof = getRevision.proofs.find(pro => pro.id == proof.id)
                    let index = getProof.issues.map(issue => issue.id).indexOf(data.data.id)
                    getProof.issues.splice(index, 1, data.data)

                } catch (error) {
                    console.log(error)
                }
            },
            stopTracker(project, issue) {
                axios
                    .put(
                        `/api/projects/project-timers/${issue.time_tracker.id}?stop=true`, {
                            end: moment().format("YYYY-MM-DD HH:mm:ss"),
                        }
                    )
                    .then((response) => {
                        if (response.data.status) {

                            issue.time_tracker = response.data.data;
                            issue.totalTracker = issue.totalTracker + response.data.data.duration;
                            project.totalTracker = project.totalTracker + response.data.data.duration;

                            this.$notify({
                                title: "Success",
                                message: response.data.message,
                                type: "success",
                            });

                        } else {
                            this.$notify({
                                title: "Error",
                                message: response.data.errors[0],
                                type: "error",
                            });
                        }
                    })
                    .catch((error) => {
                        this.$notify({
                            title: "Error",
                            message: "Something went wrong please try again later",
                            type: "error",
                        });
                    });
            },
            startTracker(project, issue) {
                axios
                    .post(`/api/projects/project-timers`, {
                        // description: this.dialogData.description
                        project_id: project.id,
                        issue_id: issue.id,
                        start: moment().format("YYYY-MM-DD HH:mm:ss"),
                    })
                    .then((response) => {
                        if (response.data.status) {
                            issue.time_tracker = response.data.data;

                            this.$notify({
                                title: "Success",
                                message: response.data.message,
                                type: "success",
                            });

                        } else {
                            this.$notify({
                                title: "Error",
                                message: response.data.errors[0],
                                type: "error",
                            });
                        }
                    })
                    .catch((error) => {
                        this.$notify({
                            title: "Error",
                            message: "Something went wrong please try again later",
                            type: "error",
                        });
                    });
            },
            trackerTimer(time) {
                if (this.now) {
                    var diff = this.now.diff(time.start);
                    return moment.utc(diff).format("HH:mm:ss");
                }
            },
        },
        computed: {
            ...mapGetters(['isBothRole', 'in_progress', 'on_revision', 'on_draft', 'on_hold', 'approved', 'completed']),
        },
        watch: {
            collapse(val) {
                if (val) {
                    this.projectNameClose = [this.project.id]
                } else {
                    this.projectNameClose = []
                }
                localStorage.setItem('projectNameClose-' + this.project.id, JSON.stringify(this.projectNameClose))
            }  
        },
        mounted() {
        },
        created() {
            setInterval(() => {
                this.now = moment();
            }, 1000);
        }
    }
</script>

<style lang="scss" scoped>
    .play-tracker,
    .stop-tracker {
        --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        margin: 0 6px;
        display: table;
        width: 24px;
        font-size: 12px;
        border-radius: 100%;
        height: 24px;
        cursor: pointer;
    }

    .play-tracker {
        color: #e8fff6;
        background-color: #81b4a1;
    }

    .stop-tracker {
        background-color: #F56565;
        color: #FFF5F5;
    }

    .play-tracker .icon-time-tracker,
    .stop-tracker .icon-time-tracker-stop {
        display: table-cell;
        padding-left: 2px;
        vertical-align: middle;
        text-align: center;
        color: #fff;
    }

    .time-counts-active {
        color: #FFF5F5;
        background-color: #F56565;
    }

    .issue-icons {
        border: 1px dashed rgba(107, 114, 128, 1);
        border-radius: 100%;

        &:hover {
            border: 1px dashed rgba(55, 65, 81, 1);
            color: rgba(55, 65, 81, 1);
            background-color: rgba(243, 244, 246, 1);
        }
    }
</style>