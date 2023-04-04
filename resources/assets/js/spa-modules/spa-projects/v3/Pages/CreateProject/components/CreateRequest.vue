<template>
    <project-stepper step='freelancer' :step-num="0">
        <el-form :model="formData" @submit.native.prevent="submitForm('ProjectForm')" status-icon :rules="rules"
            ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <h5 style="line-height: 0;">Get Started</h5>
                    <small>Step 1 of 6</small>
                </div>
                <el-row :gutter="32">

                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-if="users.length > 0">
                        <section class="section-panel">
                            <div class="section-header"><i class="el-icon-user"></i> Freelancers</div>
                            <div class="section-body" v-if="users.filter(user => user.role == 'freelancer').length > 0">
                                <el-row :gutter="32">
                                    <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" v-for="(user ,key) in users.filter(user => user.role == 'freelancer')" :key="key">
                                        <label
                                            class="tw-relative tw-flex tw-justify-between tw-cursor-pointer tw-items-center tw-shadow tw-rounded-md tw-bg-gray-50 tw-text-gray-500 tw-py-4 tw-px-8 tw-mb-4">
                                            <input type="radio"
                                                :value="user.profile" 
                                                name="workers"
                                                v-model="formData.worker"
                                                class="card-input-element hidden">
                                            <div class="tw-flex tw-items-center freelancer-card">
                                                <el-avatar
                                                    :src="user.photo_url"
                                                    :size="48" class="tw-mr-4 tw-shadow"></el-avatar>
                                                <div style="line-height:1">{{user.name}}</div>
                                            </div>
                                            
                                        </label>
                                    </el-col>
                                </el-row>
                            </div>
                            <el-alert v-else type="warning" title="No Freelancers"></el-alert>
                        </section>

                        <section class="section-panel">
                            <div class="section-header"><i class="el-icon-office-building"></i> Agencies</div>
                            <div class="section-body" v-if="users.filter(user => user.role == 'agency').length > 0">
                                <el-row :gutter="32">
                                    <el-col :xs="24" :sm="24" :md="12" :lg="12" :xl="12" v-for="(user ,key) in users.filter(user => user.role == 'agency')" :key="key">
                                        <label
                                            class="tw-relative tw-flex tw-justify-between tw-cursor-pointer tw-items-center tw-shadow tw-rounded-md tw-bg-gray-50 tw-text-gray-500 tw-py-4 tw-px-8 tw-mb-4">
                                            <input type="radio"
                                                :value="user.profile"
                                                name="workers"
                                                v-model="formData.worker"
                                                class="card-input-element hidden">
                                            <div class="tw-flex tw-items-center freelancer-card">
                                                <el-avatar
                                                    :src="user.photo_url"
                                                    :size="48" class="tw-mr-4 tw-shadow"></el-avatar>
                                                <div style="line-height:1">{{user.name}}</div>
                                            </div>
                                            
                                        </label>
                                    </el-col>
                                </el-row>
                            </div>
                            <el-alert v-else type="warning" title="No Agencies"></el-alert>
                        </section>
                    </el-col>

                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" v-else>
                        <el-alert title="You don't have Freelancers or Agencies assigned to you" type="warning"></el-alert>
                        <!-- <el-button>Back to Home</el-button> -->
                    </el-col>

                </el-row>

                <el-form-item style="margin-top: 24px"
                    v-if="type != 'website' || (project && project.type != 'website')">
                    <el-button @click="cancel('ProjectForm')"  :loading="saveLoading">Cancel</el-button>
                    <el-button type="primary" :disabled="!formData.worker" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
                    </el-button>
                </el-form-item>
            </el-card>
        </el-form>
    </project-stepper>
</template>

<script>
    import {
        mapGetters,
    } from "vuex";
    import ProjectStepper from './ProjectStepper.vue';
    export default {
        components: {
            ProjectStepper
        },
        props: ['type', 'project', 'project_id'],
        data() {
            return {
                formData: {
                    worker: null
                },
                rules: {
                    name: [{
                        required: true,
                        message: "Project name is required",
                        trigger: "change",
                    }, ],

                },
                // allowProfile: '',
                saveLoading: false,
                revision_id: "",
                user_id: "",
                users: [],
            };
        },
        methods: {
            async submitForm(formName) {
                    this.$router.push({
                        name: 'project-type',
                        query: {
                            worker: this.formData.worker,
                        }
                    })
            },
            async getUsers() {
                try {
                    let { data } = await axios.get(`/api/request/workers`);
                    this.users = data
                } catch (error) {
                    console.log(error)
                }
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            cancel(formName) {
                this.$router.push({
                    name: 'projects'
                })
            },
            initialData() {


            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix;
            },
            ...mapGetters([
                "user",
            ]),
        },
        created() {
            this.initialData()
            this.getUsers()
        }
    }
</script>

<style lang="scss" scoped>
    .box-card {
        margin-bottom: 12px;
    }

    .section-panel {
        margin-bottom: 24px;
        margin-left: 12px;
        margin-right: 12px;

        .section-header {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 12px;
        }
    }

    .card-input-element:checked+.freelancer-card::before {
        content: '\f058';
        color: #72A78F;
        font-family: 'FontAwesome';
        position: absolute;
        top: 20px;
        right: 12px;
        font-size: 18px;
        padding-right: 8px;
        padding-left: 4px;
        -webkit-animation-name: fadeInCheckbox;
        animation-name: fadeInCheckbox;
        -webkit-animation-duration: .5s;
        animation-duration: .5s;
        -webkit-animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        animation-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-input-element:checked+.freelancer-card {
        // border-inline: 5px solid #72A78F;
    }

    @-webkit-keyframes fadeInCheckbox {
        from {
            opacity: 0;
            -webkit-transform: rotateZ(-20deg);
        }

        to {
            opacity: 1;
            -webkit-transform: rotateZ(0deg);
        }
    }

    @keyframes fadeInCheckbox {
        from {
            opacity: 0;
            transform: rotateZ(-20deg);
        }

        to {
            opacity: 1;
            transform: rotateZ(0deg);
        }
    }
</style>