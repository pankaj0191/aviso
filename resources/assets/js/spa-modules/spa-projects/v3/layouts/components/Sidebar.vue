<template>

    <el-menu default-active="2" class="el-menu-vertical-demo aside-menu" :collapse="isCollapse"
        background-color="#304156" text-color="rgb(191, 203, 217)" active-text-color="#263445">
        <div class="text-center" style="height:60px;background-color: #141a23;">
            <router-link tag="a" style="line-height: 60px;" :to="{name: 'projects'}">
                <img :src="`${spacePrefix}/assets/img/prooflo-logo.png`" width="120" v-if="!isCollapse" />
                <img :src="`${spacePrefix}/assets/img/prooflo-small.png`" width="24" v-else />
            </router-link>
        </div>
        <el-tooltip class="tooltip-item" effect="dark" content="Dashboard" placement="right">
            <el-menu-item index="2" @click="load('dashboard')" :class="{'active_menu' : active_dashboard}">
                <i class="el-icon-menu icon-projects"></i>
                <span class="labels">DASHBOARD</span>
            </el-menu-item>
        </el-tooltip>
        <el-tooltip class="tooltip-item" effect="dark" :content="currentRole == 'Agency' ? 'Task Manager' : 'My Tasks'" placement="right">
            <el-menu-item index="2" @click="load('my-tasks')" :class="{'active_menu' : active_my_tasks}">
                <i class="el-icon-circle-check icon-projects"></i>
                <span class="labels" v-if="currentRole == 'Agency'">Task Manager</span>
                <span class="labels" v-else>MY TASKS</span>
            </el-menu-item>
        </el-tooltip>
        <!-- <el-tooltip class="tooltip-item" effect="dark"
            content="Project List: This section is for all projects currently in your dashboard." placement="right">
            <el-menu-item index="2" @click="load('/')" :class="{'active_menu' : active_list}">
                <i class="el-icon-tickets icon-projects"></i>
                <span class="labels">PROJECT LIST</span>
                <el-badge :value="projects.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts">
                </el-badge>
            </el-menu-item>
        </el-tooltip> -->
        <!--In progerss-->
        <el-tooltip v-if="isBothRole" class="tooltip-item" effect="dark" placement="right">
            <div slot="content">Inbox: This section is for projects you are currently working on.
                <a href="https://support.prooflo.com/article/81-agency-inbox" target="_blank"  class="custom-content-tooltip"> <i class="el-icon-caret-right custom-video-play-icon"></i> <span class="prooflo-text">Learn More: Play Video</span></a>
            </div>
            <el-menu-item index="2" @click="load('progress')" :class="{'active_menu' : active_progress}">
                <div v-if="getReviewCount(in_progress)" class="review-alert">
                    <i class="fa fa-circle"></i>
                </div>
                <el-badge v-if="in_progress.length > 0 && getUnreadComments(in_progress) > 0"
                    :value="getUnreadComments(in_progress)" class="unread-comments">
                    <i class="el-icon-receiving icon-projects"></i>
                </el-badge>
                <i v-else class="el-icon-receiving icon-projects"></i>
                <span class="labels">
                    INBOX
                    <!-- <span class="user-info">(ME)</span> -->
                </span>
                <el-badge :value="in_progress.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts"></el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip v-else-if="is_collaborator.length > 0" class="tooltip-item" effect="dark"
            content="Inbox: This section is for projects awaiting client revisions." placement="right">
            <el-menu-item index="2" @click="load('revision')" :class="{'active_menu' : active_revision}">
                <div v-if="getReviewCount(on_revision)" class="review-alert">
                    <i class="fa fa-circle"></i>
                </div>
                <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                    :value="getUnreadComments(on_revision)" class="unread-comments">
                    <i class="el-icon-receiving icon-projects"></i>
                </el-badge>
                <i v-else class="el-icon-receiving icon-projects"></i>
                <span class="labels">
                    INBOX
                    <!-- <span class="user-info">(CLIENT)</span> -->
                </span>
                <el-badge :value="on_revision.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts"></el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip v-else class="tooltip-item" effect="dark"
            content="Inbox: This section is for projects awaiting your revisions. Please review and approve or send back to the designer for new revisions."
            placement="right">
            <el-menu-item index="2" @click="load('revision')" :class="{'active_menu' : active_revision}">
                <div v-if="getReviewCount(on_revision)" class="review-alert">
                    <i class="fa fa-circle"></i>
                </div>
                <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                    :value="getUnreadComments(on_revision)" class="unread-comments">
                    <i class="el-icon-receiving icon-projects"></i>
                </el-badge>
                <i v-else class="el-icon-receiving icon-projects"></i>
                <span class="labels">
                    INBOX
                    <!-- <span class="user-info">(ME)</span> -->
                </span>
                <el-badge :value="on_revision.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts"></el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip v-if="isBothRole" class="tooltip-item" effect="dark"
            content="Sent: This section is for projects being reviewed by your client.  You will be notified when the client has made corrections."
            placement="right">
            <!--In Review-->
            <el-menu-item v-if="isBothRole" index="2" @click="load('revision')"
                :class="{'active_menu' : active_revision}">
                <div v-if="getReviewCount(on_revision)" class="review-alert">
                    <i class="fa fa-circle"></i>
                </div>
                <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                    :value="getUnreadComments(on_revision)" class="unread-comments">
                    <i class="el-icon-edit icon-projects"></i>
                </el-badge>
                <i v-else class="el-icon-position icon-projects"></i>
                <span class="labels">
                    SENT
                    <!-- <span class="user-info">(CLIENT)</span> -->
                </span>
                <el-badge :value="on_revision.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts"></el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip v-else class="tooltip-item" effect="dark"
            content="Sent: This section is for projects currently being worked on by your designer. You will be notified when there are revisions for review."
            placement="right">
            <el-menu-item index="2" @click="load('progress')" :class="{'active_menu' : active_progress}">
                <div v-if="getReviewCount(in_progress)" class="review-alert">
                    <i class="fa fa-circle"></i>
                </div>
                <el-badge v-if="in_progress.length > 0 && getUnreadComments(in_progress) > 0"
                    :value="getUnreadComments(in_progress)" class="unread-comments">
                    <i class="el-icon-edit icon-projects"></i>
                </el-badge>
                <i v-else class="el-icon-position icon-projects"></i>
                <span class="labels">
                    SENT
                    <!-- <span class="user-info">(DESIGNER)</span> -->
                </span>
                <el-badge :value="in_progress.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts"></el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip v-if="(isBothRole  || projectRoleClient.length > 0) && on_draft.length > 0"
            class="tooltip-item" effect="dark"
            content="In Draft: This section is for projects that are waiting for you to be sent to your clients."
            placement="right">
            <el-menu-item index="2" @click="load('draft')" :class="{'active_menu' : active_draft}">
                <i class="el-icon-edit-outline icon-projects"></i>
                <span class="labels">IN DRAFT</span>
                <el-badge :value="on_draft.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts">
                </el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip class="tooltip-item" effect="dark"
            content="On Hold: This section is for projects that are not being worked on at the moment and are on hold."
            placement="right">
            <!--on Hold-->
            <el-menu-item index="2" @click="load('hold')" v-if="on_hold.length > 0"
                :class="{'active_menu' : active_hold}">
                <i class="el-icon-refresh icon-projects"></i>
                <span class="labels">ON HOLD</span>
                <el-badge :value="on_hold.length  || (loadingStatus ? 'Loading' : 0)" class="projects-counts">
                </el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip class="tooltip-item" effect="dark"
            content="Approved: This section is for projects that have been approved by the client." placement="right">
            <el-menu-item index="2" @click="load('approved')" :class="{'active_menu' : active_approved}">
                <i class="el-icon-check icon-projects"></i>
                <span class="labels">APPROVED</span>
                <el-badge :value="approved.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts">
                </el-badge>
            </el-menu-item>
        </el-tooltip>

        <el-tooltip class="tooltip-item" effect="dark" placement="right">
            <div slot="content">Delivered: This section contains delivered files ready for client to download for use.
                <a href="https://www.youtube.com/watch?v=guCwdHngTmM" target="_blank"  class="custom-content-tooltip"> <i class="el-icon-caret-right custom-video-play-icon"></i> <span class="prooflo-text">Learn More: Play Video</span></a>
            </div>
            <el-menu-item index="2" @click="load('completed')" :class="{'active_menu' : active_completed}">
                <i class="el-icon-star-off icon-projects"></i>
                <span class="labels">DELIVERED</span>
                <el-badge v-if="!isCollapse" :value="completed.length || (loadingStatus ? 'Loading' : 0)" class="projects-counts">
                </el-badge>
            </el-menu-item>
        </el-tooltip>
    </el-menu>
