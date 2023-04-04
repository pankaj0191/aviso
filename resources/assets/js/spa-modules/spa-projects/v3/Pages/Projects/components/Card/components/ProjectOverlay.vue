<template>
    <div class="rollover">
        <!-- <div class="menu-select"><i class="el-icon-more"></i></div> -->
        <div></div>
        <div v-if="project.owner == user.id && project.active" class="delete-btn">
            <el-dropdown @command="handleDropdownCommand" trigger="click">
                <i :id="'cardIcon' + index" class="tw-mr-5 el-icon-more icon-center"
                    style="font-size: 24px; color: #faf9f9"></i>
                <el-dropdown-menu slot="dropdown">
                    <!--Rename-->
                    <el-dropdown-item icon="el-icon-edit-outline"
                        v-if="project.status != 'completed' && project.created_role != 'client'"
                        :command="{action: 'rename', project: project}">Rename Project</el-dropdown-item>

                    <!--Delete-->
                    <el-dropdown-item icon="el-icon-folder-delete"
                        :command="{action: 'delete', project_id: project.id}">Delete Project</el-dropdown-item>

                    <!--Email-->
                    <el-dropdown-item icon="el-icon-message" v-if="project.status != 'draft'"
                        :command="{action: 'email', project_id: project.id}">Email Client</el-dropdown-item>

                    <!--Move to In Progress-->
                    <el-dropdown-item icon="el-icon-message"
                        v-if="project.status == 'approved' || project.status == 'completed' || project.status == 'hold'"
                        :command="{action: 'moveToInProgress', project_id: project.id, project_type: project.type, status: 'progress'}">
                        Move to InBox</el-dropdown-item>

                    <!--Move to On Hold-->
                    <el-dropdown-item icon="el-icon-video-pause"
                        v-if="project.status == 'progress' || project.status == 'revision'"
                        :command="{action: 'moveToOnHold', project_id: project.id, project_type: project.type, status: 'hold'}">
                        Move to On Hold</el-dropdown-item>

                    <!--Manage Team Members-->
                    <el-dropdown-item icon="el-icon-user" v-if="ownedTeam && currentRole == 'Agency'"
                        :command="{action: 'addTeamMember', project: project}">Add Team Member</el-dropdown-item>
                    <!--Open Summary-->
                    <el-dropdown-item v-if="project.created_role != 'client'"
                        icon="el-icon-tickets" :command="{action: 'summary', project: project}">Summary</el-dropdown-item>

                    <el-dropdown-item icon="el-icon-star-off" v-if="project.status == 'approved'"
                        :command="{action: 'moveToCompleted', project_id: project.id, project_type: project.type, status: 'completed'}">
                        Move to Delivered</el-dropdown-item>
                        <!--Copy Slack-->
                    <el-dropdown-item class="tw-flex tw-items-center"
                         :command="{action: 'slack-hash', project: project}">
                            <svg class="tw-mr-2" width="14" enable-background="new 0 0 2447.6 2452.5" viewBox="0 0 2447.6 2452.5" xmlns="http://www.w3.org/2000/svg"><g clip-rule="evenodd" fill-rule="evenodd"><path d="m897.4 0c-135.3.1-244.8 109.9-244.7 245.2-.1 135.3 109.5 245.1 244.8 245.2h244.8v-245.1c.1-135.3-109.5-245.1-244.9-245.3.1 0 .1 0 0 0m0 654h-652.6c-135.3.1-244.9 109.9-244.8 245.2-.2 135.3 109.4 245.1 244.7 245.3h652.7c135.3-.1 244.9-109.9 244.8-245.2.1-135.4-109.5-245.2-244.8-245.3z" fill="#36c5f0"/><path d="m2447.6 899.2c.1-135.3-109.5-245.1-244.8-245.2-135.3.1-244.9 109.9-244.8 245.2v245.3h244.8c135.3-.1 244.9-109.9 244.8-245.3zm-652.7 0v-654c.1-135.2-109.4-245-244.7-245.2-135.3.1-244.9 109.9-244.8 245.2v654c-.2 135.3 109.4 245.1 244.7 245.3 135.3-.1 244.9-109.9 244.8-245.3z" fill="#2eb67d"/><path d="m1550.1 2452.5c135.3-.1 244.9-109.9 244.8-245.2.1-135.3-109.5-245.1-244.8-245.2h-244.8v245.2c-.1 135.2 109.5 245 244.8 245.2zm0-654.1h652.7c135.3-.1 244.9-109.9 244.8-245.2.2-135.3-109.4-245.1-244.7-245.3h-652.7c-135.3.1-244.9 109.9-244.8 245.2-.1 135.4 109.4 245.2 244.7 245.3z" fill="#ecb22e"/><path d="m0 1553.2c-.1 135.3 109.5 245.1 244.8 245.2 135.3-.1 244.9-109.9 244.8-245.2v-245.2h-244.8c-135.3.1-244.9 109.9-244.8 245.2zm652.7 0v654c-.2 135.3 109.4 245.1 244.7 245.3 135.3-.1 244.9-109.9 244.8-245.2v-653.9c.2-135.3-109.4-245.1-244.7-245.3-135.4 0-244.9 109.8-244.8 245.1 0 0 0 .1 0 0" fill="#e01e5a"/></g></svg>Copy Hash for Slack
                         </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
        <div v-else-if="project.role == 'client'" class="delete-btn">
            <el-dropdown @command="handleDropdownCommand" trigger="click">
                <i :id="'cardIcon' + index" class="el-icon-more icon-center"
                    style="font-size: 24px; color: #faf9f9"></i>
                <el-dropdown-menu slot="dropdown">
                    <!--Creative Brief-->
                    <el-dropdown-item :command="{action: 'summary', project: project}">Summary
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
        <el-tooltip v-if="project.status != 'draft' && project.active_revision.proofs.length > 0" content="View Project"
            placement="top">
            <el-button v-if="project.status != 'draft'" class="roll-btn"
                @click="openProofer(project.id, project.active_revision.id, project)" icon="el-icon-view" size="small"
                type="primary" circle></el-button>
        </el-tooltip>
        <el-button
            v-else-if="!isBothRole && project.created_role == 'client' && project.active_revision.proofs.length == 0"
            class="roll-btn" @click="goToSummary(project)" size="small" type="primary">
            Resume</el-button>
        <el-tooltip
            v-else-if="project.status == 'draft' && project.active_revision.proofs.length == 0 && isBothRole && project.created_role == 'client'"
            content="View Design Request" placement="top">
            <el-button class="roll-btn" icon="el-icon-link" size="small" type="warning" circle
                @click="addPictures(project.id, project.active_revision.id, project.type)"></el-button>
        </el-tooltip>
        <el-tooltip
            v-else-if="project.status == 'draft' && isBothRole && project.created_role != 'client'"
            content="Upload files" placement="top">
            <el-button class="roll-btn" icon="el-icon-upload" size="small" type="warning" circle
                @click="addPictures(project.id, project.active_revision.id, project.type)"></el-button>
        </el-tooltip>
        <el-tooltip v-else-if="project.active_revision.proofs.length == 0 && isBothRole"
            content="Add Proofs" placement="top">
            <button class="edition-btn" @click="addPictures(project.id, project.active_revision.id, project.type)"
                style="background-color: #fc7c7c; border: 0px">
                <i class="el-icon-plus icon-center" style="font-size: 20px ;color: #fff; font-weight: bold"></i>
            </button>
        </el-tooltip>
        <template v-else-if="!project.active">
            <el-button class="expired">OWNER SUBSCRIPTION IS EXPIRED</el-button>
        </template>
        <div class="project-team-members tw-mb-2">
            <el-row type="flex" justify="center" class="list-team-members" v-if="project.teamMembers.length">
                <el-col :span="12" v-for="member in project.teamMembers" :key="member.id">
                    <el-tooltip :content="`${member.name} | ${member.pivot.role}`">
                        <div style="padding-bottom;: 5px">
                            <img :src="member.photo_url" class="img-circle special-img"
                                style="width: 24px;height: 24px;" />
                        </div>
                    </el-tooltip>
                </el-col>
            </el-row>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
    export default {
        props: ['project', 'user', 'index', 'isBothRole', 'currentRole', 'selectedable'],
        data() {
            return {
                user_type: "freelancer",
                copyText: '',
            }
        },
        methods: {
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
            handleDropdownCommand(command) {
                if (command.action == "email") {
                    this.sendProject(command.project_id);
                } else if (command.action == "delete") {
                    this.deleteProject(command.project_id);
                } else if (command.action == "rename") {
                    this.renameProject(command.project);
                } else if (command.action == "moveToInProgress") {
                    if (command.project_type == "design") {
                        if (this.currentRole === 'Client') {
                            this.changeProjectStatus(
                                command.project_id,
                                'revision'
                            );
                        } else {
                            this.openInProgressDialog(command.project_id);
                        }
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
                } else if (command.action == "slack-hash") {
                    this.slackCopyHash(command.project);
                } else if (command.action == "summary") {
                    this.goToSummary(command.project);
                }
            },
            slackCopyHash(project){
                self = this
                this.$copyText(project.project_hash).then(
                    function (e) {
                        self.$notify({
                            title: 'Success',
                            message: 'Hash copied to clipboard',
                            type: 'success'
                        });
                    },
                    function (e) {
                        self.$notify({
                            title: 'Success',
                            message: "Hash dosn't copy",
                            type: 'warning'
                        });
                    })
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
                        this.$emit('loadingMethod', {project_id: project_id, status: true})
						axios
							.get(
								"/api/projects/send_project/" +
									project_id +
									"/" +
									this.user_type
							)
							.then((response) => {
                                toastr["success"](response.data.message, "Success");
                                this.$emit('loadingMethod', {project_id: project_id, status: false})
							})
							.catch((error) => {
                                console.log(error.message);
                                this.$emit('loadingMethod', {project_id: project_id, status: false})
							});
					})
					.catch(() => {
						console.log("canceled");
					});
            },
            deleteProject(project_id) {
                this.$emit('selectableChange', project_id)
                // var self = this;
				// this.$confirm(
				// 	"Are you sure you want to delete this project?",
				// 	"Warning",
				// 	{
				// 		confirmButtonText: "Yes",
				// 		confirmButtonColor: "#ee5757",
				// 		cancelButtonText: "Cancel",
				// 		type: "warning",
				// 	}
				// )
				// 	.then(() => {
                //         this.$emit('loadingMethod', {project_id: project_id, status: true})
				// 		axios
				// 			.delete("/api/projects/delete_project/" + project_id)
				// 			.then((response) => {
                //                 toastr["success"](response.data.message, "Success");
                //                 this.$store.commit('DELETE_PROJECT', project_id)
                //                 this.$emit('loadingMethod', {project_id: project_id, status: false})
				// 			})
				// 			.catch((error) => {
                //                 this.$emit('loadingMethod', {project_id: project_id, status: false})
				// 			});
				// 	})
				// 	.catch(() => {
				// 		// Canceled
				// 	});
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
					this.$emit('loadingMethod', {project_id: project_id, status: true})
					axios
						.put(`/api/projects/change_status/${project_id}/${status}`)
						.then((response) => {
							this.$emit('loadingMethod', {project_id: project_id, status: false})

                            if (response.status == 200) {
                                toastr["success"](response.data.message, "Success");
                                this.project.status = status
                                this.$store.commit('UPDATE_PROJECT_STATUS', this.project)
							} else {
								toastr["error"](response.error, "Error");
							}
						});
				});
			},
            renameProject(project) {
                let role = 'project';
                if (this.currentRole == 'Client') {
                    role = 'request';
                }
				this.$router.push({
					name: "exist-project-title",
					params: { role: role, type: project.type, project_id: project.id},
                    query: {rename: true}
				});
            },
            openInProgressDialog(project_id) {
                this.$emit('openInProgressDialog', project_id)
			},
            openAddTeamMemberDialog(project) {
				this.$emit('openAddTeamMemberDialog', project)
            },
            goToSummary(project) {
                let role = 'project';
                if (this.currentRole == 'Client') {
                    role = 'request';
                }
                this.$router.push({name: 'project-summary', params: {role: role,project_id: project.id, type: project.type}})
            },
            showCardMenu(index) {
                $("#myDropdown" + index).toggleClass("show");
            },
            hideCardMenu(event) {
                var target = event.target;
                for (var i = 0; i < this.projects.length; i++) {
                    var str = "cardIcon" + i;
                    if (target.id !== str) {
                        $("#myDropdown" + i).removeClass("show");
                    }
                }
            },
            addPictures(project_id, revision_id, type = null) {
                let role = 'project';
                if (this.currentRole == 'Client') {
                    role = 'request';
                }
                this.$router.push({
                    name: "project-summary",
                    params: {
                        project_id: project_id,
                        revision_id: revision_id,
                        type: type,
                        role: role
                    },
                });
            },
            openRequest(project_id, revision_id) {
                if (!this.isBothRole) {
                    this.$router.push({
                        name: "request-project",
                        params: {
                            project_id: project_id,
                            revision_id: revision_id
                        },
                    });
                } else {
                    alert('You are not a client to edit a project request')
                }
            },
            
        },
        computed: {
            ...mapGetters([
				"ownedTeam",
			]),
        }
    }
</script>

<style lang="scss" scoped>
    .menu-select {
        width: 100%;
        text-align: right;
        margin-right: 37px;
        font-size: 20px;
        color: #fff;
        cursor: pointer;
    }

    .delete-btn {
        position: absolute;
        top: 5px;
        right: 10px;
    }

    .edition-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        padding-left: 6px;
        padding-bottom: 10px;
        font-size: 22px;
        border: 1px solid #cacbcc;
        color: #626364;
        outline: none;
        background-color: #fff;
    }
</style>