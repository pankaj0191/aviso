<template>
    <el-container class="main-container">
        <el-header>
            <div class="nav-right" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
                <ul class="nav-left">
                    <li>
                        <el-tooltip class="item" effect="dark" content="Minimize Menu">
                            <i class="el-icon-s-fold icon hidden-xs-only" @click="toggleCollapse"></i>
                        </el-tooltip>
                    </li>
                    <li class="nav-left-item">
                        <router-link class="dropdown-link" tag="a" :to="{name: 'create-project', params: {role: 'project'}}"
                            v-if="isBothRole && user && isSubscribed">
                            <el-tooltip class="item" effect="dark" content="Add New Project">
                                    <div slot="content">Learn how to create new project.
                                    <a href="https://www.youtube.com/watch?v=guCwdHngTmM" target="_blank"  class="custom-content-tooltip"> <i class="el-icon-caret-right custom-video-play-icon"></i> <span class="prooflo-text">Play Video</span></a>
                                </div>
                                <i class="el-icon-circle-plus-outline icon"></i>
                            </el-tooltip>
                        </router-link>

                        <router-link class="dropdown-link" tag="a" :to="{name: 'create-project', params: {role: 'request'}}"
                            v-if="!isBothRole && user">
                            <el-tooltip class="item" effect="dark" content="Add New Design Request">
                                <i class="el-icon-circle-plus-outline icon"></i>
                            </el-tooltip>
                        </router-link>

                        <!-- <el-dropdown trigger="click" placement="bottom-start" @command="handleCommandCreateProject">
							<span class="el-dropdown-link">
								<i class="el-icon-circle-plus-outline icon"></i>
							</span>
							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item command="design">
									<i class="fa fa-globe"></i>
									Graphic Design
								</el-dropdown-item>
								<el-dropdown-item command="website">
									<i class="el-icon-picture-outline"></i>
									Website
								</el-dropdown-item>
								<el-dropdown-item command="video" disabled>
									<i class="el-icon-video-camera"></i>
									Videography
								</el-dropdown-item>
							</el-dropdown-menu>
						</el-dropdown>-->
                    </li>
                </ul>
            </div>
            <div class="nav-center">
                <transition name="el-zoom-in-center">
                    <input-search :searchAble="searchAble" :projects="projects" />
                </transition>
            </div>
            <ul class="nav-left">
                <li class="nav-left-item">
                    <i v-if="projects && projects.length > 0" class="el-icon-search icon"
                        @click="searchAble = !searchAble"></i>
                </li>
                <li class="nav-left-item" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
                    <nav-apps :isBothRole="isBothRole" :currentRole="currentRole" />
                </li>
                <li class="nav-left-item notification" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
                    <notification :user="user" />
                </li>
                <li class="nav-left-item" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
                    <profile-menu />
                </li>
            </ul>
        </el-header>
        <el-main>
            <router-view></router-view>
        </el-main>
    </el-container>
</template>

<script>
    import {
        mapGetters
    } from "vuex";
    import InputSearch from './components/InputSearch'
    import NavApps from './components/NavApps.vue';
    import Notification from './components/Notification.vue';
    import ProfileMenu from './components/ProfileMenu.vue';

    export default {
        components: {
            InputSearch,
            NavApps,
            Notification,
            ProfileMenu,
        },
        data() {
            return {
                isCollapse: false,
                searchAble: false,
            };
        },
        computed: {
            ...mapGetters([
                "user",
                "isBothRole",
                "currentRole",
                "isSubscribed",
                "windowBreakPoint",
                "projects"
            ]),
            windowWidth() {
                return this.$store.state.users.windowWidth;
            },
            spacePrefix() {
                return spacePrefix;
            },
        },
        methods: {
            toggleCollapse() {
                this.isCollapse = !this.isCollapse;
                this.$emit("isCollapse", this.isCollapse);
            },
        },

    };
</script>

<style lang="scss" scoped>
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;

    .hideSidebar .main-container {
        margin-left: 54px;
    }

    .main-container {
        min-height: 100%;
        -webkit-transition: margin-left 0.28s;
        transition: margin-left 0.28s;
        margin-left: 220px;
        position: relative;

        .el-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: $box-shadow;
            background-color: #fff;

            .nav-left {
                display: flex;
                align-items: center;
                height: 100%;
                margin: 0;

                .notification {
                    margin-right: 12px;
                }

                .nav-left-item {
                    padding: 0 8px;
                }
            }
        }
    }

    .icon {
        font-size: 24px;
        cursor: pointer;
    }


    .dropdown-link {
        color: #606266;
    }

    .dropdown-link:hover {
        color: $green;
    }

    .el-dropdown-menu__item:hover .dropdown-link {
        color: $green;
        text-decoration: none;
    }

    .primary {
        color: $green;
    }

    .nav-center {
        width: 50%;
    }

    @media only screen and (max-width: 576px) {
        .nav-center {
            width: 90%;
        }
    }

    @media only screen and (max-width: 1200px) and (min-width: 577px) {
        .nav-center {
            width: 40%;
        }
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
<style lang="scss">
    $green: #80b4a0;

    .profile-active {
        color: $green;
        font-weight: 500;
    }
</style>

