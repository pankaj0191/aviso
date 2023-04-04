<template>
    <el-row :gutter="32">
        <el-col class="text-center" :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <el-button @click="inviteDialog = true" type="secondery">Invite Approver</el-button>
            <el-popover placement="top" width="auto" trigger="click">
                <div><i class="el-icon-success" style="color:#80b4a0"></i> Link copied to clipboard</div>
                <el-button slot="reference" type="primary"
                    @click="clipboardLink(project.project_invitations.find(inv => inv.role == 'client' && !inv.email && !inv.user_id).link)">
                    Copy Invitation Link</el-button>
            </el-popover>
        </el-col>
        <el-col class="text-center" :xs="24" :sm="24" :md="24" :lg="24" :xl="24">
            <el-table :data="invitations" style="width: 100%">
                <el-table-column prop="email" label="Approver Email">
                </el-table-column>
                <el-table-column prop="name" label="Approver Name">
                </el-table-column>
                <el-table-column prop="link" label="Link" width="120px">
                    <template slot-scope="scope">
                        <el-popover placement="top" width="auto" trigger="click" as>
                            <div><i class="el-icon-success" style="color:#80b4a0"></i> Link copied to
                                clipboard</div>
                            <el-tag size="medium" style="cursor:pointer" slot="reference"
                                @click="clipboardLink(scope.row.link)">
                                Copy Link</el-tag>
                        </el-popover>
                        <span v-else>Empty</span>
                    </template>
                </el-table-column>
                <el-table-column label="Status" width="120px">
                    <template slot-scope="scope">
                        <el-tag size="medium" :type="scope.row.status == 'Accepted' ? 'scuess' : 'info'">
                            {{scope.row.status}}</el-tag>
                    </template>
                </el-table-column>
            </el-table>
        </el-col>

        <!-- Dialog-->
        <el-dialog :visible.sync="inviteDialog" class="project-details-dialog">
            <span slot="title" class="display-title">
                <div class="title-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <circle cx="8.5" cy="8.5" r="1.5" />
                        <polyline points="21 15 16 10 5 21" />
                    </svg>
                </div>
                <span class="title">Invite Approver to {{project.name}}</span>
            </span>
            <el-form :model="formData" @submit.native.prevent="submitForm('dialogForm')" status-icon :rules="rules"
                ref="dialogForm">
                <div>
                    <el-form-item :label="`To:`" prop="inviteEmail">
                        <!-- <el-input id="email"  v-model="formData.inviteEmail"></el-input> -->
                        <el-autocomplete id="email" popper-class="client-autocomplete" style="width:100%"
                            v-model="formData.inviteEmail" :fetch-suggestions="fetchClients" :trigger-on-focus="false"
                            placeholder="Type more than 3 char" @select="handleSelect">
                            <template slot-scope="{ item }">
                                <div class="autocomplete-item">
                                    <div class="autocomplete-content">
                                        <div class="project-name" v-html="$options.filters.highlight(item.name, formData.inviteEmail)">{{ item.name }}</div>
                                        <div class="footer-content">
                                            <div class="project-company" v-html="$options.filters.highlight(item.email, formData.inviteEmail)">{{ item.email }}</div>
                                            <!-- <div class="project-company">{{ item.current_profile.profile_type.name }}</div> -->
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </el-autocomplete>
                    </el-form-item>
                </div>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <div class="creative-footer">
                    <el-popover placement="top" width="auto" trigger="click">
                        <div><i class="el-icon-success" style="color:#80b4a0"></i> Link copied to clipboard</div>
                        <div class="project-info"
                            @click="clipboardLink(project.project_invitations.find(inv => inv.role == 'client' && !inv.email && !inv.user_id).link)"
                            slot="reference">
                            <i class="el-icon-link copy-icon"></i>
                            <div>
                                <div class="owner-name">Copy a client invitation link
                                </div>
                            </div>
                        </div>
                    </el-popover>
                    <div class="text">
                        <el-button class="canacel-creative" :disabled="saveLoading" @click="submitForm('dialogForm')"
                            type="primary">
                            Send
                        </el-button>
                    </div>
                </div>
            </span>
        </el-dialog>
    </el-row>
</template>

<script>
    import {
        mapGetters
    } from "vuex";
    export default {
        props: ['project', 'project_id', 'type'],
        data() {

            return {
                formData: {
                    inviteEmail: '',
                },
                rules: {
                    inviteEmail: [{
                        trigger: 'change',
                        required: true,
                        message: "Invitation email is required",
                    }],
                },
                inviteDialog: false,
                saveLoading: false,
                copyText: 'copy'
            };
        },
        computed: {
            windowWidth() {
                return this.$store.getters.windowWidth
            },
            ...mapGetters([
                'currentRole'
            ]),
            invitations() {
                let inv = this.project.project_invitations.filter(inv => inv.user_id || inv.email).map(inv => {
                    return {
                        id: inv.id,
                        user_id: inv.user_id,
                        email: inv.email,
                        name: inv.user ? inv.user.name : 'N/A',
                        link: inv.link,
                        status: 'Pending'
                    }
                })
                let existUser = this.project.users.filter(user => user.pivot.role == 'client').map(user => {
                    return {
                        user_id: user.id,
                        email: user.email,
                        name: user.name,
                        link: '',
                        status: 'Accepted'
                    }
                })
                return [...inv, ...existUser];
            }
        },
        filters: {
            highlight(words, query) {
                var iQuery = new RegExp(query, "ig");
                return words.toString().replace(iQuery, function (matchedTxt, a, b) {
                    return ('<span class=\'highlight\'>' + matchedTxt + '</span>');
                });
            }
        },
        methods: {
            async fetchClients(queryString, cb) {
                if (queryString.length > 2) {
                    try {
                        let {
                            data
                        } = await axios.get('/api/projects/clients/fetch', {
                            params: {
                                queryString: queryString
                            }
                        });
                        _.debounce(cb(data.data), 300);
                    } catch (error) {
                        console.log(error)
                    }
                }
            },
            handleSelect(item) {
                this.formData.inviteEmail = item.email;
            },
            async submitForm(formName) {
                try {
                    await this.$refs[formName].validate(async (valid) => {
                        if (valid) {
                            const payload = {
                                email: this.formData.inviteEmail,
                                type: this.type
                            }
                            this.saveLoading = true;
                            let {
                                data
                            } = await axios.put(`/api/projects/invitation-email/${this.project_id}`,
                                payload)
                            this.saveLoading = false;

                            if (this.type == 'proof') {
                                let pushData = data.data.project.project_invitations.find(inv => inv.email == this.formData.inviteEmail)
                                this.project.project_invitations.push(pushData)
                            }

                            if (data.data.status == 1 && this.type != 'proof') {
                                this.$emit('updateProject', data.data.project)
                            }
                            if (data.data.status == 0) {
                                this.$notify({
                                    title: "Error",
                                    message: data.data.message,
                                    type: "error"
                                });
                            } else {
                                this.inviteDialog = false;
                                this.$notify({
                                    title: "Success",
                                    message: data.data.message,
                                    type: "success"
                                });
                                this.formData.inviteEmail = ''
                                this.$refs[formName].resetFields();
                            }
                        }
                    })
                } catch (error) {
                    this.saveLoading = false;
                    console.log(error)
                }
            },
            clipboardLink(copy) {
                self = this
                this.$copyText(copy).then(
                    function (e) {
                        self.copyText = 'copied'
                        setTimeout(() => {
                            self.copyText = 'copy'
                        }, 1500);
                    },
                    function (e) {
                        self.copyText = 'can not copy'
                    })
            },
        },

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
</style>