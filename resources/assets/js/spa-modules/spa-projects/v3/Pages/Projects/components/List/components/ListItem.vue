<template>
    <tr
        :class="{'has-new-actions-list': project.unreadComments > 0 || project.viewedByUser == 0, 'unactive' : !project.active}">

        <!--Client-->
        <td>
            <div class="project-details">
                <el-popover trigger="hover" placement="right" width="480">
                    <div class="image-list">
                        <img height="300px" width="100%" v-if="project.active_revision.proofs.length"
                            :src="`${spacePrefix}/${project.active_revision.proofs[0].project_files.path}`" alt />
                        <el-row
                            v-else-if="project.created_role != 'client' && !isBothRole && !project.active_revision.proofs.length"
                            type="flex" justify="center" align="middle" style="height: 100%">
                            <el-col class="list-draft-project">
                                <i class="el-icon-info"></i>
                                <h5>Your design will be available soon.</h5>
                            </el-col>
                        </el-row>
                        <el-row
                            v-else-if="project.created_role == 'client' && !isBothRole && !project.active_revision.proofs.length"
                            type="flex" justify="center" align="middle" style="height: 100%">
                            <el-col class="list-draft-project">
                                <i class="el-icon-info"></i>
                                <h5>Design Request</h5>
                            </el-col>
                        </el-row>
                        <img v-else :src="`${spacePrefix}/assets/img/placeholder.jpg`" alt height="300px"
                            width="100%" />
                    </div>
                    <div class="thumbnail-table" slot="reference">
                        <router-link tag="div" :to="{ name: 'calender'}" class="over-due" v-if="checkOverDue(project)">
                            <i class="el-icon-alarm-clock"></i>
                        </router-link>
                        <router-link tag="div" :to="{ name: 'calender'}" class="in-due" v-else-if="checkDue(project)">
                            <i class="el-icon-alarm-clock"></i>
                        </router-link>
                        <div
                            @click="(project.status == 'draft' && isBothRole)
                            ? addPictures(project.id, project.active_revision.id)
                            : (project.status != 'draft') ? openProofer(project.id, project.active_revision.id, project) : ''">
                            <img height="300px" width="100%" v-if="project.active_revision.proofs.length"
                                :src="`${spacePrefix}/${project.active_revision.proofs[0].project_files.path}`" alt />
                            <el-row
                                v-else-if="project.created_role != 'client' && !isBothRole && !project.active_revision.proofs.length"
                                type="flex" justify="center" align="middle" style="height: 100%">
                                <el-col class="list-draft-project">
                                    <i class="el-icon-info"></i>
                                    <h5>Your design will be available soon.</h5>
                                </el-col>
                            </el-row>
                            <el-row
                                v-else-if="project.created_role == 'client' && !isBothRole && !project.active_revision.proofs.length"
                                type="flex" justify="center" align="middle" style="height: 100%">
                                <el-col class="list-draft-project">
                                    <i class="el-icon-info"></i>
                                    <h5>Design Request</h5>
                                </el-col>
                            </el-row>
                            <img v-else :src="`${spacePrefix}/assets/img/placeholder.jpg`" alt />
                        </div>
                    </div>
                </el-popover>
                <div class="project-content text-left">
                    <div class="project-name"
                        @click="(project.status == 'draft' && isBothRole)
                            ? addPictures(project.id, project.active_revision.id)
                            : (project.status != 'draft') ? openProofer(project.id, project.active_revision.id, project) : ''">
                        <i class="fa fa-globe" v-if="project.type == 'website'"></i>
                        <i class="el-icon-picture-outline" v-if="project.type == 'design'"></i>
                        <el-tooltip class="item" effect="dark" :content="project.name" placement="top">
                            <span>{{ project.name | truncate(20, '...') | capitalize }}</span>
                        </el-tooltip>
                    </div>
                    <div class="client-details">
                        <el-tooltip class="item" effect="dark"
                            :content="`${(project.client ? project.client.name : '')} | ${project.company}`"
                            placement="top">
                            <div>
                                <span
                                    class="client-table">{{ (project.client ? project.client.name : '') | truncate(15, '...') | capitalize }}</span>
                                |
                                <!-- <span
                                    class="label-client">{{ project.company | truncate(15, '...') | capitalize }}</span> -->
                            </div>
                        </el-tooltip>
                    </div>
                    <div class="email-table">{{(project.client ? project.client.email : '') | truncate(30, '...')}}
                    </div>
                </div>
            </div>
        </td>
        <!--Team-->
        <td width="150px">
            <template v-if="project.teamMembers.length">
                <el-row type="flex" justify="center" class="list-team-members">
                    <el-col v-for="(member, index) in project.teamMembers" :key="member.id" :xs="6" :md="6"
                        class="member-thumbnail" :style="'z-index' + index">
                        <el-tooltip :content="member.name" placement="bottom" effect="light">
                            <img :src="member.photo_url" class="img-circle special-img"
                                style="width: 100%;height: 100%;" />
                        </el-tooltip>
                    </el-col>
                </el-row>
            </template>
            <template v-else>---</template>
        </td>
        <!--Collaborators-->
        <td>
            <div style="position: relative; padding: 10px">
                <div v-if="project.collabCount > 0" class="comment-counter"
                    style="right: 27px; top: -10px; background: #949292">
                    <div v-for="(collaborator, key) in project.collaborators" :key="key">
                        <el-tooltip :content="project.name" placement="bottom" effect="light">
                            <img :src="collaborator.photo_url" class="img-circle special-img"
                                style="width: 100%;height: 100%;" />
                        </el-tooltip>
                    </div>
                </div>
                <div v-else>---</div>
            </div>
        </td>
        <!--Revision Round-->
        <td>
            <template v-if="project.type != 'website'">
                <el-tag size="small" style="background-color: #545c64; border-radius: 30px; color: #fff"
                    title="Current revision">
                    <b>R - {{project.active_revision.version}}</b>
                </el-tag>
            </template>
            <template v-else>---</template>
        </td>
        <!--Progress-->
        <td>
            <div class="list-progress">
                <div style="position: relative; width: 90%">
                    <div v-if="project.unreadComments > 0" class="comment-counter list-counter">
                        {{project.unreadComments}}</div>

                    <el-tooltip class="item" effect="dark" :content="project.percentage + '%'" placement="top">
                        <el-progress :percentage="project.percentage" :color="colors"
                            :status="project.percentage == 100 ? 'success' : 'warning'"></el-progress>
                    </el-tooltip>
                    <div style="text-align: center">
                        <p style="font-size: 12px;">
                            <b>{{project.solved_issues}} OF {{project.total_issues}}</b>
                            {{project.type == 'design' ? 'CORRECTIONS' : 'ISSUES'}} COMPLETED
                        </p>
                    </div>
                </div>
                <div v-if="isBothRole" class="delete-btn-list">
                    <el-dropdown @command="handleDropdownCommand" trigger="click">
                        <i :id="'cardIcon' + index" class="el-icon-more icon-center"
                            style="font-size: 24px; color: #949292"></i>
                        <el-dropdown-menu slot="dropdown">
                            <!--Rename-->
                            <el-dropdown-item v-if="project.status != 'completed'"
                                :command="{action: 'rename', project: project}">Rename Project</el-dropdown-item>

                            <!--Delete-->
                            <el-dropdown-item :command="{action: 'delete', project_id: project.id}">Delete
                            </el-dropdown-item>

                            <!--Email-->
                            <el-dropdown-item v-if="project.status != 'draft'"
                                :command="{action: 'email', project_id: project.id}">Email Client</el-dropdown-item>

                            <!--Move to In Progress-->
                            <el-dropdown-item v-if="project.status == 'approved' || project.status == 'completed'"
                                :command="{action: 'moveToInProgress', project_id: project.id, project_type: project.type}">
                                Move to In Progress</el-dropdown-item>
                            <el-dropdown-item v-if="ownedTeam" :command="{action: 'addTeamMember', project: project}">
                                Add Team Member</el-dropdown-item>

                            <el-dropdown-item icon="el-icon-star-off" v-if="project.status == 'approved'"
                                :command="{action: 'moveToCompleted', project_id: project.id, project_type: project.type, status: 'completed'}">
                                Move to Delivered</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </div>
            </div>
        </td>
    </tr>
