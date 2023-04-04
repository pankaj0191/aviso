<template>
    <div>
        <div class="project-content">
            <div class="content-header">
                <div>
                    <div class="card-title">
                        <i :class="projectType(project)"></i> {{ project.name | truncate(20, '...') | capitalize }}
                    </div>
                   <el-tooltip
                        v-if="project.active_revision.proofs.length > 0 && project.type == 'design'"
                        :content="`Revision Round R - ${project.active_revision.version}`"
                        placement="bottom"
                   >
                        <div class="company"> <span class="revision" >R
                            - {{ project.active_revision.version }}</span></div>
                   </el-tooltip>
                </div>
                <!-- TODO: Time Tracker -->
                <div class="tw-flex">
                    <el-tooltip
                            v-if="isBothRole && project.timeTracker && project.timeTracker.end === null && project.timeTracker.start"
                            content="Stop Tracker"
                            placement="bottom"
                        >
                        <div class="stop-tracker" @click="stopTracker(project)">
                            <i class="fa fa-stop icon-time-tracker-stop"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip
                            v-else-if="isBothRole"
                            content="Start Tracker"
                            placement="bottom"
                        >
                        <div class="play-tracker" @click="startTracker(project)"><i class="fa fa-play icon-time-tracker"></i></div>
                    </el-tooltip>
                    <div class="calender">
                        <el-tooltip
                            content="Over due calender"
                            v-if="checkOverDue(project)"
                            placement="bottom">
                            <router-link tag="div" :to="{ name: 'calender'}" class="over-due" >
                                <i class="el-icon-alarm-clock"></i>
                            </router-link>
                        </el-tooltip>
                        <el-tooltip
                            content="In due calender"
                            v-else-if="checkDue(project)"
                            placement="bottom">
                            <router-link tag="div" :to="{ name: 'calender'}" class="in-due" >
                                <i class="el-icon-alarm-clock"></i>
                            </router-link>
                        </el-tooltip>
                    </div>
                </div>
            </div>
            <div class="content-footer">
                <div><i class="el-icon-warning"></i> {{project.solved_issues}} of {{project.total_issues}}</div>
            </div>
        </div>
        <div class="project-progress">
            <el-progress :status="(project.percentage == 100) ? 'success' : 'warning'" :percentage="project.percentage"></el-progress>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['project', 'isBothRole'],
        data() {
            return {
                tracker: [],
            }
        },
        methods: {
            projectType(project) {
                switch (project.type) {
                    case 'website':
                        return 'fa fa-globe'
                        break;
                    case 'design':
                        return 'el-icon-picture-outline'
                        break;
                    case 'video':
                        return 'el-icon-video-play'
                        break;
                }
            },
            startTracker(project) {
                this.$emit('startTracker', project)
            },
            stopTracker(project) {
                this.$emit('stopTracker', project)
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
        },
        filters: {
            capitalize(value) {
                if (!value) return "";
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            truncate: function (text, length, suffix) {
                if (text.length > length) {
                    return text.substring(0, length) + suffix;
                } else {
                    return text;
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
    .project-progress {
        text-align: center;
        padding-bottom: 8px;
    }

    .project-content {
        padding: 8px 16px 0px 16px;

        .content-header {
            .calender {
                cursor: pointer;
            .over-due {
                background: #ef5b5b;
                color: #fff;
                display: table;
                width: 24px;
                font-size: 12px;
                border-radius: 100%;
                height: 24px;
                box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.2),
                    0 4px 20px 0 rgba(0, 0, 0, 0.19);
                i {
                    display: table-cell;
                    vertical-align: middle;
                    text-align: center;
                    color: #fff;
                }
            }

            .in-due {
                display: table;
                width: 24px;
                font-size: 12px;
                border-radius: 100%;
                height: 24px;
                background: #80b4a0;
                color: #fff;
                box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.2),
                    0 4px 20px 0 rgba(0, 0, 0, 0.19);
                i {
                    display: table-cell;
                    vertical-align: middle;
                    text-align: center;
                    color: #fff;
                }
            }
        }
            .card-title {
                font-size: 14px;
                font-weight: 700;
            }

            display: flex;
            justify-content: space-between;
            align-items: start;

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

                i {
                    // display: table-cell;
                    // vertical-align: middle;
                    // text-align: center;
                }
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

            


            .company {
                .revision {
                    background-color: #E2E8F0;
                    padding: 0px 6px;
                    color: #718096;
                    border-radius: 4px;
                    font-size: 10px;
                }
            }
        }

        .content-footer {
            margin-top: 6px;
            text-align: center;
        }
    }
</style>