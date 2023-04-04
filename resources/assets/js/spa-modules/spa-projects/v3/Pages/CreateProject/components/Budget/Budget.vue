<template>
    <project-stepper step="budget" :step-num="2">
        <el-form :model="formData" @submit.native.prevent="submitForm('ProjectForm')" status-icon :rules="rules" ref="ProjectForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <h5 style="line-height: 0;">Budget</h5>
                    <small>Step 2 of 6</small>
                </div>
                <el-row :gutter="32">
                    <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
                        <radio-button-card title="Hourly" name="budget" v-model="budgetType" val="hourly" classes="light"> 
                            <template v-slot:image-svg>
                                    <graphic-design-svg />
                                </template>
                        </radio-button-card>
                    </el-col>
                    <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
                        <radio-button-card title="Fixed" name="budget" v-model="budgetType" val="budget" classes="light"> 
                            <template v-slot:image-svg>
                                    <graphic-design-svg />
                                </template>
                        </radio-button-card>
                    </el-col>
                    <el-col :xs="24" :sm="8" :md="8" :lg="8" :xl="8">
                        <radio-button-card title="Let Client decide" name="budget" v-model="budgetType" val="none" classes="light"> 
                            <template v-slot:image-svg>
                                    <graphic-design-svg />
                                </template>
                        </radio-button-card>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :xs="18" :sm="18" :md="20" :lg="14" :xl="10">
                        <el-form-item label="Set your own hourly" prop="hourly_rate" v-if="budgetType == 'hourly'">
                            <el-input v-model.number="formData.hourly_rate" prefix-icon="el-icon-money"></el-input>
                        </el-form-item>
                        <el-form-item label="Set your own budget" prop="budget" v-if="budgetType == 'budget'">
                            <el-input v-model.number="formData.budget" prefix-icon="el-icon-money"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

            </el-card>
            <el-card class="box-card">
                <el-row :gutter="32">
                    <el-col :xs="18" :sm="18" :md="20" :lg="14" :xl="10">
                        <el-form-item label="Due Date" prop="daterange">
                            <el-date-picker style="width:100%" v-model="formData.daterange" type="daterange"
                                align="right" start-placeholder="Start Date" end-placeholder="End Date"
                                format="yyyy-MM-dd" value-format="yyyy-MM-dd HH:mm:ss"></el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item style="margin-top: 24px">
                    <el-button @click="$router.push({name: 'exist-project-title'})" :loading="saveLoading">Back
                    </el-button>
                    <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
                    </el-button>
                </el-form-item>
            </el-card>
        </el-form>
    </project-stepper>
</template>

<script>
import GraphicDesignSvg from './../assets/GraphicDesignSvg';
import RadioButtonCard from '../shared/RadioButtonCard';
import ProjectStepper from "./../ProjectStepper";

    export default {
        components: {
            ProjectStepper,
            RadioButtonCard,
            GraphicDesignSvg
        },
        props: ['project', 'project_id'],
        data() {
            return {
                formData: {
                    hourly_rate: '',
                    budget: '',
                    daterange: [],
                },
                rules: {
                    hourly_rate: [{
                            required: true,
                            message: "Hourly rate is required",
                            trigger: "change",
                        },
                        {
                            type: "number",
                            message: "Hourly must be a number"
                        },
                    ],
                    budget: [{
                            required: true,
                            message: "Budget rate is required",
                            trigger: "change",
                        },
                        {
                            type: "number",
                            message: "Budget must be a number"
                        },
                    ],
                    daterange: [{
                        required: true,
                        message: "Date range is required",
                    }, ],
                },
                saveLoading: false,
                budgetType: '',
            };
        },
        methods: {
            async submitForm(formName) {
                try {
                    await this.$refs[formName].validate(async (valid) => {
                        if (valid) {
                                const payload = {
                                    hourly_rate: this.formData.hourly_rate,
                                    budget: this.formData.budget,
                                    start: this.formData.daterange[0],
                                    end: this.formData.daterange[1],
                                    budget_type: this.budgetType,
                                }
                                this.saveLoading = true;
                                let { data } = await axios.put(`/api/projects/update/budget/${this.project_id}`, payload)
                                this.$emit('updateProject', data.data)
                                this.$router.push({name: 'project-descrip'})
                                this.saveLoading = false;
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
                        users,
                        created_by,
                        start,
                        end,
                        budget
                    } = JSON.parse(JSON.stringify(project));

                    this.formData.budget = Number(budget)

                    

                    if (start && end) {
                        this.formData.daterange[0] = start
                        this.formData.daterange[1] = end
                    }
                    if (users.length > 0) {
                        users.forEach(user => {
                            if (user.id == created_by) {
                                this.formData.hourly_rate = Number(user.pivot.hourly_rate)
                            }
                        })
                    }
                    if (this.formData.budget > 0) {
                        this.budgetType = 'budget'
                    } else if (this.formData.hourly_rate > 0) {
                        this.budgetType = 'hourly'
                    } else {
                        this.budgetType = 'none'
                    }
                }
            }
        },
        created() {
            this.initialData(this.project);
        }
    }
</script>
<style lang="scss" scoped>
    .box-card {
        margin-bottom: 12px;
    }
</style>