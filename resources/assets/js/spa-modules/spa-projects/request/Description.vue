<template>
	<div>
		<form action>
			<div class="text-center">
				<i class="el-icon-tickets description-icon"></i>
			</div>
			<h4 class="request_step_question text-center">What are you looking for?</h4>
			<div>
				<ul class="description-list-types">
					<li v-if="project.sub_categories[0]">
						<strong>Request Type:</strong>
						{{project.sub_categories[0].category.name}}
					</li>
					<li v-for="cat in project.sub_categories" :key="cat.id">
						<strong>{{cat.name}}:</strong>
						({{cat.width}} x {{cat.height}})
					</li>
					<li v-if="project.width > 0 && project.height > 0">
						<strong>Custom Size:</strong>
						{{project.width}} * {{project.height}}
					</li>
				</ul>
				<p
					class="instruction-p"
				>Use sentences or paragraphs below to share your request. When you press enter, we'll create a new line for you. After you're done, we'll take each line and create a request checklist for your designer.</p>
				<div id="next_detail_task" v-for="(ins, key) in instructions" :key="key">
					<el-alert
						type="error"
						style="margin-bottom: 8px;"
						v-if="ins.error"
					>{{"To add a new line you have to fill 5 letters at least"}}</el-alert>
					<div class="detail_task_form">
						<div
							class="detail_task_field el-textarea"
							style="margin: 0px -9.34375px 0px 0px; height: 60px; width: 100%;"
						>
							<textarea
								autocomplete="off"
								placeholder="Enter request instructions"
								v-model="ins.text"
								@keydown.enter.exact.prevent
								@blur="removeEmptyString(ins, $event)"
								@keyup.enter.exact="instructionAdd(ins)"
								@keydown.enter.shift.exact="newline(ins.text)"
								@keydown.delete.exact="instructionDelete(ins, $event)"
								class="el-textarea__inner"
								spellcheck="false"
								style="min-height: 32px;"
								:ref="`instruction${key}`"
							></textarea>
						</div>
					</div>
				</div>
				<div class="add_new_detail_line" @click="newInstruction()">
					<div class="add_new_detail_task_lines form-control">Press Enter for another line</div>
				</div>
				<el-row class="include-section-assets">
					<el-col :md="24" class="include-text">
						<h4>Does your design need to include text?</h4>
						<div class="switch-include-text">
							<el-switch v-model="includeText"></el-switch>
						</div>
					</el-col>
				</el-row>
				<el-row
					v-show="projectComponent.creative_brief && projectComponent.creative_brief.length > 0 || includeText"
				>
					<el-col :md="24">
						<p class="text-muted">
							Include text copy
							<span class="font-italic">exactly</span>
							as you'd like it to appear in your design
						</p>
						<el-input
							type="textarea"
							:rows="6"
							placeholder="Please input"
							v-model="projectComponent.creative_brief"
						></el-input>
					</el-col>
				</el-row>
				<el-row class="include-section-assets">
					<el-col :md="24" class="include-text">
						<h4>Do you have any assets to upload?</h4>
						<div class="switch-include-text">
							<el-switch v-model="uploadAssets"></el-switch>
						</div>
					</el-col>
					<el-col :md="24">
						<p class="text-muted">Content, photos, fonts, logos, etc.</p>
					</el-col>
				</el-row>
				<el-row class="add-files" v-if="uploadAssets">
					<el-col :md="24">
						<el-button plain size="small" icon="el-icon-plus" @click="addFilesDailog = true">Add Files</el-button>
					</el-col>
					<el-col>
						<div class="unsplash-selected" v-show="fileList.length > 0">
							<stack :column-min-width="96" :gutter-width="4" :gutter-height="4" monitor-images-loaded>
								<stack-item v-for="(image, i) in fileList" :key="i" style="transition: transform 300ms">
									<div class="unsplash-images">
										<img :src="`${spacePrefix}/` + (image.url ? image.url : image.thumb)" :alt="image.id" />
										<div class="unsplash-overlay">
											<div class="overlay hover-overlay"></div>
											<div class="tools hover-overlay">
												<div class="add-tool">
													<div class="add-collection" @click="removeSelected(image)">
														<svg
															class="add-svg"
															version="1.1"
															viewBox="0 0 32 32"
															width="32"
															height="32"
															aria-hidden="false"
														>
															<path
																d="M25.33 8.55l-1.88-1.88-7.45 7.45-7.45-7.45-1.88 1.88 7.45 7.45-7.45 7.45 1.88 1.88 7.45-7.45 7.45 7.45 1.88-1.88-7.45-7.45z"
															/>
														</svg>
													</div>
												</div>
											</div>
										</div>
									</div>
								</stack-item>
							</stack>
						</div>
					</el-col>
				</el-row>
			</div>
			<div class="text-center continue-button">
				<el-button
					v-if="active == 4"
					type="primary"
					:loading="saveLoading"
					size="small"
				>Send to My Designer</el-button>
				<el-button v-else @click="update" type="primary" :loading="saveLoading" size="small">Continue</el-button>
				<div style="margin-top: 8px">
					<el-button type="text" @click="saveDraft" v-if="project.type == 'draft'">Save Draft</el-button>
				</div>
			</div>
		</form>
		<el-dialog title="Add Files" :visible.sync="addFilesDailog" width="50%">
			<add-files
				v-if="addFilesDailog"
				:project_id="project_id"
				:revision_id="revision_id"
				:exists_files="fileList"
				project_type="design"
				@send-files="getFiles"
			/>
		</el-dialog>
	</div>
