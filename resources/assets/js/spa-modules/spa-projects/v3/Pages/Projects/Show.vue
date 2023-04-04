<template>
	<div>
		<el-row v-if="!isSubscribed && isBothRole" style="margin-bottom: 24px">
			<el-alert type="error">
				<b>
					Your subscription as a freelancer is expired and access to your projects has been denied. Please
					<router-link tag="a" :to="{name: 'setting-billing'}" class="tw-text-primary tw-hover-text-primary">upgrade your subscription</router-link>to access your projects
				</b>
			</el-alert>
		</el-row>
		<el-row type="flex" align="middle">
			<el-row :gutter="24" style="width:100%">
				<el-col :xs="14" :sm="12" :md="12" :lg="6" :xl="6">
					<el-input
						placeholder="Search"
						suffix-icon="el-icon-search"
						class="search-project"
						v-model="search"
					></el-input>
				</el-col>
				<el-col :xs="8" :sm="6" :md="6" :lg="4" :xl="4">
					<el-select v-model="filters" multiple placeholder="Filter" style="margin-right:8px">
						<el-option
							v-for="item in filterList"
							:key="item.value"
							:label="item.label"
							:value="item.value"
						></el-option>
					</el-select>
				</el-col>
				<!-- <el-col :xs="2" :sm="2" :md="2" :lg="2" :xl="2">
                    <i class="el-icon-user-solid" v-if="activeAllProfiles"></i>
                    <i class="el-icon-user" v-else></i>
				</el-col>-->
			</el-row>
			<el-row type="flex" class="view-type" align="middle">
				<i
					class="el-icon-menu"
					@click="changeProjectsListingType('card')"
					:class="{'active-type' : projects_listing_type == 'card'}"
				></i>
				<i
					class="el-icon-tickets"
					@click="changeProjectsListingType('list')"
					:class="{'active-type' : projects_listing_type == 'list'}"
				></i>
			</el-row>
		</el-row>
		<hr />
		<project-card
			:isSubscribed="isSubscribed"
			v-if="projects_listing_type == 'card'"
			:projects="projectsFilterBySelect"
			:user="user"
			@openInProgressDialog="openInProgressDialog"
			@openAddTeamMemberDialog="openAddTeamMemberDialog"
			@openCreativeBriefModal="openCreativeBriefModal"
			@loadingMethod="loadingMethod"
			:loading="loading"
		/>

		<project-list
			v-else-if="projects_listing_type == 'list'"
			:projects="projectsFilterBySelect"
			@openAddTeamMemberDialog="openAddTeamMemberDialog"
			@openInProgressDialog="openInProgressDialog"
			@openCreativeBriefModal="openCreativeBriefModal"
		/>

		<!--Move to In Progress Dialog-->
		<el-dialog
			title="Move Project to InBox"
			:close-on-click-modal="false"
			:close-on-press-escape="false"
			:show-close="false"
			:visible.sync="showInProgressDialog"
			@open="onOpenDialogInProgress"
			@close="onCloseDialogInProgress"
			width="30%"
			center
			class="inProgressDialog"
		>
			<el-row>
				<el-col style="text-align: center">
					<div class="uploader" style="width: 100%; margin-top: 0px">
						<el-upload
							ref="upload"
                            :headers="headers"
							drag
							:data="imageFormData"
							action="/api/files/upload"
							:on-error="handleError"
							:on-remove="deleteFile"
							:on-success="handleSuccess"
							:auto-upload="true"
							:file-list="fileList"
							name="photos"
							list-type="picture"
						>
							<i class="el-icon-upload"></i>
							<div class="el-upload__text">
								Drop file here or
								<em>click to upload</em>
							</div>
						</el-upload>
					</div>
				</el-col>
			</el-row>
			<el-row>
				<el-col style="margin-top: 12px; text-align: center">
					<p>Send Proofs for next revision round</p>
				</el-col>
			</el-row>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelInProgress()">Cancel</el-button>
                <el-button type="primary" @click="moveToInProgress()">Finish & Send</el-button>
			</span>
		</el-dialog>

		<!--Add Team Members-->
		<el-dialog
			:close-on-click-modal="false"
			:close-on-press-escape="false"
			:show-close="true"
			:visible.sync="showDialogAddTeamMembers"
			width="50%"
			center
			style="padding: 20px !important"
			class="teammember-dialog"
		>
			<div style="padding: 25px 25px 30px">
				<el-row type="flex" justify="center">
					<el-col>
						<el-row style="text-align: center">
							<h3>Add Team Member</h3>
						</el-row>

						<!--Team members-->
						<el-row style="margin-top: 20px" type="flex" justify="center">
							<div>
								<div
									v-if="teamMembers.length > 0"
									v-for="member in teamMembers"
									:key="member.id"
									style="margin-top: 10px"
								>
									<label class="notify-label">
										<div style="display: flex; align-items: center">
											<img
												style="float: left"
												:src="member.photo_url"
												class="img-circle special-img team-circle"
											/>
											<span style="margin-left: 20px">{{member.email}}</span>
										</div>
									</label>
									<div style="float: right; margin-left: 20px">
										<label class="switch">
											<input
												type="checkbox"
												name="new_project"
												:id="member.id"
												@change="modifyMemberAccess"
												:checked="current_project.users && current_project.users.indexOf(member.id) >= 0"
											/>
											<span class="slider round"></span>
										</label>
									</div>
								</div>
							</div>
						</el-row>
					</el-col>
				</el-row>
				<el-row style="text-align: center">
					<el-col style="margin-top: 20px">
						<el-button type="primary" @click="closeAddTeamMemberDialog">Close</el-button>
					</el-col>
				</el-row>
			</div>
		</el-dialog>

		<!--Creative Brief Dialog-->
		<el-dialog
			title="Project Creative Brief"
			:visible.sync="showDialogCreativeBrief"
			class="creative-brief-dialog"
		>
			<quill-editor v-model="creativeBrief" ref="myQuillEditor" :options="editorOption"></quill-editor>
			<span slot="footer" class="dialog-footer">
				<el-button type="primary" :loading="buttonLoading" @click="saveCreativeBrief">Save</el-button>
				<el-button @click="showDialogCreativeBrief = false" class="cancel-btn">Cancel</el-button>
			</span>
		</el-dialog>
	</div>
