<template>
    <div v-loading.fullscreen.lock="fullscreenLoading" class="container-fluid"
        style="padding-left: 0px; padding-right: 0px">
        <el-container :class="isCollapse ? 'hideSidebar' : 'openSidebar'">
            <div class="sidebar-container">
                <!---->
                <div class="el-scrollbar">
                    <div class="scrollbar-wrapper el-scrollbar__wrap"
                        style="margin-bottom: -15px; margin-right: -15px;">
                        <div class="el-scrollbar__view">
                            <el-menu default-active="2" class="el-menu-vertical-demo aside-menu" @open="handleOpen"
                                @close="handleClose" :collapse="isCollapse" background-color="#304156"
                                text-color="rgb(191, 203, 217)" active-text-color="#263445">
                                <div class="text-center" style="height:60px;background-color: #141a23;">
                                    <router-link tag="a" style="line-height: 60px;" :to="{name: 'projects'}">
                                        <img :src="`${spacePrefix}/assets/img/prooflo-logo.png`" width="120"
                                            v-if="!isCollapse" />
                                        <img :src="`${spacePrefix}/assets/img/prooflo-small.png`" width="24" v-else />
                                    </router-link>
                                </div>
                                <el-tooltip class="tooltip-item" effect="dark" content="Dashboard" placement="right">
                                    <el-menu-item index="2" @click="load('dashboard')"
                                        :class="{'active_menu' : active_dashboard}">
                                        <i class="el-icon-menu icon-projects"></i>
                                        <span class="labels">DASHBOARD</span>
                                    </el-menu-item>
                                </el-tooltip>
                                <!-- <el-menu-item
									v-if="isFreelancer"
									index="2"
									@click="load('time-trackers')"
									:class="{'active_menu' : active_time_trackers}"
								>
									<i class="el-icon-timer icon-projects"></i>
									<span class="labels">TIME TRACKER</span>
								</el-menu-item>-->
                                <el-tooltip class="tooltip-item" effect="dark"
                                    content="Project List: This section is for all projects currently in your dashboard."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('/')" :class="{'active_menu' : active_list}">
                                        <i class="el-icon-tickets icon-projects"></i>
                                        <span class="labels">PROJECT LIST</span>
                                        <el-badge v-if="projects.length > 0" :value="projects.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>
                                <el-tooltip
                                    v-if="(is_agency.length > 0 || is_freelancer.length > 0  || projectRoleClient.length > 0) && on_draft.length > 0"
                                    class="tooltip-item" effect="dark"
                                    content="In Draft: This section is for projects that are waiting for you to be sent to your clients."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('draft')"
                                        :class="{'active_menu' : active_draft}">
                                        <i class="el-icon-edit-outline icon-projects"></i>
                                        <span class="labels">IN DRAFT</span>
                                        <el-badge v-if="on_draft.length > 0" :value="on_draft.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>
                                <!--In progerss-->
                                <el-tooltip v-if="is_agency.length > 0 || is_freelancer.length > 0" class="tooltip-item" effect="dark"
                                    content="Inbox: This section is for projects you are currently working on."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('progress')"
                                        :class="{'active_menu' : active_progress}">
                                        <div v-if="getReviewCount(in_progress)" class="review-alert">
                                            <i class="fa fa-circle"></i>
                                        </div>
                                        <el-badge v-if="in_progress.length > 0 && getUnreadComments(in_progress) > 0"
                                            :value="getUnreadComments(in_progress)" class="unread-comments">
                                            <i class="el-icon-time icon-projects"></i>
                                        </el-badge>
                                        <i v-else class="el-icon-time icon-projects"></i>
                                        <span class="labels">
                                            IN PROGRESS
                                            <span class="user-info">(ME)</span>
                                        </span>
                                        <el-badge v-if="in_progress.length > 0" :value="in_progress.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>

                                <el-tooltip v-else-if="is_collaborator.length > 0" class="tooltip-item" effect="dark"
                                    content="Inbox: This section is for projects awaiting client revisions."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('revision')"
                                        :class="{'active_menu' : active_revision}">
                                        <div v-if="getReviewCount(on_revision)" class="review-alert">
                                            <i class="fa fa-circle"></i>
                                        </div>
                                        <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                                            :value="getUnreadComments(on_revision)" class="unread-comments">
                                            <i class="el-icon-time icon-projects"></i>
                                        </el-badge>
                                        <i v-else class="el-icon-time icon-projects"></i>
                                        <span class="labels">
                                            IN PROGRESS
                                            <span class="user-info">(CLIENT)</span>
                                        </span>
                                        <el-badge v-if="on_revision.length > 0" :value="on_revision.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>

                                <el-tooltip v-else class="tooltip-item" effect="dark"
                                    content="Inbox: This section is for projects awaiting your revisions. Please review and approve or send back to the designer for new revisions."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('revision')"
                                        :class="{'active_menu' : active_revision}">
                                        <div v-if="getReviewCount(on_revision)" class="review-alert">
                                            <i class="fa fa-circle"></i>
                                        </div>
                                        <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                                            :value="getUnreadComments(on_revision)" class="unread-comments">
                                            <i class="el-icon-time icon-projects"></i>
                                        </el-badge>
                                        <i v-else class="el-icon-time icon-projects"></i>
                                        <span class="labels">
                                            IN PROGRESS
                                            <span class="user-info">(ME)</span>
                                        </span>
                                        <el-badge v-if="on_revision.length > 0" :value="on_revision.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>

                                <el-tooltip v-if="is_agency.length > 0 || is_freelancer.length > 0" class="tooltip-item" effect="dark"
                                    content="Send: This section is for projects being reviewed by your client.  You will be notified when the client has made corrections."
                                    placement="right">
                                    <!--In Review-->
                                    <el-menu-item v-if="is_agency.length > 0 || is_freelancer.length > 0" index="2" @click="load('revision')"
                                        :class="{'active_menu' : active_revision}">
                                        <div v-if="getReviewCount(on_revision)" class="review-alert">
                                            <i class="fa fa-circle"></i>
                                        </div>
                                        <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                                            :value="getUnreadComments(on_revision)" class="unread-comments">
                                            <i class="el-icon-edit icon-projects"></i>
                                        </el-badge>
                                        <i v-else class="el-icon-edit icon-projects"></i>
                                        <span class="labels">
                                            IN REVIEW
                                            <span class="user-info">(CLIENT)</span>
                                        </span>
                                        <el-badge v-if="on_revision.length > 0" :value="on_revision.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>

                                <el-tooltip v-else class="tooltip-item" effect="dark"
                                    content="Send: This section is for projects currently being worked on by your designer. You will be notified when there are revisions for review."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('progress')"
                                        :class="{'active_menu' : active_progress}">
                                        <div v-if="getReviewCount(in_progress)" class="review-alert">
                                            <i class="fa fa-circle"></i>
                                        </div>
                                        <el-badge v-if="in_progress.length > 0 && getUnreadComments(in_progress) > 0"
                                            :value="getUnreadComments(in_progress)" class="unread-comments">
                                            <i class="el-icon-edit icon-projects"></i>
                                        </el-badge>
                                        <i v-else class="el-icon-edit icon-projects"></i>
                                        <span class="labels">
                                            IN REVIEW
                                            <span class="user-info">(DESIGNER)</span>
                                        </span>
                                        <el-badge v-if="in_progress.length > 0" :value="in_progress.length"
                                            class="projects-counts"></el-badge>
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
                                        <el-badge v-if="on_hold.length > 0" :value="on_hold.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>

                                <el-tooltip class="tooltip-item" effect="dark"
                                    content="Approved: This section is for projects that have been approved by the client."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('approved')"
                                        :class="{'active_menu' : active_approved}">
                                        <i class="el-icon-check icon-projects"></i>
                                        <span class="labels">APPROVED</span>
                                        <el-badge v-if="approved.length > 0" :value="approved.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>

                                <el-tooltip class="tooltip-item" effect="dark"
                                    content="Final Files: This section contains final files for approved projects."
                                    placement="right">
                                    <el-menu-item index="2" @click="load('completed')"
                                        :class="{'active_menu' : active_completed}">
                                        <i class="el-icon-star-off icon-projects"></i>
                                        <span class="labels">DELIVERED</span>
                                        <el-badge v-if="completed.length > 0" :value="completed.length"
                                            class="projects-counts"></el-badge>
                                    </el-menu-item>
                                </el-tooltip>
                            </el-menu>
                            <hr class="sidebar-divider" v-if="isFreelancer" />
                            <navigation-team :isCollapse="isCollapse" @showMemberProjects="showMemberProjects"
                                @showMembersDialog="showMembersDialog" />
                            <!-- <hr class="sidebar-divider" v-if="isFreelancer"> -->
                        </div>
                    </div>
                </div>
                <div class="el-scrollbar__bar is-horizontal">
                    <div class="el-scrollbar__thumb" style="transform: translateX(0%);"></div>
                </div>
                <div class="el-scrollbar__bar is-vertical">
                    <div class="el-scrollbar__thumb" style="height: 33.3333%; transform: translateY(0%);"></div>
                </div>
            </div>
            <main-nav @isCollapse="toggleCollapse"></main-nav>
        </el-container>
        <el-dialog v-if="activePlan" :title="currentTeam.name" :close-on-click-modal="false"
            :close-on-press-escape="false" :show-close="true" :visible.sync="showDialogInviteTeamMembers"
            class="team-dialog" @open="onOpenDialogNewTeamMembers" @close="onCloseDialogNewTeamMembers" center>
            <invite-team v-if="showDialogInviteTeamMembers" @closeDialog="hideMembersDialog" :currentTeam="currentTeam">
            </invite-team>
        </el-dialog>
    </div>
