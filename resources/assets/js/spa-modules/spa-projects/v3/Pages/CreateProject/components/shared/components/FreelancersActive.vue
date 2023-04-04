<template>
    <section class="section-panel">
        <div class="section-header tw-flex tw-justify-between tw-items-center">
            <div>
                <div class="header"><i class="el-icon-user"></i> Freelancers</div>
                <small class="tw-italic"
                    >Allow freelancer to be can edit in this project</small
                >
            </div>
            <el-dropdown @command="handleCommandChangeTeam" trigger="click">
                <el-button size="mini" type="primary">
                    Team: {{teams.find(team => team.id == selectedTeam) ? teams.find(team => team.id == selectedTeam).name : 'Select Team'}}<i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item v-for="team in teams" :key="team.id"  :command="team.id" >
                        <i class="el-icon-success color-success" v-if="selectedTeam == team.id"></i> {{ team.name }}</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
        <div class="section-body">
            <div class="tw-mb-8">
                <div
                    class="tw-flex tw-items-end tw-justify-between tw-px-4 tw-rounded tw-bg-gray-50 tw-shadow tw-py-2 tw-mb-4"
                    v-for="(member, key) in teamMembers"
                    :key="key"
                >
                    <div class="tw-flex tw-items-center">
                        <el-avatar :src="member.photo_url"> </el-avatar>
                        <div class="tw-mx-4">
                            <div style="line-height:1">{{ member.name }}</div>
                            <small class="">{{ member.email }}</small>
                        </div>
                    </div>
                    <div>
                        <label class="switch">
                            <input
                                type="checkbox"
                                name="new_project"
                                v-model="check"
                                :value="member.id"
                                :id="member.id"
                                @change="modifyMemberAccess"
                            />
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div
                    class="tw-flex tw-items-center tw-justify-center tw-px-4 tw-rounded tw-bg-gray-50 tw-shadow tw-py-4"
                    v-if="teamMembers.length == 0"
                >
                    <div>There is no team member added, please go to</div>
                    <router-link :to="{ name: 'teams'}"  class="tw-mx-2 tw-font-semibold tw-text-primary">teams</router-link>
                    to add new member.
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import {
        mapGetters,
        mapActions
    } from 'vuex';
    export default {
        props: ['project', 'project_id', 'type', 'status'],
        computed: {
            ...mapGetters(['teams', 'user']),
            teamMembers() {
                const team = this.teams.find(team => team.id == this.selectedTeam)

                if (team) {
                    return team.users.filter(user => user.pivot.role == 'member')
                }
                return [];
            }
        },
        data() {
            return {
                check: [],
                selectedTeam: null,
            }
        },
        methods: {
            handleCommandChangeTeam(command) {
                this.selectedTeam = command
            },
            fetchData() {
                if (this.project && this.project.users) {
                    this.check =  _.map(this.project.users.filter(user => user.pivot.role == 'freelancer'), 'id')
                }
            },
            modifyMemberAccess(event) {
                let formData = {
                    member_id: event.target.id,
                    project_id: this.project_id,
                };

                if (event.target.checked) {
                    axios
                        .post("/api/projects/add-team-member", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                this.$emit('updateProject', response.data.data)
                                toastr['success'](response.data.message, 'Success');
                            } else {
                                event.target.checked = false;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            event.target.checked = false;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                            this.handle_error({});
                        });
                } else {
                    axios
                        .delete("/api/projects/delete-team-member/" + this.project_id + '/' + event.target.id)
                        .then(response => {
                            if (response.data.status == 1) {
                                this.$emit('updateProject', response.data)
                                toastr['success'](response.data.message, 'Success');
                            } else {
                                event.target.checked = true;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            event.target.checked = true;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                        });
                }
            },
        },
        
        watch: {
            status() {
                this.fetchData()
            }
        },
        mounted() {
            this.fetchData()
            this.selectedTeam = this.user.current_team_id
        }
    }
</script>

<style lang="scss" scoped>
    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 49px;
        height: 23px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        display: none;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }

    .slider:before {
        position: absolute;
        content: '';
        height: 15px;
        width: 15px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: 0.4s;
        transition: 0.4s;
    }

    input:checked + .slider {
        background-color: #80b4a0;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #80b4a0;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .notify-body {
        text-align: center;
    }

    .notify-info {
        margin-bottom: 20px;
    }

    .notify-label {
        text-align: right;
        font-weight: normal;
    }

    @media (max-width: 550px) {
        .notify-label .member-emailaddress {
            margin-left: 10px !important;
        }
        .notify-label .user-avatar {
            margin: 0 !important;
        }
    }
</style>
