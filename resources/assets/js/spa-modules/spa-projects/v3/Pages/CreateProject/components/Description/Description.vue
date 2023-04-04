<template>
    <project-stepper step="descrip" :step-num="3">
        <el-form :model="formData" status-icon ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <h5 style="line-height: 0;">Description</h5>
                    <small>Step 3 of 6</small>
                </div>
                <el-row :gutter="32">
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">

                        <div>
                            <instructions :project="project" :project_id="project_id" :instructions="instructions" />

                            <include-text :creative_brief="creative_brief" :includeText="includeText">
                                <template v-slot:switch>
                                    <el-switch v-model="includeText"></el-switch>
                                </template>
                                <el-input type="textarea" v-if="includeText" :rows="6" placeholder="Please input" v-model="creative_brief">
                                </el-input>
                            </include-text>

                            <upload-assets :project="project" :uploadAssets="uploadAssets" :revision_id="revision_id">
                                <template v-slot:switch>
                                    <el-switch v-model="uploadAssets"></el-switch>
                                </template>
                            </upload-assets>

                            <file-types  style="margin-top: 24px" :formData="formData" />

                            <el-divider v-if="$route.params.role =='project'" ></el-divider>
                        
                            <project-type-temeplates class="tw-mt-6" :project="project" v-if="$route.params.role =='project'" />
                        </div>
                    </el-col>
                </el-row>
                <el-form-item style="margin-top: 24px">
                    <el-button @click="back()" :loading="saveLoading">Back
                    </el-button>
                    <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
                    </el-button>
                </el-form-item>
            </el-card>
        </el-form>
    </project-stepper>
</template>

<script>
    import ProjectStepper from "./../ProjectStepper.vue";
    import Instructions from './components/Instructions.vue';
    import IncludeText from './components/IncludeText.vue';
    import UploadAssets from './components/UploadAssets.vue';
    import FileTypes from './components/FileTypes.vue';
    import ProjectTypeTemeplates from '../shared/ProjectTypeTemeplates.vue';
    export default {
        components: {
            ProjectStepper,
            Instructions,
            IncludeText,
            UploadAssets,
            FileTypes,
            ProjectTypeTemeplates
        },
        props: ['project', 'project_id', 'revision_id'],
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
                saveLoading: false,
            };
        },
        methods: {
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
            async back() {
                await this.$confirm('Do you want save before you leave?', 'Warning', {
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No, Thanks!',
                    type: 'warning'
                }).then(async () => {
                    await this.submitForm('ProjectForm', false)
                    this.$router.push({
                        name: 'project-budget'
                    })
                }).catch(() => {
                    this.$router.push({
                        name: 'project-budget'
                    })
                });
            },
            async submitForm(formName, confirm = true) {
                try {
                    await this.$refs[formName].validate(async (valid) => {
                        if (valid) {
                            const payload = {
                                creative_brief: this.creative_brief,
                                includeText: this.includeText,
                                instructions: this.instructions,
                                extention: this.formData.extention,
                                extentionAdobe: this.formData.extentionAdobe
                            }
                            this.saveLoading = true;
                            let {
                                data
                            } = await axios.put(`/api/projects/update/description/${this.project_id}`,
                                payload)
                            this.saveLoading = false;
                            this.$emit('updateProject', data.data)
                            if (confirm) {
                                if (this.$route.params.role == 'project') {
                                    this.$router.push({
                                        name: 'project-upload'
                                    })
                                } else {
                                    this.$router.push({
                                        name: 'project-share'
                                    })
                                }
                            }
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


    .example-title {
        font-weight: 600;
    }

    .example-list {
        list-style: disc;
        padding-left: 32px;
        margin-bottom: 24px;
    }

    .box-card {
        margin-bottom: 12px;
    }

    .description {
        height: 250px;
        padding-bottom: 66px;
    }
    
   
</style>