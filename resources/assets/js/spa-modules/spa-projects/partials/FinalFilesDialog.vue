<template>
	<div v-loading.fullscreen.lock="fullscreenLoading">
		<!--Final files options dialog-->
		<el-dialog
			:title="project.rate || isBothRole || !getTrueRevuewNotify ? 'Final Files' : 'Please, rate work before downloading files'"
			:close-on-click-modal="false"
			:close-on-press-escape="false"
			:show-close="false"
			:visible.sync="showFinalFilesDialog"
			:width="project.rate && '80%' || '70%'"
			center
			top="10vh"
		>
			<el-row v-if="!getTrueRevuewNotify || project.rate || isBothRole" type="flex" justify="center" align="middle">
				<el-col>
					<el-row style="text-align: center">
						<h4
							v-if="isBothRole"
						>Please choose what option below best fits from links or/and files uploads.</h4>
						<h4
							v-else-if="!isBothRole && project.status == 'completed'"
						>Here are your final files click the links below to access and download.</h4>
						<h4 v-else>Your files will be uploaded soon and will be available in the completed section.</h4>
					</el-row>

					<!--Options-->
					<el-row style="margin-top: 20px">
						<!--Links-->
						<el-col :xs="24" :sm="24" :md="12" id="links">
							<h2 v-if="isBothRole">UPLOAD LINKS</h2>
							<h2 v-else>FINAL LINKS</h2>
							<h5 v-if="isBothRole">
								Copy and paste your downloadable link,
								<br />allowing your customer to download.
							</h5>
							<i v-if="isBothRole">EX: Dropbox, Google Drive, or Box.</i>
							<div class="links-bckg">
								<div v-if="!isBothRole && !links.length">
									<div
										style="padding: 50px; display:flex; align-items:center; justify-content: center; color: #a4aaae;"
									>
										<div style="text-align: center">
											<i
												class="el-icon-share"
												style="font-size: 80px !important; font-style: normal !important"
											></i>
											<h4>No Links</h4>
										</div>
									</div>
								</div>
								<div class="links">
									<el-tag
										:key="link"
										v-for="link in links"
										:disable-transitions="false"
										:closable="isBothRole ? true : false"
										@close="handleLinkDelete(link)"
										style="margin: 5px 0; display:block"
									>
										<a :href="link" target="_blank">{{link}}</a>
									</el-tag>
									<br />
									<div v-if="isBothRole">
										<el-input
											class="input-new-tag"
											v-if="inputVisible"
											v-model="inputValue"
											ref="saveLinkInput"
											size="mini"
											@keyup.enter.native="handleLinkInput"
											@blur="handleLinkInput"
										></el-input>
										<el-button v-else class="button-new-tag" size="small" @click="showLinkInput">
											+
											New
											Link
										</el-button>
									</div>
								</div>
								<el-button
									v-if="isBothRole"
									type="primary"
									@click="sendFinalLinks()"
								>Finish & Send</el-button>
							</div>
						</el-col>
						<el-col :xs="24" :sm="24" :md="12" id="files">
							<h2 v-if="isBothRole">UPLOAD FILES</h2>
							<h2 v-else>FINAL FILES</h2>
							<h5
								v-if="isBothRole"
							>Upload final files directly into your Prooflo projects.</h5>
							<div v-if="isBothRole" class="upload-bckg">
								<div class="final-upload">
									<el-upload
										ref="upload"
                                        :headers="headers"
										drag
										:data="imageFormData"
										action="/api/files/upload"
										:on-preview="handleFilePreview"
										:on-error="handleUploadError"
										:on-remove="deleteFile"
										:before-remove="beforeDeleteFile"
										:on-success="handleUploadSuccess"
										:before-upload="handleUpload"
										:auto-upload="true"
										:file-list="fileList"
										name="photos"
										list-type="picture"
										:multiple="true"
									>
                                    <i class="el-icon-upload"></i>
                                    <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
									</el-upload>
								</div>
								<el-button type="primary" @click="sendFinalFiles()" style="margin-bottom: 8px">Finish & Send</el-button>
								<el-button
                                    v-if="exitingFiles.length > 1"
									class="tw-text-primary"
									@click="exitingFilesDialog = true"
									style="margin: 0px"
								>View All Files ({{ exitingFiles.length }})</el-button>
							</div>
							<div v-else class="exiting-files">
								<template v-if="exitingFiles.length">
									<ul class="el-upload-list el-upload-list--picture-card">
										<li
											v-for="(file, index) in exitingFiles"
											:key="file.id"
											class="el-upload-list__item is-success"
										>
											<template v-if="file.path.substring(file.path.indexOf('.') + 1) == 'pdf'">
												<img
													:src="`${spacePrefix}/assets/img/pdf-icon.png`"
													class="el-upload-list__item-thumbnail"
												/>
											</template>
											<template v-else>
												<img :src="`${spacePrefix}/${file.path}`" class="el-upload-list__item-thumbnail" />
											</template>
											<i class="el-icon-close"></i>
											<span class="el-upload-list__item-actions">
												<template v-if="file.path.substring(file.path.indexOf('.') + 1) != 'pdf'">
													<span class="el-upload-list__item-preview" @click="handleFilePreview(file)">
														<i class="el-icon-zoom-in"></i>
													</span>
												</template>
												<span
													class="el-upload-list__item-preview"
													@click="handleFileDownload(file.path, index)"
												>
													<i class="el-icon-download"></i>
												</span>
											</span>
										</li>
									</ul>
								</template>
								<template v-else>
									<div
										style="padding: 50px; display:flex; align-items:center; justify-content: center; color: #a4aaae;"
									>
										<div style="text-align: center">
											<i class="el-icon-picture-outline" style="font-size: 80px !important"></i>
											<h4>No Files</h4>
										</div>
									</div>
								</template>
							</div>
						</el-col>
					</el-row>
				</el-col>
			</el-row>
			<el-row v-else class="display-rate">
                <el-alert
                    type="warning"
                    v-if="project.project_status == 'approved' && project.type !== 'website'"
                    title="Your projects final files will be uploaded soon."
                    style="margin-bottom: 16px;"
                    show-icon>
                </el-alert>
				<el-col :xs="24" :sm="24" :md="4">
					<label class="demonstration">Rate</label>
				</el-col>
				<el-col :xs="24" :sm="24" :md="20">
					<el-rate
                        :disabled="project.project_status == 'approved'"
						v-model="rate"
						:texts="['Poor', 'Below Average', 'Average', 'Above Averge', 'Excellent']"
						show-text
						:colors="colors"
					></el-rate>
				</el-col>
				<el-col :xs="24" :sm="24" :md="4">
					<label class="demonstration">Review</label>
				</el-col>
				<el-col :xs="24" :sm="24" :md="20">
					<el-input :disabled="project.project_status == 'approved'" type="textarea" :rows="2" placeholder="Please input" v-model="review"></el-input>
				</el-col>
			</el-row>
			<el-row style="text-align: center; margin-top: 30px">
				<el-button
					v-if="!isBothRole && !project.rate"
					:disabled="!this.rate"
					@click="updateRate"
					type="primary"
				>Save</el-button>
				<el-button
					@click="!isBothRole ? closeFinalFilesDialog() : cancelFinalFiles()"
				>{{!isBothRole ? 'Close' : (savedFilesIds.length > 0 ? `Delete Upload Files (${savedFilesIds.length})`   : 'Cancel')}}</el-button>
			</el-row>
		</el-dialog>

		<!--Show uploaded file dialog-->
		<el-dialog :visible.sync="filePreviewDialog" top="10vh">
			<div style="padding: 20px">
				<img width="100%" :src="filePreviewUrl" alt />
			</div>
		</el-dialog>

		<!--Show exiting files dialog-->
		<el-dialog :visible.sync="exitingFilesDialog" top="10vh">
			<div class="exiting-files">
				<template v-if="exitingFiles.length">
					<ul class="el-upload-list el-upload-list--picture-card">
						<li
							v-for="(file, index) in exitingFiles"
							:key="file.id"
							class="el-upload-list__item is-success"
							style="text-align: center"
						>
							<template v-if="file.path.substring(file.path.indexOf('.') + 1) == 'pdf'">
								<img :src="`${spacePrefix}/assets/img/pdf-icon.png`" class="el-upload-list__item-thumbnail" />
							</template>
							<template v-else>
								<img :src="`${spacePrefix}/${file.path}`" class="el-upload-list__item-thumbnail" />
							</template>
							<i class="el-icon-close"></i>
							<span class="el-upload-list__item-actions">
								<template v-if="file.path.substring(file.path.indexOf('.') + 1) != 'pdf'">
									<span class="el-upload-list__item-preview" @click="handleFilePreview(file)">
										<i class="el-icon-zoom-in"></i>
									</span>
								</template>
								<span
									v-if="!isBothRole"
									class="el-upload-list__item-preview"
									@click="handleFileDownload(file.path, index)"
								>
									<i class="el-icon-download"></i>
								</span>
								<span
									v-if="isBothRole"
									class="el-upload-list__item-delete"
									@click="deleteFile(file)"
								>
									<i class="el-icon-delete"></i>
								</span>
							</span>
						</li>
					</ul>
				</template>
				<template v-else>
					<div
						style="padding: 50px; display:flex; align-items:center; justify-content: center; color: #a4aaae;"
					>
						<div style="text-align: center">
							<i class="el-icon-picture-outline" style="font-size: 80px !important"></i>
							<h4>No Files</h4>
						</div>
					</div>
				</template>
			</div>
		</el-dialog>
	</div>
