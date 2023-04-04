<template>
    <project-stepper step="upload" :step-num="4">
        <el-form :model="formData" status-icon ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <h5 style="line-height: 0;">Upload Proofs</h5>
                    <small>Step 4 of 6</small>
                </div>
                <div class="project-upload-proof">
                    <upload-proofs
                        :project="project"
                        :project_id="project_id"
                        :type="type"
                    />
                </div>
                <el-form-item style="margin-top: 24px">
                    <el-button
                        @click="$router.push({ name: 'project-descrip' })"
                        :loading="saveLoading"
                        >Back
                    </el-button>
                    <el-button
                        type="primary"
                        @click="submitForm('ProjectForm')"
                        :loading="saveLoading"
                        >Continue
                    </el-button>
                </el-form-item>
            </el-card>
        </el-form>
    </project-stepper>
</template>

<script>
    import ProjectStepper from './ProjectStepper.vue';
    import UploadProofs from './shared/UploadProofs';

    export default {
        props: ['project', 'project_id', 'type'],
        components: {
            ProjectStepper,
            UploadProofs,
        },
        data() {
            return {
                formData: {},
                saveLoading: false,
            };
        },
        methods: {
            async submitForm(formName) {
                try {
                    this.saveLoading = true;
                    await this.$refs[formName].validate(async (valid) => {
                        if (valid) {
                            this.saveLoading = false;
                            this.$emit('updateProject', this.project);
                            this.$router.push({ name: 'project-share' });
                        }
                    });
                } catch (error) {
                    this.saveLoading = false;
                    console.log(error);
                }
            },
        },
        created() {
            if (this.$route.params.role == 'request') {
                return this.$router.push({ name: 'not-auth' });
            }
        },
    };
</script>
<style lang="scss">
    .project-upload-proof {
        .el-upload--picture {
            width: 100%;
        }
        .el-upload-dragger {
            width: 100%;
        }
    }
</style>
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
</style>
