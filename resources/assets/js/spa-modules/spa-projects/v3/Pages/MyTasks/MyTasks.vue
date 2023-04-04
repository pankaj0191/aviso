<template>
    <div>
        <div>
            <el-card class="tasks-card tw-rounded tw-shadow">
                <div class="tw-flex tw-items-center tw-justify-between">
                    <div>
                        <el-dropdown @command="handleCommandTaskFiltration" trigger="click">
                            <el-button size="mini" type="primary">
                                Task: {{taskFiltration > 0 ? findTeam() : taskFiltration}}<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="'All'"><i class="el-icon-success color-success"
                                        v-if="taskFiltration == 'All'"></i> All</el-dropdown-item>
                                <el-dropdown-item :command="'My Tasks'" ><i class="el-icon-success color-success"
                                        v-if="taskFiltration == 'My Tasks'"></i> My Tasks</el-dropdown-item>
                                <el-dropdown-item disabled divided style="color:#9fa6b2;font-size:12px">Teams</el-dropdown-item>
                                <el-dropdown-item :command="team.id" v-for="(team, key) in teams" :key="key"><i class="el-icon-success color-success"
                                        v-if="taskFiltration == team.id"></i> {{team.name}}</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <el-button type="info" size="mini" @click="collapse = !collapse">{{collapse ? 'Expend' : 'Collapse'}}</el-button>
                        <el-dropdown @command="handleCommandTaskUsersFiltration" trigger="click" v-if="taskFiltration != 'All' && taskFiltration != 'My Tasks'"> 
                            <el-button size="mini" type="default">
                                <span class="tw-inline-flex tw-items-center">
                                    <img class="tw-rounded-full tw-mr-2" v-if="taskUsersFiltration > 0" :src="findUser('photo_url')" :width="12" /> {{taskUsersFiltration > 0 ? findUser('name') : 'Select User'}}<i class="el-icon-arrow-down el-icon--right"></i>
                                </span>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="user.id" v-for="(user, key) in getUsers()" :key="key">
                                            <div class="tw-flex tw-items-center">
                                                <div class="tw-flex tw-items-center">
                                                    <el-avatar :src="user.photo_url" class="tw-mr-2" :size="24" /> {{user.name}}
                                                </div>
                                                <i class="el-icon-success tw-ml-6" v-if="taskUsersFiltration && taskUsersFiltration == user.id"></i>
                                            </div>
                                        </el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>
                    <div>
                        <el-dropdown @command="handleCommandFilterProjectLabel" trigger="click">
                            <el-button size="mini" type="primary">
                                {{filterLabel}}<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="'INBOX'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'INBOX'"></i> INBOX</el-dropdown-item>
                                <el-dropdown-item :command="'SENT'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'SENT'"></i> SENT</el-dropdown-item>
                                <el-dropdown-item :command="'DRAFT'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'DRAFT'"></i> DRAFT</el-dropdown-item>
                                <el-dropdown-item :command="'HOLD'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'HOLD'"></i> HOLD</el-dropdown-item>
                                <el-dropdown-item :command="'APPROVED'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'APPROVED'"></i> APPROVED</el-dropdown-item>
                                <el-dropdown-item :command="'DELIVERED'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'DELIVERED'"></i> DELIVERED</el-dropdown-item>
                                <el-dropdown-item :command="'All Projects'"><i class="el-icon-success color-success"
                                        v-if="filterLabel == 'All Projects'"></i> All Projects</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <el-dropdown @command="handleCommandFilterCompleted" trigger="click">
                            <el-button size="mini" type="primary">
                                {{filterCompleted}}<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="'Incompleted'"><i class="el-icon-success color-success"
                                        v-if="filterCompleted == 'Incompleted'"></i> Incomplete tasks</el-dropdown-item>
                                <el-dropdown-item :command="'Completed'"><i class="el-icon-success color-success"
                                        v-if="filterCompleted == 'Completed'"></i> Completed tasks</el-dropdown-item>
                                <el-dropdown-item :command="'All Tasks'"><i class="el-icon-success color-success"
                                        v-if="filterCompleted == 'All Tasks'"></i> All tasks</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <el-dropdown @command="handleCommandFilterTypes" trigger="click">
                            <el-button size="mini" type="primary">
                                Type: {{filterTypes}}<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="'Design'"><i class="el-icon-success color-success"
                                        v-if="filterTypes == 'Design'"></i> Design</el-dropdown-item>
                                <el-dropdown-item :command="'Website'"><i class="el-icon-success color-success"
                                        v-if="filterTypes == 'Website'"></i> Website</el-dropdown-item>
                                <el-dropdown-item :command="'Video'"><i class="el-icon-success color-success"
                                        v-if="filterTypes == 'Video'"></i> Video production</el-dropdown-item>
                                <el-dropdown-item :command="'All'"><i class="el-icon-success color-success"
                                        v-if="filterTypes == 'All'"></i> All types</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                        <el-dropdown @command="handleCommandFilterTags" trigger="click">
                            <el-button size="mini" type="primary">
                                Priority: {{filterTags}}<i class="el-icon-arrow-down el-icon--right"></i>
                            </el-button>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item :command="'High'"><i class="el-icon-success color-success"
                                        v-if="filterTags == 'High'"></i> High</el-dropdown-item>
                                <el-dropdown-item :command="'Medium'"><i class="el-icon-success color-success"
                                        v-if="filterTags == 'Medium'"></i> Medium</el-dropdown-item>
                                <el-dropdown-item :command="'Low'"><i class="el-icon-success color-success"
                                        v-if="filterTags == 'Low'"></i> Low</el-dropdown-item>
                                <el-dropdown-item :command="'All'"><i class="el-icon-success color-success"
                                        v-if="filterTags == 'All'"></i> All</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </div>
                </div>
                <!-- Table header of issues  -->
                <div class="tw-flex tw-font-medium tw-text-gray-500 tw-mt-8 tw-items-center table-mytasks">
                    <div class="task-item tw-px-4 tw-py-4 ">Task name</div>
                    <div class="date-item tw-px-4 tw-py-4 ">Due Date</div>
                    <div class="tag-item tw-px-4 tw-py-4">Priority</div>
                    <div class="tag-item tw-px-4 tw-py-4">Timetracker</div>
                    <div class="empty-item tw-px-4 tw-px-4"></div>
                </div>
                <div>
                    <div>
                        <!-- Graphic Design Project Title  -->
                        <div class="tw-flex tw-items-center tw-mt-4">
                            <button type="button"
                                @click="methodCardType('design')"
                                class="tw-flex tw-items-center tw-px-2 tw-mr-2 tw-py-1 tw-font-semibold tw-rounded-md tw-border-0 tw-text-white button-icon">
                                <svg class="svg-icon" focusable="false" viewBox="0 0 32 32" v-if="cardTypeOpen.includes('design')">
                                    <path
                                        d="M7.207,13.707L16.5,23l9.293-9.293c0.63-0.63,0.184-1.707-0.707-1.707H7.914C7.023,12,6.577,13.077,7.207,13.707z">
                                    </path>
                                </svg>
                                <svg class="svg-icon" v-else focusable="false" viewBox="0 0 32 32"><path d="M13.707,6.707L23,16l-9.293,9.293C13.077,25.923,12,25.477,12,24.586V7.414C12,6.523,13.077,6.077,13.707,6.707z"></path></svg>
                            </button>
                            <div class="tw-font-semibold tw-text-base tw-text-primary tw-cursor-pointer" @click="methodCardType('design')">Graphic Design</div>
                        </div>
                        <!-- Graphic Design issues  -->
                        <transition-group name="el-zoom-in-top">
                            <project-issues v-for="design in designs" :key="design.id" v-show="cardTypeOpen.includes('design')" :collapse="collapse" :project="design" :filterCompleted="filterCompleted" :filterTags="filterTags" />
                        </transition-group>
                    </div>
                    <div>
                        <!-- Website Project Title  -->
                        <div class="tw-flex tw-items-center tw-mt-4">
                            <button type="button"
                                @click="methodCardType('website')"
                                class="tw-flex tw-items-center tw-px-2 tw-mr-2 tw-py-1 tw-font-semibold tw-rounded-md tw-border-0 tw-text-white button-icon">
                                <svg class="svg-icon" focusable="false" viewBox="0 0 32 32" v-if="cardTypeOpen.includes('website')">
                                    <path
                                        d="M7.207,13.707L16.5,23l9.293-9.293c0.63-0.63,0.184-1.707-0.707-1.707H7.914C7.023,12,6.577,13.077,7.207,13.707z">
                                    </path>
                                </svg>
                                <svg v-else class="svg-icon" focusable="false" viewBox="0 0 32 32"><path d="M13.707,6.707L23,16l-9.293,9.293C13.077,25.923,12,25.477,12,24.586V7.414C12,6.523,13.077,6.077,13.707,6.707z"></path></svg>
                            </button>
                            <div class="tw-font-semibold tw-text-base tw-text-primary tw-cursor-pointer" @click="methodCardType('website')">Website</div>
                        </div>
                        <!-- Website issues  -->
                        <transition-group name="el-zoom-in-top">
                            <project-issues v-for="website in websites" :key="website.id" v-show="cardTypeOpen.includes('website')" :collapse="collapse" :project="website" :filterCompleted="filterCompleted" :filterTags="filterTags" />
                        </transition-group>
                    </div>
                    <div>
                        <div class="tw-flex tw-items-center tw-mt-4">
                            <button type="button"
                                @click="methodCardType('video')"
                                class="tw-flex tw-items-center tw-px-2 tw-mr-2 tw-py-1 tw-font-semibold tw-rounded-md tw-border-0 tw-text-white button-icon">
                                <svg class="svg-icon" focusable="false" viewBox="0 0 32 32" v-if="cardTypeOpen.includes('video')">
                                    <path
                                        d="M7.207,13.707L16.5,23l9.293-9.293c0.63-0.63,0.184-1.707-0.707-1.707H7.914C7.023,12,6.577,13.077,7.207,13.707z">
                                    </path>
                                </svg>
                                <svg v-else class="svg-icon" focusable="false" viewBox="0 0 32 32"><path d="M13.707,6.707L23,16l-9.293,9.293C13.077,25.923,12,25.477,12,24.586V7.414C12,6.523,13.077,6.077,13.707,6.707z"></path></svg>
                            </button>
                            <div class="tw-font-semibold tw-text-base tw-text-primary tw-cursor-pointer" @click="methodCardType('video')">Video Production</div>
                        </div>
                        <transition-group name="el-zoom-in-top">
                            <project-issues v-for="video in videos" :key="video.id" v-show="cardTypeOpen.includes('video')" :collapse="collapse" :project="video" :filterCompleted="filterCompleted" :filterTags="filterTags" />
                        </transition-group>
                    </div>
                </div>
            </el-card>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import {mapGetters} from 'vuex'
    import ProjectIssues from './components/ProjectIssues.vue'
    export default {
        components: { ProjectIssues },
        data() {
            return {
                filterCompleted: 'All Tasks',
                filterLabel: 'All Projects',
                taskFiltration: localStorage.getItem('taskFilter') || 'All',
                taskUsersFiltration: localStorage.getItem('taskUserFilter') || '',
                filterTags: 'All',
                filterTypes: 'All',
                projects: [],
                activeNames: 0,
                activeNames2: 0,
                cardTypeOpen: ['design', 'website', 'video'],
                collapse: true,
            }
        },
    methods: {
            getUsers() {
                const team = this.teams.find(team => team.id == this.taskFiltration)
                if (team && team.users) {
                    return team.users
                }  
            },
            findTeam() {
                const team = this.teams.find(team => team.id == this.taskFiltration)
                if (team) {
                    return team.name
                }
            },
            findUser(data) {
                if (this.taskUsersFiltration > 0 && this.taskFiltration > 0) {
                    const team = this.teams.find(team => team.id == this.taskFiltration)
                    if (team) {
                        return team.users.find(user => user.id == this.taskUsersFiltration)[`${data}`]
                    }
                }
            },
            handleCommandFilterCompleted(command) {
                this.filterCompleted = command
            },
            handleCommandFilterProjectLabel(command) {
                this.filterLabel = command
            },
            handleCommandFilterTags(command) {
                this.filterTags = command
            },
            handleCommandFilterTypes(command) {
                this.filterTypes = command
            },
            handleCommandTaskFiltration(command) {
                localStorage.setItem('taskFilter', command)
                this.taskFiltration = command
                this.projects = []
                this.fetch(command)
            },
            handleCommandTaskUsersFiltration(command) {
                if (this.taskUsersFiltration == command) {
                    this.taskUsersFiltration = ''
                    this.fetch(this.taskFiltration)
                } else {
                    localStorage.setItem('taskUserFilter', command)
                    this.taskUsersFiltration = command
                    this.fetch(this.taskFiltration, command)
                }
            },
            methodCardType(type) {
                if (this.cardTypeOpen.includes(type)) {
                    let index = this.cardTypeOpen.indexOf(type)
                    this.cardTypeOpen.splice(index, 1)
                } else {
                    this.cardTypeOpen.push(type)
                }
            },
            async fetch(taskFilter = 'All', user = '') {
                try {
                    let {
                        data
                    } = await axios.get(`/api/proof/issues?task=${taskFilter}&user=${user}`);
                    if (data.status) {
                        this.projects = data.data
                    } else {
                        toastr["error"](data.message, "Error");
                    }
                } catch (error) {
                    console.log(error)
                }

            },
            filterIssues(issues) {
                let issuesFilter = [];
                if (this.filterCompleted == 'Completed' && this.projects.length > 0) {
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
        },
        computed: {
            ...mapGetters(['teams', 'isBothRole', 'in_progress', 'on_revision', 'on_draft', 'on_hold', 'approved', 'completed']),
            projectFilter() {
                let projects = this.projects;
                if (this.filterTypes != 'All') {
                    projects = this.projects.filter(project => {
                        return project.type == this.filterTypes.toLowerCase()
                    })
                }

                return projects
            },
            projectLabel() {
                if (this.filterLabel == 'All Projects') {
                    return this.projectFilter
                } else {
                    // Search in two projects
                    const inProgress = this.projectFilter.filter(project => this.in_progress.some(prog => prog.id == project.id))
                    const onRevision = this.projectFilter.filter(project => this.on_revision.some(rev => rev.id == project.id))
                    const onDraft = this.projectFilter.filter(project => this.on_draft.some(draf => draf.id == project.id))
                    const onHold = this.projectFilter.filter(project => this.on_hold.some(hold => hold.id == project.id))
                    const approved = this.projectFilter.filter(project => this.approved.some(appro => appro.id == project.id))
                    const completed = this.projectFilter.filter(project => this.completed.some(comp => comp.id == project.id))
                    
                    if (this.filterLabel == 'INBOX') {
                        return inProgress
                    } else if (this.filterLabel == 'SENT') {
                        return onRevision
                    } else if (this.filterLabel == 'DRAFT') {
                        return onDraft
                    } else if (this.filterLabel == 'HOLD') {
                        return onHold
                    } else if (this.filterLabel == 'APPROVED') {
                        return approved
                    } else if (this.filterLabel == 'COMPLETED') {
                        return completed
                    }
                }
            },
            designs() {
                if (this.projectLabel.length > 0) {
                    return this.projectLabel.filter(project => project.type == 'design');
                }
            },
            websites() {
                if (this.projectLabel.length > 0) {
                    return this.projectLabel.filter(project => project.type == 'website');
                }
            },
            videos() {
                if (this.projectLabel.length > 0) {
                    return this.projectLabel.filter(project => project.type == 'video');
                }
            }
    },
    watch: {
        teams: {
            handler: function (val, oldVal) {
                if (this.teams.length > 0 && this.taskFiltration > 0) {
                    this.fetch(this.taskFiltration, Number(localStorage.getItem('taskUserFilter') ? localStorage.getItem('taskUserFilter') : this.taskUsersFiltration));
                } else {
                    this.fetch(this.taskFiltration);
                }
            },
            deep: true
        }
    },
    async mounted() {
        const taskFilter = localStorage.getItem('taskFilter') ? localStorage.getItem('taskFilter') : this.taskFiltration;
            if (this.teams.length > 0 && taskFilter > 0) {    
                await this.fetch(localStorage.getItem('taskFilter') ? localStorage.getItem('taskFilter') : this.taskFiltration, localStorage.getItem('taskUserFilter') ? localStorage.getItem('taskUserFilter') : this.taskUsersFiltration);
            } else {
                await this.fetch(localStorage.getItem('taskFilter') ? localStorage.getItem('taskFilter') : this.taskFiltration)
            }
        }
    }
</script>

<style lang="scss">
    .table-mytasks {
        border-top: 1px solid #e8ecee;
        border-bottom: 1px solid #e8ecee;
        font-size: 12px;

        .task-item {
            border-right: 1px solid #e8ecee;
            width: 60%;
        }

        .date-item {
            border-right: 1px solid #e8ecee;
            width: 15%;
        }

        .tag-item {
            border-right: 1px solid #e8ecee;
            width: 10%;
        }

        .track-item {
            border-right: 1px solid #e8ecee;
            width: 10%;
        }

        .empty-item {
            width: 5%;
        }
    }

    .button-icon {
        height: 28px;
        min-height: 28px;
        min-width: 28px;
        width: 28px;
        background: #00000000;
        border-color: #00000000;
        fill: #6f7782;

        // background-color: #fff0;
        &:hover {
            background-color: #fff5;
            background: rgba(21, 7, 38, 0.04);
            border-color: transparent;
            fill: #151b26;
        }
    }

    .svg-icon {
        height: 16px;
        width: 16px;
    }
    .tasks-card {
        .el-card__body {
            border-radius: 6px;
        }
    }
</style>