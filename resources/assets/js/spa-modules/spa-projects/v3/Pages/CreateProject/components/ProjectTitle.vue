<template>
    <project-stepper step='title' :step-num="0">
        <el-form :model="formData" @submit.native.prevent="submitForm('ProjectForm')" status-icon :rules="rules" ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <h5 style="line-height: 0;">Get Started</h5>
                    <small>Step 1 of 6</small>
                </div>
                <el-row :gutter="32">

                    <el-col :xs="24" :sm="24" :md="24" :lg="16" :xl="12">
                        <el-form-item label="Project Name" prop="name">
                            <el-input v-model="formData.name"></el-input>
                        </el-form-item>

                        <!-- <div class="example-title">Here are some good examples:</div>
                        <ul class="example-list"> -->
                            <!-- <li>Developer needed for creating a responsive WordPress Theme</li>
                            <li>CAD designer to create a 3D model of a residential building</li>
                            <li>Need a design for a new company logo</li> -->
                        <!-- </ul> -->
                    </el-col>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <h5 style="font-weight:700; marign-bottom:4px line-height: 0; display:flex; align-items: center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star prooflo-text mr-2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                             Turn on Project Rating</h5><h6> When project rating is turned on your project will be rated by your client after approval.</h6>
                    </el-col>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <el-tooltip class="item" effect="dark" content="Reviews" placement="top">
                            <el-switch v-model="formData.review_project">Turn on Project Rating!</el-switch>
                        </el-tooltip>
                    </el-col>

                    <!-- <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <h5 style="font-weight:700; marign-bottom:4px line-height: 0;">Allow Profiles</h5>
                    </el-col>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <el-radio v-model="allowProfile" label="freelancer">Allow in Freelancer profile!</el-radio>
                    </el-col>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <el-radio v-model="allowProfile" label="agency">Allow in Agency profile!</el-radio>
                    </el-col>
                    <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
                        <el-radio v-model="allowProfile" label="both">Allow in Both (Agency & Freelancer)</el-radio>
                    </el-col> -->
                </el-row>
                <el-divider></el-divider>
                <el-form-item style="margin-top: 24px" v-if="type != 'website' || (project && project.type != 'website')">
                    <el-button @click="cancel('ProjectForm')" :loading="saveLoading">Cancel</el-button>
                    <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
                    </el-button>
                    <el-button v-if="$route.query.rename" type="primary" @click="submitForm('ProjectForm', true)" :loading="saveLoading">Submit, Back to Projects
                    </el-button>
                </el-form-item>
            </el-card>
            <el-card class="box-card" v-if="type == 'website' || (project && project.type == 'website')">

                <el-row :gutter="32">

                    <el-col :xs="24" :sm="24" :md="24" :lg="16" :xl="12">
                        <el-form-item v-if="type == 'website'" label="Website URL" prop="website_url">
                            <div class="example-title"><h6> Copy and paste below the website url you want your client to review. </h6></div>
                            <el-input type="text" v-model="formData.website_url"></el-input>
                        </el-form-item>

                        <!-- <el-form-item v-if="type == 'video'" label="Video URL" prop="video_url">
                            <el-input type="text" v-model="formData.video_url"></el-input>
                        </el-form-item>

                        <div class="example-title">Try to copy link from:</div>
                        <ul class="example-list">
                            <li>Copy and paste a URL from youtube or dropbox</li>
                            <li>Copy the shared link from google drive.</li>
                            <li><a href="http://prooflo.com/video-projects/" target="_blank">
                                    To Learn more click here.
                                    <i class="el-icon-question"></i>
                                </a></li> -->
                        </ul>
                    </el-col>
                </el-row>

                <el-form-item style="margin-top: 24px">
                    <el-button @click="cancel('ProjectForm')" :loading="saveLoading">Cancel</el-button>
                    <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
                    </el-button>
                    <el-button v-if="$route.query.rename" type="primary" @click="submitForm('ProjectForm', true)" :loading="saveLoading">Submit, Back to Projects
                    </el-button>
                </el-form-item>
            </el-card>
        </el-form>
    </project-stepper>
</template>

