<template>
    <section class="section-panel">
        <div class="section-header">
            <div class="header"><i class="el-icon-user"></i> Collaborators</div>
            <small class="tw-italic">You can add more than one collaborator</small>
        </div>
        <div class="section-body">
            <div class="tw-mb-8">
                <div class="tw-flex tw-items-center tw-justify-between tw-px-4 tw-rounded tw-bg-gray-50 tw-shadow tw-py-2 tw-mb-4" 
                    v-for="(collaborator, key) in invitations" :key="key">
                    <div class="tw-flex tw-items-center">
                        <el-avatar :src="collaborator.avatar">
                        </el-avatar>
                        <div class="tw-mx-4">{{collaborator.email}}</div>
                        <el-tooltip effect="dark" content="Pending" v-if="collaborator.status == 'Pending'">
                            <i class="el-icon-warning color-warning"></i>
                        </el-tooltip>
                        <el-tooltip effect="dark" content="Success" v-else>
                            <i class="el-icon-success color-success"></i>
                        </el-tooltip>
                    </div>
                    <div>{{collaborator.name}}</div>
                    <div class="tw-flex tw-items-center">
                        <el-popover placement="top" width="auto" trigger="click" v-if="collaborator.link">
                            <div><i class="el-icon-success" style="color:#80b4a0"></i> Link copied to
                                clipboard</div>
                            <el-tag size="medium" class="tw-cursor-pointer tw-mr-4" slot="reference"
                                @click="clipboardLink(collaborator.link)">
                                Copy Link</el-tag>
                        </el-popover>
                        <el-tooltip class="item" effect="dark" content="Delete Collaborator" placement="bottom">
							<el-popconfirm
								confirmButtonText="OK"
								@confirm="deleteCollaborator(collaborator.id)"
								cancelButtonText="No, Thanks"
								icon="el-icon-info"
								iconColor="red"
								title="Are you sure to delete this collaborator invitation?"
							>
								<el-button slot="reference" size="mini" circle icon="el-icon-delete"></el-button>
							</el-popconfirm>
						</el-tooltip>
                    </div>
                </div>
                <div class="tw-flex tw-items-center tw-justify-center tw-px-4 tw-rounded tw-bg-gray-50 tw-shadow tw-py-4" v-if="invitations.length == 0">
                    <div>There is no Collaborators added yet</div>
                </div>
            </div>
            <el-row :gutter="20">
                <el-col :xs="16" :sm="16" :md="16" :lg="14" :xl="14">
                    <el-form :model="formData" class="tw-flex" @submit.native.prevent="submitForm('dialogForm')"
                        status-icon :rules="rules" ref="dialogForm">
                        <el-form-item prop="collaborator">
                            <el-autocomplete id="email" popper-class="client-autocomplete" style="width:100%"
                                v-model="formData.collaborator" :fetch-suggestions="fetchClients" :trigger-on-focus="false"
                                placeholder="Invite Collaborator" @select="handleSelect">
                                <template slot-scope="{ item }">
                                    <div class="autocomplete-item">
                                        <div class="autocomplete-content">
                                            <div class="project-name"
                                                v-html="$options.filters.highlight(item.name, formData.collaborator)">
                                                {{ item.name }}</div>
                                            <div class="footer-content">
                                                <div class="project-company"
                                                    v-html="$options.filters.highlight(item.email, formData.collaborator)">
                                                    {{ item.email }}</div>
                                                <!-- <div class="project-company">{{ item.current_profile.profile_type.name }}</div> -->
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </el-autocomplete>
                        </el-form-item>
                        <el-button class="tw-ml-4" icon="el-icon-position" @click="submitForm('dialogForm')" :loading="saveLoading" type="primary">Send</el-button>
                    </el-form>
                </el-col>
                <el-col :xs="8" :sm="8" :md="8" :lg="10" :xl="10" class="tw-text-right">
                    <div class="tw-inline-block">
                        <el-popover placement="top" width="auto" trigger="click">
                            <div><i class="el-icon-success" style="color:#80b4a0"></i> Link copied to clipboard
                            </div>
                            <div class="project-info text-right"
                                @click="clipboardLink(project.project_invitations.find(inv => inv.role == 'collaborator' && !inv.email && !inv.user_id).link)"
                                slot="reference">
                                <i class="el-icon-link copy-icon"></i>
                                <div>
                                    <div class="owner-name">Copy global link
                                    </div>
                                </div>
                            </div>
                        </el-popover>
                        <small><i class="fa fa-globe"></i> Any one the internet with the link can invite</small>
                    </div>
                </el-col>
            </el-row>
        </div>
    </section>
</template>

<script>
    export default {
        props: ['project', 'project_id', 'type'],
        data() {
            return {
                formData: {
                    collaborator: '',
                },
                rules: {
                    collaborator: [{
                        trigger: 'change',
                        required: true,
                        message: "Invitation email is required",
                    }],
                },
                saveLoading: false,
                copyText: 'copy'
            };
        },
        computed: {
            invitations() {
                let inv = this.project.project_invitations.filter(inv => (inv.user_id || inv.email) && inv.role == 'collaborator').map(inv => {
                    return {
                        id: inv.id,
                        user_id: inv.user_id,
                        email: inv.email,
                        name: inv.user ? inv.user.name : 'N/A',
                        link: inv.link,
                        avatar: inv.user ? inv.user.photo_url : 'https://www.gravatar.com/avatar/00000000000000000000000000000000',
                        status: 'Pending'
                    }
                })
                let existUser = this.project.users.filter(user => user.pivot.role == 'collaborator').map(user => {
                    return {
                        user_id: user.id,
                        email: user.email,
                        name: user.name,
                        avatar: user.photo_url,
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
            async deleteCollaborator(id) {
                try {
                    this.saveLoading = true;
                    let {
                        data
                    } = await axios.delete(`/api/projects/${this.project_id}/collaborators/${id}/invitation`);
                    this.$emit('updateProject', data.data);
                    this.saveLoading = false;
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
                    }
                } catch (error) {
                    console.log(error)
                }
            },
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
                this.formData.collaborator = item.email;
            },
            async submitForm(formName) {
                try {
                    await this.$refs[formName].validate(async (valid) => {
                        if (valid) {
                            const payload = {
                                email: this.formData.collaborator,
                                type: this.type,
                                role: 'collaborator'
                            }
                            this.saveLoading = true;
                            let {
                                data
                            } = await axios.put(`/api/projects/invitation-email/${this.project_id}`,
                                payload)
                            this.saveLoading = false;

                            if (this.type == 'proof') {
                                let pushData = data.data.project.project_invitations.find(inv => inv
                                    .email == this.formData.collaborator)
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
                                this.formData.collaborator = ''
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