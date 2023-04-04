<template>
	<div>
		<form>
			<el-row>
				<pdf-converting v-if="pdfLoader" v-on:confirm="pdfLoader = false"></pdf-converting>
				<el-row class="upload-container">
					<el-col :span="24" style="padding-bottom: 12px; padding-top:12px">
						<el-steps
							finish-status="success"
							:active="2"
							align-center
							style="background: #fff;padding: 10px 0px;box-shadow: 0 0 8px 0 rgba(232,237,250,.6), 0 2px 4px 0 rgba(232,237,250,.5);"
						>
							<el-step title="Step 1" icon="el-icon-folder" description="Add Project"></el-step>
							<el-step title="Step 2" icon="el-icon-tickets" description="Fill Design Request"></el-step>
							<el-step title="Step 3" icon="el-icon-upload" description="Upload Proofs"></el-step>
						</el-steps>
					</el-col>
					<el-col :xs="24" :md="{span:8, offset:8}" class="upload-area text-center">
						<el-upload
							ref="upload"
							drag
                            :headers="headers"
							:data="formData"
							action="/api/files/upload"
							:on-preview="handlePreview"
							:on-remove="handleRemove"
							:before-remove="beforeRemove"
							:on-error="handleError"
							:on-success="handleSuccess"
							:before-upload="handleUpload"
							:auto-upload="true"
							:file-list="fileList"
							name="photos"
							multiple
							list-type="picture"
						>
							<i class="el-icon-upload"></i>
							<div class="el-upload__text">
								Drop file here or
								<em>click to upload</em>
							</div>
						</el-upload>
					</el-col>
				</el-row>
				<el-row style="text-align: center">
					<el-col :xs="24" :md="{span:8, offset:8}" class="buttons-area-files">
						<!-- <el-button type="primary" @click="finishCreateProject()" :loading="saveLoading">Finish</el-button> -->
						<el-button type="primary" @click="sendProject()" :loading="sendLoading">Finish & Send</el-button>
						<el-button @click="cancel()" class="save-draft-btn">Save As Draft</el-button>
					</el-col>
				</el-row>
				<el-row style="text-align: center">
					<el-col :xs="24" :md="{span:8, offset:8}" class="buttons-area-files creative-brief">
						<el-button
							icon="el-icon-document"
							class="creative-brief-button"
							@click="openCreativeBriefDialog"
						>
							<span v-if="project && project.type != 'design'">View Creative Brief</span>
							<span v-else>View Design Request</span>
						</el-button>
					</el-col>
				</el-row>
			</el-row>
		</form>

		<!--Creative Brief Dialog-->
		<el-dialog :visible.sync="showDialogCreativeBrief" class="project-details-dialog">
			<span slot="title" class="display-title">
				<div class="title-icon">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="1.5"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="feather feather-image"
					>
						<rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
						<circle cx="8.5" cy="8.5" r="1.5" />
						<polyline points="21 15 16 10 5 21" />
					</svg>
				</div>
				<span class="title">{{project.name}}</span>
			</span>
			<div class="text-center" v-if="loading">
				<div class="lds-ellipsis">
					<div></div>
					<div></div>
					<div></div>
					<div></div>
				</div>
			</div>
			<project-details v-if="project && project.type == 'design' && !loading" :project="project" />
			<quill-editor
				v-else-if="!loading"
				v-model="creativeBrief"
				ref="myQuillEditor"
				:options="editorOption"
			></quill-editor>
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">
					<div class="project-info" v-if="project.owner && !loading">
						<el-avatar class="avatar" :src="project.owner.photo_url"></el-avatar>
						<div>
							<div class="owner-name">{{project.owner.name}}</div>
							<div class="details">
								<span>#{{project.job_number}}</span>
								<span>
									<i class="el-icon-date"></i>
									{{projectDate(project.created_at)}}
								</span>
							</div>
						</div>
					</div>
					<div>
						<el-button
							class="canacel-creative"
							:disabled="loading"
							type="primary"
							@click="saveCreativeBrief"
							v-if="project.type != 'design'"
						>Save</el-button>
						<el-button
							class="canacel-creative"
							:disabled="loading"
							@click="showDialogCreativeBrief = false"
						>Close</el-button>
					</div>
				</div>
			</span>
		</el-dialog>
	</div>
</template>

