<template>
	<div id="dashboard">
		<el-row :gutter="20">
			<el-col :xs="12" :sm="8" :md="6">
				<el-card class="state-card">
					<div>
						<div class="state-icon">
							<i class="el-icon-receiving state-start-icon"></i>
						</div>
						<div class="tw-flex tw-items-center tw-justify-center">
							<div class="state-number">
								<div v-if="loading">
									<div class="lds-ripple">
										<div></div>
										<div></div>
									</div>
								</div>
								<span v-if="!loading">{{storage_states.total_formate}}</span>
							</div>
							<span v-if="!loading" class="tw-font-semibold tw-ml-4">/ {{storage_capacity}}GB</span>
						</div>
						<div class="state-content">Total Space Used</div>
					</div>
				</el-card>
			</el-col>
			<el-col :xs="12" :sm="8" :md="6">
				<el-card class="state-card">
					<div>
						<div class="state-icon">
							<i class="el-icon-star-on state-start-icon"></i>
						</div>
						<div class="state-number">
							<div v-if="loading">
								<div class="lds-ripple">
									<div></div>
									<div></div>
								</div>
							</div>
							<span v-if="!loading">{{rateAverage}}</span>
						</div>
						<div class="state-content">Average Rate</div>
					</div>
				</el-card>
			</el-col>
			<el-col :xs="12" :sm="8" :md="6">
				<el-card class="state-card">
					<div>
						<div class="state-icon">
							<i class="el-icon-warning-outline color-warning state-start-icon"></i>
						</div>
						<div class="state-number">
							<div v-if="loading">
								<div class="lds-ripple">
									<div></div>
									<div></div>
								</div>
							</div>
							<span v-if="!loading">{{totalIssues}}</span>
						</div>
						<div class="state-content color-warning">Issues</div>
					</div>
				</el-card>
			</el-col>
			<el-col :xs="12" :sm="8" :md="6">
				<el-card class="state-card">
					<div>
						<div class="state-icon">
							<i class="el-icon-chat-dot-square state-start-icon"></i>
						</div>
						<div class="state-number">
							<div v-if="loading">
								<div class="lds-ripple">
									<div></div>
									<div></div>
								</div>
							</div>
							<span v-if="!loading">{{unread_comments}}</span>
						</div>
						<div class="state-content">Messages</div>
					</div>
				</el-card>
			</el-col>
		</el-row>
		<h3
			class="hidden-sm-and-up text-center"
			style="text-transform:uppercase;font-weight:bold"
		>Dashboard</h3>
		<el-row :gutter="20">
			<el-col :span="24" class="progress-dashboard" v-if="websiteProjects != 0 || designProjects != 0">
				<el-card class="progress-dashboard-card" v-loading="loading">
					<h5 v-if="websiteProjects > 0">
						<i class="fa fa-globe"></i> Website
						<span style="float:right;font-size:12px;color:#6b7280">
							<strong>{{totalIssueProjectDesign.solved_issuesWeb}} of {{totalIssueProjectDesign.total_issuesWeb}}</strong> Correction and
							<strong>{{totalIssueProjectDesign.completed_issuesWeb}}</strong> of Completed projects
						</span>
					</h5>
					<el-progress
						v-if="websiteProjects > 0"
						:text-inside="true"
						:stroke-width="15"
						:percentage="averageProjectWebsite"
						:color="colors"
					></el-progress>
					<h5 v-if="designProjects > 0">
						<i class="el-icon-picture-outline"></i> Design
						<span style="float:right;font-size:12px;color:#6b7280">
							<strong>{{totalIssueProjectDesign.solved_issues}} of {{totalIssueProjectDesign.total_issues}}</strong> Correction and
							<strong>{{totalIssueProjectDesign.completed_issues}}</strong> of Completed Projects
						</span>
					</h5>
					<el-progress
						v-if="designProjects > 0"
						:text-inside="true"
						:stroke-width="15"
						:percentage="averageProjectDesign"
						:color="colors"
					></el-progress>
					<h5 v-if="videoProjects > 0">
						<i class="el-icon-video-play"></i> Video
						<span style="float:right;font-size:12px;color:#6b7280">
							<strong>{{totalIssueProjectDesign.solved_issuesVideo}} of {{totalIssueProjectDesign.total_issuesVideo}}</strong> Correction and
							<strong>{{totalIssueProjectDesign.completed_issuesVideo}}</strong> of Completed Projects
						</span>
					</h5>
					<el-progress
						v-if="videoProjects > 0"
						:text-inside="true"
						:stroke-width="15"
						:percentage="averageProjectVideo"
						:color="colors"
					></el-progress>
				</el-card>
			</el-col>
			<el-col :xs="24" :sm="24" :md="12" class="progress-dashboard">
				<el-card v-loading="loading">
					<div slot="header" class="clearfix">
						<el-badge :value="totalIssues" style="padding-right: 8px">
							<i class="el-icon-warning-outline"></i> Issues
						</el-badge>
					</div>
					<div v-if="current_projects.length == 0">
						<p style="text-align:center" v-if="!loading">There is not issues to show...</p>
						<p style="text-align:center" v-else>Loading...</p>
					</div>
					<el-timeline class="issue-card">
						<div v-for="(project,index) in current_projects" :key="index">
							<div v-for="(proof, key) in project.active_revision.proofs" :key="key">
								<el-timeline-item
									v-for="(issue, indexIssue) in proof.issues"
									:key="indexIssue"
									:color="issueColors(project, issue)"
									:timestamp="utcToLocal(issue.created_at)"
								>
									<el-popover
										placement="top-start"
										:title="project.name"
										width="300"
										trigger="hover"
										:content="issue.description"
										:disabled="issue.description.length < 100"
									>
										<h5
											style="cursor:pointer"
											slot="reference"
											@click="openProofer(project.id, project.active_revision.id, project)"
										>
											<i class="el-icon-warning-outline" style="color:#E6A23C"></i>
											{{issue.description | truncate(100, '...')}}
										</h5>
									</el-popover>
									<div
										@click="openProofer(project.id, project.active_revision.id, project)"
										class="timeline-pronect"
									>
										<span :class="project.type == 'website' ? 'fa fa-globe' : 'el-icon-picture-outline'"></span>
										{{project.name}} project ({{project.type}})
										<i class="el-icon-link"></i>
									</div>
								</el-timeline-item>
							</div>
						</div>
					</el-timeline>
				</el-card>
			</el-col>
			<el-col :xs="24" :sm="24" :md="12" class="progress-dashboard">
				<el-card v-loading="loading">
					<div slot="header" class="clearfix">
						<el-badge :value="unread_comments" style="padding-right: 8px">
							<i class="el-icon-chat-dot-square"></i> Messages
						</el-badge>
					</div>
					<div v-if="current_projects.length == 0">
						<p style="text-align:center" v-if="!loading">There is not issues to show...</p>
						<p style="text-align:center" v-else>Loading...</p>
					</div>
					<el-timeline class="message-card">
						<div v-for="(project,index) in current_projects" :key="index">
							<div v-for="(proof, key) in project.active_revision.proofs" :key="key">
								<div v-for="(issue, indexIssue) in proof.issues" :key="indexIssue">
									<el-timeline-item
										v-for="(comment, indexComment) in issue.comments"
										:key="indexComment"
										:color="comment.unread_comment && comment.unread_comment.user_id == user.id ? '#0bbd87' : ''"
										:timestamp="utcToLocal(comment.created_at)"
									>
										<div style="font-size:12px">
											<i class="el-icon-warning-outline" style="color:#E6A23C"></i>
											{{issue.description | truncate(30, '...')}}
										</div>

										<el-popover
											placement="top-start"
											:title="project.name"
											width="300"
											trigger="hover"
											:content="comment.description"
											:disabled="comment.description.length < 100"
										>
											<h5 style="cursor:pointer;font-size:14px" slot="reference">
												<i class="el-icon-chat-dot-square" style="color:#F56C6C;font-size:12px"></i>
												{{comment.description | truncate(100, '...')}}
											</h5>
										</el-popover>
										<div
											@click="openProofer(project.id, project.active_revision.id, project)"
											class="timeline-pronect"
										>
											<span :class="project.type == 'website' ? 'fa fa-globe' : 'el-icon-picture-outline'"></span>
											{{project.name}} project ({{project.type}})
											<i class="el-icon-link"></i>
										</div>
									</el-timeline-item>
								</div>
							</div>
						</div>
					</el-timeline>
				</el-card>
			</el-col>
			<el-col :span="24" class="progress-dashboard">
				<el-card v-loading="loading">
					<div slot="header" class="clearfix">
						<span v-if="isFreelancer">{{filterProject}} Projects</span>
						<span v-else>Download Final files</span>
						<!-- <el-badge :value="100" :max="10" class="message-badge">
							<span class="message">Messages</span>
						</el-badge>
						<span class="message-issue-and"></span>
						<el-badge :value="100" :max="10" class="issue-badge" type="warning">
							<span class="issue">Issues</span>
						</el-badge>-->
						<el-dropdown
							trigger="click"
							style="float: right;"
							@command="handleCommandFilter"
							v-if="isFreelancer"
						>
							<span class="el-dropdown-link">
								<i class="el-icon-more-outline icon-filter"></i>
							</span>
							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item
									icon="el-icon-upload2"
									command="Completed"
								>Completed ({{completed.length}})</el-dropdown-item>
								<el-dropdown-item
									icon="el-icon-check"
									command="Approved"
									v-if="isFreelancer"
								>Approved ({{approved.length}})</el-dropdown-item>
								<el-dropdown-item
									icon="el-icon-odometer"
									command="Progress"
									v-if="isFreelancer"
								>Progress ({{in_progress.length + on_revision.length}})</el-dropdown-item>
								<!-- <el-dropdown-item icon="el-icon-odometer" command="Revision" v-if="isFreelancer">Revision ({{}})</el-dropdown-item> -->
								<!-- <el-dropdown-item icon="el-icon-edit" command="Review" v-if="isFreelancer">Review ({{completed.length}})</el-dropdown-item> -->
								<el-dropdown-item
									icon="el-icon-video-pause"
									command="Hold"
									v-if="isFreelancer"
								>Hold ({{on_hold.length}})</el-dropdown-item>
								<el-dropdown-item
									icon="el-icon-folder-delete"
									command="Draft"
									v-if="isFreelancer"
								>Draft ({{on_draft.length}})</el-dropdown-item>
							</el-dropdown-menu>
						</el-dropdown>
					</div>
					<div v-if="filterProjectStatus.length == 0">
						<p style="text-align:center" v-if="!loading">You not have Project here...</p>
						<p style="text-align:center" v-else>Loading...</p>
					</div>
					<!-- <transition-group name="el-fade-in-linear" tag="div" > -->
					<div v-for="(project, index) in filterProjectStatus" :key="index" :data-index="project.id">
						<el-divider v-if="index > 0"></el-divider>
						<el-row :gutter="12" type="flex" align="middle">
							<el-col :xs="12" :sm="8" :md="8" :lg="6" :xl="6" class="design-flex">
								<span
									data-v-4d94ffeb
									class="el-avatar el-avatar--square image-container"
									style="height: 76px; width: 76px; line-height: 76px;"
                                    v-if="project.active_revision.proofs.length > 0"
								>
									<img
										:src="`${spacePrefix}/${project.active_revision.proofs[0].project_files.path}`"
										style="object-fit: fill;"
									/>
									<div class="middle-overlay" @click="openFinalfilesDialog(project)" v-if="!isFreelancer">
										<i class="el-icon-view image-icon"></i>
									</div>
								</span>
								<div class="design-content">
									<div class="title" @click="openProofer(project.id, project.active_revision.id, project)">
										<i :class="project.type == 'website' ? 'fa fa-globe' : 'el-icon-picture-outline'"></i>
										{{ project.name | truncate(12, '...') }}
									</div>
									<div class="content">
										<strong>{{ project.company | truncate(10, '...') }}</strong> company
									</div>
									<el-tooltip
										class="item"
										effect="dark"
										content="Download"
										placement="bottom"
										v-if="project.finalFiles.files.length > 0"
									>
										<div class="image-name" @click="downloadFinalFiles(project)">
											Download final
											<i class="el-icon-download"></i>
										</div>
									</el-tooltip>
								</div>
							</el-col>
							<el-col
								class="hidden-sm-and-down"
								:md="6"
								:lg="6"
								:xl="6"
								v-if="lastIssue(project.active_revision) != 0"
							>
								<transition name="el-fade-in-linear">
									<el-popover
										placement="top-start"
										:title="'Last Issue and (' + (project.total_issues - project.solved_issues) + ') not solve'"
										width="300"
										trigger="hover"
										:content="lastIssue(project.active_revision)"
									>
										<el-badge slot="reference" :value="project.total_issues - project.solved_issues">
											<el-tag type="danger">{{lastIssue(project.active_revision) | truncate(30, '...')}}</el-tag>
										</el-badge>
									</el-popover>
								</transition>
							</el-col>
							<el-col
								:xs="12"
								:sm="8"
								:md="(lastIssue(project.active_revision) == 0 ? 12 : 6)"
								:lg="(lastIssue(project.active_revision) == 0 ? 12 : 6)"
								:xl="(lastIssue(project.active_revision) == 0 ? 12 : 6)"
							>
								<el-tooltip class="item" effect="dark" :content="project.percentage + '%'" placement="top">
									<el-progress
										:percentage="project.percentage"
										:color="colors"
										:status="project.percentage == 100 ? 'success' : 'warning'"
									></el-progress>
								</el-tooltip>
							</el-col>
							<el-col class="hidden-xs-only" :sm="4" :md="4" :lg="4" :xl="4">
								<div class="design-date">{{utcToLocal(project.active_revision.created_at)}}</div>
							</el-col>
							<el-col class="hidden-xs-only" :sm="4" :md="2" :lg="2" :xl="2">
								<el-tooltip class="item" effect="dark" content="View or edit" placement="top">
									<!-- <el-badge :value="4" class="item"> -->
									<el-button
										@click="openProofer(project.id, project.active_revision.id, project)"
										icon="el-icon-edit"
										circle
									></el-button>
									<!-- </el-badge> -->
								</el-tooltip>
							</el-col>
						</el-row>
					</div>
					<!-- </transition-group> -->
				</el-card>
			</el-col>
		</el-row>
		<!-- Upload Final Files Dialog -->
		<final-files-dialog
			v-if="showFinalFilesDialog"
			:showFinalFilesDialog="showFinalFilesDialog"
			:currentRole="currentRole"
			:isBothRole="isBothRole"
			:project="project"
			:revisionId="last_revision"
			v-on:closeFinalFilesDialog="showFinalFilesDialog = false"
			v-on:filesSended="finalFilesSended"
		></final-files-dialog>
	</div>
