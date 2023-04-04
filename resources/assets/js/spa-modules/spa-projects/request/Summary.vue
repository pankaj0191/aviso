<template>
	<div>
		<div class="text-center">
			<i class="el-icon-tickets file-type-icon"></i>
		</div>
		<div class="request-name">
			<div class="el-input el-input--suffix">
				<input
					type="text"
					v-model="formData.name"
					autocomplete="off"
					placeholder="* Name Your Request"
					class="el-input__inner"
				/>
			</div>
			<div class="your-freelancer">
				<div v-if="formData.freelancer">
					Your Freelancer is
					<span
						class="freelancer-name"
						@click="selectFreelancerShow = !selectFreelancerShow"
					>{{formData.freelancer.name}}</span>
				</div>
				<div v-else class="select-freelancer">
					<span @click="selectFreelancerShow = !selectFreelancerShow">Select Your Freelancer</span>
					<i class="el-icon-arrow-down"></i>
				</div>
				<div class="select-freelancer-box-p">
					<transition name="el-zoom-in-top">
						<div class="select-freelancer-box" v-if="selectFreelancerShow">
							<el-row>
								<el-col>
									<el-radio
										v-for="(item, key) in freelancers"
										:key="key"
										v-model="formData.freelancer"
										:label="item"
									>{{item.name}}</el-radio>
								</el-col>
							</el-row>
						</div>
					</transition>
				</div>
			</div>
		</div>
		<div class="dimensions">
			<h4 class="request_summary_heading">
				<svg
					class="svg-inline--fa fa-arrows-h fa-w-16"
					aria-hidden="true"
					data-prefix="fal"
					data-icon="arrows-h"
					role="img"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 512 512"
					data-fa-i2svg
				>
					<path
						fill="currentColor"
						d="M399.959 170.585c-4.686 4.686-4.686 12.284 0 16.971L451.887 239H60.113l51.928-51.444c4.686-4.686 4.686-12.284 0-16.971l-7.071-7.07c-4.686-4.686-12.284-4.686-16.97 0l-84.485 84c-4.686 4.686-4.686 12.284 0 16.971l84.485 84c4.686 4.686 12.284 4.686 16.97 0l7.071-7.07c4.686-4.686 4.686-12.284 0-16.971L60.113 273h391.773l-51.928 51.444c-4.686 4.686-4.686 12.284 0 16.971l7.071 7.07c4.686 4.686 12.284 4.686 16.97 0l84.485-84c4.687-4.686 4.687-12.284 0-16.971l-84.485-84c-4.686-4.686-12.284-4.686-16.97 0l-7.07 7.071z"
					/>
				</svg>
				<!-- <i class="fal fa-arrows-h"></i> -->
				Dimensions
			</h4>
			<div style="padding: 0 24px">
				<div class="dimensions-select">
					<label for="type">Request type</label>
					<el-select
						id="type"
						class="dimensions-select-type"
						v-model="type"
						@change="changeType"
						clearable
						placeholder="Select"
					>
						<el-option-group v-for="(group, name) in groups" :key="name" :label="name">
							<el-option
								v-for="(category, key) in group"
								:key="key"
								:label="category.name"
								:value="category.id"
							></el-option>
						</el-option-group>
					</el-select>
				</div>
				<div class="dimensions-size">
					<p>
						<strong>What size should your design be?</strong>
					</p>
					<el-row style="padding: 0 24px">
						<el-col v-for="(subCat, index) in getSubCategories.sub_categories" :key="index">
							<el-checkbox
								v-model="formData.selectedSubCategories"
								:label="subCat.id"
							>{{subCat.name}} ({{subCat.width}}" x {{subCat.height}}")</el-checkbox>
						</el-col>
						<el-col>
							<el-checkbox v-model="customSizeCheck">Custom Size(s)</el-checkbox>
						</el-col>
						<el-col style="padding:8px 0 12px 0" :md="12" v-if="customSizeCheck">
							<el-input
								style="width: 90%"
								placeholder="File size or dimensions (ex: 8.5' x 11')"
								v-model="formData.width"
							></el-input>
						</el-col>
						<el-col style="padding:8px 0 12px 0" :md="12" v-if="customSizeCheck">
							<el-input
								style="width: 90%"
								placeholder="File size or dimensions (ex: 8.5' x 11')"
								v-model="formData.height"
							></el-input>
						</el-col>
					</el-row>
				</div>
			</div>
		</div>
		<el-divider></el-divider>
		<div class="description">
			<h4 class="request_summary_heading">
				<svg
					class="svg-inline--fa fa-question fa-w-12"
					aria-hidden="true"
					data-prefix="fal"
					data-icon="question"
					role="img"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 384 512"
					data-fa-i2svg
				>
					<path
						fill="currentColor"
						d="M200.343 0C124.032 0 69.761 31.599 28.195 93.302c-14.213 21.099-9.458 49.674 10.825 65.054l42.034 31.872c20.709 15.703 50.346 12.165 66.679-8.51 21.473-27.181 28.371-31.96 46.132-31.96 10.218 0 25.289 6.999 25.289 18.242 0 25.731-109.3 20.744-109.3 122.251V304c0 16.007 7.883 30.199 19.963 38.924C109.139 360.547 96 386.766 96 416c0 52.935 43.065 96 96 96s96-43.065 96-96c0-29.234-13.139-55.453-33.817-73.076 12.08-8.726 19.963-22.917 19.963-38.924v-4.705c25.386-18.99 104.286-44.504 104.286-139.423C378.432 68.793 288.351 0 200.343 0zM192 480c-35.29 0-64-28.71-64-64s28.71-64 64-64 64 28.71 64 64-28.71 64-64 64zm50.146-186.406V304c0 8.837-7.163 16-16 16h-68.292c-8.836 0-16-7.163-16-16v-13.749c0-86.782 109.3-57.326 109.3-122.251 0-32-31.679-50.242-57.289-50.242-33.783 0-49.167 16.18-71.242 44.123-5.403 6.84-15.284 8.119-22.235 2.848l-42.034-31.872c-6.757-5.124-8.357-14.644-3.62-21.677C88.876 60.499 132.358 32 200.343 32c70.663 0 146.089 55.158 146.089 127.872 0 96.555-104.286 98.041-104.286 133.722z"
					/>
				</svg>
				<!-- <i class="fal fa-arrows-h"></i> -->
				Description
			</h4>
			<p
				class="instruction-p"
			>Use sentences or paragraphs below to share your request. When you press enter, we'll create a new line for you. After you're done, we'll take each line and create a request checklist for your designer.</p>
			<div style="padding: 0 24px">
				<div id="next_detail_task" v-for="(ins, key) in instructions" :key="key">
					<el-alert type="error" v-if="ins.error" style="margin-bottom: 8px;">{{ins.error}}</el-alert>
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
								@blur="removeEmptyString(ins)"
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
				<el-row style="padding-top: 12px">
					<el-col style="padding-top: 24px">
						<el-checkbox v-model="includeText">
							<strong>My design needs to include text</strong>
						</el-checkbox>
						<el-row v-show="includeText">
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
									v-model="formData.includeTextStr"
								></el-input>
							</el-col>
						</el-row>
					</el-col>
					<el-col style="padding: 24px 0">
						<el-checkbox v-model="uploadAssets">
							<strong>I have assets to upload (content, photos, fonts, logos, etc.)</strong>
						</el-checkbox>
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
					</el-col>
				</el-row>
			</div>
		</div>
		<el-divider></el-divider>
		<div class="assets">
			<h4 class="request_summary_heading">
				<svg
					class="svg-inline--fa fa-camera fa-w-16"
					aria-hidden="true"
					data-prefix="fal"
					data-icon="camera"
					role="img"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 512 512"
					data-fa-i2svg
				>
					<path
						fill="currentColor"
						d="M324.3 64c3.3 0 6.3 2.1 7.5 5.2l22.1 58.8H464c8.8 0 16 7.2 16 16v288c0 8.8-7.2 16-16 16H48c-8.8 0-16-7.2-16-16V144c0-8.8 7.2-16 16-16h110.2l20.1-53.6c2.3-6.2 8.3-10.4 15-10.4h131m0-32h-131c-20 0-37.9 12.4-44.9 31.1L136 96H48c-26.5 0-48 21.5-48 48v288c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V144c0-26.5-21.5-48-48-48h-88l-14.3-38c-5.8-15.7-20.7-26-37.4-26zM256 408c-66.2 0-120-53.8-120-120s53.8-120 120-120 120 53.8 120 120-53.8 120-120 120zm0-208c-48.5 0-88 39.5-88 88s39.5 88 88 88 88-39.5 88-88-39.5-88-88-88z"
					/>
				</svg>
				Photos
			</h4>
			<div style="padding-top: 8px;"></div>
			<el-link
				style="padding: 0px 24px;"
				icon="el-icon-edit"
				v-if="!notStock"
				@click="notStock = true"
				:underline="false"
			>None</el-link>
			<div v-else style="padding: 0px 24px;">
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
					style="margin-top:12px"
					type="textarea"
					v-if="findStockForMe || formData.selectedPhotos.length > 0"
					:rows="2"
					placeholder="Any special requests for the images you want?"
					v-model="formData.assets_text"
				></el-input>
				<div class="unsplash-selected" v-show="formData.selectedPhotos.length > 0">
					<stack :column-min-width="96" :gutter-width="4" :gutter-height="4" monitor-images-loaded>
						<stack-item
							v-for="(image, i) in formData.selectedPhotos"
							:key="i"
							style="transition: transform 300ms"
						>
							<div class="unsplash-images">
								<img :src="image.urls.thumb" :alt="image.alt_description" />
								<div class="unsplash-overlay">
									<div class="overlay hover-overlay"></div>
									<div class="tools hover-overlay">
										<div class="add-tool">
											<div class="add-collection" @click="removeSelectedUnsplash(image)">
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
				<el-divider content-position="center" v-if="formData.selectedPhotos.length == 0">Or</el-divider>
				<div class="stock-buttons" v-if="formData.selectedPhotos.length == 0 && notStock">
					<el-row type="flex" justify="space-around">
						<el-col :span="12" class="text-center">
							<el-link
								type="primary"
								v-if="findStockForMe"
								@click="findStockForMe = false"
								:underline="false"
							>
								Search for
								Photos
							</el-link>
							<el-link
								type="primary"
								v-else
								:underline="false"
								@click="findStockForMe = true"
							>Find Photos For Me</el-link>
						</el-col>
						<el-col :span="12" class="text-center">
							<el-link
								type="primary"
								@click="notStock = false"
								:underline="false"
							>I Don't Need Stock Photos</el-link>
						</el-col>
					</el-row>
				</div>
			</div>
			<el-dialog
				:title="'Unlash Shoping Results ' + (unsplash.total ? unsplash.total : 0)"
				:visible.sync="unsplashDailog"
				width="70%"
				:show-close="false"
			>
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
		<el-divider></el-divider>
		<div class="file-type">
			<h4 class="request_summary_heading">
				<svg
					class="svg-inline--fa fa-info-circle fa-w-16"
					aria-hidden="true"
					data-prefix="fal"
					data-icon="info-circle"
					role="img"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 512 512"
					data-fa-i2svg
				>
					<path
						fill="currentColor"
						d="M256 40c118.621 0 216 96.075 216 216 0 119.291-96.61 216-216 216-119.244 0-216-96.562-216-216 0-119.203 96.602-216 216-216m0-32C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm-36 344h12V232h-12c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12h48c6.627 0 12 5.373 12 12v140h12c6.627 0 12 5.373 12 12v8c0 6.627-5.373 12-12 12h-72c-6.627 0-12-5.373-12-12v-8c0-6.627 5.373-12 12-12zm36-240c-17.673 0-32 14.327-32 32s14.327 32 32 32 32-14.327 32-32-14.327-32-32-32z"
					/>
				</svg>
				File Type
			</h4>
			<div style="padding: 0 24px">
				<div class="switch-file-type" v-for="(extention, key) in extentions" :key="key">
					<div v-if="!extention.children">
						<el-checkbox v-model="formData.extention" :label="extention.name">{{extention.name}}</el-checkbox>
					</div>
					<div v-else>
						<el-checkbox v-model="extention.value">{{extention.name}}</el-checkbox>

						<span style="padding-left:12px" v-if="extention.value">
							(
							<el-radio
								v-for="(child,index) in extention.children"
								:key="index"
								v-model="formData.extentionAdobe"
								:label="child.value"
							>{{child.name}}</el-radio>)
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="text-center continue-button">
			<el-button
				v-if="project.status == 'progress' && active == 4"
				type="primary"
				:loading="saveLoading"
				size="small"
				@click="update"
			>Update Request</el-button>
			<el-button
				v-else-if="active == 4 && project.status != 'progress'"
				type="primary"
				:loading="saveLoading"
				size="small"
				@click="update"
			>Send to My Designer</el-button>
			<el-button v-else @click="update" type="primary" :loading="saveLoading" size="small">Continue</el-button>
			<div style="margin-top: 8px">
				<el-button @click="saveDraft" v-if="project.status == 'draft'" type="text">Save Draft</el-button>
			</div>
		</div>
		<el-dialog title="Add Files" :visible.sync="addFilesDailog" width="50%">
			<add-files
				v-if="addFilesDailog"
				:project_id="project_id"
				:exists_files="fileList"
				:revision_id="revision_id"
				project_type="design"
				@send-files="getFiles"
			/>
		</el-dialog>
		<el-dialog class="success-dialog" width="30%" :show-close="false" :visible.sync="dialogSuccess">
			<div>
				<div class="title-icon">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="2"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="feather feather-check"
					>
						<polyline points="20 6 9 17 4 12" />
					</svg>
				</div>
				<div class="title">Request was Successfully Submitted</div>
				<div class="body">Thank you for your project submission.</div>
			</div>
			<el-button class="save-success" type="primary" @click="goToDashboard">Go to Dashboard</el-button>
		</el-dialog>
	</div>