<script>
	import { validationMixin } from "vuelidate";
	import Ls from "../../services/ls";
	import { mapGetters } from "vuex";
	import PdfConverting from "./partials/PdfConverting";
	const { email, required } = require("vuelidate/lib/validators");

	export default {
		props: [
			"project_id",
			"revision_id",
			"user_id",
			"creative_brief",
			"project_type"
		],
		components: {
			"pdf-converting": PdfConverting,
			"project-details": require("./partials/ProjectDetails")
		},
		data() {
			return {
				formData: {
					file_type: "",
					owner_type: "",
					project_id: "",
					user_id: ""
				},

				fileList: [],
				headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
				saveLoading: false,
				sendLoading: false,
				user_type: "freelancer",
				savedFiles: [],
				pdfLoader: false,
				editorOption: {},
				creativeBrief: "",
				showDialogCreativeBrief: false,
				project: {},
				loading: false
			};
		},
    methods: {
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
			projectDate(date) {
				if (!date) return "";
				var utc = moment.utc(date).toDate();
				return moment(utc)
					.local()
					.format("MMMM DD, YYYY");
			},
			handlePreview(file) {
				console.log(file, "as");
			},
			handleRemove(file, fieldList) {
				console.log(file, fileList);
			},
			beforeRemove(file) {
				var file_id;
				if (file.response) {
					file_id = file.response.data.id;
				} else {
					file_id = file.id;
				}
				console.log(file);
				let self = this;
				return this.$confirm(
					"This will permanently delete the file. Continue?",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning"
					}
				).then(() => {
					axios
						.delete(`/api/files/delete/${file_id}`, {
							params: {
								type: "proof"
							}
						})
						.then(response => {
							if (response.data.status == 1) {
								// item.files.forEach(function(element, index) {
								// 	if (element.id == file.id) {
								// 		item.files.splice(index, 1);
								// 		return;
								// 	}
								// });
								this.$message({
									type: "success",
									message: "Delete completed"
								});
							} else {
								this.handle_error(response.data.errors);
							}
						})
						.catch(error => {
							console.log(error);
						});
				});
			},
			handleError(error) {
				console.log(error);
			},
			handleSuccess(response) {
				var self = this;
				if (response.status == 1) {
					if (response.data.length) {
						response.data.forEach(function(element, index) {
							self.savedFiles.push(element);
							self.fileList.push({
								name: "Converted Image-" + (index + 1),
								url: `${self.spacePrefix}/${element.path}`
							});
						});
					} else {
						this.savedFiles.push(response.data);
					}
					toastr["success"](response.message, "Success");
				} else {
					this.pdfLoader = false;
					toastr["error"](response.error, "Error");
				}
			},
			handleUpload(file) {
				if (file.type == "application/pdf") {
					this.pdfLoader = true;
				}
			},
			sendProject() {
				var self = this;
				this.$confirm(
					"Are you ready to send this project to client?",
					"Warning",
					{
						confirmButtonText: "Confirm",
						cancelButtonText: "Cancel",
						type: "success",
						title: ""
					}
				)
					.then(() => {
						if (
							self.savedFiles.length > 0 ||
							self.project_type == "website" ||
							self.fileList.length != 0
						) {
							self.sendLoading = true;
							axios
								.get(
									"/api/projects/send_project/" +
										self.project_id +
										"/" +
										self.user_type
								)
								.then(response => {
									self.$router.push({ name: "projects" });
									self.sendLoading = false;
									toastr["success"](
										response.data.message,
										"Success"
									);
									self.clearForm();
								})
								.catch(error => {
									self.sendLoading = false;
									console.log(error.message);
								});
						} else {
							toastr["error"](
								"You should upload some proofs before send this project to the client",
								"Error"
							);
						}
					})
					.catch(() => {
						console.log("canceled");
					});
			},
			finishCreateProject() {
				this.$router.push({name: "projects"});
			},
			jqueryCssAdjust() {
				$(".el-upload__input").css("display", "none");
			},
			cancel() {
				toastr["success"]("Project saved as draft", "Success");
				this.$router.push({ name: "projects" });
			},
			openCreativeBriefDialog() {
				this.showDialogCreativeBrief = true;
			},
			saveCreativeBrief() {
				let formData = {
					project_id: this.project_id,
					creative_brief: this.creativeBrief
				};

				axios
					.post("/api/projects/save-creative-brief", formData)
					.then(response => {
						this.fullscreenLoading = false;
						if (response.data.status) {
							this.showDialogCreativeBrief = false;
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success"
							});
						} else {
							this.$notify({
								title: "Error",
								message: response.data.errors,
								type: "error"
							});
						}
					})
					.catch(error => {
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error"
						});
					});
			},
			getProjectCreativeBrief() {
				axios
					.get(`/api/projects/creative-brief/${this.project_id}`)
					.then(response => {
						if (response.data.status) {
							this.creativeBrief = response.data.data;
						} else {
							this.$notify({
								title: "Error",
								message: "Can not get project creative brief",
								type: "error"
							});
						}
					})
					.catch(error => {
						this.$notify({
							title: "Error",
							message: "Can not get project creative brief",
							type: "error"
						});
					});
			},
			exsitsingFiles() {
				this.loading = true;
				axios
					.get(`/api/projects/files/${this.project_id}`)
					.then(response => {
						this.loading = false;
						if (response.data.status) {
							this.fileList = response.data.data.revision.map(
								({ project_files, id }) => ({
									proof_id: id,
									name: project_files.file_type,
									url: `${this.spacePrefix}/${project_files.thumb_path}`,
									id: project_files.id
								})
							);
							this.project = response.data.data.project;
							// this.fileList = response.data.data
						} else {
							this.$notify({
								title: "Error",
								message: "Can not get project exist files",
								type: "error"
							});
						}
					})
					.catch(error => {
						this.loading = false;
						this.$notify({
							title: "Error",
							message: "Can not get project exist files",
							type: "error"
						});
					});
			}
		},
		computed: {
            spacePrefix() {
              return spacePrefix
			},
			...mapGetters(["user"]),
			updateUserID() {
				this.sendLoading;
				if (this.user) return (this.formData.user_id = this.user.id);
			}
        },
        created() {
            // set to headers XSRF-TOKEN
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
        },
		mounted() {
			const AUTH_TOKEN = Ls.get("auth.token");
			this.headers = {
				Authorization: `Bearer ${AUTH_TOKEN}`,
				Accept: "application/json"
			};
			this.formData.file_type = "picture";
			this.formData.owner_type = "proof";
			this.formData.project_id = this.project_id;
			this.formData.user_id = JSON.parse(localStorage.getItem("user")).id;
			if (this.revision_id) {
				this.formData.revision_id = this.revision_id;
			}
			this.$nextTick(function() {
				this.jqueryCssAdjust();
			});

			if (!this.creative_brief) {
				this.getProjectCreativeBrief();
			} else {
				this.creativeBrief = this.creative_brief;
			}
			this.exsitsingFiles();
		}
	};
