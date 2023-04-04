<template>
    <div>
        <el-row :gutter="24">
            <el-col :xs="24" :sm="12" :md="8" :lg="6" :xl="4" v-if="isSubscribed" style="margin-bottom: 24px;">
                <new-project />
            </el-col>
            <template v-if="loadingStatus">
                <transition-group name="el-fade-in" >
                    <el-col :xs="24" :sm="12" :md="8" :lg="6" :xl="4" v-for="item in 10" :key="item">
                        <project-loading />
                    </el-col>
                </transition-group>
            </template>
            <template v-else>
                <transition-group name="el-fade-in">
                <el-col :xs="24" :sm="12" :md="8" :lg="6" :xl="4" v-for="(project, index) in projects" :key="project.id" >
                    <div class="project-card" :class="{'select': selected.includes(project.id)}" @click="addSelectable(project)">
                        <div class="project-loading" v-if="loading.length && loading.includes(project.id)">
                            <spinner line-fg-color="#81b4a1" />
                        </div>
                        <div class="project-tag" v-if="!selected.length">
                            <span  class="tw-flex tw-items-center" v-if="project.created_role == 'client'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tw-mr-2 feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                Client Request</span>
                            <span v-else-if="project.team == '' && project.created_role !== 'client'">{{project.role | capitalize}}</span>
                            <span v-else>Team - {{project.team | capitalize}}</span>
                        </div>
                        <div class="members" v-if="!selected.length && project.client && currentRole !== 'Client'">
                            <div class="member-avatar">
                                <el-tooltip :content="`${project.client.name} | Client`">
                                    <el-avatar :size="32" :src="project.client.photo_url"></el-avatar>
                                </el-tooltip>
                            </div>
                        </div>
                        <div class="project-image">
                            <project-image :project="project" :isBothRole="isBothRole" />
                            <project-overlay :selectable="selected" @selectableChange="selectableChange" v-if="!selected.length" :project="project" :user="user" :index="index" :currentRole="currentRole" :isBothRole="isBothRole" @loadingMethod="loadingMethod" @openInProgressDialog="openInProgressDialog" @openAddTeamMemberDialog="openAddTeamMemberDialog" @openCreativeBriefModal="openCreativeBriefModal" />
                            <div class="project-notifiy" v-if="!selected.length">
                                <el-tooltip v-if="getReviewsOfProject(project) > 0" content="Issues" placement="top">
                                <span 
                                    class="corrections">{{ getReviewsOfProject(project) }}</span>
                                </el-tooltip>
                                <el-tooltip v-if="project.unreadComments > 0" content="Comments" placement="top">
                                    <span class="comments">{{project.unreadComments}}</span>
                                </el-tooltip>
                            </div>
                            <div v-if="!selected.length" class="time-counts" :class="{'time-counts-active': (project.timeTracker && project.timeTracker.end === null && project.timeTracker.start)}">
                                <span
                                    v-if="project.timeTracker && project.timeTracker.end === null && project.timeTracker.start">{{trackerTimer(project.timeTracker)}}</span>
                                <span v-else-if="isBothRole">{{projectDuration(project)}}</span>  <span v-if="project.budget > 0" class="time-counts-budget tw-text-white">| <b>${{ project.budget }}</b> F</span><span class="time-counts-budget tw-text-white" v-else-if="project.freelancer_hrate > 0">| <b>${{ project.freelancer_hrate }}</b>/h</span>
                            </div>
                        </div>
                        <project-content :project="project" @startTracker="startTracker" @stopTracker="stopTracker" :isBothRole="isBothRole" />
                    </div>
                </el-col>
                </transition-group>
            </template>
            <selected-card @clearSelected="clearSelected" @loadingMethod="loadingMethod" :selected="selected" />
        </el-row>
    </div>
</template>