</template>

<script>
import {mapGetters} from 'vuex'
	export default {
		name: "FinalFilesDialog",
		props: ["showFinalFilesDialog", "currentRole", "isBothRole", "project", "revisionId"],
		data() {
			return {
				fullscreenLoading: false,
				rate: null,
				colors: ["#99A9BF", "#F7BA2A", "#FF9900"],
				review: "",
				links: [],
				savedFiles: [],
                savedFilesIds: [],
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
				exitingFiles: [],
				linksFormData: {
					project_id: this.project.id,
					revision_id: this.revisionId,
					links: null
				},
				imageFormData: {
					project_id: this.project.id,
					file_type: "picture",
					owner_type: "final"
				},
				fileList: [],
				inputVisible: false,
				inputValue: "",
				filePreviewUrl: "",
				filePreviewDialog: false,
				exitingFilesDialog: false
			};
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
            },
            getTrueRevuewNotify() {
                return (this.project.freelancers.filter(item => item.email_notifications.review_project == 1).length > 0) ||
                 		(this.project.agencies.filter(item => item.email_notifications.review_project == 1).length > 0)
            }
		},
		methods: {
			updateRate() {
				var self = this;
				const payload = {
					rate: this.rate,
					review: this.review
				};
				if (this.rate) {
					this.fullscreenLoading = true;
					axios
						.put(
							`/api/projects/update-rate/${this.project.id}`,
							payload
						)
						.then(response => {
							this.fullscreenLoading = false;
							this.project.rate = this.rate;
                            this.project.review = this.review;
                            if (this.project.project_type === 'website') {
                                this.$emit("approveProject");
                                this.$emit("closeFinalFilesDialog");
                                this.$emit('approvedSuccessfully')
                            }
						})
						.catch(error => {
							this.fullscreenLoading = false;
							if (error.exception) {
								toastr["error"](
									"Something went wrong, please try again later",
									"Error"
								);
							} else {
								toastr["error"](error.message, "Error");
							}
						});
				} else {
					toastr["error"]("Rate is requird", "Warning");
				}
			},
			getFinalFiles() {
				var self = this;
				this.fullscreenLoading = true;
				axios
					.get(`/api/projects/getFinalFiles/${this.project.id}`)
					.then(response => {
						this.fullscreenLoading = false;
						if (response.data.status) {
							self.links = response.data.data.links;
                            self.exitingFiles = response.data.data.files;
                            this.fileList = response.data.data.files.map(
								({ path, id }) => ({
									id: id,
									name: 'Click to zoom',
									url: `${this.spacePrefix}/${path}`,
								})
							);
						} else {
							toastr["error"](response.data.errors, "Error");
						}
					})
					.catch(error => {
						this.fullscreenLoading = false;
						if (error.exception) {
							toastr["error"](
								"Something went wrong, please try again later",
								"Error"
							);
						} else {
							toastr["error"](error.message, "Error");
						}
					});
			},
			showLinkInput() {
				this.inputVisible = true;
				this.$nextTick(_ => {
					this.$refs.saveLinkInput.$refs.input.focus();
				});
			},
			sendFinalLinks() {
				var self = this;
				this.fullscreenLoading = true;
				if (this.links.length > 0) {
					this.linksFormData.links = this.links;
					axios
						.post("/api/projects/sendFinalFiles", this.linksFormData)
						.then(response => {
							self.fullscreenLoading = false;
							if (response.data.status) {
								// Identify project owner on Gist
								convertfox.identify(response.data.data.owner.id, {
									email: response.data.data.owner.email,
									name: response.data.data.owner.name
								});

								// Trigger 'Congratulations Email' event
								convertfox.track("Congratulations Email", {
									userId: response.data.data.owner.id,
									projectId: this.project_id
								});
								self.$emit("filesSended", response.data.message);
							} else {
								toastr["error"](response.data.errors, "Error");
							}
						})
						.catch(error => {
							this.fullscreenLoading = false;
							if (error.exception) {
								toastr["error"](
									"Something went wrong, please try again later",
									"Error"
								);
							} else {
								toastr["error"](error.message, "Error");
							}
						});
				} else {
					this.fullscreenLoading = false;
					toastr["error"](
						"You should write some links, before sending",
						"Error"
					);
				}
			},
			sendFinalFiles() {
				var self = this;
				this.fullscreenLoading = true;
				if (this.savedFiles.length > 0) {
					this.imageFormData.files = this.savedFiles;
					axios
						.post("/api/projects/sendFinalFiles", this.imageFormData)
						.then(response => {
							self.fullscreenLoading = false;
							if (response.data.status) {
								// Identify project owner on Gist
								convertfox.identify(response.data.data.owner.id, {
									email: response.data.data.owner.email,
									name: response.data.data.owner.name
								});

								// Trigger 'Congratulations Email' event
								convertfox.track("Congratulations Email", {
									userId: response.data.data.owner.id,
									projectId: this.project_id
								});

								self.$emit("filesSended", response.data.message);
							} else {
								toastr["error"](response.data.errors, "Error");
							}
						})
						.catch(error => {
							this.fullscreenLoading = false;
							if (error.exception) {
								toastr["error"](
									"Something went wrong, please try again later",
									"Error"
								);
							} else {
								toastr["error"](error.message, "Error");
							}
						});
				} else {
					this.fullscreenLoading = false;
					toastr["error"](
						"You should upload some files, before sending",
						"Error"
					);
				}
			},
			isURL(link) {
				let regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;

				if (regexp.test(link)) {
					return true;
				} else {
					return false;
				}
			},
			closeFinalFilesDialog() {
				this.$emit("closeFinalFilesDialog");
			},
			cancelFinalFiles() {
				var self = this;
				if (this.savedFilesIds.length) {
					this.fullscreenLoading = true;
					axios
						.post("/api/files/deleteFinalFiles", {
							projectId: this.project.id,
							ids: this.savedFilesIds
						})
						.then(response => {
							if (response.data.status) {
								self.fullscreenLoading = false;
								this.$emit("closeFinalFilesDialog");
							} else {
								self.fullscreenLoading = false;
								toastr["error"](response.data.errors, "Error");
							}
						})
						.catch(error => {
							this.fullscreenLoading = false;
							if (error.exception) {
								toastr["error"](
									"Something went wrong, please try again later",
									"Error"
								);
							} else {
								toastr["error"](error.message, "Error");
							}
						});
				} else {
					this.$emit("closeFinalFilesDialog");
				}
            },
            beforeDeleteFile(file, fileList) {
                return this.$confirm(`Are you sure you want to delete ${file.name}?`);
            },
			deleteFile(file) {
				var self = this;
                var id = file.response ? file.response.data.id : file.id;
                this.fullscreenLoading = true;
                axios
                    .delete(
                        `/api/files/deleteFinalFile/${this.project.id}/${id}`
                    )
                    .then(response => {
                        if (response.data.status) {
                            self.fullscreenLoading = false;
                            toastr["success"](response.data.message, "Success");
                            self.getFinalFiles();
                        } else {
                            self.fullscreenLoading = false;
                            toastr["error"](response.data.errors, "Error");
                        }
                    })
                    .catch(error => {
                        this.fullscreenLoading = false;
                        if (error.exception) {
                            toastr["error"](
                                "Something went wrong, please try again later",
                                "Error"
                            );
                        } else {
                            toastr["error"](error.message, "Error");
                        }
                    });
            },
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
			handleUpload(file) {},
			handleFilePreview(file) {
				this.filePreviewUrl = file.url
					? file.url
					: `${this.spacePrefix}/${file.path}`;
				this.filePreviewDialog = true;
			},
			handleFileDownload(file, index) {
				// const headers = {
				// 		'Content-Type': 'application/json',
				// 		'Access-Control-Allow-Origin': '*',
				// 		'Access-Control-Allow-Methods': 'GET,POST,PATCH,OPTIONS'
				// }
				fetch(`https://cors-anywhere.herokuapp.com/${this.spacePrefix}/${file}`)
					.then(response => response.blob())
					.then(blob => {
						let link = document.createElement("a");
						const blobURL = URL.createObjectURL(blob);
						link.href = blobURL;
						link.download = `${this.project.name}_RR${this.project.versions[0].value}_${index + 1}`;
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					})
					.catch(err => {
						console.log(err);
					});
			},
			handleUploadSuccess(response) {
				var self = this;
				if (response.status) {
					self.savedFilesIds.push(response.data.id);
					self.savedFiles.push(response.data);

					if (response.data.type && response.data.type == "pdf") {
						self.fileList.push({
							url: "/img/pdf-icon.png",
							response: { data: response.data }
						});
					}
					toastr["success"](response.message, "Success");
				} else {
					toastr["error"](response.error, "Error");
				}
			},
			handleUploadError() {},
			handleLinkDelete(key) {
				this.links.splice(this.links.indexOf(key), 1);
			},
			handleLinkInput() {
				if (this.inputValue) {
					if (this.links.indexOf(this.inputValue) == -1) {
						if (this.isURL(this.inputValue)) {
							this.links.push(this.inputValue);
						} else {
							toastr["error"]("Invalid URL", "Error");
						}
					} else {
						toastr["error"]("This link is already exist", "Error");
					}
				}
				this.inputVisible = false;
				this.inputValue = "";
			}
		},
		created() {
            this.getFinalFiles();
            // set to headers XSRF-TOKEN
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
		},
		mounted() {}
	};