</template>

<script>
import Spinner from 'vue-simple-spinner'
    import {
        mapActions,
        mapGetters
    } from "vuex";
    export default {
        components: {
            Spinner
        },
        props: ['isCollapse'],
        data() {
            return {
                // active_list: this.$route.name == "projects_list" ? true : false,
                active_progress: this.$route.name == "projects" ? true : false,
                active_revision: false,
                active_draft: false,
                active_hold: false,
                active_approved: false,
                active_completed: false,
                active_my_tasks: this.$route.name == "my-tasks" ? true : false,
                active_dashboard: this.$route.name == "dashboard" ? true : false,
            }
        },
        methods: {
            toggleCollapse(val) {
                this.isCollapse = val;
            },
            load(status) {
                this.$router.push({
                    name: "projects"
                });
                switch (status) {
                    case "/":
                        // this.active_list = true;
                        this.active_time_trackers = false;
                        this.active_dashboard = false;
                        this.active_hold = false;
                        this.active_progress = true;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 0);
                        this.$store.commit("set_current_project_listing", "all");
                        break;
                    case "dashboard":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_dashboard = true;
                        this.active_hold = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$router.push({
                            name: "dashboard"
                        });
                        this.$store.commit("set_show_project_by_status", 7);
                        this.$store.commit(
                            "set_current_project_listing",
                            "dashbaord"
                        );
                        break;
                    case "my-tasks":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_dashboard = false;
                        this.active_hold = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = true;
                        this.$router.push({
                            name: "my-tasks"
                        });
                        this.$store.commit("set_show_project_by_status", 7);
                        this.$store.commit(
                            "set_current_project_listing",
                            "dashbaord"
                        );
                        break;
                    case "progress":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_hold = false;
                        this.active_dashboard = false;
                        this.active_progress = true;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 1);
                        this.$store.commit(
                            "set_current_project_listing",
                            "progress"
                        );

                        break;
                    case "revision":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_hold = false;
                        this.active_dashboard = false;
                        this.active_progress = false;
                        this.active_revision = true;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 2);
                        this.$store.commit(
                            "set_current_project_listing",
                            "revision"
                        );
                        break;
                    case "approved":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_hold = false;
                        this.active_dashboard = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = true;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 3);
                        this.$store.commit(
                            "set_current_project_listing",
                            "approved"
                        );
                        break;
                    case "completed":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_hold = false;
                        this.active_dashboard = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = true;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 4);
                        this.$store.commit(
                            "set_current_project_listing",
                            "completed"
                        );
                        break;
                    case "draft":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_hold = false;
                        this.active_dashboard = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = true;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 5);
                        this.$store.commit("set_current_project_listing", "draft");
                        break;
                    case "hold":
                        this.active_list = false;
                        this.active_time_trackers = false;
                        this.active_dashboard = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_hold = true;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.active_my_tasks = false;
                        this.$store.commit("set_show_project_by_status", 6);
                        this.$store.commit("set_current_project_listing", "hold");
                        break;
                }
            },
            getUnreadComments(projects) {
                let unread_comments = 0;
                projects.forEach(function (project) {
                    unread_comments += project.unreadComments;
                });

                return unread_comments;
            },
            getReviewCount(projects) {
                if (!projects.length) return;

                let count = 0;
                projects.forEach(project => {
                    project.active_revision.proofs.forEach(proof => {
                        proof.issues.forEach(issue => {
                            if (this.isBothRole) {
                                if (issue.status == "todo") count++;
                            } else {
                                if (issue.status == "review") count++;
                            }
                        });
                    });
                });
                return count;
            },
            ...mapActions([
                "getActiveSubscription",
                "loadProjects",
                "bootstrap",
            ])
        },
        computed: {
            ...mapGetters([
                "user",
                "isBothRole",
                "activePlan",
                "unread_comments",
                "is_freelancer",
                "is_agency",
                "is_collaborator",
                "projects",
                "in_progress",
                "on_revision",
                "approved",
                "completed",
                "on_draft",
                "on_hold",
                "loadingStatus",
                "currentRole"
            ]),
            projectRoleClient() {
                let data = [];
                if (Object.entries(this.on_draft).length != 0) {
                    data = this.on_draft.filter(
                        project => project.created_role == "client"
                    );
                }
                return data;
            },
            spacePrefix() {
                return spacePrefix;
            }
        },
        watch: {
            currentRole(val) {
                if (val == 'Client') {
                    this.$store.commit("set_show_project_by_status", 2);
                    this.$store.commit("set_current_project_listing", "revision");
                    this.active_revision = true
                    this.active_progress = false
                } else {
                    this.$store.commit("set_show_project_by_status", 1);
                    this.$store.commit("set_current_project_listing", "progress");
                }
            }
        },
        mounted() {
            this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
        },
        async created() {
            await this.loadProjects();
            await this.getActiveSubscription();
            if (this.currentRole == 'Client') {
                this.$store.commit("set_show_project_by_status", 2);
                this.$store.commit("set_current_project_listing", "revision");
            } else {
                this.$store.commit("set_show_project_by_status", 1);
                this.$store.commit("set_current_project_listing", "progress");
            }
            window.addEventListener("resize", this.handleWindowResize);
        },
       
        destroyed() {
            window.removeEventListener("resize", this.handleWindowResize);
        },
        beforeRouteUpdate: function (to, from, next) {
            this.loadProjects();
            next();
        }
    }
</script>

<style lang="scss" scoped>
.spinner-sidebar {
    display:inline-block;
    margin-left: 52px;
}

.custom-content-tooltip {
        color:white;
        font-size:14px;
        margin-top:6px;
        display:flex;
        text-decoration: none;
        align-items: center; 
    }
    .custom-content-tooltip:hover {

        span {
            text-decoration: underline;
        }
    }
.custom-video-play-icon {
    margin-right: 4px;
    height: 20px;
    width: 20px;
    display: flex;
    border-radius: 100%;
    background-color: #81b4a1;
    align-items: center;
    justify-content: center;
}
</style>