<script>
    import Spinner from 'vue-simple-spinner'
    import NewProject from './components/NewProject.vue'
    import ProjectContent from './components/ProjectContent.vue'
    import ProjectImage from './components/ProjectImage.vue'
    import ProjectLoading from './components/ProjectLoading.vue'
    import ProjectOverlay from './components/ProjectOverlay.vue'
    import SelectedCard from './components/SelectedCard.vue'
    export default {
        props: ['projects', 'user', 'index', 'loading', "isSubscribed"],
        components: {
            Spinner,
            NewProject,
            ProjectLoading,
            ProjectContent,
            ProjectOverlay,
            ProjectImage,
            SelectedCard
        },
        data() {
            return {
                tracker: [],
                now: '',
                selectable: false,
                selected: [],
            }
        },
        computed: {
            loadingStatus() {
                return this.$store.getters.loadingStatus
            },
            currentRole() {
                return this.$store.getters.currentRole
            },
            isBothRole() {
                return this.$store.getters.isBothRole
            }
        },
        methods: {
            addSelectable(project) {
                if (this.selectable && project && project.owner == this.user.id && project.active) {
                    let index = this.selected.indexOf(project.id)
                    if (!this.selected.includes(project.id)) {
                        this.selected.push(project.id)
                    } else {
                        console.log(index)
                        this.selected.splice(index, 1)
                    } 
                }
                if (this.selected.length == 0) {
                    this.selectable = false
                }
            },
            clearSelected() {
                this.selected = []
                this.selectable = false
            },
            selectableChange(id) {
                this.selectable = true
                this.selected.push(id)
            },
            getReviewsOfProject(project) {
                var count = 0;
                project.active_revision.proofs.forEach((proof) => {
                    proof.issues.forEach((issue) => {
                        if (this.isBothRole) {
                            if (issue.status == "todo") count++;
                        }
                        if (this.currentRole == "Client") {
                            if (issue.status == "review") count++;
                        }
                    });
                });
                return count;
            },
            checkOverDue(project) {
                if (project.end) {
                    return moment.utc().isAfter(moment.utc(project.end));
                } else {
                    return false;
                }
            },
            checkDue(project) {
                if (project.end) {
                    return moment.utc().isBefore(moment.utc(project.end));
                } else {
                    return false;
                }
            },
            trackerTimer(time) {
                if (this.now) {
                    var diff = this.now.diff(time.start);
                    return moment.utc(diff).format("HH:mm:ss");
                }
            },
            projectDuration(project) {
                return moment.utc(project.totalTracker * 1000).format("HH:mm:ss");
            },
            startTracker(project) {
				axios
					.post(`/api/projects/project-timers`, {
						// description: this.dialogData.description
						project_id: project.id,
						start: moment().format("YYYY-MM-DD HH:mm:ss"),
					})
					.then((response) => {
						if (response.data.status) {
                            
                            project.timeTracker = response.data.data;

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
			stopTracker(project) {
				axios
					.put(
						`/api/projects/project-timers/${project.timeTracker.id}?stop=true`,
						{
							end: moment().format("YYYY-MM-DD HH:mm:ss"),
						}
					)
					.then((response) => {
						if (response.data.status) {

                            project.timeTracker = response.data.data;
                            project.totalTracker += response.data.data.duration;

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
            loadingMethod(data) {
                this.$emit('loadingMethod', data)
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
        },
        filters: {
            capitalize(value) {
                if (!value) return "";
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
        },
        created() {
			setInterval(() => {
				this.now = moment();
            }, 1000);
        }
    }
</script>

<style lang="scss" scoped>
    .project-card {
        --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        background-color: #fff;
        min-height: 300px;
        border-radius: 12px;
        position: relative;
        margin-bottom: 24px;

        &.select {
            border: 3px solid #81b4a1;
        }
        .project-loading {
            position: absolute;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 100%;
            border-radius: 12px;
            cursor: wait;
            background-color: #718096a1;
        }

        .project-tag,
        .members {
            position: absolute;
            --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            color: #81b4a1;
            background-color: #e8fff6;
        }

        .members {
            top: -5px;
            z-index: 999;
            right: -5px;
            cursor: pointer;
            display: table;
            width: 24px;
            font-size: 12px;
            border-radius: 100%;
            height: 24px;

            .member-avatar {
                display: flex;
                color: #fff;
                color: #fff;
                position: absolute;
                border-radius: 25px;
                font-weight: bold;
                top: -5px;
                right: -5px;
                z-index: 1000;
                box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.2),
                    0 4px 20px 0 rgba(0, 0, 0, 0.19);
            }
        }

        .project-tag {
            top: -5px;
            left: -5px;
            border-radius: 4px;
            padding: 2px 8px;
            z-index: 999;
        }

        .project-image:hover .rollover {
            opacity: 1;
            transition: 0.1s
        }

        .project-image {
            position: relative;
            padding: 0px;

            .rollover {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                height: 100%;
                z-index: 99;
                width: 100%;
                opacity: 0;
                -webkit-transition: .2s ease;
                transition: .2s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: space-between;
                background-color: #718096b3;
            }

            .project-notifiy {
                width: 100%;
                position: absolute;
                bottom: 4px;
                left: 6px;
                user-select: none;
                color: #fff;

                .corrections,
                .comments {
                    position: absolute;
                    font-size: 12px;
                    z-index: 999;
                    padding: 0 6px;
                    bottom: 4px;
                    border-radius: 25px;
                    --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                    box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
                }

                .corrections {
                    background-color: #ED8936;
                    color: #FFFAF0;
                }

                .comments {
                    background-color: #F56565;
                    color: #FFF5F5;
                }
            }

            .time-counts {
                position: absolute;
                bottom: 4px;
                right: 6px;
                color: #E2E8F0;
                background-color: #718096;
                border-radius: 4px;
                padding: 0px 6px;
                z-index: 999;
                font-size: 12px;

            }

            .time-counts-budget {
                display: none;
            }

            .time-counts:hover {
                .time-counts-budget {
                    display: inline-block;
                }
            }
            .time-counts-active {
                color: #FFF5F5;
                background-color: #F56565;
            }
        }
    }

</style>