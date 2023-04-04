<template>
    <project-stepper step='summary' :step-num="6">
        <el-form :model="formData" @submit.native.prevent="submitForm('ProjectForm')" status-icon ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="card-header">
                    <div>
                        <div class="summary-head">Summary</div>
                        <h5 style="line-height: 0;">{{project.name}}<span v-if="project.budget > 0" class="tw-text-gray-500">: ${{ project.budget }} Fixed Budget</span><span class="tw-text-gray-500" v-else-if="project.users && project.users.find(user => user.id === project.created_by).pivot.hourly_rate > 0">: ${{ project.users.find(user => user.id === project.created_by).pivot.hourly_rate }} Hourly Rate</span></h5>
                        <template v-if="$route.params.role == 'request'">
                            <div v-for="(worker, key) in workers" :key="key" class="tw-flex tw-items-center">
                                <el-tooltip effect="dark" :content="worker.pivot.role">
                                    <div class="tw-flex tw-items-center tw-mt-6 tw-cursor-pointer tw-mr-4">
                                        <el-avatar :size="24" :src="worker.photo_url"></el-avatar> <small class="tw-ml-2">{{ worker.name }}</small>
                                    </div>
                                </el-tooltip>
                            </div>
                        </template>
                    </div>
                    <div>
                        <div class="capitalize">{{project.type}}</div>
                        <div>{{project.width}} <i class="el-icon-close"></i> {{project.height}}</div>
                    </div>
                    <!-- <small>Step 6 of 6</small> -->
                </div>
                <el-alert style="margin-bottom:8px" v-if="currentRole == 'Agency' && teamMembers.length == 0" type="warning" show-icon>
                    <div slot="title">
                        You should have members in your team, if you want to add freelancers go to <router-link class="tw-font-semibold tw-text-primary" :to="{name: 'teams'}">Teams</router-link>
                    </div>
                </el-alert>
                <el-row :gutter="32">
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">

                        <section class="section-panel">
                            <div class="section-header"><i class="el-icon-document"></i> Description</div>
                            <div class="section-body">
                                <div>
                                    <instructions :project="project" :project_id="project_id"
                                        :instructions="instructions" />

                                    <include-text :creative_brief="creative_brief" :includeText="includeText">
                                        <template v-slot:switch>
                                            <el-switch v-model="includeText"></el-switch>
                                        </template>
                                        <el-input type="textarea" :rows="6" placeholder="Please input"
                                            v-model="creative_brief"></el-input>
                                    </include-text>

                                    <upload-assets :project="project" :uploadAssets="uploadAssets"
                                        :revision_id="revision_id">
                                        <template v-slot:switch>
                                            <el-switch v-model="uploadAssets"></el-switch>
                                        </template>
                                    </upload-assets>


                                </div>
                            </div>
                        </section>

                        <section class="section-panel">
                            <div class="section-header"><i class="el-icon-document"></i> File types</div>
                            <div class="section-body">
                                <file-types :formData="formData" />
                            </div>
                        </section>

                        <section class="section-panel" v-if="$route.params.role =='project'" >
                            <div class="section-header"><i class="el-icon-document"></i> Template Downloads</div>
                            <div class="section-body">
                                <project-type-temeplates :project="project" />
                            </div>
                        </section>

                        <section class="section-panel" v-if="$route.params.role =='project'">
                            <div class="section-header"><i class="el-icon-upload"></i> Upload Proofs</div>
                            <div class="section-body upload-proof">
                                <upload-proofs :project="project" :project_id="project_id" :type="type" />
                            </div>
                        </section>
                        <el-divider></el-divider>
                        <section class="section-panel">
                            <div class="section-header"><i class="el-icon-s-promotion"></i> Invite & Share</div>
                            <div class="section-body">
                                <share status="summary" :project="project" :project_id="project_id" @updateProject="updateProject" />
                            </div>
                        </section>
                    </el-col>
                </el-row>

                <el-form-item style="margin-top: 24px">
                    <el-button @click="cancel('ProjectForm')" :loading="saveLoading">Cancel</el-button>
                    <el-button @click="draft('ProjectForm')" type="info" :loading="saveLoading">Draft</el-button>
                    <el-button type="primary" @click="openDialog" :loading="saveLoading">Send
                    </el-button>
                </el-form-item>
            </el-card>

        </el-form>

        <!-- Dialog-->
        <!-- <el-dialog :visible.sync="dialogTeamMember" class="project-details-dialog">
            <span slot="title" class="display-title">
                <div class="title-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                </div>
                <span class="title">Invite People to {{project.name}}</span>
            </span>
            <el-form :model="formData" @submit.native.prevent="submitForm('dialogForm')" status-icon ref="dialogForm">
                <team-members :project_id="project_id" :project="project" />
            </el-form>
            <span slot="footer" class="dialog-footer">
                <div class="creative-footer">
                    <div class="text">
                        <el-button class="canacel-creative" :disabled="saveLoading" @click="submitForm('dialogForm')"
                            type="primary">
                            Send
                        </el-button>
                    </div>
                </div>
            </span>
        </el-dialog> -->
        <el-dialog class="success-dialog" width="70%" :show-close="false" :visible.sync="dialogSuccess">
            <div>
                <div class="title-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div class="title">Your Project was Successfully Sent</div>
                <div class="body">Thank you for your project submission.</div>
            </div>
            <el-button class="save-success" type="primary" @click="goToDashboard">Go to Dashboard</el-button>
        </el-dialog>
    </project-stepper>
