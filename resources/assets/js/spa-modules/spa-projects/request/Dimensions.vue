<template>
	<div>
		<form>
			<div class="text-center">
				<i class="el-icon-tickets dimensions-icon"></i>
			</div>
			<div class="dimensions-types">
				<div class="dimensions-has-types" v-if="!dimensionsSelect && getSubCategories">
					<div class="dimensions-choosen">
						<strong>{{getSubCategories ? getSubCategories.name : 'Other' }}</strong>
					</div>
					<div class="dimensions-not-right">
						Not right?
						<span
							class="dimensions-choose-type"
							@click="dimensionsSelect = true"
						>Change Request Type</span>
					</div>
				</div>
				<div class="dimensions-not-have" v-else>
					<label for="type">Request type</label>
					<el-select
						@change="changeType"
						id="type"
						class="dimensions-select-type"
						v-model="type"
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
			</div>
			<h4 class="request_step_question text-center">What size(s) should your design be?</h4>
			<el-row :gutter="32">
				<template v-if="getSubCategories.sub_categories && getSubCategories.sub_categories.length > 0">
					<el-col :md="8" v-for="sub in getSubCategories.sub_categories" :key="sub.id">
						<div
							@click="doActiveCard(sub)"
							:class="['request_skill_size_panel template_request_skill_size_panel', {'request_size_chosen': activeCard.includes(sub.id)}]"
							data-size-id="153"
						>
							<h5 class="request_skill_size_name">{{sub.name}}</h5>
							<div class="request_skill_size_image">
								<img class="img-responsive" :src="`${spacePrefix}/${sub.image}`" />
							</div>
							<div
								class="request_skill_size_details"
							>{{parseFloat(sub.width)}} x {{parseFloat(sub.height)}}</div>
						</div>
					</el-col>
				</template>
				<el-col :md="8">
					<div
						@click="customCard = !customCard"
						:class="['request_skill_size_panel', {'request_size_chosen': customCard}]"
						id="custom_request_skill_size_panel"
					>
						<h5 class="request_skill_size_name">Custom</h5>
						<div class="request_skill_size_image">
							<svg
								class="svg-inline--fa fa-pencil-ruler fa-w-16"
								aria-hidden="true"
								data-prefix="fal"
								data-icon="pencil-ruler"
								role="img"
								xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 512 512"
								data-fa-i2svg
							>
								<path
									fill="currentColor"
									d="M502.71 368.14L379.88 245.31l49.4-49.4 68.65-68.66c18.76-18.76 18.75-49.17 0-67.93l-45.25-45.25C443.3 4.69 431 0 418.71 0s-24.59 4.69-33.97 14.07l-68.65 68.64-49.4 49.4L143.87 9.29C137.68 3.1 129.56 0 121.44 0s-16.23 3.1-22.43 9.29L9.31 99c-12.38 12.39-12.39 32.47 0 44.86l122.8 122.8-113.01 113L.34 487.11c-2.72 15.63 11.22 26.9 24.59 24.56l107.44-18.84 112.94-112.96L368.14 502.7a31.621 31.621 0 0 0 22.42 9.29c8.12 0 16.24-3.1 22.43-9.29l89.72-89.7c12.39-12.39 12.39-32.47 0-44.86zM407.36 36.7c4.09-4.09 18.6-4.09 22.69 0l45.25 45.24c6.25 6.25 6.25 16.42 0 22.67l-46.03 46.03-67.94-67.94 46.03-46zM31.93 121.63l89.51-89.52L177.39 88l-39.03 39.03c-3.12 3.12-3.12 8.19 0 11.31l11.31 11.31c3.12 3.12 8.19 3.12 11.31 0l39.04-39.04 44.1 44.05-89.5 89.49L31.93 121.63zm84.96 341.43L34.5 477.51l14.37-82.37 289.83-289.8 67.94 67.94-289.75 289.78zm273.88 17.02l-122.86-122.8 89.47-89.48 44.12 44.07-39.15 39.16c-3.12 3.12-3.12 8.19 0 11.31l11.31 11.31c3.12 3.12 8.19 3.12 11.31 0l39.17-39.17 55.94 55.88-89.31 89.72z"
								/>
							</svg>
							<!-- <div class="fal fa-pencil-ruler"></div> -->
						</div>
						<div class="request_skill_size_details">
							<div id="custom_request_size_text" v-show="!customCard">Click to enter your dimensions</div>
							<div
								v-show="customCard"
								id="custom_request_size_field"
								class="form-group text optional request_size"
							>
								<label
									class="control-label text optional"
									for="custom_request_size_text_area"
								>What are the dimensions?</label>
								<!-- <textarea @click.stop="customCard = true"
									class="form-control text optional"
									id="custom_request_size_text_area"
									placeholder="* File size or dimensions (ex: 8.5&quot; x 11&quot;)"
								></textarea>
								-->
								<div @click.stop="customCard = true" id="custom_request_size_text_area" class="custom-size">
									<el-input size="mini" class="custom" placeholder="Width" v-model="width"></el-input>
									<el-input size="mini" class="custom" placeholder="Height" v-model="height"></el-input>
								</div>
							</div>
						</div>
					</div>
				</el-col>
			</el-row>
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
	</div>