</template>

<script>
	import ProjectCard from "./components/Card/Card";
	import ProjectList from "./components/List/List";
	import { mapActions, mapGetters } from "vuex";
	export default {
		components: {
			ProjectCard,
			ProjectList
		},
		data() {
			return {
				search: "",
				activeAllProfiles: false,
				loading: [],
                showInProgressDialog: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
				imageFormData: {
					project_id: "",
					file_type: "picture",
					owner_type: ""
				},
				filterList: [
					{
						value: "website",
						label: "Website"
					},
					{
						value: "design",
						label: "Design"
					},
					{
						value: "video",
						label: "Video"
					}
				],
				filters: [],
				fileList: [],
				project_id: "",
				current_project: {},
				editorOption: {},
				creativeBrief: "",
				showDialogAddTeamMembers: false,
				showDialogCreativeBrief: false,
				savedFiles: [],
				buttonLoading: false
			};
		},
		methods: {
            ...mapActions(["loadProjects", "changeProjectsListingType"]),
            getCookie(cName) {
                const name = cName + "=";
                const cDecoded = decodeURIComponent(document.cookie); //to be careful
                const cArr = cDecoded.split('; ');
                let res;
                cArr.forEach(val => {
                    if (val.indexOf(name) === 0) res = val.substring(name.length);
                })
                return res
            },  
			openInProgressDialog(project_id) {
				this.$confirm(
					"Are you sure you want to move this project to In Progress?",
					"Warning",
					{
						confirmButtonText: "Yes, move",
						cancelButtonText: "Cancel",
						type: "success",
						title: ""
					}
				).then(() => {
					this.project_id = project_id;
					var formData = {
						project_id: this.project_id
					};

					this.loading.push(project_id);
					axios
						.post("/api/revisions/create", formData)
						.then(response => {
							if (response.data.status == 1) {
								this.loading.pop(project_id);
								this.showInProgressDialog = true;
								this.imageFormData.project_id = this.project_id;
							} else {
								this.loading.pop(project_id);
								this.handle_error(response.data.errors);
							}
						})
						.catch(error => {
							this.handle_error({});
						});
				});
			},
			moveToInProgress(project_type) {
				this.loading.push(this.project_id);
				if (this.savedFiles.length > 0) {
					axios
						.get(
							"/api/projects/send_project/" +
								this.project_id +
								"/" +
								this.currentRole
						)
						.then(response => {
							if (response.data.status == 1) {
								this.loading.pop(this.project_id);
								this.showInProgressDialog = false;
								this.savedFiles = [];
								if (response.data.status == 1) {
                                    this.$notify({
                                        title: "Success",
                                        message: response.data.message,
                                        type: "success"
                                    });
                                    this.loadProjects();
								} else {
									this.loading.pop(this.project_id);
									this.handle_error(response.data.errors);
								}
							} else {
								this.loading.pop(this.project_id);
								this.handle_error(response.data.errors);
							}
						})
						.catch(error => {
							this.handle_error({});
						});
					this.loading.pop(this.project_id);
				} else {
					toastr["error"](
						"You should upload some proofs, before creating new revision round",
						"Error"
					);
					this.loading.pop(this.project_id);
				}
			},
			onOpenDialogInProgress() {
				this.imageFormData.owner_type = "proof";
			},
			onCloseDialogInProgress() {
				this.imageFormData.owner_type = "";
				this.fileList = [];
			},
			openCreativeBriefModal(project) {
				this.creativeBrief = project.creative_brief;
				this.current_project = project;
				this.showDialogCreativeBrief = true;
			},
			saveCreativeBrief() {
				let formData = {
					project_id: this.current_project.id,
					creative_brief: this.creativeBrief
				};
				this.buttonLoading = true;
				axios
					.post("/api/projects/save-creative-brief", formData)
					.then(response => {
						this.buttonLoading = false;
						if (response.data.status) {
							this.showDialogCreativeBrief = false;
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success"
							});
							this.current_project.creative_brief = this.creative_brief;
							this.$store.commit(
								"UPDATE_PROJECT_STATUS",
								this.current_project
							);
						} else {
							this.$notify({
								title: "Error",
								message: response.data.errors,
								type: "error"
							});
						}
					})
					.catch(error => {
						this.buttonLoading = false;
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error"
						});
					});
			},
			handleError(error) {
				console.log(error);
			},
			deleteFile(file, fileList) {
				var self = this;
				this.loading.push(this.project_id);
				axios
					.delete("/api/files/delete/" + file.response.data.id)
					.then(response => {
						if (response.data.status == 1) {
							self.loading.pop(this.project_id);
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch(error => {
						self.loading.pop(this.project_id);
						self.handle_error({});
					});
			},
			handleSuccess(response) {
				var self = this;
				if (response.status == 1) {
					if (response.data.length) {
						response.data.forEach(function(element, index) {
							self.savedFiles.push(element);
							self.fileList.push({
								name: "Converted Image-" + (index + 1),
								url: `${self.spacePrefix}/${element.path}`,
								response: {
									data: element
								}
							});
						});
					} else {
						this.savedFiles.push(response.data);
					}
					toastr["success"](response.message, "Success");
				} else {
					toastr["error"](response.error, "Error");
				}
			},
			cancelInProgress() {
				this.showInProgressDialog = false;
				this.loading.push(this.project_id);
				axios
					.delete("/api/revisions/delete/" + this.project_id)
					.then(response => {
						if (response.data.status == 1) {
							this.loading.pop(this.project_id);
							this.project_id = "";
						} else {
							this.handle_error(response.data.errors);
						}
					})
					.catch(error => {
						this.handle_error({});
					});
			},
			openAddTeamMemberDialog(project) {
				this.project_id = project.id;
				this.current_project = project;
				this.showDialogAddTeamMembers = true;
			},
			closeAddTeamMemberDialog() {
				this.project_id = "";
				this.showDialogAddTeamMembers = false;
			},
			modifyMemberAccess(event) {
				let formData = {
					member_id: event.target.id,
					project_id: this.project_id
				};

				if (event.target.checked) {
					axios
						.post("/api/projects/add-team-member", formData)
						.then(response => {
							if (response.data.status == 1) {
								this.current_project.users.push(
									Number(event.target.id)
								);
								toastr["success"](response.data.message, "Success");
								this.$store.commit(
									"UPDATE_PROJECT_STATUS",
									this.current_project
								);
							} else {
								event.target.checked = false;
								toastr["error"](response.data.errors, "Error");
							}
						})
						.catch(error => {
							event.target.checked = false;
							toastr["error"](
								"Something went wrong, please try again later",
								"Error"
							);
							this.handle_error({});
						});
				} else {
					axios
						.delete(
							"/api/projects/delete-team-member/" +
								this.project_id +
								"/" +
								event.target.id
						)
						.then(response => {
							if (response.data.status == 1) {
								this.current_project.users.splice(
									this.current_project.users.indexOf(
										Number(event.target.id)
									),
									1
								);
								toastr["success"](response.data.message, "Success");
								this.$store.commit(
									"UPDATE_PROJECT_STATUS",
									this.current_project
								);
							} else {
								event.target.checked = true;
								toastr["error"](response.data.errors, "Error");
							}
						})
						.catch(error => {
							event.target.checked = true;
							toastr["error"](
								"Something went wrong, please try again later",
								"Error"
							);
						});
				}
			},
			handle_error(errors) {
				var text = "Connection Error!";
				if (Object.keys(errors).length > 0) {
					text = "";
					for (let index in errors) {
						text += errors[index] + "\n";
					}
				}
				this.$notify({
					title: "Error",
					message: text,
					type: "error"
				});
			},
			loadingMethod(data) {
				if (data.selected && data.selected.length > 0) {
					if (data.status) {
						this.loading = data.selected;
					} else {
						this.loading = [];
					}
				}
				if (data.project_id) {
					if (data.status) {
						this.loading.push(data.project_id);
					} else {
						this.loading.pop(data.project_id);
					}
				}
            },
            jqueryCssAdjust() {
                $(".el-upload__input").css("display", "none");
            }
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
			},
			showByStatus() {
				return this.$store.state.projects.show_project_by_status;
			},
			...mapGetters([
				"projects",
				"projectsFilter",
				"user",
				"currentRole",
				"isBothRole",
				"isSubscribed",
				"in_progress",
				"on_revision",
				"approved",
				"completed",
				"listing",
				"on_draft",
				"on_hold",
				"member_projects",
				"current_member",
				"ownedTeam",
				"teamMembers",
				"projects_listing_type"
			]),
			searchProjects() {
				let projects = this.projectsFilter;
				if (this.filters.includes("all")) {
					projects = this.projects;
				}
				if (projects.length > 0) {
					return projects.filter(project => {
						return (
							project.name
								.toLowerCase()
								.indexOf(this.search.toLowerCase()) !== -1
						);
					});
				} else {
					return projects;
				}
			},
			projectsFilterBySelect() {
				if (
					this.searchProjects &&
					this.searchProjects.length > 0 &&
					this.filters.length > 0
				) {
					const projects = [];
					for (const project of this.searchProjects) {
						if (
							this.filters
								.filter(filter => filter != "all")
								.includes(project.type.toLowerCase())
						) {
							projects.push(project);
						}
					}
					return projects;
				} else {
					return this.searchProjects;
				}
			}
    },
        created() {
            // set to headers XSRF-TOKEN
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
        },  
		mounted() {
            this.imageFormData.user_id = Spark.userId;
            this.$nextTick(function() {
				this.jqueryCssAdjust();
			});
		}
	};
