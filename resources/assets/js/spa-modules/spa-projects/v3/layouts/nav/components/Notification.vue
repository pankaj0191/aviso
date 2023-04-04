<template>
    <el-dropdown trigger="click">
        <span class="el-dropdown-link">
            <el-badge v-if="notifications && hasUnreadNotificationsCount > 0" :value="hasUnreadNotificationsCount"
                :max="99" class="item">
                <i class="el-icon-bell icon" @click="markNotificationsAsRead"></i>
            </el-badge>
            <i class="el-icon-bell icon" v-else></i>
        </span>
        <el-dropdown-menu style="    width: 365px;" slot="dropdown" class="notification-dropdown">
            <div class="infinite-list" style="overflow:auto;max-height:300px;" v-infinite-scroll="loadMore"
                infinite-scroll-disabled="disabled">
                <ul class="bordered-items" v-if="notifications && notifications.notifications.data.length > 0">
                    <router-link tag="li" :class="['notification', (notification.read == 2 && 'notification-read' )]"
                        v-for="(notification,key) in notifications.notifications.data" :key="key"
                        :to="`/${notification.action_url}`" @click.native="markNotificationsAsReadOne(notification.id)">
                        <div class="notification-content">
                            <span class="feather-icon">
                                <i :class="notification.icon"></i>
                            </span>
                            <div class="notification-text">
                                <span class="notification-title">{{notification.project}}</span>
                                <small>{{notification.body}}</small>
                            </div>
                        </div>
                        <small class="notification-ago">{{notificationAgo(notification.created_at)}}
                            <div class="notification-tag" v-if="notification.company"><el-tag  size="small">{{notification.company}}</el-tag></div>
                        </small>
                    </router-link>
                </ul>
                <ul class="bordered-items" v-else>
                    <li class="bordered-items">
                        <div class="notification-content">
                            <h4 class="text-center">You not have notifications</h4>
                        </div>
                    </li>
                </ul>
                <div class="text-center">
                    <div v-if="loading">
                        <div class="lds-ellipsis" v-if="loading">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    <p v-if="noMore">No more</p>
                </div>
            </div>
        </el-dropdown-menu>
    </el-dropdown>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                loadingNotifications: false,
                notifications: null,
                loading: false
            }
        },
        computed: {
            /**
             * Determine if the user has any unread notifications.
             */
            hasUnreadNotifications() {
                if (this.notifications) {
                    return (
                        _.filter(
                            this.notifications.notifications.data,
                            notification => {
                                return notification.read == 0;
                            }
                        ).length > 0
                    );
                }

                return false;
            },
            /**
             * Determine if the user has any unread notifications.
             */
            hasUnreadNotificationsCount() {
                if (this.notifications) {
                    return _.filter(
                        this.notifications.notifications.data,
                        notification => {
                            return notification.read == 0;
                        }
                    ).length;
                }
                return 0;
            },
            noMore() {
                if (this.notifications && this.notifications.notifications.data) {
                    return (
                        this.notifications.notifications.total <=
                        this.notifications.notifications.data.length
                    );
                }
            },
            disabled() {
                return this.loading || this.noMore;
            },
        },
        methods: {
            async loadMore() {
                this.loading = true;
                let current_page = this.notifications.notifications.current_page;
                await axios
                    .get(`/notifications/fetch`, {
                        params: {
                            page: current_page + 1
                        }
                    })
                    .then(response => {
                        this.notifications.notifications.current_page =
                            response.data.notifications.current_page;
                        this.notifications.notifications.next_page_url =
                            response.data.notifications.next_page_url;
                        this.notifications.notifications.data = [
                            ...this.notifications.notifications.data,
                            ...response.data.notifications.data
                        ];
                        this.loading = false;
                    })
                    .catch(error => {
                        this.loading = false;
                        console.log(error);
                    });
            },
            /**
             * Get the application notifications.
             */
            getNotifications() {
                this.loadingNotifications = true;

                axios.get("/notifications/fetch").then(response => {
                    this.notifications = response.data;
                    this.loadingNotifications = false;
                });
            },
            /**
             * Mark the current notifications as read.
             */
            markNotificationsAsRead() {
                if (!this.hasUnreadNotifications) {
                    return;
                }

                axios.put("/notifications/read", {
                    notifications: _.map(
                        this.notifications.notifications.data,
                        "id"
                    )
                });

                this.notifications.notifications.data.forEach(notification => {
                    notification.read = 1;
                });
            },
            /**
             * Mark the current notifications as read.
             */
            markNotificationsAsReadOne(id) {
                let countNotifi =
                    _.filter(
                        this.notifications.notifications.data,
                        notification => {
                            return notification.read != 2 && notification.id == id;
                        }
                    ).length > 0;
                if (!countNotifi) {
                    return;
                }

                axios.put("/notifications/read-one", {
                    notifications: id
                });

                this.notifications.notifications.data.forEach(notification => {
                    if (notification.id == id) {
                        notification.read = 2;
                    }
                });
            },
            notificationAgo(data) {
                return moment.utc(data).fromNow();
            }
        },
        mounted() {
            Echo.channel("notifiction-realtime").listen(
                "NotificationRealTime",
                e => {
                    if (this.user) {
                        if (e.notification.user_id == this.user.id) {
                            this.notifications.notifications.data.unshift(
                                e.notification
                            );
                            this.$notify({
                                title: e.notification.project,
                                message: e.notification.body,
                                iconClass: e.notification.icon,
                                position: "bottom-left"
                            });
                        }
                    }
                }
            );
        },
        created() {
            this.getNotifications();
        }
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

    .el-dropdown-menu {
        padding: 0;
        margin: 0;
    }

    .icon {
        font-size: 24px;
        cursor: pointer;
    }

    .bordered-items {
        .notification {
            display: flex;
            justify-content: space-between;
            padding: 16px;
            cursor: pointer;

            .notification-content {
                display: flex;
                align-self: start;

                .feather-icon {
                    user-select: none;
                    color: $green;
                    font-size: 16px;
                    margin-right: 4px;
                }

                .notification-text {
                    margin: 0 6px;

                    .notification-title {
                        color: $green;
                        font-weight: 500;
                        display: block;
                    }
                }
            }

            .notification-ago {
                margin-top: 4px;
                white-space: nowrap;
                .notification-tag {
                    text-transform: capitalize;
                    margin-top: 6px;
                    text-align: right;
                }
            }
        }
    }

    ul.bordered-items>li:not(:last-of-type):not([class*="shadow"]) {
        border-bottom: 1px solid #dae1e7;
    }

    .notification-dropdown {
        padding: 0;

        .notification:hover {
            background-image: linear-gradient(rgba(29, 33, 41, 0.04),
                    rgba(29, 33, 41, 0.04));
        }

        .notification-read {
            background-color: #edf2fa;
        }
    }

    .lds-ellipsis {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 50px;
    }

    .lds-ellipsis div {
        position: absolute;
        top: 33px;
        width: 13px;
        height: 13px;
        border-radius: 50%;
        background: $green;
        animation-timing-function: cubic-bezier(0, 1, 1, 0);
    }

    .lds-ellipsis div:nth-child(1) {
        left: 8px;
        animation: lds-ellipsis1 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(2) {
        left: 8px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(3) {
        left: 32px;
        animation: lds-ellipsis2 0.6s infinite;
    }

    .lds-ellipsis div:nth-child(4) {
        left: 56px;
        animation: lds-ellipsis3 0.6s infinite;
    }

    @keyframes lds-ellipsis1 {
        0% {
            transform: scale(0);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes lds-ellipsis3 {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(0);
        }
    }

    @keyframes lds-ellipsis2 {
        0% {
            transform: translate(0, 0);
        }

        100% {
            transform: translate(24px, 0);
        }
    }
</style>