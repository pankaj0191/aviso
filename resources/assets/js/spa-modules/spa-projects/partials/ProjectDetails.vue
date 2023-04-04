<template>
	<div>
		<div class="text-center" v-if="loading">
			<div class="lds-ellipsis">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
			</div>
		</div>
		<transition name="el-zoom-in-center">
			<div v-if="!loading">
				<section class="dimensions">
					<div class="title-with-icon">
						<i class="el-icon-full-screen icon"></i>
						<span>Dimensions</span>
					</div>
					<hr />
					<div class="body">
						<div
							class="type-with-icon"
							v-if="project.sub_categories && project.sub_categories.length > 0"
						>
							<i class="el-icon-check icon"></i>
							<span>{{project.sub_categories[0].category.name}}</span>
						</div>
						<ul class="list">
							<li v-for="sub in project.sub_categories" :key="sub.id">
								<span class="dimension">{{parseFloat(sub.width)}} x {{parseFloat(sub.height)}}</span>
								{{sub.name}}
								<span
									v-if="isFreelancer"
									class="template-download"
									@click="openTemplateDialog(sub)"
								>(Download template)</span>
							</li>
							<li class="custom-width" v-if="project.width > 0 && project.height > 0">
								<span class="dimension">{{parseFloat(project.width)}} x {{parseFloat(project.height)}}</span> Custom Size
							</li>
						</ul>
					</div>
				</section>
				<section class="description">
					<div class="title-with-icon">
						<i class="el-icon-document icon"></i>
						<span>Description</span>
					</div>
					<hr />
					<div class="body">
						<ul class="instruction-list">
							<li v-for="instruction in project.instructions" :key="instruction.id">
								<el-checkbox
									:disabled="!isFreelancer"
									@change="updateInstruction"
									v-model="instructionSelected"
									:label="instruction.id"
								>{{instruction.text}}</el-checkbox>
							</li>
						</ul>
						<div class="include-text">
							<div class="title-with-out-icon">Include Text</div>
							<p v-html="project.creative_brief"></p>
						</div>
						<div class="assets">
							<div class="title-with-out-icon">Assets</div>
							<div class="body">
								<div class="assets-images">
									<stack :column-min-width="96" :gutter-width="4" :gutter-height="4" monitor-images-loaded>
										<stack-item v-for="(image, i) in fileList" :key="i" style="transition: transform 300ms">
											<div class="unsplash-images">
												<img :src="`${spacePrefix}/${image.thumb_path}`" :alt="image.type" />
												<div class="unsplash-overlay">
													<div class="overlay not-hover-overlay" v-if="imageLoading.includes(image.id)"></div>
													<div class="overlay hover-overlay"></div>
													<div
														:class="['tools', (imageLoading.includes(image.id) ? 'not-hover-overlay' : 'hover-overlay')]"
													>
														<div class="add-tool">
															<div
																class="add-collection"
																@click="downloadWithAxios(`https://cors-anywhere.herokuapp.com/${spacePrefix}/${image.url}`, image.id)"
															>
																<div class="lds-ring" v-if="imageLoading.includes(image.id)">
																	<div></div>
																	<div></div>
																	<div></div>
																	<div></div>
																</div>
																<svg
																	v-else
																	class="add-svg"
																	version="1.1"
																	viewBox="0 0 32 32"
																	width="32"
																	height="32"
																	aria-hidden="false"
																>
																	<path d="M25.8 15.5l-7.8 7.2v-20.7h-4v20.7l-7.8-7.2-2.7 3 12.5 11.4 12.5-11.4z" />
																</svg>
															</div>
														</div>
													</div>
												</div>
											</div>
										</stack-item>
									</stack>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section class="photos">
					<div class="title-with-icon">
						<i class="el-icon-picture-outline icon"></i>
						<span>Photos</span>
					</div>
					<hr />
					<div class="photo-text">
						<div class="title-with-out-icon">Photo Text</div>
						<p v-html="project.assets_text"></p>
					</div>
					<div class="body">
						<div class="photos-images">
							<stack :column-min-width="96" :gutter-width="4" :gutter-height="4" monitor-images-loaded>
								<stack-item
									v-for="(image, i) in selectedPhotos"
									:key="i"
									style="transition: transform 300ms"
								>
									<div class="unsplash-images">
										<img :src="image.urls.thumb" :alt="image.alt_description" />
										<div class="unsplash-overlay">
											<div class="overlay not-hover-overlay" v-if="imageLoading.includes(image.id)"></div>
											<div class="overlay hover-overlay"></div>
											<div
												:class="['tools', (imageLoading.includes(image.id) ? 'not-hover-overlay' : 'hover-overlay')]"
											>
												<div class="add-tool">
													<div class="add-collection" @click="downloadWithAxios(image.urls.full, image.id)">
														<div class="lds-ring" v-if="imageLoading.includes(image.id)">
															<div></div>
															<div></div>
															<div></div>
															<div></div>
														</div>
														<svg
															v-else
															class="add-svg"
															version="1.1"
															viewBox="0 0 32 32"
															width="32"
															height="32"
															aria-hidden="false"
														>
															<path d="M25.8 15.5l-7.8 7.2v-20.7h-4v20.7l-7.8-7.2-2.7 3 12.5 11.4 12.5-11.4z" />
														</svg>
													</div>
												</div>
											</div>
										</div>
									</div>
								</stack-item>
							</stack>
						</div>
					</div>
				</section>
				<section class="files-type">
					<div class="title-with-icon">
						<i class="el-icon-document-copy icon"></i>
						<span>Files Type</span>
					</div>
					<hr />
					<div class="body">
						<ul class="list-with-icon">
							<li :class="{'not-selected': !extention.jpg}">
								<i :class="['icon', (extention.jpg ? ' el-icon-check' : 'el-icon-close')]"></i>
								<span>JPG</span>
							</li>
							<li :class="{'not-selected': !extention.png}">
								<i :class="['icon', (extention.png ? ' el-icon-check' : 'el-icon-close')]"></i>
								<span>PNG</span>
							</li>
							<li :class="{'not-selected': !extention.pdf}">
								<i :class="['icon', (extention.pdf ? ' el-icon-check' : 'el-icon-close')]"></i>
								<span>PDF</span>
							</li>
						</ul>
						<div class="adobe-title">Adobe</div>
						<ul class="list-with-icon">
							<li :class="{'not-selected': !extention.psd}">
								<i :class="['icon', (extention.psd ? ' el-icon-check' : 'el-icon-close')]"></i>
								<span>PSD</span>
							</li>
							<li :class="{'not-selected': !extention.ai}">
								<i :class="['icon', (extention.ai ? ' el-icon-check' : 'el-icon-close')]"></i>
								<span>AI</span>
							</li>
							<li :class="{'not-selected': !extention.indd}">
								<i :class="['icon', (extention.indd ? ' el-icon-check' : 'el-icon-close')]"></i>
								<span>INDD</span>
							</li>
						</ul>
					</div>
				</section>
				<el-dialog
					width="40%"
					class="project-details-dialog"
					title="Templates"
					:visible.sync="templateDialog"
					append-to-body
				>
					<div class="file-style">
						<stack :column-min-width="192" :gutter-width="8" :gutter-height="8" monitor-images-loaded>
							<stack-item
								v-for="(file, index) in current_template.files"
								:key="index"
								style="transition: transform 300ms"
							>
								<div class="unsplash-images">
									<img :src="'/storage/' + file.image" :alt="file.name" />
									<div class="unsplash-overlay">
										<div class="overlay not-hover-overlay" v-if="imageLoading.includes(file.id)"></div>
										<div class="overlay hover-overlay"></div>
										<div
											:class="['tools', (imageLoading.includes(file.id) ? 'not-hover-overlay' : 'hover-overlay')]"
										>
											<div class="add-tool">
												<div class="add-collection" @click="downloadZipFile(file)">
													<div class="lds-ring" v-if="imageLoading.includes(file.id)">
														<div></div>
														<div></div>
														<div></div>
														<div></div>
													</div>
													<svg
														v-else
														class="add-svg"
														version="1.1"
														viewBox="0 0 32 32"
														width="32"
														height="32"
														aria-hidden="false"
													>
														<path d="M25.8 15.5l-7.8 7.2v-20.7h-4v20.7l-7.8-7.2-2.7 3 12.5 11.4 12.5-11.4z" />
													</svg>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="file-details">
									<div class="file-name">
										<i class="el-icon-picture-outline"></i>
										{{file.name | truncate(20, '...')}}
									</div>
									<div class="file-size">
										<i class="el-icon-rank"></i>
										{{Number(file.size / (1000 * 1000)).toLocaleString(undefined, {
										minimumFractionDigits: 2,
										maximumFractionDigits: 2
										})}} MB
									</div>
								</div>
							</stack-item>
						</stack>
					</div>
					<span slot="footer" class="dialog-footer">
						<div>
							<el-button class="canacel-creative" :disabled="loading" @click="templateDialog = false">Close</el-button>
						</div>
					</span>
				</el-dialog>
			</div>
		</transition>
	</div>
