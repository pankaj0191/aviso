<template>
    <el-row :gutter="32">
        <el-col :xs="24" :sm="24" :md="24" :lg="24" :xl="24" class="upload-area text-center">
			<el-dialog
				title="PDf Converter" class="project-details-dialog"
				:visible.sync="pdfLoader"
				width="50%">
				<pdf-converting
					:dialog="true"
					v-on:confirm="pdfLoader = false"
				></pdf-converting>
			</el-dialog>
            <el-upload :headers="headers" ref="upload" class="custom-upload-style" drag :data="formData"  action="/api/files/upload"
                :on-preview="handlePreview" :on-remove="handleRemove" :before-remove="beforeRemove"
                :on-error="handleError" :on-success="handleSuccess" :before-upload="handleUpload" :auto-upload="true"
                :file-list="fileList" name="photos" multiple list-type="picture">
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">
                    Drop file here or
                    <em>click to upload</em>
                </div>
            </el-upload>
        </el-col>
    </el-row>
</template>

<script>
    import Ls from "./../../../../../../../services/ls";
	import { mapGetters } from "vuex";
	import PdfConverting from "./../../../../../partials/PdfConverting";
    export default {
        props: ['project', 'project_id', 'type'],
        components: {
            "pdf-converting": PdfConverting,
            "project-details": require("./../../../../../partials/ProjectDetails")
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
				projectFiles: {},
				loading: false,
            }
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
			handleClose(done) {
				this.$confirm('Are you sure to close this dialog?')
				.then(_ => {
					done();
				})
				.catch(_ => {});
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
            finishCreateProject() {
				this.$router.push({name: "projects"});
			},
			jqueryCssAdjust() {
				$(".el-upload__input").css("display", "none");
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
							this.projectFiles = response.data.data.project;
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
			},
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
			// this.headers = {
			// 	Authorization: `Bearer ${AUTH_TOKEN}`,
			// 	Accept: "application/json"
			// };
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

			this.exsitsingFiles();
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

<style lang="scss" scoped>
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;
.custom-upload-style {
	.el-upload  {
		width: 100%;
		.el-upload-dragger {
			width: 100%;
		}
	}
}

.el-upload-list {
	max-height: 300px;
	overflow: scroll;
}

.project-details-dialog {
	.el-dialog {
		height: 350px;
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

</style>