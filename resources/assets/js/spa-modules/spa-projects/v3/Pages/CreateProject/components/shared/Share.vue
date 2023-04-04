<template>
    <el-row :gutter="32">
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <approver-invitation  :project="project" :project_id="project_id" :type="type" @updateProject="updateProject" />
            <el-divider></el-divider>
            <collaborator-invitation :project="project" :project_id="project_id" :type="type" @updateProject="updateProject" />
            <el-divider v-if="currentRole == 'Agency'"></el-divider>
            <freelancers-active :status="status" v-if="currentRole == 'Agency'" :project="project" :project_id="project_id" :type="type" @updateProject="updateProject" />
        </el-col>
    </el-row>
</template>

<script>
    import ApproverInvitation from './components/ApproverInvitation.vue';
    import CollaboratorInvitation from './components/CollaboratorInvitation.vue';
    import FreelancersActive from './components/FreelancersActive.vue';
    import {
        mapGetters
    } from "vuex";
    export default {
        components: {
            CollaboratorInvitation,
            ApproverInvitation,
            FreelancersActive
        },
        props: ['project', 'project_id', 'type', 'status'],
        methods: {
            updateProject(project) {
                this.$emit('updateProject', project)
            },
        },
        computed: {
            ...mapGetters(['currentRole'])
        }
    }
</script>

<style lang="scss">
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;

    .highlight {
        background-color: #c1ffe7;
        font-weight: 700;
    }

    .section-panel {
        margin-bottom: 24px;
        margin-left: 12px;
        margin-right: 12px;

        .section-header {
            .header {
                font-weight: 700;
                font-size: 16px;
            }

            margin-bottom: 24px;
        }

    }

    .el-form {
        .el-form-item {
            margin-bottom: 0;
            width: 100%;
        }
    }

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

    }

    .project-info {
        display: flex;
        height: 16px;
        align-items: center;
        justify-content: flex-end;
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
</style>