</script>

<style lang="scss" scoped>
@media (min-width: 550px) {
	#links,
	#files {
		text-align: center;
	}

	#links i,
	#files i {
		font-size: 14px;
		font-style: italic;
	}

	#links {
		padding-right: 25px;
		border-right: 1px solid rgba(0, 0, 0, 0.25);
	}

	#files {
		padding-left: 25px;
		border-left: 0px solid rgba(0, 0, 0, 0.25);
	}
}

.upload-bckg {
	height: 340px;
	width: 90%;
	margin: auto;
	margin-top: 31px;
	background: #ffffff;
	padding: 20px 30px;
}

.links-bckg {
	height: 340px;
	padding: 20px 30px;
}

.final-upload {
	height: 240px;
	overflow-y: auto;
	background: #fff;
	padding: 10px;
	border: 0px dashed #80b4a0 !important;
	border-radius: 10px;
	margin-bottom: 20px;
    
}

.links {
	height: 240px;
	overflow-y: auto;
	padding: 10px;
	margin-bottom: 20px;
}

.final-upload i {
	font-size: 80px !important;
	font-style: normal !important;
}

.exiting-files {
	padding: 10px;
	display: flex;
	justify-content: center;
	flex-wrap: wrap;
}

.exiting-files img {
	width: 100px;
	height: 100px;
	object-fit: cover;
}

.exiting-files i {
	font-size: 20px !important;
	font-style: normal !important;
}

.el-upload-list__item {
	height: 100px !important;
	width: 100px !important;
}

.el-dialog__header {
	padding-top: 40px !important;
}
.display-rate {
	text-align: left;
	.demonstration {
		margin-right: 16px;
	}
}
</style>

<style lang="scss">
.el-rate {
	height: 40px;
}
.el-rate__item {
	.el-rate__icon {
		font-size: 32px;
	}
}

.final-upload {
    .el-upload--picture {
        width: 100%;
    }
}
</style>