</template>

<script>
	import { Stack, StackItem } from "vue-stack-grid";
	import AddFiles from "./partials/AddFiles";
	import Unsplash from "./partials/Unsplash";
	export default {
		props: [
			"project_id",
			"project",
			"categories",
			"freelancers",
			"revision_id",
			"active"
		],
		components: {
			Unsplash,
			Stack,
			StackItem,
			AddFiles
		},
		data() {
			return {
				dialogSuccess: false,
				name: "",
				freelancer: "",
				haveFreelancer: false,
				selectFreelancerShow: false,
				checked: "",
				customSizeCheck: false,
				customSize: "",
				fileList: [],
				formData: {
					id: null,
					name: null,
					freelancer: null,
					width: null,
					height: null,
					subCategories: null,
					selectedSubCategories: [],
					includeTextStr: null,
					assets_text: "",
					selectedPhotos: [],
					extention: [],
					extentionAdobe: null
				},
				instructions: [
					{
						instruction: "",
						error: ""
					}
				],
				includeText: false,
				uploadAssets: false,
				addFilesDailog: false,
				search: "",
				findStockForMe: false,
				notStock: false,
				saveLoading: false,
				assetsText: "",
				unsplashDailog: false,
				unsplash: {},
				type: "",
				extentions: [
					{
						name: ".jpg",
						value: true
					},
					{
						name: ".png",
						value: false
					},
					{
						name: ".pdf",
						value: false
					},
					{
						name: "Adobe",
						value: false,
						radio: "",
						children: [
							{
								name: "AI",
								value: "ai"
							},
							{
								name: "PSD",
								value: "psd"
							},
							{
								name: "INDD",
								value: "indd"
							}
						]
					}
				]
			};
		},
		watch: {
			formData: {
				handler: function(after, before) {
                    let defaultFreelancer = {};
					if (Object.values(this.freelancers).length == 0) {
                        defaultFreelancer = Object.values(this.freelancers).find(item => {
                            return item.id == this.project.user_role_freelancer[0].id;
                        });
                    }

					let selectedSubCategories = this.project.sub_categories.map(
						({ id }) => id
					);
					let selectedPhotos = [];
					this.project.project_photos.forEach(item => {
						selectedPhotos.push(JSON.parse(item.url));
					});
					if (
						this.project.assets_text != after.assets_text ||
						this.project.name != after.name ||
						defaultFreelancer != after.freelancer ||
						this.project.width != after.width ||
						this.project.height != after.height ||
						this.project.creative_brief != after.includeTextStr ||
						JSON.stringify(selectedSubCategories) !=
							JSON.stringify(after.selectedSubCategories) ||
						JSON.stringify(selectedPhotos) !=
							JSON.stringify(after.selectedPhotos)
					) {
						return (window.onbeforeunload = function() {
							return "Are you sure you want to close the window?";
						});
					} else {
						window.onbeforeunload = null;
					}
				},
				deep: true
            },
		},
		methods: {
			initValues() {
				if (Object.entries(this.project).length === 0) {
					return;
				} else {
					let {
						id,
						name,
						instructions,
						user_role_freelancer,
						sub_categories,
						width,
						height,
						creative_brief,
						assets_text,
						project_assets,
						project_photos,
						file_types
					} = JSON.parse(JSON.stringify(this.project));
					this.formData.id = id;
					this.formData.name = name;
					this.instructions = instructions;
					this.instructions.push({
						text: "",
						error: ""
                    });

					if (user_role_freelancer && user_role_freelancer.length > 0 && Object.values(this.freelancers).length > 0) {
						this.formData.freelancer = Object.values(this.freelancers).find(item => {
							return item.id == user_role_freelancer[0].id;
						});
					}
					this.formData.subCategories = sub_categories;
					if (sub_categories.length > 0) {
						this.type = sub_categories[0].category_id;
					}
					this.formData.selectedSubCategories = sub_categories.map(
						({ id }) => id
					);

					if (width > 0 && height > 0) {
						this.customSizeCheck = true;
						this.formData.width = parseFloat(width);
						this.formData.height = parseFloat(height);
					}

					if (creative_brief) {
						this.formData.includeTextStr = creative_brief;
						this.includeText = true;
					}

					// this.exsitsingFiles();

					if (project_assets && project_assets.length > 0) {
						this.uploadAssets = true;
						this.fileList = project_assets;
					}

					if (project_photos.length > 0) {
						this.notStock = true;
						this.formData.assets_text = assets_text;
						project_photos.forEach(item => {
							this.formData.selectedPhotos.push(JSON.parse(item.url));
						});
					} else if (project_photos.length == 0) {
						this.notStock = true;
						this.findStockForMe == true;
					}

					if (file_types) {
						this.formData.extention = JSON.parse(file_types).filter(
							item => {
								return (
									item == ".jpg" ||
									item == ".png" ||
									item == ".pdf"
								);
							}
						);

						this.formData.extentionAdobe = JSON.parse(file_types).find(
							item => {
								return (
									item == "ai" || item == "psd" || item == "indd"
								);
							}
						);
					}

					if (this.formData.extentionAdobe) {
						this.extentions.forEach(item => {
							if (item.name == "Adobe") {
								item.value = true;
							}
						});
					}
				}
            },
			dialogNevermind() {
				this.findStockForMe = true;
				this.search = "";
				this.unsplashDailog = false;
				this.formData.selectedPhotos = [];
			},
			goToDashboard() {
				this.dialogSuccess = false;
				this.$router.push({name: "projects"});
			},
			update() {
				this.saveLoading = true;
				const data = {
					project_id: this.project_id,
					width: this.formData.width,
					height: this.formData.height,
					customCard: this.customSizeCheck,
					sub_categories: this.formData.selectedSubCategories,
					name: this.formData.name,
					company: this.formData.freelancer.company,
					email: this.formData.freelancer.email,
					client_name: this.formData.freelancer.name,
					instructions: this.instructions,
					type: "design",
					job_number: "",
					creative_brief: this.formData.includeTextStr,
					assets_tex: this.formData.assets_text,
					photos: this.formData.selectedPhotos,
					extention: this.formData.extention,
					extentionAdobe: this.formData.extentionAdobe
				};
				axios
					.post("/api/request/summary", data)
					.then(response => {
						this.saveLoading = false;
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
							// this.$emit("update_active", response.data.data);
							this.dialogSuccess = true;
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch(error => {
						this.saveLoading = false;
						console.log(error);
					});
			},
			saveDraft() {
				const data = {
					project_id: this.project_id,
					width: this.formData.width,
					height: this.formData.height,
					customCard: this.customSizeCheck,
					sub_categories: this.formData.selectedSubCategories,
					name: this.formData.name,
					instructions: this.instructions,
					company: this.formData.freelancer.company,
					email: this.formData.freelancer.email,
					client_name: this.formData.freelancer.name,
					type: "design",
					job_number: "",
					creative_brief: this.formData.includeTextStr,
					assets_text: this.formData.assets_text,
					photos: this.formData.selectedPhotos,
					extention: this.formData.extention,
					extentionAdobe: this.formData.extentionAdobe
				};
				console.log(data);
				this.$emit("save_draft", data);
			},
			getFiles(data) {
				this.fileList = [...this.fileList, ...data];
				this.addFilesDailog = false;
			},
			// exsitsingFiles() {
			// 	axios
			// 		.get(`/api/projects/files/${this.project_id}`)
			// 		.then(response => {
			// 			if (response.data.status) {
			// 				if (response.data.data.length > 0) {
			// 					this.uploadAssets = true;
			// 				}
			// 				this.fileList = response.data.data.map(
			// 					({ project_files, id }) => ({
			// 						proof_id: id,
			// 						name: project_files.file_type,
			// 						url: project_files.thumb_path,
			// 						id: project_files.id
			// 					})
			// 				);
			// 			} else {
			// 				this.$notify({
			// 					title: "Error",
			// 					message: "Can not get project exist files",
			// 					type: "error"
			// 				});
			// 			}
			// 		})
			// 		.catch(error => {
			// 			this.$notify({
			// 				title: "Error",
			// 				message: "Can not get project exist files",
			// 				type: "error"
			// 			});
			// 		});
			// },
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
			},
			changeType() {
				this.formData.selectedSubCategories = [];
			},
			instructionDelete(value, e) {
				if (this.instructions.length > 1) {
					if (value.text.length == 0) {
						let index = this.instructions.indexOf(value);
						this.instructions.splice(index, 1);
						e.preventDefault();
						this.$nextTick(() => {
							this.$refs[`instruction${index - 1}`][0].focus();
						});
					}
				}
			},
			removeEmptyString(ins) {
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
				// 				}q
				// 			})
				// 			.catch(error => {
				// 				console.log(error);
				// 			});
				// 	} else {
				// 		this.instructions.splice(this.instructions.indexOf(ins), 1);
				// 	}
				// }
			},
			instructionAdd(value) {
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
									this.$refs[`instruction${ins - 1}`][0].focus();
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
			newInstruction() {
				let inst = this.instructions[this.instructions.length - 1];
				if (inst.instruction.length > 4) {
					inst.error = "";
					let ins = this.instructions.push({
						instruction: "",
						error: ""
					});
					this.$nextTick(() => {
						this.$refs[`instruction${ins - 1}`][0].focus();
					});
				} else {
					return (inst.error =
						"To add a new line you have to fill 5 letters at least");
				}
			},
			newline(value) {
				value = `${value}\n`;
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
				this.formData.selectedPhotos = [
					...this.formData.selectedPhotos,
					...data
				];
				this.unsplashDailog = false;
			},
			removeSelectedUnsplash(data) {
				this.formData.assets_text = null;
				this.formData.selectedPhotos.pop(data);
			},
			groupBy(array, key) {
				const result = {};
				array.forEach(item => {
					if (!result[item[key]]) {
						result[item[key]] = [];
					}
					result[item[key]].push(item);
				});
				return result;
			}
		},
		computed: {
            spacePrefix() {
                return spacePrefix
            },
			assetText() {
				// return this.formData.assets_text;
				// if (
				// 	this.formData.selectedPhotos >
				// 		this.project.project_assets.length ||
				// 	this.project.assets_text !== this.formData.assets_text
				// ) {
				// 	return (window.onbeforeunload = function() {
				// 		return "Are you sure you want to close the window?";
				// 	});
				// }
			},
			groups() {
				if (this.categories.length > 0) {
					return this.groupBy(this.categories, "group");
				}
			},
			getSubCategories() {
				let categories = {};
				if (this.type) {
					categories = this.categories.find(item => {
						return item.id == this.type;
					});
				}
				return categories;
			}
		},
		created() {
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

.file-type-icon {
	font-size: 64px;
	margin-bottom: 24px;
}

.request_step_question {
	font-size: 24px;
	font-weight: 700;
}

.primary-color {
	color: $green;
}

.switch-file-type {
	padding-right: 24px;
	display: inline-block;
}

.request-name {
	text-align: center;
	margin-bottom: 24px;
	.el-input {
		width: 84%;
		text-align: center;
		.el-input__inner {
			border: none !important;
			border-bottom: 2px solid $grey !important;
			width: 100%;
			border-radius: 0;
			color: $green;
			text-transform: capitalize;
			text-align: center;
			font-size: 20px;
			font-weight: 700;
		}
		.el-input__inner:focus {
			border-bottom: 2px solid $green !important;
		}
		::-webkit-input-placeholder {
			/* Edge */
			color: $green;
			text-align: center;
			font-size: 20px;
			font-weight: 700;
		}

		:-ms-input-placeholder {
			/* Internet Explorer */
			color: $green;
			text-align: center;
			font-size: 20px;
			font-weight: 700;
		}

		::placeholder {
			color: $green;
			text-align: center;
			font-size: 20px;
			font-weight: 700;
		}
	}
}

.your-freelancer {
	padding: 20px 0;
	user-select: none;
	.freelancer-name {
		cursor: pointer;
		font-weight: bold;
		color: $green;
	}
}

.select-freelancer {
	font-weight: bold;
	color: $grey;
	span {
		cursor: pointer;
	}
}

.select-freelancer-box-p {
	padding-top: 8px;
}
.select-freelancer-box {
	box-shadow: $box-shadow;
	padding: 16px 20px;
	width: 40%;
	margin: 0 auto;
	max-height: 128px;
	overflow: auto;
	text-align: left;
}

.request_summary_heading {
	color: $green;
	margin: 12px 0;
	svg {
		color: #fff;
		background-color: $green;
		border-radius: 15px;
		padding: 5px 0;
		font-size: 55px;
		width: 30px;
		height: 30px;
		vertical-align: middle;
	}
	svg:not(:root).svg-inline--fa {
		overflow: visible;
	}
}
.dimensions {
	.dimensions-select-type {
		margin-bottom: 12px;
		width: 100%;
	}
	.dimensions-size {
		p {
			margin-bottom: 12px;
		}
	}
}
.description {
	.instruction-p {
		margin-bottom: 24px;
		padding: 0 24px;
	}
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

.success-dialog {
	text-align: center;
	.el-dialog {
		box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
			0 10px 10px -5px rgba(0, 0, 0, 0.04);
		border-radius: 8px;
		.title-icon {
			margin: 0 auto 24px auto;
			width: 4.5rem;
			height: 4.5rem;
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
			font-weight: 600;
			font-size: 20px;
			margin-bottom: 12px;
			color: #161e2e;
		}
		.body {
			width: 80%;
			margin: 0 auto;
			color: #6b7280;
		}
		.el-dialog__body {
			padding: 30px 32px;
		}
		.save-success {
			border-radius: 6px;
			border: none;
			box-shadow: 0px 2px 4px 2px rgba(0, 0, 0, 0.08);
			padding: 14px 32px;
			margin-top: 24px;
		}
	}
}
</style>