</template>


<script>
	import { mapActions, mapGetters } from "vuex";
	import "element-ui/lib/theme-chalk/display.css";
	export default {
		components: {
			"final-files-dialog": require("./partials/FinalFilesDialog")
		},
		data() {
			return {
				filterProject: "Completed",
				project: "",
				last_revision: "",
				activities: [
					{
						content: "Freelance Project",
						message: "issues message",
						timestamp: "2018-04-12 20:46",
						size: "large",
						type: "primary",
						icon: "el-icon-more"
					},
					{
						content: "Freelance Project",
						message: "issues message",
						timestamp: "2018-04-03 20:46",
						color: "#0bbd87"
					},
					{
						content: "Freelance Project",
						message: "issues message",
						timestamp: "2018-04-03 20:46",
						size: "large"
					},
					{
						content: "Freelance Project",
						message: "issues message",
						timestamp: "2018-04-03 20:46"
					}
				],
				showFinalFilesDialog: false,
				current_projects: [],
				search: "",
				loading: true,
				colors: [
					{ color: "#f56c6c", percentage: 20 },
					{ color: "#e6a23c", percentage: 40 },
					{ color: "#6f7ad3", percentage: 60 },
					{ color: "#1989fa", percentage: 80 },
					{ color: "#5cb87a", percentage: 100 }
				]
			};
		},
		methods: {
			issueColors(project, issue) {
				if (
					project.status === "completed" ||
					project.status === "approved"
				) {
					return issue.status == "done" ? "#0bbd87" : "#6f7ad3";
				} else {
					return issue.status == "done" ? "#0bbd87" : "";
				}
			},
			openFinalfilesDialog(project) {
				this.project = project;
				this.last_revision = project.active_revision.id;
				this.showFinalFilesDialog = true;
			},
			finalFilesSended(message) {
				this.showFinalFilesDialog = false;
                this.$notify({
                            title: "Success",
							message: message,
							type: "success"
                })
			},
			...mapActions(["loadProjects", "userStorage"]),
			utcToLocal(date) {
				var utc = moment.utc(date).toDate();
				return moment(utc)
					.local()
					.format("dddd, MMMM DD, YYYY HH:mm:A");
			},
			lastIssue(data) {
				var issue = [];
				var item = [];
				item = data.proofs.filter(proof => {
					issue = proof.issues.filter(issue => {
						return issue.status === "todo";
					});
				});
				if (issue.length > 0) {
					return issue[issue.length - 1].description;
				}
				return 0;
			},
			openProofer(project_id, revision_id, project) {
				this.$router.push({
					name: "proofer",
					params: {
						project_id: project_id,
						revision_id: revision_id,
						project: project
					}
				});
			},
			handleCommandFilter(command) {
				this.filterProject = command;
			},
			downloadFinalFiles(project) {
				axios({
					url: `/api/projects/download-zip/${project.id}`,
					method: "GET",
					responseType: "blob",
					header: {
						"Content-Type": "application/zip"
					}
				})
					.then(response => {
						const url = window.URL.createObjectURL(
							new Blob([response.data])
						);
						const link = document.createElement("a");
						link.href = url;
						link.setAttribute(
							"download",
							"prooflo" +
								"-" +
								project.name +
								".zip"
						);
						document.body.appendChild(link);
						link.click();
					})
					.catch(error => {
						this.handle_error({});
					});
			}
		},
		handle_error(errors) {
			this.fullscreenLoading = false;
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
                })
		},
		filters: {
			truncate: function(text, length, suffix) {
				if (text.length > length) {
					return text.substring(0, length) + suffix;
				} else {
					return text;
				}
			}
		},
		watch: {
			projects() {
				this.loading = false;
				this.current_projects = this.projects;
				if (this.current_projects.length > 0) {
					for (var i in this.current_projects) {
						if (this.current_projects[i].role == "freelancer") {
							this.current_rol = this.current_projects[i].role;
						} else if (this.current_projects[i].role == "client") {
							this.current_rol = this.current_projects[i].role;
						} else if (
							this.current_projects[i].role == "collaborator"
						) {
							this.current_rol = this.current_projects[i].role;
						}
						return;
					}
				}
			}
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
			},
			rateAverage() {
				let rate = 0;
				var projects = this.current_projects.filter(item => item.rate > 0)
					.length;
				if (projects > 0) {
					this.current_projects.map(item => {
						if (item.rate > 0) {
							console.log(item.rate, rate);
							rate += item.rate;
						}
					});
					return (rate / projects).toFixed(2);
				} else {
					return 0;
				}
			},
			...mapGetters([
				"user",
				"unread_comments",
				"isFreelancer",
				"isSubscribed",
				"projects",
				"in_progress",
				"on_revision",
				"approved",
				"completed",
				"listing",
				"on_draft",
				"on_hold",
				"member_projects",
				"current_member",
				"isBothRole",
				"currentRole",
				"ownedTeam",
				"teamMembers",
				"storage_states",
				"storage_capacity",
			]),
			websiteProjects() {
				let projects = [];
				if (this.current_projects && this.current_projects.length > 0) {
					projects = this.current_projects.filter(
						item => item.type == "website"
					).length;
				}
				return projects;
			},
			designProjects() {
				let projects = [];
				if (this.current_projects && this.current_projects.length > 0) {
					projects = this.current_projects.filter(
						item => item.type == "design"
					).length;
				}
				return projects;
			},
			videoProjects() {
				let projects = [];
				if (this.current_projects && this.current_projects.length > 0) {
					projects = this.current_projects.filter(
						item => item.type == "video"
					).length;
				}
				return projects;
			},
			filterProjectStatus() {
				return this.current_projects.filter(project => {
					if (this.filterProject.toLowerCase() == "progress") {
						return (
							project.status == this.filterProject.toLowerCase() ||
							project.status == "revision"
						);
					} else {
						return project.status == this.filterProject.toLowerCase();
					}
				});
			},
			filterunCompletedProjects() {
				var project = [];
				if (this.current_projects.length > 0) {
					project = this.current_projects.filter(project => {
						return (
							project.status != "hold" &&
							project.status != "approved" &&
							project.status != "completed" &&
							project.total_issues > 0
						);
					});
				}
				return project;
			},
			totalIssueProjectDesign: function() {
				let issues = [];
				let solved_issues = [];
				let total_issues = [];
				let completed_issues = [];
				let solved_issuesWeb = [];
				let total_issuesWeb = [];
				let completed_issuesWeb = [];
				let solved_issuesVideo = [];
				let total_issuesVideo = [];
				let completed_issuesVideo = [];

				// Design
				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "design" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						solved_issues.push(val.solved_issues); // the value of the current key.
				});

				solved_issues = solved_issues.reduce(function(solved_issues, num) {
					return solved_issues + num;
				}, 0);

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "design" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						total_issues.push(val.total_issues); // the value of the current key.
				});

				total_issues = total_issues.reduce(function(total_issues, num) {
					return total_issues + num;
				}, 0);

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						(val.type == "design" &&
							val.total_issues > 0 &&
							val.status == "approved") ||
						val.status == "completed"
					)
						completed_issues.push(val.total_issues - val.solved_issues); // the value of the current key.
				});

				completed_issues = completed_issues.reduce(function(
					completed_issues,
					num
				) {
					return completed_issues + num;
				},
				0);

				// WEb
				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "website" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						solved_issuesWeb.push(val.solved_issues); // the value of the current key.
				});

				solved_issuesWeb = solved_issuesWeb.reduce(function(
					solved_issuesWeb,
					num
				) {
					return solved_issuesWeb + num;
				},
				0);

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "website" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						total_issuesWeb.push(val.total_issues); // the value of the current key.
				});

				total_issuesWeb = total_issuesWeb.reduce(function(
					total_issuesWeb,
					num
				) {
					return total_issuesWeb + num;
				},
				0);

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "website" &&
						val.total_issues > 0 &&
						(val.status == "approved" || val.status == "completed")
					)
						completed_issuesWeb.push(
							val.total_issues - val.solved_issues
						); // the value of the current key.
				});

				completed_issuesWeb = completed_issuesWeb.reduce(function(
					completed_issuesWeb,
					num
				) {
					return completed_issuesWeb + num;
				},
				0);

				// Video
				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "video" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						solved_issuesVideo.push(val.solved_issues); // the value of the current key.
				});

				solved_issuesVideo = solved_issuesVideo.reduce(function(
					solved_issuesVideo,
					num
				) {
					return solved_issuesVideo + num;
				},
				0);

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "video" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						total_issuesVideo.push(val.total_issues); // the value of the current key.
				});

				total_issuesVideo = total_issuesVideo.reduce(function(
					total_issuesVideo,
					num
				) {
					return total_issuesVideo + num;
				},
				0);

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "video" &&
						val.total_issues > 0 &&
						(val.status == "approved" || val.status == "completed")
					)
						completed_issuesVideo.push(
							val.total_issues - val.solved_issues
						); // the value of the current key.
				});

				completed_issuesVideo = completed_issuesVideo.reduce(function(
					completed_issuesVideo,
					num
				) {
					return completed_issuesVideo + num;
				},
				0);

				return (issues = {
					total_issues: total_issues,
					solved_issues: solved_issues,
					completed_issues: completed_issues,
					total_issuesWeb: total_issuesWeb,
					solved_issuesWeb: solved_issuesWeb,
					completed_issuesWeb: completed_issuesWeb,
					total_issuesVideo: total_issuesVideo,
					solved_issuesVideo: solved_issuesVideo,
					completed_issuesVideo: completed_issuesVideo
				});
			},
			averageProjectDesign: function() {
				let total = [];

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "design" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						total.push(
							Math.round((val.solved_issues / val.total_issues) * 100)
						); // the value of the current key.
				});

				var count = total.reduce(function(total, num) {
					return total + num;
				}, 0);
				if (this.filterunCompletedProjects.length > 0) {
					return Math.round(
						count / this.filterunCompletedProjects.length
					);
				} else {
					return 0;
				}
			},
			averageProjectWebsite: function() {
				let total = [];

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "website" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						total.push(
							Math.round((val.solved_issues / val.total_issues) * 100)
						); // the value of the current key.
				});

				var count = total.reduce(function(total, num) {
					return total + num;
				}, 0);

				if (this.filterunCompletedProjects.length > 0) {
					return Math.round(
						count / this.filterunCompletedProjects.length
					);
				} else {
					return 0;
				}
			},
			averageProjectVideo: function() {
				let total = [];

				Object.entries(this.current_projects).forEach(([key, val]) => {
					if (
						val.type == "video" &&
						val.total_issues > 0 &&
						val.status != "hold" &&
						val.status != "approved" &&
						val.status != "completed"
					)
						total.push(
							Math.round((val.solved_issues / val.total_issues) * 100)
						); // the value of the current key.
				});

				var count = total.reduce(function(total, num) {
					return total + num;
				}, 0);

				if (this.filterunCompletedProjects.length > 0) {
					return Math.round(
						count / this.filterunCompletedProjects.length
					);
				} else {
					return 0;
				}
			},
			totalIssues: function() {
				let total = [];

				Object.entries(this.current_projects).forEach(([key, val]) => {
					total.push(val.total_issues - val.solved_issues); // the value of the current key.
				});

				return total.reduce(function(total, num) {
					return total + num;
				}, 0);
			}
		},
		created() {
			this.loadProjects();
			this.userStorage();
		}
	};