</template>

<script>
	import { mapGetters } from "vuex";
	import { Stack, StackItem } from "vue-stack-grid";
	export default {
		props: ["project"],
		components: {
			Stack,
			StackItem
		},
		data() {
			return {
				loading: false,
				imageLoading: [],
				fileList: [],
				selectedPhotos: [],
				extentions: [],
				extention: {
					jpg: null,
					png: null,
					pdf: null,
					ai: null,
					psd: null,
					indd: null
				},
				instructionSelected: [],
				templateDialog: false,
				current_template: []
			};
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
			},
			...mapGetters(["isFreelancer"])
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
		methods: {
			downloadZipFile(file) {
				this.imageLoading.push(file.id);
				axios({
					url: `/api/request/download-zip/${file.id}`,
					method: "GET",
					responseType: "blob",
					headers: {
						"Content-Type": "application/json"
					}
				})
					.then(response => {
						const url = window.URL.createObjectURL(
							new Blob([response.data])
						);
						const link = document.createElement("a");
						link.href = url;
						// link.download = "download";
						link.setAttribute("download", file.name);
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
						this.imageLoading.splice(
							this.imageLoading.indexOf(file.id),
							1
						);
					})
					.catch(() => {
						console.log("error occured");
						this.imageLoading.splice(
							this.imageLoading.indexOf(file.id),
							1
						);
					});
			},
			openTemplateDialog(sub) {
				this.current_template = sub;
				this.templateDialog = true;
			},
			pushImageLoading(image) {
				if (this.imageLoading.includes(image)) {
					this.imageLoading.splice(this.imageLoading.indexOf(image), 1);
				} else {
					this.imageLoading.push(image);
				}
			},
			createDownload(response) {
				const url = window.URL.createObjectURL(new Blob([response.data]));
				const link = document.createElement("a");
				link.href = url;
				link.setAttribute("download", moment().unix() + ".png"); //or any other extension
				document.body.appendChild(link);
				link.click();
			},
			downloadWithAxios(data, id) {
				this.imageLoading.push(id);
				axios({
					method: "get",
					url: data,
					responseType: "arraybuffer"
				})
					.then(response => {
						this.createDownload(response);
						this.imageLoading.splice(this.imageLoading.indexOf(id), 1);
					})
					.catch(() => {
						console.log("error occured");
						this.imageLoading.splice(this.imageLoading.indexOf(id), 1);
					});
			},
			initValues() {
				if (Object.entries(this.project).length === 0) {
					return;
				} else {
					let {
						project_photos,
						project_assets,
						file_types,
						instructions
					} = JSON.parse(JSON.stringify(this.project));
					project_photos.forEach(item => {
						this.selectedPhotos.push(JSON.parse(item.url));
					});

					instructions.filter(instruction => {
						return (
							instruction.status == 1 &&
							this.instructionSelected.push(instruction.id)
						);
					});

					this.fileList = project_assets;

					this.extentions = JSON.parse(file_types);
					this.extention.jpg = this.extentions.some(
						item => item === ".jpg"
					);
					this.extention.png = this.extentions.some(
						item => item === ".png"
					);
					this.extention.pdf = this.extentions.some(
						item => item === ".pdf"
					);
					this.extention.ai = this.extentions.some(item => item === "ai");
					this.extention.psd = this.extentions.some(
						item => item === "psd"
					);
					this.extention.indd = this.extentions.some(
						item => item === "indd"
					);
				}
			},
			exsitsingFiles() {
				this.loading = true;
				axios
					.get(`/api/projects/files/${this.project.id}`)
					.then(response => {
						this.loading = false;
						if (response.data.status) {
							this.fileList = response.data.data.map(
								({ project_files, id }) => ({
									proof_id: id,
									name: project_files.file_type,
									url: project_files.thumb_path,
									id: project_files.id
								})
							);
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
			updateInstruction(selected) {
				const data = {
					project_id: this.project.id,
					instructions: this.instructionSelected,
					update: true
				};
				axios
					.post("/api/request/instruction", data)
					.then(response => {
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch(error => {
						console.log(error);
					});
			}
		},
		created() {
			// this.exsitsingFiles();
			this.initValues();
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

hr {
	padding: 0;
	margin: 8px 0;
	opacity: 0.5;
}

section:not(:last-child) {
	margin-bottom: 24px;
}

.title-with-icon,
.title-with-out-icon {
	color: #a0aec0;
	font-weight: 500;
}

.title-with-icon {
	font-size: 18px;
}
.title-with-out-icon {
	font-size: 16px;
	margin-bottom: 8px;
}

.include-text {
	padding-bottom: 12px;
	p {
		padding: 0 22px;
	}
}

.photo-text {
	padding: 12px 16px;
	p {
		padding: 0 22px;
	}
}

.icon {
	margin-right: 4px;
}

.body {
	padding-top: 4px;
	padding-left: 12px;
	padding-right: 12px;
	.type-with-icon {
		color: #2d3748;
		font-weight: 500;
	}
	.list {
		padding: 8px 22px 0 22px;
		color: #718096;
		.dimension {
			color: #2d3748;
			font-weight: 600;
		}
		.template-download {
			font-weight: 500;
			margin-left: 4px;
			cursor: pointer;
			color: $green;
		}
	}
	.instruction-list {
		margin-bottom: 24px;
		color: #2d3748;
		font-weight: 400;
		padding: 0px 22px;
	}
	.assets-images {
		padding: 0 12px;
	}
	.photos-images {
		padding: 0 24px;
	}
	.list-with-icon {
		display: flex;
		margin-bottom: 12px;
		font-weight: 400;
		color: #2d3748;
		padding: 0 24px;
		li {
			margin-right: 24px;
		}
		.not-selected {
			color: #e53e3e;
		}
	}
	.adobe-title {
		color: #a0aec0;
		font-weight: 500;
		margin-bottom: 4px;
	}
}

img {
	width: 100%;
	height: auto;
	border-radius: 4px;
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

.lds-ring {
	margin-top: 6px;
	display: inline-block;
	position: relative;
	width: 24px;
	height: 24px;
}
.lds-ring div {
	box-sizing: border-box;
	display: block;
	position: absolute;
	width: 18px;
	height: 18px;
	// margin: 8px;
	border: 2px solid #fff;
	border-radius: 50%;
	animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
	border-color: #fff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
	animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
	animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
	animation-delay: -0.15s;
}
@keyframes lds-ring {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
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

.file-style {
	.file-details {
		margin-top: 6px;
		.file-name {
			color: #1a202c;
			font-weight: 500;
		}
		.file-size {
			color: #a0aec0;
			font-size: 12px;
		}
	}
}
</style>