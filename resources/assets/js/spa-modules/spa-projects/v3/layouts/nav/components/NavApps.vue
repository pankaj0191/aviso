<template>
    <el-dropdown trigger="click">
        <span class="el-dropdown-link">
            <el-tooltip class="item" effect="dark" content="Prooflo Apps">
                <i class="el-icon-s-grid icon"></i>
            </el-tooltip>
        </span>
        <el-dropdown-menu style="width: 348px;" slot="dropdown">
            <el-row class="menu-row">
                <el-col :span="8" class="menu-content" v-if="currentRole != 'Collaborator'">
                    <router-link tag="div" :to="{ name: 'time-trackers'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-play-circle">
                            <circle cx="12" cy="12" r="10" />
                            <polygon points="10 8 16 12 10 16 10 8" />
                        </svg>
                        <div class="title">Time Tracker</div>
                    </router-link>
                </el-col>
                <el-col :span="8" class="menu-content">
                    <router-link tag="div" :to="{ name: 'calender'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-calendar">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <div class="title">Calender</div>
                    </router-link>
                </el-col>
                <el-col :span="8" class="menu-content" v-if="currentRole != 'Collaborator'">
                    <router-link tag="div" :to="{ name: 'drive'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-hard-drive">
                            <line x1="22" y1="12" x2="2" y2="12" />
                            <path
                                d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                            <line x1="6" y1="16" x2="6.01" y2="16" />
                            <line x1="10" y1="16" x2="10.01" y2="16" />
                        </svg>
                        <div class="title">Drive</div>
                    </router-link>
                </el-col>
                <el-col :span="8" class="menu-content" v-if="currentRole != 'Collaborator'">
                    <router-link tag="div" :to="{ name: 'teams'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <div class="title">Team</div>
                    </router-link>
                </el-col>
                <el-col :span="8" class="menu-content">
                    <router-link tag="div" :to="{ name: 'clients'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-user">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                        <div class="title" v-if="isBothRole">Clients</div>
                        <div class="title" v-else>Freelancers</div>
                    </router-link>
                </el-col>
                <el-col :span="8" class="menu-content" v-if="currentRole != 'Collaborator'">
                    <router-link tag="div" :to="{ name: 'invoices'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-file-text">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="16" y1="13" x2="8" y2="13" />
                            <line x1="16" y1="17" x2="8" y2="17" />
                            <polyline points="10 9 9 9 8 9" />
                        </svg>
                        <div class="title">Invoices</div>
                    </router-link>
                </el-col>
                <el-col :span="8" class="menu-content" v-if="isBothRole">
                    <router-link tag="div" :to="{ name: 'categories'}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-grid">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <div class="title">Categories</div>
                    </router-link>
                </el-col>
            </el-row>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
    export default {
        props: ['isBothRole', 'currentRole']
    }
</script>

<style lang="scss" scoped>
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;

    .icon {
        font-size: 24px;
        cursor: pointer;
    }

    .menu-row {
        padding: 16px;

        .menu-content {
            .router-link-exact-active {
                color: $green;
            }

            cursor: pointer;
            margin-bottom: 16px;
            text-align: center;

            .menu-icon {
                font-size: 48px;
            }

            .title {
                font-weight: 500;
                font-size: 14px;
            }
        }
    }
</style>