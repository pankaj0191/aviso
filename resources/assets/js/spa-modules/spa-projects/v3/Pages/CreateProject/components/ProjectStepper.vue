<template>
    <el-row>
        <el-col class="hidden-sm-and-down" :md="8" :lg="6" :xl="4">
            <ul class="project-stepper">
                <!-- <li :class="[step == 'category' && 'static', stepNum > 0 && 'active', stepNum < 0 && 'is-disabled']">
                    <span><i class="el-icon-discover stepper-icon"></i>
                        <router-link v-if="step == 'summary'" :to="{name: 'exist-project-title'}">Project Category</router-link>
                        <span v-else>Project Category</span>
                    </span><i class="el-icon-success"></i></li> -->
                <li :class="[step == 'title' && 'static', stepNum > 1 && 'active', stepNum < 1 && 'is-disabled']">
                    <span><i class="el-icon-edit stepper-icon"></i>
                        <router-link v-if="step == 'summary'" :to="{name: 'exist-project-title'}">Title</router-link>
                        <span v-else>Title</span>
                    </span><i class="el-icon-success"></i></li>
                <li :class="[step == 'budget' && 'static', stepNum > 2 && 'active', stepNum < 2 && 'is-disabled']">
                    <span><i class="el-icon-bank-card stepper-icon"></i>
                        <router-link v-if="step == 'summary'" :to="{name: 'project-budget'}">Budget</router-link>
                        <span v-else>Budget</span>
                    </span><i class="el-icon-success"></i>
                </li>
                <li :class="[step == 'descrip' && 'static', stepNum > 3 && 'active', stepNum < 3 && 'is-disabled']">
                    <span><i class="el-icon-document stepper-icon"></i>
                        <router-link v-if="step == 'summary'" :to="{name: 'project-descrip'}">Description</router-link>
                        <span v-else>Description</span>
                    </span><i class="el-icon-success"></i>
                </li>
                <li v-if="$route.params.role == 'project'" :class="[step == 'upload' && 'static', stepNum > 4 && 'active', stepNum < 4 && 'is-disabled']">
                    <span><i class="el-icon-upload stepper-icon"></i>
                        <router-link v-if="step == 'summary'" :to="{name: 'project-upload'}">Upload Proofs</router-link>
                        <span v-else>Upload Proofs</span>
                    </span><i class="el-icon-success"></i></li>
                <li :class="[step == 'share' && 'static', stepNum > 5 && 'active', stepNum < 5 && 'is-disabled']">
                    <span><i class="el-icon-s-promotion stepper-icon"></i>
                        <router-link v-if="step == 'summary'" :to="{name: 'project-share'}">Invites & Share</router-link>
                        <span v-else>Invites & Share</span>
                    </span><i class="el-icon-success"></i></li>
                <li :class="[step == 'summary' && 'static', , stepNum < 6 && 'is-disabled']">
                        <span><i class="el-icon-check stepper-icon"></i><router-link v-if="step == 'summary'" :to="{name: 'project-summary'}">Summary</router-link>
                        <span v-else>Summary</span>
                    </span></li>
            </ul>
        </el-col>
        <el-col :xs="24" :sm="24" :md="16" :lg="18" :xl="18">
            <slot></slot>
        </el-col>
    </el-row>
</template>

<script>
    export default {
        props: ['step', 'step-num'],
    }
</script>

<style lang="scss" scoped>
    .project-stepper {
        a {
            color: #80b4a0 !important;

            i {
                text-decoration: none !important;
            }
        }

        a:hover {
            color: #5e9882 !important;
            cursor: pointer;

            span {
                text-decoration: underline;
            }
        }

        .is-disabled {
            cursor: not-allowed;
            user-select: none;
        }

        .static,
        .active {
            color: #1A202C;

            i {
                color: #5e9882;
            }

            .stepper-icon {
                color: #1A202C;
            }
        }

        .static {
            border-left: 4px solid #80b4a0;
        }

        li {
            padding-top: 6px;
            margin-bottom: 6px;
            padding-bottom: 6px;
            padding-left: 16px;
            padding-right: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #A0AEC0;

            .stepper-icon {
                color: #718096;
                margin-right: 12px;
                font-size: 16px;
            }



            i {
                color: #CBD5E0;
            }
        }

    }
</style>