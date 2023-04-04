<template>
	<div>
		<div class="text-center">
			<i class="el-icon-camera photos-icon"></i>
		</div>
		<h4 class="request_step_question text-center">What photos do you need?</h4>
		<el-input
			v-if="!findStockForMe"
			@keyup.enter.native="openUnsplash"
			placeholder="Search photos by keyword"
			v-model="search"
			class="input-with-select"
		>
			<el-button slot="append" icon="el-icon-search" @click="openUnsplash"></el-button>
		</el-input>
		<el-input
			type="textarea"
			v-else
			:rows="2"
			placeholder="Any special requests for the image you want?"
			v-model="projectComponent.assets_text"
		></el-input>
		<div class="unsplash-selected" v-show="selectedPhotos.length > 0">
			<stack :column-min-width="96" :gutter-width="4" :gutter-height="4" monitor-images-loaded>
				<stack-item v-for="(image, i) in selectedPhotos" :key="i" style="transition: transform 300ms">
					<div class="unsplash-images">
						<img :src="image.urls.thumb" :alt="image.alt_description" />
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
		<el-input
			type="textarea"
			v-if="selectedPhotos.length > 0"
			:rows="2"
			placeholder="Any special requests for the images you want?"
			v-model="projectComponent.assets_text"
		></el-input>
		<div class="text-center stock-buttons" v-if="selectedPhotos.length == 0">
			<el-link type="primary" v-if="findStockForMe" @click="findStockForMe = false" :underline="false">
				Search for
				Photos
			</el-link>
			<el-link
				type="primary"
				v-else
				:underline="false"
				@click="findStockForMe = true"
			>Find Photos For Me</el-link>
		</div>
		<div class="text-center continue-button">
			<el-button
				v-if="active == 4"
				type="primary"
				:loading="saveLoading"
				size="small"
			>Send to My Designer</el-button>
			<el-button
				v-else
				@click="update"
				type="primary"
				:disabled="projectComponent.assets_text && projectComponent.assets_text.length == 0"
				:loading="saveLoading"
				size="small"
			>
				<span v-if="selectedPhotos.length == 0">I Don't Need Stock Photos</span>
				<span v-else>Continue</span>
			</el-button>
			<div style="margin-top: 8px">
				<el-button type="text" @click="saveDraft" v-if="project.type == 'draft'">Save Draft</el-button>
			</div>
		</div>
		<el-dialog :show-close="false" :visible.sync="unsplashDailog" width="70%">
			<span slot="title" style="display:flex;justify-content: space-between;">
				<div>Unsplash Photos ({{unsplash.total ? unsplash.total : 0}})</div>
				<el-link type="primary" @click="dialogNevermind" :underline="false">Nevermind pick photo for me</el-link>
			</span>
			<unsplash
				v-if="unsplashDailog"
				:search="search"
				@unsplashData="unsplashData"
				@newSearchVal="newSearchVal"
				@addSelectedPhotos="addSelectedPhotos"
			/>
		</el-dialog>
	</div>
</template>

<script>
	import { Stack, StackItem } from "vue-stack-grid";
	import Unsplash from "./partials/Unsplash";
	export default {
		components: {
			Unsplash,
			Stack,
			StackItem
		},
		props: ["project_id", "project", "active"],
		data() {
			return {
				search: "",
				findStockForMe: false,
				assetsText: "",
				unsplashDailog: false,
				unsplash: {},
				selectedPhotos: [],
				saveLoading: false,
				projectComponent: {}
			};
		},
		methods: {
			dialogNevermind() {
				this.findStockForMe = true;
				this.unsplashDailog = false;
				this.selectedPhotos = [];
			},
			unsplashData(data) {
				this.unsplash = data;
			},
			newSearchVal(data) {
				this.search = data;
			},
			openUnsplash() {
				this.unsplashDailog = true;
			},
			addSelectedPhotos(data) {
				this.selectedPhotos = [...this.selectedPhotos, ...data];
				this.unsplashDailog = false;
			},
			removeSelected(data) {
				this.selectedPhotos.pop(data);
			},
			saveDraft() {
				if (
					this.findStockForMe == true &&
					(!this.projectComponent.assets_text ||
						this.projectComponent.assets_text == null)
				) {
					return toastr["error"](
						"Please write something in text",
						"Error"
					);
				}
				const data = {
					step: "assets",
					project_id: this.project_id,
					assets_text: this.projectComponent.assets_text,
					photos: this.selectedPhotos
				};
				this.$emit("save_draft", data);
			},
			update() {
				if (
					this.findStockForMe == true &&
					(!this.projectComponent.assets_text ||
						this.projectComponent.assets_text == null)
				) {
					return toastr["error"](
						"Please write something in text",
						"Error"
					);
				}
				this.saveLoading = true;
				const data = {
					step: "assets",
					project_id: this.project_id,
					assets_text: this.projectComponent.assets_text,
					photos: this.selectedPhotos
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
			}
		},
		created() {
			this.projectComponent = this.project;
			this.project.project_photos.forEach(item => {
				this.selectedPhotos.push(JSON.parse(item.url));
			});
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

.photos-icon {
	font-size: 64px;
	margin-bottom: 24px;
}

.request_step_question {
	font-size: 24px;
	font-weight: 700;
}

.stock-buttons {
	margin-top: 24px;
}

img {
	width: 100%;
	height: auto;
	border-radius: 6px;
}

.unsplash-selected {
	padding: 12px 0;
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