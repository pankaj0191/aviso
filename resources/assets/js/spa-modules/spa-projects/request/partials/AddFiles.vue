<template>
	<div>
		<form>
			<el-row>
				<pdf-converting v-if="pdfLoader" v-on:confirm="pdfLoader = false"></pdf-converting>
				<el-row class="upload-container">
					<el-col :xs="24" :md="24" class="upload-area">
						<el-upload
							ref="upload"
							drag
							:data="formData"
                            :headers="headers"
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
						<el-button type="primary" @click="sendProject()" :loading="sendLoading">Finish</el-button>
					</el-col>
				</el-row>
			</el-row>
		</form>
	</div>
</template>

<script>
	import { validationMixin } from "vuelidate";
	import Ls from "../../../../services/ls";
	import { mapGetters } from "vuex";
	import PdfConverting from "../../partials/PdfConverting";
	const { email, required } = require("vuelidate/lib/validators");

	export default {
		props: [
			"project_id",
			"revision_id",
			"user_id",
			"project_type",
			"exists_files"
		],
		components: {
			"pdf-converting": PdfConverting
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
				editorOption: {}
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
								type: "assets"
							}
						})
						.then(response => {
							if (response.data.status == 1) {
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
								url: spacePrefix +"/" + element.path
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
				if (
					self.savedFiles.length > 0 ||
					self.project_type == "website" ||
					self.fileList.length != 0
				) {
					this.$emit("send-files", self.savedFiles);
				} else {
					toastr["error"](
						"You should upload some proofs before send this project to the client",
						"Error"
					);
				}
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
			exsitsingFiles() {
				axios
					.get(`/api/request/files/${this.project_id}`)
					.then(response => {
						console.log(response.data.status == 1);
						if (response.data.status == 1) {
							if (response.data.data) {
								this.fileList = response.data.data.map(
									({ thumb_path, id, type }) => ({
										name: type,
										url: spacePrefix + "/" + thumb_path,
										id: id
									})
								);
							}
						} else {
							this.$notify({
								title: "Error",
								message: "Can not get project exist files",
								type: "error"
							});
						}
					})
					.catch(error => {
						this.$notify({
							title: "Error",
							message: "Can not get project exist files",
							type: "error"
						});
					});
			}
		},
		computed: {
			...mapGetters(["user"]),
			updateUserID() {
				this.sendLoading;
				if (this.user) return (this.formData.user_id = this.user.id);
			}
		},
		created() {
			this.fileList = this.exists_files.map(({ thumb_path, id, type }) => ({
				name: type,
				url: "/storage/" + thumb_path,
				id: id
			}));
            // set to headers XSRF-TOKEN
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
		},
		mounted() {
			const AUTH_TOKEN = Ls.get("auth.token");
			this.formData.file_type = "picture";
			this.formData.owner_type = "assets";
			this.formData.project_id = this.project_id;
			this.formData.user_id = JSON.parse(localStorage.getItem("user")).id;
			if (this.revision_id) {
				this.formData.revision_id = this.revision_id;
			}
			this.$nextTick(function() {
				this.jqueryCssAdjust();
			});

			this.exsitsingFiles();
		}
	};
</script>

<style>
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
</style>