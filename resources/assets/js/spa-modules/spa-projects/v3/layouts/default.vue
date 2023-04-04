<template>
    <div class="container-fluid" style="padding-left: 0px; padding-right: 0px">
        <el-container :class="isCollapse ? 'hideSidebar' : 'openSidebar'">
            <div class="sidebar-container">
                <!---->
                <div class="el-scrollbar">
                    <div class="scrollbar-wrapper el-scrollbar__wrap"
                        style="margin-bottom: -15px; margin-right: -15px;">
                        <div class="el-scrollbar__view">
                            <sidebar :isCollapse="isCollapse" />
                            {{upgradeToTeamPlan()}}
                            <hr class="sidebar-divider"/>
                            <div class="text-center" v-if="!upgradeToTeamPlan()">
                                <el-tooltip class="item" effect="dark" placement="right">
                                    <div slot="content">
                                        <div>Learn how to upgrade</div>
                                        <a href="https://www.youtube.com/watch?v=guCwdHngTmM" target="_blank"  class="custom-content-tooltip"> <i class="el-icon-caret-right custom-video-play-icon"></i> <span class="prooflo-text">Learn More Play Tutorial</span></a>
                                    </div>
                                    <a href="/settings#/subscription">
                                        <el-button type="primary" size="small" >Upgrade To Team Plan</el-button>
                                    </a>
                                </el-tooltip>
                            </div>
                            <navigation-team v-else-if="upgradeToTeamPlan() == 'active'" :isCollapse="isCollapse" @showMemberProjects="showMemberProjects"
                                @showMembersDialog="showMembersDialog"/>
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
            <main-nav @isCollapse="toggleCollapse" />
        </el-container>
        <el-dialog v-if="activePlan" :title="currentTeam.name" :show-close="true" :visible.sync="showDialogInviteTeamMembers"
            class="team-dialog" @open="onOpenDialogNewTeamMembers" @close="onCloseDialogNewTeamMembers" center>
            <invite-team v-if="showDialogInviteTeamMembers" @closeDialog="hideMembersDialog" :currentTeam="currentTeam">
            </invite-team>
        </el-dialog>
    </div>
</template>

<script>
    import MainNav from "./nav/Main";
    import Sidebar from './components/Sidebar'
    import InviteTeam from "./../../partials/InviteTeamSecond";
    import NavigationTeam from "./../../layout/NavigationTeam";
    import {
        mapActions,
        mapGetters
    } from "vuex";
    export default {
        components: {
            MainNav,
            Sidebar,
            InviteTeam,
            NavigationTeam
        },
        data() {
            return {
                isCollapse: false,
                fullscreenLoading: false,
                showDialogInviteTeamMembers: false,
                newTeamMembers: [],
                currentTeam: {},
            }
        },
        watch: {
            windowWidth(val) {
                this.updateWindowWidth()
            }
        },
        methods: {
            ...mapActions(['loadTeams']),
            toggleCollapse(val) {
                this.isCollapse = val;
            },
            updateWindowWidth() {
                if (window.innerWidth <= 768) {
                    this.isCollapse = true;
                } else {
                    this.isCollapse = false;
                }
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
            handleClose(key, keyPath) {
                this.newTeamMembers.splice(this.newTeamMembers.indexOf(key), 1);
            },
            showMembersDialog(row) {
                if (!this.activePlan || this.activePlan.teams_members_count <= 1) {
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
            upgradeToTeamPlan() {
                if (this.activePlan && this.activePlan.teams_count > 0 && this.currentRole == 'Agency' || (this.hasTeams && this.isBothRole)) {
                    return 'active'
                } else if (this.currentRole != 'Agency') {
                    return 'deactivate'
                }
                return false
            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix;
            },
            windowWidth() {
                return this.$store.state.users.windowWidth;
            },
            ...mapGetters([
                "user",
                "activePlan",
                "current_member",
                'isBothRole',
                "currentRole",
                "hasTeams",
                "membersLimit"
            ]),
        },
        mounted() {
            this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
        },
        created() {
            this.updateWindowWidth();
			this.loadTeams();
            window.addEventListener("resize", this.handleWindowResize);
        },
        destroyed() {
            window.removeEventListener("resize", this.handleWindowResize);
        },
    }
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
        margin-left: 16px;
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
    @import "../../../../../sass/layouts/layout.scss";
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