</template>

<script>
    import {
        mapGetters
    } from 'vuex'
    export default {
        props: ['project', 'index'],
        data() {
            return {
                colors: [
					{ color: "#f56c6c", percentage: 20 },
					{ color: "#e6a23c", percentage: 40 },
					{ color: "#6f7ad3", percentage: 60 },
					{ color: "#1989fa", percentage: 80 },
					{ color: "#5cb87a", percentage: 100 },
                ],
                user_type: 'freelancer'
            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix
            },
            ...mapGetters(['ownedTeam', 'isBothRole'])
        },
        methods: {
            handleDropdownCommand(command) {
				if (command.action == "email") {
					this.sendProject(command.project_id);
				} else if (command.action == "delete") {
					this.deleteProject(command.project_id);
				} else if (command.action == "rename") {
					this.renameProject(command.project);
				} else if (command.action == "moveToInProgress") {
					if (command.project_type == "design") {
						this.openInProgressDialog(command.project_id);
					} else if (command.project_type == "website") {
						this.changeProjectStatus(
							command.project_id,
							command.status
						);
					}
				} else if (command.action == "moveToOnHold") {
					this.changeProjectStatus(command.project_id, command.status);
				} else if (command.action == "moveToCompleted") {
					this.changeProjectStatus(command.project_id, command.status);
				} else if (command.action == "addTeamMember") {
					this.openAddTeamMemberDialog(command.project);
				} else if (command.action == "creativeBrief") {
					this.openCreativeBriefModal(command.project);
				}
            },
            openProofer(project_id, revision_id, project) {
                this.$router.push({
                    name: "proofer",
                    params: {
                        project_id: project_id,
                        revision_id: revision_id,
                        project: project,
                    },
                });
            },
             sendProject(project_id) {
				var self = this;
				this.$confirm(
					"Are you ready to send this revision to the client?",
					"Warning",
					{
						confirmButtonText: "Confirm",
						cancelButtonText: "Cancel",
						type: "success",
						title: "",
					}
				)
					.then(() => {
                        this.$emit('loadingMethod', true)
						axios
							.get(
								"/api/projects/send_project/" +
									project_id +
									"/" +
									this.user_type
							)
							.then((response) => {
                                toastr["success"](response.data.message, "Success");
                                this.$emit('loadingMethod', false)
							})
							.catch((error) => {
                                console.log(error.message);
                                this.$emit('loadingMethod', false)
							});
					})
					.catch(() => {
						console.log("canceled");
					});
            },
            deleteProject(project_id) {
                var self = this;
				this.$confirm(
					"Are you sure you want to delete this project?",
					"Warning",
					{
						confirmButtonText: "Yes",
						confirmButtonColor: "#ee5757",
						cancelButtonText: "Cancel",
						type: "warning",
					}
				)
					.then(() => {
                        this.$emit('loadingMethod', true)
						axios
							.delete("/api/projects/delete_project/" + project_id)
							.then((response) => {
                                toastr["success"](response.data.message, "Success");
                                this.$store.commit('DELETE_PROJECT', project_id)
                                this.$emit('loadingMethod', false)
							})
							.catch((error) => {
                                this.$emit('loadingMethod', false)
							});
					})
					.catch(() => {
						// Canceled
					});
            },
            changeProjectStatus(project_id, status) {
				let statusText = "";

				if (status === "progress") statusText = "In Progress";
				else if (status === "hold") statusText = "On hold";
				else if (status === "completed") statusText = "Completed";

				this.$confirm(
					"Are you sure you want to move this project to " +
						statusText +
						"?",
					"Warning",
					{
						confirmButtonText: "Yes, move",
						cancelButtonText: "Cancel",
						type: "success",
						title: "",
					}
				).then(() => {
					this.$emit('loadingMethod', true)
					axios
						.put(`/api/projects/change_status/${project_id}/${status}`)
						.then((response) => {
							this.$emit('loadingMethod', false)

							if (response.status == 200) {
								this.$alert(
									"Project successfully moved to " + statusText,
									"",
									{
										confirmButtonText: "OK",
										callback: (action) => {
                                            this.project.status = status
                                            this.$store.commit('UPDATE_PROJECT_STATUS', this.project)
                                            
										},
									}
								);
							} else {
								toastr["error"](response.error, "Error");
							}
						});
				});
            },
            renameProject(project) {
                console.log(project)
				this.$router.push({
					name: "exist-project-title",
					params: { project_id: project.id, type: project.type},
                    query: {rename: true}
				});
            },
            openInProgressDialog(project_id) {
				this.$emit('openInProgressDialog', project_id)
            },
            openAddTeamMemberDialog(project) {
				this.$emit('openAddTeamMemberDialog', project)
            },
            openCreativeBriefModal(project) {
				this.$emit('openCreativeBriefModal', project)
            },
            checkOverDue(project) {
                if (project.end) {
                    return moment.utc().isAfter(moment.utc(project.end));
                } else {
                    return false;
                }
            },
            checkDue(project) {
                if (project.end) {
                    return moment.utc().isBefore(moment.utc(project.end));
                } else {
                    return false;
                }
            },
        },
        filters: {
            capitalize(value) {
                if (!value) return "";
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            truncate: function (text, length, suffix) {
                if (text.length > length) {
                    return text.substring(0, length) + suffix;
                } else {
                    return text;
                }
            },
        },
    }
</script>

<style lang="scss" scoped>
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;
    #list img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }

    .unactive {
        opacity: 0.5 !important;
    }


    .project-details {
        .thumbnail-table {
            position: relative;
            cursor: pointer;

            img {
                box-shadow: $box-shadow;
            }
        }

        display: flex;

        .project-content {
            margin-left: 12px;

            .project-name {
                font-size: 16px;
                color: #000;
                font-weight: 700;
            }

            .client-details {
                font-size: 12px;
                color: #000;

                .client-table {
                    color: #6b7280;
                    font-weight: 500;
                }

                .label-client {
                    color: #6b7280;
                    font-weight: 500;
                }
            }
        }
    }

    .image-list {
        text-align: center;

        img {
            border-radius: 12px;
        }
    }

    .over-due {
        padding: 0 5px;
        background: #ef5b5b;
        color: #fff;
        position: absolute;
        border-radius: 25px;
        font-weight: bold;
        top: -5px;
        right: -5px;
        z-index: 1000;
        box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.2),
            0 4px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .in-due {
        padding: 0 5px;
        background: #80b4a0;
        color: #fff;
        position: absolute;
        border-radius: 25px;
        font-weight: bold;
        top: -5px;
        right: -5px;
        z-index: 1000;
        box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.2),
            0 4px 20px 0 rgba(0, 0, 0, 0.19);
    }

    .comment-counter {
        background-color: rgb(250, 115, 115);
        width: 23px;
        height: 23px;
        border-radius: 30px;
        text-align: center;
        color: white;
        font-weight: 600;
        margin-left: 5px;
    }

    .list-counter {
        top: 5px !important;
        right: -5px !important;
    }

    .list-team-members {
        flex-wrap: wrap;
        text-align: center;
    }

    .list-team-members img {
        border-radius: 50% !important;
    }


    .member-thumbnail {
        margin-right: -15px;
    }

    .project-name:hover {
        cursor: pointer;
    }

    .project-name:hover p {
        color: #3890b5;
    }

    .delete-btn-list i {
        margin-top: -3px;
        transform: rotate(90deg);
    }

    .delete-btn-list i:hover {
        color: #3890b5 !important;
    }

    .list-progress {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    i.el-icon-more {
        color: white;
        font-size: 20px;
        outline: none;
    }


    .list-draft-project i {
        font-size: 30px;
        color: #80b4a0;
    }
</style>