</template>

<script>
    import ProjectStepper from './../ProjectStepper.vue';
    import Instructions from '../Description/components/Instructions.vue';
    import IncludeText from '../Description/components/IncludeText.vue';
    import UploadAssets from '../Description/components/UploadAssets.vue';
    import FileTypes from '../Description/components/FileTypes.vue';
    import UploadProofs from '../shared/UploadProofs'
    import Share from '../shared/Share'
    // import TeamMembers from './../shared/TeamMembers.vue';
    import ProjectTypeTemeplates from '../shared/ProjectTypeTemeplates.vue';
    import {
        mapGetters,
        mapActions
    } from 'vuex';
    export default {
        components: {
            ProjectStepper,
            Instructions,
            IncludeText,
            UploadAssets,
            UploadProofs,
            Share,
            // TeamMembers,
            FileTypes,
            ProjectTypeTemeplates
        },
        props: ['type', 'project', 'project_id', 'revision_id'],
        data() {
            return {
                formData: {
                    extention: [],
					extentionAdobe: null
                },
                creative_brief: '',
                instructions: [{
                    text: "",
                    error: ""
                }],
                includeText: false,
                uploadAssets: false,
                dialogSuccess: false,
                saveLoading: false,
            };
        },
        methods: {
            ...mapActions(['loadProjects']),
            goToDashboard() {
                this.dialogSuccess = false;
                this.loadProjects()
                this.$router.push({
                    name: "projects"
                });
            },
            openDialog() {
                    this.submitForm('ProjectForm')
            },
            cancel(formName) {
                this.$confirm('Cancel will delete your project do you want to do this?', 'Cancel Project', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    lockScroll: false,
                    type: 'warning'
                }).then(async () => {
                    // await this.submitForm('ProjectForm')
                    this.$router.push({
                        name: 'projects'
                    })
                }).catch(() => {
                    // this.$router.push({
                    //     name: 'projects'
                    // })
                })
            },
            draft(formName) {
                this.$confirm('Do you want to save the project and keep it into Draft or stay?', 'Warning', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, Thanks!',
                    lockScroll: false,
                    type: 'warning'
                }).then(async () => {
                    await this.submitForm('ProjectForm', true)
                }).catch(() => {
                    
                })
            },
            updateProject(project) {
                this.$emit('updateProject', project)
            },
            projectCall() {
                let {
                    instructions
                } = this.project;
                this.instructions = instructions;
                this.instructions.push({
                    text: "",
                    error: ""
                });
            },
            async submitForm(formName, draft = false) {
                try {
                    await this.$refs[formName].validate(async (valid) => {
                        if (valid) {
                            const payload = {
                                creative_brief: this.creative_brief,
                                includeText: this.includeText,
                                instructions: this.instructions,
                                extention: this.formData.extention,
                                extentionAdobe: this.formData.extentionAdobe,
                                draft: draft
                            }
                            this.saveLoading = true;
                            let {
                                data
                            } = await axios.put(`/api/projects/update/summary/${this.project_id}`,
                                payload)
                            this.saveLoading = false;
                            this.$emit('updateProject', data.data)
                            if(draft) {
                                await this.loadProjects()
                                this.$router.push({
                                    name: 'projects'
                                })
                            } else {
                                this.dialogSuccess = true;
                            }
                            // this.$router.push({
                            //     name: 'project-upload'
                            // })
                        }
                    })
                } catch (error) {
                    this.saveLoading = false;
                    console.log(error)
                }
            },
            initialData(project) {
                if (Object.entries(project).length === 0) {
                    return;
                } else {
                    let {
                        creative_brief,
                        file_types
                    } = JSON.parse(JSON.stringify(project));

                    this.creative_brief = creative_brief

                    if (file_types) {
						this.formData.extention = JSON.parse(file_types).filter(
							item => {
								return (
									item == ".jpg" ||
									item == ".png" ||
									item == ".pdf"
								);
							}
						);

						this.formData.extentionAdobe = JSON.parse(file_types).find(
							item => {
								return (
									item == "ai" || item == "psd" || item == "indd"
								);
							}
						);
					}

					if (this.formData.extentionAdobe) {
						this.extentions.forEach(item => {
							if (item.name == "Adobe") {
								item.value = true;
							}
						});
					}

                }
            }
        },
        computed: {
            workers() {
                if(this.project) {
                    return this.project.users.filter(user => user.pivot.role == 'freelancer' || user.pivot.role == 'agency')
                }
            },
            spacePrefix() {
                return spacePrefix;
            },
            ...mapGetters([
                'currentRole',
                'teamMembers'
            ])
        },
        created() {
            this.initialData(this.project);
            this.projectCall();
            if (
                this.project.project_assets &&
                this.project.project_assets.length > 0
            ) {
                this.uploadAssets = true;
                this.fileList = this.project.project_assets;
            }
            if (this.project.creative_brief != null) {
                this.project.creative_brief.length > 0 && (this.includeText = true);
            }
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

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        .summary-head {
            color: #c0c4cc;
        }
        .capitalize {
            text-transform: capitalize;
        }
    }