</script>

<style scoped lang="scss">
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;
.progress-dashboard {
	padding-bottom: 16px;
}

.message-issue-and {
	padding-left: 28px;
}

.message,
.issue {
	padding-right: 8px;
}

.icon-fitler {
	font-size: 16px;
	color: #6b7280;
}

.icon-filter:hover {
	color: #1989fa;
}

.design-flex {
	display: flex;
}

.design-content {
	padding-left: 12px;
	cursor: pointer;
}

.design-content .title {
	font-size: 16px;
	color: #424949;
	font-weight: 500;
}
.design-content .content {
	color: #6b7280;
	font-size: 12px;
}
.design-content .image-name {
	font-size: 12px;
	color: #777777;
	cursor: pointer;
}
.design-content .image-name:hover {
	color: #292a2b;
}

.design-content .image-name i {
	visibility: hidden;
	padding-left: 2px;
}
.design-content .image-name:hover i {
	visibility: visible;
}

.design-date {
	color: #6b7280;
	font-size: 11px;
}

.latest-issue {
	color: #6b7280;
}

.issue-card,
.message-card {
	height: 300px;
	padding: 0 8px;
	overflow: auto;
}

.timeline-pronect {
	font-size: 12px;
	color: #9095a1;
	cursor: pointer;
}