</script>
<style lang="scss" scoped>
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;
.pdfLoader {
	width: 35%;
	height: 100%;
	position: absolute;
	top: 9px;
	left: -10px;
	z-index: 999;
}

.upload-container {
	max-height: calc(100vh - 170px);
	overflow-x: auto;
}
.upload-container .upload-area {
	margin-top: 20px;
}
.upload-area .el-upload,
.upload-area .el-upload-dragger {
	width: 100%;
}
.buttons-area-files {
	margin-top: 20px;
}

@media (max-width: 367px) {
	.save-draft-btn {
		margin-top: 10px;
		margin-left: unset !important;
	}
}

@media (max-width: 550px) {
	.el-message-box {
		width: 90%;
	}
}
@media (max-width: 720px) {
	.el-dialog__wrapper .el-dialog {
		width: 90%;
	}
}

.project-details-dialog {
	.el-dialog {
		box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
			0 10px 10px -5px rgba(0, 0, 0, 0.04);
		border-radius: 8px;
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
.creative-footer {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 0 12px;
	.project-info {
		text-align: left;
		display: flex;
		.avatar {
			margin-right: 12px;
			box-shadow: $box-shadow;
		}
		.owner-name {
			color: #2d3748;
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

// loading
.lds-ellipsis {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 50px;
}
.lds-ellipsis div {
	position: absolute;
	top: 33px;
	width: 13px;
	height: 13px;
	border-radius: 50%;
	background: $green;
	animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
	left: 8px;
	animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
	left: 8px;
	animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
	left: 32px;
	animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
	left: 56px;
	animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
	0% {
		transform: scale(0);
	}
	100% {
		transform: scale(1);
	}
}
@keyframes lds-ellipsis3 {
	0% {
		transform: scale(1);
	}
	100% {
		transform: scale(0);
	}
}
@keyframes lds-ellipsis2 {
	0% {
		transform: translate(0, 0);
	}
	100% {
		transform: translate(24px, 0);
	}
}
</style>