</template>

<script>
    import {
        mapActions,
        mapGetters
    } from "vuex";
    import MainNav from "./layout/nav/Main";
    import InviteTeam from "./partials/InviteTeamSecond";
    import NavigationTeam from "./layout/NavigationTeam";

    export default {
        components: {
            MainNav,
            InviteTeam,
            NavigationTeam
        },
        data() {
            return {
                teamShow: true,
                currentTeam: {},
                isCollapse: false,
                active_list: this.$route.name == "projects_list" ? true : false,
                active_progress: false,
                active_revision: false,
                active_draft: false,
                active_hold: false,
                active_approved: false,
                active_completed: false,
                showDialogInviteTeamMembers: false,
                newTeamMembers: [],
                inputVisible: false,
                inputValue: "",
                fullscreenLoading: false,
                inviteDisabled: false,
                active_dashboard: this.$route.name == "dashboard" ? true : false,
                active_time_trackers: this.$route.name == "time-trackers" ? true : false
            };
        },
        watch: {
            windowWidth(val) {
                this.updateWindowWidth()
            }
        },
        methods: {
            updateWindowWidth() {
                if (window.innerWidth <= 768) {
                    this.isCollapse = true;
                } else {
                    this.isCollapse = false;
                }
            },
            toggleCollapse(val) {
                this.isCollapse = val;
            },
            handleOpen(collapse) {
                console.log(key, keyPath);
            },
            handleClose(collapse) {
                console.log(key, keyPath);
            },
            load(status) {
                this.$router.push({
                    name: "projects"
                });
                switch (status) {
                    case "/":
                        this.active_list = true;
                        this.active_time_trackers = false;
                        this.active_dashboard = false;
                        this.active_hold = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
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
                        this.$router.push({
                            name: "dashboard"
                        });
                        this.$store.commit("set_show_project_by_status", 7);
                        this.$store.commit(
                            "set_current_project_listing",
                            "dashbaord"
                        );
                        break;
                        // case "time-trackers":
                        // 	this.active_list = false;
                        // 	this.active_time_trackers = true;
                        // 	this.active_dashboard = false;
                        // 	this.active_hold = false;
                        // 	this.active_progress = false;
                        // 	this.active_revision = false;
                        // 	this.active_draft = false;
                        // 	this.active_approved = false;
                        // 	this.active_completed = false;
                        // 	this.$router.push({
                        // 		name: "time-trackers"
                        // 	});
                        // 	this.$store.commit("set_show_project_by_status", 8);
                        // 	this.$store.commit(
                        // 		"set_current_project_listing",
                        // 		"time-trackers"
                        // 	);
                        // 	break;
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
                            if (this.isFreelancer) {
                                if (issue.status == "todo") count++;
                            } else {
                                if (issue.status == "review") count++;
                            }
                        });
                    });
                });
                return count;
            },
            showMembersDialog(row) {
                if (!this.activePlan) {
                    return this.$notify.error({
                        title: "Error",
                        message: "Upgrade you subscribtion pleas!"
                    });
                }
                this.currentTeam = row;
                this.showDialogInviteTeamMembers = true;
            },
            hideMembersDialog() {
                this.showDialogInviteTeamMembers = false;
            },
            onOpenDialogNewTeamMembers() {},
            onCloseDialogNewTeamMembers() {
                this.newTeamMembers = [];
            },
            showInput() {
                this.inputVisible = true;
                this.inviteDisabled = true;
                this.$nextTick(_ => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },
            handleClose(key, keyPath) {
                this.newTeamMembers.splice(this.newTeamMembers.indexOf(key), 1);
            },
            showMemberProjects(member) {
                this.active_list = false;
                this.active_time_trackers = false;
                this.active_hold = false;
                this.active_dashboard = false;
                this.active_progress = false;
                this.active_revision = false;
                this.active_draft = false;
                this.active_approved = false;
                this.active_completed = false;
                this.$store.commit("set_show_project_by_status", member);
                this.$store.commit("set_current_project_listing", "member");
                this.loadMemberProjects(member);
            },
            handleWindowResize() {
                this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
            },
            ...mapActions([
                "getActiveSubscription",
                "loadProjects",
                "loadMemberProjects",
                // "bootstrap",
                "addInvitation",
                "deleteInvitation",
                "deleteTeamMembers"
            ])
            /*  ...mapActions([
            																																																																																																																																							            																																																																						               'bootstrap',
            																																																																																																																																							            																																																																						             ]) */
        },
        computed: {
            ...mapGetters([
                "user",
                "isFreelancer",
                "activeSubscription",
                "activePlan",
                "project_listing_type",
                "unread_comments",
                "ownedTeam",
                "teamMembers",
                "exitingEmails",
                "pendingEmails",
                "invitations",
                "membersLimit",
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
                "member_projects",
                "current_member",
                "windowBreakPoint"
            ]),
            windowWidth() {
                return this.$store.state.users.windowWidth;
            },
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
        filters: {
            pendingFilter(text) {
                if (!text) return "";
                text = text.toString();
                return text
                    .charAt(0)
                    .toUpperCase()
                    .substring(0, 1);
            },
            truncate: function (text, length, suffix) {
                if (text.length > length) {
                    return text.substring(0, length) + suffix;
                } else {
                    return text;
                }
            }
        },
        mounted() {
            this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
            this.$nextTick(() => {
                /* this.bootstrap() */
            });
        },
        created() {
            this.updateWindowWidth();
            this.loadProjects();
            this.getActiveSubscription();
            window.addEventListener("resize", this.handleWindowResize);
        },
        destroyed() {
            window.removeEventListener("resize", this.handleWindowResize);
        },
        beforeRouteUpdate: function (to, from, next) {
            this.loadProjects();
            next();
        }
    };