.timeline-pronect i {
	visibility: hidden;
}

.timeline-pronect:hover i {
	visibility: visible;
	color: #292a2b;
}

.image-container {
	position: relative;
}

.image-container img {
	transition: 0.3s ease;
}

.image-container:hover .middle-overlay {
	opacity: 0.75;
}
.middle-overlay {
	position: absolute;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	height: 100%;
	width: 100%;
	opacity: 0;
	transition: 0.5s ease;
	background-color: #000;
	cursor: pointer;
}

.middle-overlay .image-icon {
	color: white;
	font-size: 16px;
	position: absolute;
	top: 50%;
	left: 50%;
	-webkit-transform: translate(-50%, -50%);
	-ms-transform: translate(-50%, -50%);
	transform: translate(-50%, -50%);
	text-align: center;
}
.state-card {
	margin-bottom: 16px;
	.state-icon {
		width: 4rem;
		height: 4rem;
		margin-left: 0;
		margin-right: 0;
		display: flex;
		align-items: center;
		background: #fff5f5;
		border-radius: 9999px;
		flex-shrink: 0;
		box-shadow: $box-shadow;
		justify-content: center;
		.state-start-icon {
			font-size: 24px;
			color: #f56565;
		}
	}
	.state-number {
		text-align: center;
		font-size: 32px;
		font-weight: 500;
		margin: 8px 0;
	}
	.state-content {
		text-align: center;
		font-size: 18px;
		color: #f56565;
	}
}
.lds-ripple {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 80px;
}
.lds-ripple div {
	position: absolute;
	border: 4px solid $red;
	opacity: 1;
	border-radius: 50%;
	animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
}
.lds-ripple div:nth-child(2) {
	animation-delay: -0.5s;
}
@keyframes lds-ripple {
	0% {
		top: 36px;
		left: 36px;
		width: 0;
		height: 0;
		opacity: 1;
	}
	100% {
		top: 0px;
		left: 0px;
		width: 72px;
		height: 72px;
		opacity: 0;
	}
}
</style>