<script>
    import {
        mapGetters,
        mapActions
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
                    name: "",
                    video_url: '',
                    website_url: '',
                    review_project: true
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
                projectType: "",
            };
        },
        methods: {
            ...mapActions([
                "loadProjects",
            ]),
            async submitForm(formName, rename = false) {
                this.$refs[formName].validate(async (valid) => {
                    if (valid) {
                        this.saveLoading = true;
                        const payload = {
                            name: this.formData.name,
                            type: this.type,
                            video_url: this.formData.video_url,
                            website_url: this.formData.website_url,
                            review_project: this.formData.review_project,
                            category: this.$route.query.category,
                            width: this.$route.query.width,
                            height: this.$route.query.height,
                            worker: this.$route.query.worker,
                        }
                        if (this.project_id) {
                            const upPayload = {
                                name: this.formData.name,
                                video_url: this.formData.video_url,
                                website_url: this.formData.website_url,
                                review_project: this.formData.review_project,
                                // allowProfile: this.allowProfile,
                                width: this.$route.query.width,
                                height: this.$route.query.height,
                                worker: this.$route.query.worker,
                            }
                            let { data } = await axios.put(`/api/projects/update/${this.project_id}`, upPayload)
                            this.$emit('updateProject', data.data)
                            this.saveLoading = false;
                            if (rename) {
                                this.loadProjects()
                                this.$router.push({name: 'projects'})
                            } else {
                                this.$router.push({name: 'project-budget'})
                            }
                        } else {
                            axios
                            .post("/api/projects/create", payload)
                            .then((response) => {
                                if (response.data.status == 1) {

                                    // Trigger 'Project Created' event
                                    convertfox.track("Project Created", {
                                        userId: response.data.data.user_id,
                                        projectId: response.data.data.project_id,
                                    });

                                    this.saveLoading = false;
                                    this.revision_id =
                                        response.data.data.revision_id;
                                    this.user_id = response.data.data.user_id;

                                    toastr["success"](
                                        response.data.message,
                                        "Success"
                                    );
                                    this.resetForm("ProjectForm");
                                        this.$router.push({
                                            name: "project-budget",
                                            params: {
                                                type: this.type,
                                                project_id: response.data.data.project_id,
                                            },
                                        });
                                } else {
                                    toastr["error"](
                                        response.data.errors[0],
                                        "Error"
                                    );
                                    this.saveLoading = false;
                                }
                            })
                            .catch((error) => {
                                this.saveLoading = false;
                            });
                        }
                    } else {
                        toastr["error"](
                            "Error creating project. Please fix all the listed issues"
                        );
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            cancel(formName) {
                this.$router.push({
                    name: 'projects'
                })
            },
            projectTypeRoles() {
                if (this.type == "website") {
					this.rules.website_url = [
						{
							required: true,
							message: "Website URL is required",
							trigger: "blur",
						},
						{
							type: "url",
							required: true,
							message: "Not valid URL",
							trigger: "blur",
						},
					];
				}
				if (this.type == "video") {
					this.rules.video_url = [
						{
							required: true,
							message: "Video URL is required",
							trigger: "blur",
						},
						{
							type: "url",
							required: true,
							message: "Not valid URL",
							trigger: "blur",
						},
					];
				}
            },
            initialData() {
                if (this.project && this.project_id > 0) {
                    let {
                        name,
                        video_url,
                        website_url,
                        users,
                    } = JSON.parse(JSON.stringify(this.project));

                    this.formData.name = name
                    this.formData.video_url = video_url
                    this.formData.website_url = website_url

                    if (users[0].id == this.user.id) {
                    this.formData.review_project = users[0].pivot.review_project == 1 ? true : false
                    }


                    // var userBoth = (users.filter(user => user.pivot.role == 'freelancer' || user.pivot.role == 'agency'));
                    // if (userBoth.length == 2) {
                    //     this.allowProfile = 'both'
                    // } else if (userBoth.length == 1) {
                    //     var userFreelancer = users.some(user => user.pivot.role == 'freelancer');
                    //     var userAgency = users.some(user => user.pivot.role == 'agency');
                    //     if (userFreelancer) {
                    //         this.allowProfile = 'freelancer'
                    //     }
                    //     if (userAgency) {
                    //         this.allowProfile = 'agency'
                    //     }
                        
                    // }
                    
                } else {
                    let category = this.$route.query.category.toLowerCase().replace(/[^\w ]+/g, ' ')
                    const categoryName = category.split(' ').map((s) => s.charAt(0).toUpperCase() + s.substring(1)).join(' ');
                    this.formData.name = this.removeLastWord(categoryName);
                }
            },

            removeLastWord(str) {
                const lastIndexOfSpace = str.lastIndexOf(' ');

                if (lastIndexOfSpace === -1) {
                    return str;
                }

                return str.slice(0, lastIndexOfSpace);
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
            this.projectTypeRoles()
        }
    }
</script>

<style lang="scss" scoped>
    .el-tag+.el-tag {
        margin-left: 5px;
    }

    .value {
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .link {
        font-size: 13px;
        color: #b4b4b4;
        font-style: italic;
    }

    .disable-autocomplete-data {
        position: absolute;
        top: 8px;
        right: 0;
        font-size: 15px;
    }

    .video_info_link {
        position: absolute;
        top: 0;
        left: 87px;
        font-style: italic;
    }

    .video_info_link a {
        font-weight: bold;
    }

    .example-title {
        font-weight: 600;
    }

    .example-list {
        list-style: disc;
        padding-left: 32px;
    }
    .box-card {
        margin-bottom: 12px;
    }
</style>

<style lang="scss">
    .client-autocomplete {
        li {
            line-height: normal;
            padding: 7px;

            .autocomplete-item {
                display: flex;
                align-items: center;

                .autocomplete-content {
                    width: 100%;
                    margin-left: 8px;

                    .project-name {
                        font-weight: 500;
                        font-size: 16px;
                        color: #1a202c;
                    }

                    .footer-content {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;

                        .project-company {
                            font-size: 12px;
                            color: #a0aec0;
                        }
                    }
                }
            }
        }
    }
</style>