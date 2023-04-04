<template>
    <div>
        <div v-if="!loading">
            <div>
                <el-alert :title="allowProject().message" :type="allowProject().status ? 'success' : 'warning'"
                    show-icon>
                </el-alert>
                <div v-if="allowProject().status" class="project-inv">
                    <el-row type="flex" justify="center">
                        <el-col :xs="20" :sm="12" :md="10" :lg="8" :xl="6">
                            <el-card class="box-card">
                                <div class="capitalize">
                                    <div>Project Name: <b>{{allowProject().data.name}}</b></div>
                                    <div>Freelancer / Agency: <b>{{allowProject().data.owner.name}}</b></div>
                                    <div>Type: <b>{{allowProject().data.type}}</b></div>
                                    <el-alert
                                        class="tw-mt-3"
                                        v-if="allowProject().role !== currentRole.toLowerCase()"
                                        :title="`Swtich to ${allowProject().role} role, and accept this project.`"
                                        type="warning"
                                        show-icon>
                                    </el-alert>
                                </div>
                                <div class="client-footer">
                                    <el-row :gutter="20">
                                        <el-col :md="12" class="client-deactive">
                                            <el-button size="small" style="width:100%" @click="declineProject()" :loading="acloading" type="danger">Decline</el-button>
                                        </el-col>
                                        <el-col :md="12" class="client-active">
                                            <el-button v-if="currentRole.toLowerCase() == allowProject().role" size="small" @click="acceptProject()" style="width:100%" :loading="acloading" type="success">
                                                Accept
                                            </el-button>
                                            <el-button v-else size="small" @click="acceptProject(true)" style="width:100%" :loading="acloading" type="warning">
                                                Accept as ({{ allowProject().role }})
                                            </el-button>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-card>
                        </el-col>
                    </el-row>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {
        mapGetters,
        mapActions
    } from 'vuex'
    export default {
        props: ['token'],
        data() {
            return {
                invData: {},
                loading: false,
                acloading: false,
            }
        },
        computed: {
            ...mapGetters(['user', 'currentRole']),
        },
        methods: {
            ...mapActions(['loadProjects']),
            allowProject() {
                if (this.user && this.user.id) {
                    if (!this.invData) {
                        return {
                            route: 'projects',
                            data: {},
                            message: 'This token is invalide.',
                            status: 0
                        }
                    } else if (this.invData.project) {
                        if (this.invData.project.created_by == this.user.id) {
                            return {
                                route: 'projects',
                                data: {},
                                message: 'Whoops this project not allow for you',
                                status: 0
                            }
                        }
                    } else if (this.user.id && this.user.profiles.filter(profile => profile.profile_type.value == 'client').length == 0) {
                        return {
                            route: 'create_account',
                            data: {},
                            message: 'This user not have client profile',
                            status: 0
                        }
                    }
                    return {
                        data: this.invData.project,
                        role: this.invData.role,
                        route: '',
                        message: 'You have a project to accept',
                        status: 1
                    }
                }

            },
            async declineProject() {
                this.acloading = true
                try {
                    let {
                        data
                    } = await axios.post(`/api/projects/invitation/decline/${this.token}`)
                    this.invData = data.data
                    this.$router.push({name: 'projects'})
                    this.acloading = false
                } catch (error) {
                    this.acloading = false
                    console.log(error)
                }
            },
            async switchProfile(profile_id) {
				await axios
					.post("/api/profile/switch-profile", { profile_id: profile_id })
					.then(response => {
						if (response.data.status == 1) {
							window.location.href = "/projects";
							toastr["success"](response.data.message, "Success");
							this.$store.commit(
								"UPDATE_USER_CURRENT_PROFILE",
								response.data
							);
						} else {
						}
					})

					.catch(error => {
						// this.handle_error({});
					});
			},
            async acceptProject(switchProfile = false) {
                
                this.acloading = true
                try {
                    let {
                        data
                    } = await axios.post(`/api/projects/invitation/accept/${this.token}`)
                    if (switchProfile) {
                        const profile = this.user.profiles.find(profile => profile.profile_type.value == this.allowProject().role)
                        if (profile) {
                            await this.switchProfile(profile.id)
                            console.log();
                        }
                    } else {
                        this.invData = data.data
                        this.loadProjects()
                        this.$router.push({name: 'projects'})
                        this.acloading = false
                    }
                } catch (error) {
                    this.acloading = false
                    console.log(error)
                }
            },
            async getProjectByToken() {
                this.loading = true
                try {
                    let {
                        data
                    } = await axios.get(`/api/projects/invitation/${this.token}`)
                    this.invData = data.data
                    this.loading = false
                } catch (error) {
                    this.loading = false
                    console.log(error)
                }
            }
        },
        created() {
            this.getProjectByToken()
        }
    }
</script>

<style lang="scss" scoped>
.capitalize {
    text-transform: capitalize;
    padding-bottom: 12px;
}
.project-inv {
    padding-top: 24px;
    padding-bottom: 24px;
}
</style>