</script>
<style lang="scss">
    @media (max-width: 750px) {

        /* Dashboard menu */
        .el-menu-item .labels,
        .el-menu-item .projects-counts {
            display: none;
        }
    }

    .unread-comments .el-badge__content {
        background-color: rgb(250, 115, 115) !important;
        color: #fff !important;
        border-radius: 10px;
        display: inline-block;
        font-size: 12px;
        height: 20px;
        line-height: 18px;
        padding: 0 6px;
        text-align: center;
        white-space: nowrap;
        border: 1px solid rgb(250, 115, 115);
        z-index: 1;
    }

    .unread-comments .el-badge__content.is-fixed {
        top: 20px;
        left: -10px;
        right: unset !important;
        transform: unset;
        width: 20px;
        top: 10px;
        display: flex;
        justify-content: center;
    }

    .review-alert {
        position: absolute;
        left: 8px;
        font-size: 10px;
        top: 2px;
        left: 38px;
        font-size: 8px;
    }

    .el-menu-vertical-demo li.el-menu-item {
        padding-left: 15px !important;
    }

    .el-menu {
        border-right: 0px !important;
    }

    .with-navbar {
        padding-top: 0px !important;
    }

    .navbar {
        border-bottom: 0px !important;
    }

    .el-menu-vertical-demo .el-badge__content {
        background-color: #fff;
        color: black;
        font-weight: bold;
    }

    .active_menu {
        background-color: #263445 !important;
    }

    .aside-menu {

        .el-menu-item:focus,
        .el-menu-item:hover {
            background-color: #263445 !important;
        }
    }

    .labels {
        color: rgb(191, 203, 217);
        font-size: 12px;
        font-weight: bold;
        position: relative;
    }

    .labels-not-bold {
        color: rgb(191, 203, 217);
        font-size: 14px;
        position: relative;
    }

    .projects-counts {
        float: right;
        padding-top: 5px;
        height: 30px;
    }

    .user-info {
        position: absolute;
        top: -4px;
        left: 0px;
        color: #a5aaaf;
        font-weight: normal;
    }

    @-moz-document url-prefix() {
        .projects-counts {
            height: 46px;
            padding-top: 5px;
            margin-left: -21px;
        }

        .projects-counts .el-badge__content {
            line-height: 16px;
        }
    }

    .team-header:hover {
        background-color: #263445;
    }

    .team-header {
        cursor: pointer;

        .team-name:hover,
        .team-name:focus {
            color: #fff;
        }

        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;

        .plusIcon:hover,
        .collabse-icon:hover {
            opacity: 1;
        }

        .plusIcon:hover,
        .collabse-icon:hover,
        .plusIcon:focus,
        .collabse-icon:focus {
            fill: #fff;
        }

        .plusIcon {
            color: rgba(255, 255, 255, 0.5);
            fill: rgba(255, 255, 255, 0.5);
            outline: none;
            visibility: visible;
            align-items: center;
            cursor: pointer;
            display: flex;
            padding: 4px;
        }

        .collabse-icon {
            height: 14px;
            box-shadow: 0 0 0 3px transparent;
            outline: none;
            min-height: 14px;
            min-width: 14px;
            width: 14px;
            background: transparent;
            border-color: transparent;
            fill: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            align-items: center;
            border-radius: 50%;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            display: inline-flex;
            justify-content: center;
            transition-duration: 0.2s;
            transition-property: background, border, box-shadow, fill;
            color: #cbd4db;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-weight: 400;
        }

        .MiniIcon {
            flex: 0 0 auto;
            height: 12px;
            width: 12px;
        }
    }

    .new-team {
        margin: unset !important;
    }

    .invite-btn,
    .create-team-btn {
        border-radius: 25px;
        background: #817f7f;
        color: #fff;
        font-weight: bold;
        padding: 0 15px;
    }

    .invite-btn:hover,
    .create-team-btn:hover {
        cursor: pointer;
    }

    .sidebar-divider {
        border-color: rgba(79, 101, 128, 0.44);
        margin-top: 12px;
        margin-bottom: 12px;
    }

    .create-team-btn {
        margin: unset !important;
    }

    .team-circle {
        font-size: 24px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        margin: 0 5px;
        background: #fff;
    }

    .pending-invite {
        margin-left: 6px;
        font-size: 12px;
        font-style: italic;
        color: #f2a731;
        font-weight: bold;
        display: block;
    }

    .invite-info {
        background: rgb(255, 253, 222);
        text-align: center;
        margin-top: 20px;
        padding: 10px 0;
    }

    .upgrade-button {
        background: rgb(252, 189, 1);
        color: rgb(255, 255, 255);
        padding: 0 30px;
        font-size: 13px;
        margin-left: 35px;
    }

    .upgrade-button:hover {
        cursor: pointer;
        background: rgb(255, 216, 99);
    }

    .team-dialog .el-dialog__body {
        padding: unset !important;
    }

    .create-team-btn-mobile {
        display: none;
        background-color: transparent;
        border: none;
    }

    .create-team-btn-mobile img {
        width: 30px;
    }

    @media (max-width: 750px) {
        .team-dialog .el-dialog {
            width: 90%;
        }

        .team-header.new-team .create-team-btn {
            display: none;
        }

        .team-header.new-team .create-team-btn-mobile {
            display: block;
        }
    }

    .el-tooltip__popper {
        max-width: 200px;
    }