</template>

<script>
	export default {
		props: ["categories", "project_id", "active", "project"],
		data() {
			return {
				type: "",
				dimensionsSelect: false,
				customCard: false,
				saveLoading: false,
				activeCard: [],
				height: 0,
				width: 0
			};
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

					if (width > 0 && height > 0) {
						this.customCard = true;
						this.width = parseFloat(width);
						this.height = parseFloat(height);
					}
					if (sub_categories && sub_categories.length > 0) {
						this.type = sub_categories[0].category_id;
						this.activeCard = sub_categories.map(({ id }) => id);
					}
				}
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
			},
			doActiveCard(sub) {
				if (this.activeCard.includes(sub.id)) {
					this.activeCard.splice(this.activeCard.indexOf(sub.id), 1);
				} else {
					this.activeCard.push(sub.id);
				}
			},
			changeType() {
				this.activeCard = [];
				this.dimensionsSelect = false;
			},
			saveDraft() {
				if (
					this.activeCard.length > 0 ||
					(this.customCard && this.width > 0 && this.height > 0)
				) {
					const data = {
						activeCard: this.activeCard,
						width: this.width,
						height: this.height,
						customCard: this.customCard,
						project_id: this.project_id,
						active_card: this.activeCard
					};
					this.$emit("save_draft", data);
				} else {
					toastr["error"](
						"Please select one of these dimensions or select custom size",
						"Error"
					);
				}
			},
			update() {
				if (
					this.activeCard.length > 0 ||
					(this.customCard && this.width > 0 && this.height > 0)
				) {
					this.saveLoading = true;
					const data = {
						activeCard: this.activeCard,
						width: this.width,
						height: this.height,
						customCard: this.customCard,
						project_id: this.project_id,
						active_card: this.activeCard
					};
					axios
						.post("/api/request/dimension", data)
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
				} else {
					toastr["error"](
						"Please select one of these dimensions or select custom size",
						"Error"
					);
				}
			}
		},
		computed: {
            spacePrefix() {
                return spacePrefix
            },
			groups() {
				if (this.categories.length > 0) {
					return this.groupBy(this.categories, "group");
				}
			},
			getSubCategories() {
				let category = {};
				if (
					this.categories &&
					Object.entries(this.categories).length > 0 &&
					this.type
				) {
					category = this.categories.find(
						category => category.id == this.type
					);
				}
				return category;
			}
		},
		created() {
			this.$emit("handler", this.submit);
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

.dimensions-icon {
	font-size: 64px;
}

.dimensions-choose-type {
	cursor: pointer;
	color: $green;
}
.dimensions-types {
	padding: 8px 0;
	.dimensions-select-type {
		width: 100%;
	}
}
.request_step_question {
	font-size: 24px;
	font-weight: 700;
}
.request_skill_size_panel.request_size_chosen {
	background-color: rgba(57, 202, 154, 0.2);
	border: 2px solid $green;
}
.request_skill_size_panel:hover {
	cursor: pointer;
}
.request_skill_size_panel.template_request_skill_size_panel {
	padding: 5px;
	user-select: none;
}
.request_skill_size_panel:hover {
	border: 2px solid $green;
}
.request_skill_size_panel {
	margin-top: 10px;
	text-align: center;
	border: 2px solid #ddd;
	color: #000 !important;
	background-color: #fff;
	border-radius: 10px;
}

#request-form h5 {
	display: inline-block;
	vertical-align: middle;
}

.request_skill_size_panel .request_skill_size_name {
	height: 66px;
	overflow: hidden;
	line-height: 1.5em;
	font-weight: 400;
	font-size: 22px;
}

.request_skill_size_panel .request_skill_size_name {
	height: 66px;
	overflow: hidden;
}
#custom_request_skill_size_panel .request_skill_size_image {
	height: 110px !important;
}
.request_skill_size_panel .request_skill_size_image {
	height: 150px;
	text-align: center;
	font-size: 80px;
}
.request_skill_size_panel .request_skill_size_image img,
.request_skill_size_panel .request_skill_size_image i {
	margin: 0 auto;
}
.request_skill_size_panel .request_skill_size_image img {
	max-height: 100%;
}
.img-responsive {
	display: block;
	max-width: 100%;
	height: auto;
}
img {
	vertical-align: middle;
}

.request_skill_size_panel .request_skill_size_image svg {
	vertical-align: top;
}
.svg-inline--fa {
	display: inline-block;
	font-size: inherit;
	height: 1em;
	overflow: visible;
	vertical-align: -0.125em;
}
.request_skill_size_panel .request_skill_size_details {
	margin-top: 15px;
	height: 50px;
	overflow: hidden;
}

#custom_request_skill_size_panel .request_skill_size_details {
	height: 100px;
}

#request-form form .form-group textarea {
	height: 150px;
	border: 1px solid #ccc;
}
.request_skill_size_panel #custom_request_size_text_area {
	background-color: #fff;
	height: 75px !important;
	border-radius: 10px;
}

form .form-group textarea {
	line-height: 20px;
	height: 170px;
	padding: 12px 15px;
}

.custom-size .custom {
	margin: 4px 0;
	width: 80%;
}

.continue-button {
	padding-top: 24px;
}
</style>