</template>

<script>
	import AddFiles from "./partials/AddFiles";
	import { Stack, StackItem } from "vue-stack-grid";
	export default {
		props: ["project_id", "revision_id", "project", "active"],
		components: {
			AddFiles,
			Stack,
			StackItem
		},
		data() {
			return {
				instructions: [
					{
						text: "",
						error: ""
					}
				],
				includeText: false,
				uploadAssets: false,
				addFilesDailog: false,
				saveLoading: false,
				includeTextStr: "",
				projectComponent: {},
				fileList: []
			};
		},
		methods: {
			removeEmptyString(ins, e) {
				// if (ins.text.length == 0) {
				// 	if (ins.id) {
				// 		const data = {
				// 			project_id: this.project_id,
				// 			instructions: this.instructions
				// 		};
				// 		axios
				// 			.post("/api/request/instruction", data)
				// 			.then(response => {
				// 				if (response.data.status == 1) {
				// 					toastr["success"](
				// 						response.data.message,
				// 						"Success"
				// 					);
				// 					this.instructions.splice(
				// 						this.instructions.indexOf(ins),
				// 						1
				// 					);
				// 				} else {
				// 					toastr["error"](
				// 						response.data.errors[0],
				// 						"Error"
				// 					);
				// 				}
				// 			})
				// 			.catch(error => {
				// 				console.log(error);
				// 			});
				// 	} else {
				// 		this.instructions.splice(this.instructions.indexOf(ins), 1);
				// 	}
				// }
			},
			projectCall() {
				let { instructions } = this.project;
				this.instructions = instructions;
				this.instructions.push({
					text: "",
					error: ""
				});
			},
			instructionDelete(value, e) {
				self = this;
				if (this.instructions.length > 1) {
					if (value.text.length == 0) {
						let index = self.instructions.indexOf(value);
						self.instructions.splice(index, 1);
						e.preventDefault();
						self.$nextTick(() => {
							self.$refs[`instruction${index - 1}`][0].focus();
						});
					}
				}
			},
			instructionAdd(value) {
				self = this;

				if (value.text.length > 4) {
					const data = {
						project_id: this.project_id,
						instructions: this.instructions
					};
					value.error = "";
					axios
						.post("/api/request/instruction", data)
						.then(response => {
							if (response.data.status == 1) {
								toastr["success"](response.data.message, "Success");
								let ins = this.instructions.push({
									text: "",
									error: ""
								});
								this.$nextTick(() => {
									self.$refs[`instruction${ins - 1}`][0].focus();
								});
							} else {
								toastr["error"](response.data.errors[0], "Error");
							}
						})
						.catch(error => {
							console.log(error);
						});
				} else {
					return (value.error =
						"To add a new line you have to fill 5 letters at least");
				}
			},
			saveDraft() {
				const data = {
					creative_brief: this.projectComponent.creative_brief,
					includeText: this.includeText,
					project_id: this.project_id,
					instructions: this.instructions
				};
				this.$emit("save_draft", data);
			},
			update() {
				this.saveLoading = true;
				const data = {
					creative_brief: this.projectComponent.creative_brief,
					includeText: this.includeText,
					project_id: this.project_id,
					instructions: this.instructions
				};
				axios
					.post("/api/request/description", data)
					.then(response => {
						this.saveLoading = false;
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
							this.$emit("update_active", response.data.data);
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch(error => {
						this.saveLoading = false;
						console.log(error);
					});
			},
			newInstruction() {
				self = this;
				let inst = this.instructions[this.instructions.length - 1];
				if (inst.text && inst.text.length > 4) {
					inst.error = "";
					let ins = this.instructions.push({
						text: "",
						error: ""
					});
					this.$nextTick(() => {
						self.$refs[`instruction${ins - 1}`][0].focus();
					});
				} else {
					return (inst.error =
						"To add a new line you have to fill 5 letters at least");
				}
			},
			newline(value) {
				value = `${value}\n`;
			},
			getFiles(data) {
				this.fileList = [...this.fileList, ...data];
				this.addFilesDailog = false;
			},
			removeSelected(image) {
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
						.delete(`/api/files/delete/${image.id}`, {
							params: {
								type: "assets"
							}
						})
						.then(response => {
							if (response.data.status == 1) {
								this.fileList.splice(
									this.fileList.indexOf(image.id),
									1
								);
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
			}
        },
        computed: {
            spacePrefix() {
                return spacePrefix
            },
        },
		created() {
			this.projectCall();
			this.projectComponent = this.project;
			if (
				this.project.project_assets &&
				this.project.project_assets.length > 0
			) {
				this.uploadAssets = true;
				this.fileList = this.project.project_assets;
			}
			if (this.project.creative_brief != null) {
				this.project.creative_brief.length > 0 && (this.includeText = true);
			}
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

.description-icon {
	font-size: 64px;
	margin-bottom: 24px;
}

.request_step_question {
	font-size: 24px;
	font-weight: 700;
}

ul {
	list-style: inside;
	display: block;
	margin-block-start: 1em;
	margin-block-end: 1em;
	margin-inline-start: 0px;
	margin-inline-end: 0px;
	padding-inline-start: 40px;
}

.description-list-types {
	margin-bottom: 48px;
}

.instruction-p {
	margin-bottom: 24px;
}

#request-form .detail_task_field {
	border: none !important;
	border-bottom: 1px solid #ccc !important;
}
.add_new_detail_task_lines {
	height: 52px;
	color: #a5a5a5;
	border: none !important;
	border-radius: 0;
	border-bottom: 1px dotted #d7d7d7 !important;
}

.add_new_detail_line {
	cursor: pointer;
}
.include-section-assets {
	margin-top: 48px;
}

.include-text {
	display: flex;
	align-items: center;
	.switch-include-text {
		margin-left: 16px;
	}
}
.add-files {
	margin-top: 24px;
}

.validate-text {
	margin-bottom: 8px;
	font-size: 12px;
	color: $red;
}

img {
	width: 100%;
	height: auto;
	border-radius: 6px;
}

.unsplash-selected {
	padding-top: 12px;
}
.unsplash-images {
	position: relative;
}

.hover-overlay,
.not-hover-overlay {
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-images:not(:hover) .hover-overlay {
	visibility: hidden;
	opacity: 0;
}

.unsplash-images:hover .not-hover-overlay {
	visibility: hidden;
	opacity: 0;
}

.overlay {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	background-image: linear-gradient(
		180deg,
		rgba(0, 0, 0, 0.34) 0,
		rgba(0, 0, 0, 0.338) 3.5%,
		rgba(0, 0, 0, 0.324) 7%,
		rgba(0, 0, 0, 0.306) 10.35%,
		rgba(0, 0, 0, 0.285) 13.85%,
		rgba(0, 0, 0, 0.262) 17.35%,
		rgba(0, 0, 0, 0.237) 20.85%,
		rgba(0, 0, 0, 0.213) 24.35%,
		rgba(0, 0, 0, 0.188) 27.85%,
		rgba(0, 0, 0, 0.165) 31.35%,
		rgba(0, 0, 0, 0.144) 34.85%,
		rgba(0, 0, 0, 0.126) 38.35%,
		rgba(0, 0, 0, 0.112) 41.85%,
		rgba(0, 0, 0, 0.103) 45.35%,
		rgba(0, 0, 0, 0.1) 48.85%,
		rgba(0, 0, 0, 0.103) 52.35%,
		rgba(0, 0, 0, 0.112) 55.85%,
		rgba(0, 0, 0, 0.126) 59.35%,
		rgba(0, 0, 0, 0.144) 62.85%,
		rgba(0, 0, 0, 0.165) 66.35%,
		rgba(0, 0, 0, 0.188) 69.85%,
		rgba(0, 0, 0, 0.213) 73.35%,
		rgba(0, 0, 0, 0.237) 76.85%,
		rgba(0, 0, 0, 0.262) 80.35%,
		rgba(0, 0, 0, 0.285) 83.85%,
		rgba(0, 0, 0, 0.306) 87.35%,
		rgba(0, 0, 0, 0.324) 90.85%,
		rgba(0, 0, 0, 0.338) 94.35%,
		rgba(0, 0, 0, 0.347) 97.85%,
		rgba(0, 0, 0, 0.35)
	);
	pointer-events: none;
	border-radius: 6px;
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-overlay {
	animation-name: Qed4z;
	animation-timing-function: ease-in;
	animation-duration: 0.3s;
}

.unsplash-overlay .tools {
	position: absolute;
	top: 0px;
	padding-right: 2px;
	padding-left: 20px;
	width: 100%;
	display: flex;
	pointer-events: none;
	height: 32px;
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-overlay .tools .add-tool {
	display: flex;
	margin-left: auto;
	padding-left: 13px;
}

// .add-collection:hover {
//         color: #111;
// fill: currentColor;
// background-color: #fff;
// box-shadow: 0 1px 3px rgba(0,0,0,.1);
// }

.add-collection {
	cursor: pointer;
	color: #fff;
	fill: currentColor;
	// background-color: hsla(0, 0%, 100%, .9);
	border: 1px solid transparent;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
	border: 1px solid transparent;
	border-radius: 4px;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
	transition: all 0.1s ease-in-out;
	text-align: center;
	-webkit-user-select: none;
	-ms-user-select: none;
	user-select: none;
	font-weight: 500;
	display: inline-flex;
	pointer-events: auto;
	margin-left: 9px;

	.add-svg {
		position: relative;
		top: -1px;
		width: 16px;
	}
}

.btn-add {
	height: 32px;
	padding: 0 11px;
	font-size: 14px;
	line-height: 30px;
	display: inline-block;
	outline: 0;
}
.btn-add:active {
	outline: 0;
}

.decoration-none {
	text-decoration: none;
}

.continue-button {
	padding-top: 24px;
}
</style>
<style lang="scss">
.detail_task_field {
	.el-textarea__inner {
		border: none !important;
		border-bottom: 1px solid #ccc !important;
		border-radius: 0;
	}
	.el-textarea__inner:focus {
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
			0 0 8px rgba(57, 202, 154, 0.6);
		border-color: #39ca86;
	}
}
</style>