</script>

<style>
@import "../../../../../../sass/pages/projects.scss";

.search-project {
	--tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
		0 2px 4px -1px rgba(0, 0, 0, 0.06);
	box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000),
		var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
	border-radius: 6px;
}

.search-project input {
	border: none;
	border-radius: 6px;
}


.inProgressDialog input[type="file"] {
    display: none;
}

/* The switch - the box around the slider */
.switch {
	position: relative;
	display: inline-block;
	width: 49px;
	height: 23px;
}

/* Hide default HTML checkbox */
.switch input {
	display: none;
}

/* The slider */
.slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #ccc;
	-webkit-transition: 0.4s;
	transition: 0.4s;
}

.slider:before {
	position: absolute;
	content: "";
	height: 15px;
	width: 15px;
	left: 4px;
	bottom: 4px;
	background-color: white;
	-webkit-transition: 0.4s;
	transition: 0.4s;
}

input:checked + .slider {
	background-color: #2196f3;
}

input:focus + .slider {
	box-shadow: 0 0 1px #2196f3;
}

input:checked + .slider:before {
	-webkit-transform: translateX(26px);
	-ms-transform: translateX(26px);
	transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
	border-radius: 34px;
}

.slider.round:before {
	border-radius: 50%;
}

.view-type i {
	font-size: 24px;
}

.view-type i:hover {
	cursor: pointer;
	color: #81b4a1;
}
.active-type {
	color: #81b4a1;
}
</style>