</style>

<style lang="scss" scoped>
    .el-aside {
        background-color: #545c64;
        position: fixed;
        height: 100%;
    }

    #spa-projects .hideSidebar .sidebar-container {
        width: 54px !important;
    }

    #spa-projects .sidebar-container {
        -webkit-transition: width 0.28s;
        transition: width 0.28s;
        width: 220px !important;
        background-color: #304156;
        height: 100%;
        position: fixed;
        font-size: 0;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 1001;
        overflow: hidden;
    }

    #spa-projects .sidebar-container .el-scrollbar {
        height: 100%;
    }

    .el-scrollbar {
        overflow: hidden;
        position: relative;
    }

    .scrollbar-wrapper {
        overflow-x: hidden !important;
    }

    .el-scrollbar__wrap {
        overflow: scroll;
        height: 100%;
    }

    #spa-projects .sidebar-container .el-menu {
        border: none;
        height: 100%;
        width: 100% !important;
    }

    #spa-projects .sidebar-container .is-horizontal {
        display: none;
    }

    .el-scrollbar__bar.is-horizontal {
        height: 6px;
        left: 2px;
    }

    #spa-projects .sidebar-container .el-scrollbar__bar.is-vertical {
        right: 0;
    }

    .el-scrollbar__bar.is-vertical {
        width: 6px;
        top: 2px;
    }

    .el-scrollbar__bar {
        position: absolute;
        right: 2px;
        bottom: 2px;
        z-index: 1;
        border-radius: 4px;
        opacity: 0;
        -webkit-transition: opacity 0.12s ease-out;
        transition: opacity 0.12s ease-out;
    }

    .el-scrollbar:hover .el-scrollbar__bar {
        opacity: 1;
        -webkit-transition: opacity 0.34s ease-out;
        transition: opacity 0.34s ease-out;
    }

    .el-scrollbar__bar.is-vertical>div {
        width: 100%;
    }

    .el-scrollbar__thumb {
        position: relative;
        display: block;
        width: 0;
        height: 0;
        cursor: pointer;
        border-radius: inherit;
        background-color: rgba(144, 147, 153, 0.3);
        -webkit-transition: background-color 0.3s;
        transition: background-color 0.3s;
    }
</style>