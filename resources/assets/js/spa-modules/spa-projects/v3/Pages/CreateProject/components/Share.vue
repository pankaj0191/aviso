<template>
    <project-stepper step="share" :step-num="5">
        <el-form :model="formData" @submit.native.prevent="submitform" status-icon :rules="rules" ref="ProjectForm">

            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <div style="display:inline-block">
                        <h5 style="line-height: 0;">Invite & Share</h5>
                        <small>Step 5 of 6</small>
                    </div>
                </div>
                <share status="share" :project="project" :project_id="project_id" @updateProject="updateProject" />
                <el-form-item style="margin-top: 24px">
                    <el-button @click="back()" :loading="saveLoading">Back
                    </el-button>
                    <el-button type="primary" @click="saveForm('ProjectForm')" :loading="saveLoading">Continue
                    </el-button>
                </el-form-item>
            </el-card>
        </el-form>

    </project-stepper>
</template>

<script>
    import ProjectStepper from "./ProjectStepper.vue";
    import {
        mapGetters
    } from "vuex";
    import Share from './shared/Share'
    export default {
        components: {
            ProjectStepper,
            Share
        },
        props: ['project', 'project_id'],
        data() {

            return {
                formData: {},
                rules: {},
                saveLoading: false
            };
        },
        computed: {
            windowWidth() {
                return this.$store.getters.windowWidth
            },
            ...mapGetters([
                'currentRole'
            ]),
        },
        methods: {
            back() {
                if (this.$route.params.role == 'project') {
                    this.$router.push({name: 'project-upload'})
                } else {
                    this.$router.push({name: 'project-descrip'})
                }
            },
            updateProject(project) {
                this.$emit('updateProject', project)
            },
            async saveForm(formName) {
                try {
                    await this.$refs[formName].validate(async (valid) => {
                        this.$router.push({
                            name: 'project-summary'
                        })
                    })
                } catch (error) {

                }
            },
        },
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