</style>


<style lang="scss">
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;
    .upload-proof {
        .el-upload--picture {
                width: 100%;
        }
            .el-upload-dragger {
                width: 100%;
            }
        }
    .project-details-dialog {
        @media screen and (max-width: 1200px) {
            .el-dialog {
                width: 50% !important;
            }
        }

        @media screen and (max-width: 992px) {
            .el-dialog {
                width: 60% !important;
            }
        }

        @media screen and (max-width: 768px) {
            .el-dialog {
                width: 70% !important;
            }
        }

        @media screen and (max-width: 576px) {
            .el-dialog {
                width: 70% !important;
            }
        }

        @media screen and (max-width: 575px) {
            .el-dialog {
                width: 80% !important;
            }
        }

        .el-dialog {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 8px;
            width: 480px;

            .display-title {
                display: flex;
                align-items: center;

                .title-icon {
                    width: 3.5rem;
                    height: 3.5rem;
                    margin-left: 0;
                    margin-right: 0;
                    display: flex;
                    align-items: center;
                    background: #ecfff1;
                    border-radius: 9999px;
                    flex-shrink: 0;
                    justify-content: center;

                    svg {
                        width: 24px;
                        height: 24px;
                        color: #38a169;
                    }
                }

                .title {
                    margin-left: 1rem;
                    margin-top: 0;
                    text-align: left;
                    line-height: 1.5rem;
                    color: #161e2e;
                    font-weight: 500;
                    font-size: 16px;
                }
            }

            .el-dialog__body {
                padding: 30px 32px;
            }

            .el-dialog__footer {
                background-color: #f9fafb;
                border-radius: 8px;

                .canacel-creative {
                    border-radius: 6px;
                    border: none;
                    box-shadow: 0px 2px 4px 2px rgba(0, 0, 0, 0.08);
                }
            }
        }
    }

    .copy-text {
        cursor: pointer;
        opacity: 0;
    }

    .creative-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 12px;

        .project-info {
            text-align: left;
            display: flex;
            align-items: center;
            color: $green;
            cursor: pointer;

            :hover .copy-text {
                opacity: 1;
                color: #223345;
            }

            .copy-icon {
                margin-right: 4px;
                font-size: 18px;
            }

            .avatar {
                margin-right: 12px;
                box-shadow: $box-shadow;
            }

            .owner-name {
                font-weight: 400;
            }

            .details {
                color: #a0aec0;

                span:not(:last-child) {
                    margin-right: 12px;
                }
            }
        }
    }

    .success-dialog {
        text-align: center;

        .el-dialog {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-radius: 8px;

            .title-icon {
                margin: 0 auto 24px auto;
                width: 4.5rem;
                height: 4.5rem;
                display: flex;
                align-items: center;
                background: #ecfff1;
                border-radius: 9999px;
                flex-shrink: 0;
                justify-content: center;

                svg {
                    width: 24px;
                    height: 24px;
                    color: #38a169;
                }
            }

            .title {
                font-weight: 600;
                font-size: 20px;
                margin-bottom: 12px;
                color: #161e2e;
            }

            .body {
                width: 80%;
                margin: 0 auto;
                color: #6b7280;
            }

            .el-dialog__body {
                padding: 30px 32px;
            }

            .save-success {
                border-radius: 6px;
                border: none;
                box-shadow: 0px 2px 4px 2px rgba(0, 0, 0, 0.08);
                padding: 14px 32px;
                margin-top: 24px;
            }
        }
    }
</style>