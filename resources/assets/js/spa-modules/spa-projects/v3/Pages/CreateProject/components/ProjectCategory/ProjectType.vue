<template>
    <project-stepper step='category' :step-num="0">
        <el-form :model="formData" status-icon :rules="rules" ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <h5 style="line-height: 0;">Getting Started</h5>
                    <small>Step 1 of 6</small>
                </div>
                    
                <el-alert
                    v-if="subCategories.length == 0"
                    style="margin-bottom: 8px"
                    :closable="false"
                    type="warning">
                    <template v-slot:title >Please add at least one types from <router-link tag="a" class="prooflo-text" :to="{name: 'categories'}">Project Types</router-link></template>
                </el-alert>

                <section class="category-cards">
                    <div class="header-flex">
                        <section-header title="Project Category" body="Choose what project you want to start?" />
                        <router-link :to="{name: 'categories'}" v-if="$route.params.role =='project'" >
                            <el-button type="primary" size="mini" icon="el-icon-edit"></el-button>
                        </router-link>
                    </div>
                    <el-row :gutter="32">
                        <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8" v-for="(category, key) in categories"
                            :key="key">
                            <template v-if="category.profiles.length > 0">
                                <category v-if="category.profiles[0].pivot.active" :title="category.name" name="types" v-model="formData.type" classes="dark"
                                    :val="category.slug">
                                    <template v-slot:image-svg>
                                        <img height="120" width="100%" :src="`${spacePrefix}/${category.image}`"
                                            :alt="category.name">
                                    </template>
                                </category>
                            </template>
                        </el-col>
                    </el-row>
                </section>

                <transition name="el-fade-in-linear">
                    <section class="category-cards" v-if="subCategoriesFilter.length > 0">
                        <hr>
                        <div class="header-flex">
                            <section-header title="Select Project type"
                                body="Choose what project type you want to start?" />
                            <router-link :to="{name: 'categories'}" v-if="$route.params.role =='project'" >
                                <el-button type="primary" size="mini" icon="el-icon-edit"></el-button>
                            </router-link>
                        </div>
                        <el-row :gutter="32">
                            <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
                                <custom-dimantions title="Custom Dimantions" name="categories"
                                    v-model="formData.category" val="custom" v-if="formData.type == 'design'">
                                    <template v-slot:image-svg>
                                        <graphic-design-svg />
                                    </template>
                                    <template v-slot:custom-input>
                                        <input type="text" v-model="formData.customWidth" class="custom-input" placeholder="Width">
                                        <i class="el-icon-close"></i>
                                        <input type="text" class="custom-input" v-model="formData.customHeight" placeholder="Height">
                                    </template>
                                </custom-dimantions>
                            </el-col>
                            <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8"
                                v-for="(category, key) in subCategoriesFilter" :key="key">
                                <category :title="category.name" v-if="category.active" name="categories" v-model="formData.category"
                                    classes="light" :val="category.slug">
                                    <template v-slot:image-svg>
                                        <img height="120" width="100%" :src="`${spacePrefix}/${category.image}`"
                                            :alt="category.name">
                                    </template>
                                    <template v-slot:custom>
                                        <div>
                                            <span>{{fomrateDimantions(category.width)}}</span>
                                            <i class="el-icon-close"></i>
                                            <span>{{fomrateDimantions(category.height)}}</span>
                                        </div>
                                    </template>
                                </category>
                            </el-col>
                        </el-row>
                    </section>
                </transition>
                <el-form-item style="margin-top: 24px">
                    <el-button @click="cancel('ProjectForm')" :loading="saveLoading">Cancel</el-button>
                    <el-button type="primary" :disabled="formData.category.length == 0" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
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
    import GraphicDesignSvg from './../assets/GraphicDesignSvg';
    import ProjectStepper from '../ProjectStepper';
    import SectionHeader from './../shared/SectionHeader.vue';
    import RadioButtonCard from './../shared/RadioButtonCard.vue';
    import CustomDimantions from './components/CustomDimantions.vue';
    import Image from '../../../../../../../../../../vendor/laravel/nova/resources/js/components/Icons/Editor/Image.vue';
    export default {
        components: {
            ProjectStepper,
            GraphicDesignSvg,
            SectionHeader,
            'category': RadioButtonCard,
            CustomDimantions,
            Image
        },
        data() {
            return {
                formData: {
                    type: '',
                    category: '',
                    customWidth: 0,
                    customHeight: 0
                },
                rules: {
                    name: [{
                        required: true,
                        message: "Project name is required",
                        trigger: "change",
                    }, ],

                },
                saveLoading: false,
                revision_id: "",
                user_id: "",
                projectType: "",
                categories: [],
                subCategories: [],
            };
        },
        methods: {
            fomrateDimantions(data) {
                if (this.formData.type == 'website') {
                    return Number(Math.floor(data));
                }
                return data;
            },
            submitForm(formName) {
                this.$refs[formName].validate(async (valid) => {
                    if (valid) {
                        this.$router.push({
                            name: 'project-title',
                            params: {
                                type: this.formData.type
                            },
                            query: {
                                worker: this.$route.query.worker,
                                category: this.formData.category,
                                width: this.formData.customWidth,
                                height: this.formData.customWidth
                            }
                        })
                    }
                })
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
                if (this.project && this.project_id > 0) {
                    let {
                        name,
                        video_url,
                        website_url
                    } = JSON.parse(JSON.stringify(this.project));

                    this.formData.customWidth = width
                    this.formData.customHeight = height

                    this.formData.name = name
                    this.formData.video_url = video_url
                    this.formData.website_url = website_url

                }
            },
            async fetchCategories() {
                try {
                    let {
                        data
                    } = await axios.get(`/api/projects/categories`, 
                    {
                        params: {
                            worker: this.$route.query.worker
                        }
                    }
                    )
                    this.categories = data.categories
                    this.subCategories = data.subCategories
                } catch (error) {
                    toastr['error'](error.response.data.message, 'Error');
                }
            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix;
            },
            ...mapGetters([
                "user",
            ]),
            subCategoriesFilter() {
                if (this.categories.length > 0) {
                    return this.subCategories.filter(cat => cat.category.slug == this.formData.type)
                } else {
                    return this.subCategories
                }
            }
        },
        created() {
            this.initialData()
            this.fetchCategories()
        }
    }
</script>

<style lang="scss" scoped>
    .box-card {
        margin-bottom: 12px;
    }

    .category-cards {
        padding-right: 24px;
        padding-left: 24px;
    }

    .custom-input {
        width: 70px;
        border: none;
        line-height: 2;
        background-color: #235066;
        border-radius: 4px;
        padding-left: 6px;
        padding-right: 6px;
        text-align: center;
        color: #ecfef7;
        outline: none;

    }

    @mixin placeholder {
        ::-webkit-input-placeholder {
            @content
        }

        :-moz-placeholder {
            @content
        }

        ::-moz-placeholder {
            @content
        }

        :-ms-input-placeholder {
            @content
        }
    }

    @include placeholder {
        color: #80b4a0;
        font-weight: 100;
    }
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>