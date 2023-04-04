<template>
	<div>
		<div>
			<el-input
				placeholder="Search assets by keyword"
				@keyup.enter.native="fetch"
				v-model="search"
				class="input-with-select"
			>
				<el-button
					slot="prepend"
					@click="addSelectedPhotos"
					v-if="selectedPhotos.length > 0"
					class="prepend-btn"
				>{{'Add ' + selectedPhotos.length + ' Photo' + (selectedPhotos.length > 1 ? 's' : '') + ' to Request'}}</el-button>
				<el-button slot="append" icon="el-icon-search" @click="fetch">Search</el-button>
			</el-input>
		</div>
		<div class="loader-inner ball-pulse text-center">
			<div></div>
			<div></div>
			<div></div>
		</div>
		<div
			class="unsplash-photos infinite-list"
			v-infinite-scroll="loadMore"
			infinite-scroll-disabled="disabled"
		>
			<stack :column-min-width="300" :gutter-width="15" :gutter-height="15" monitor-images-loaded>
				<stack-item
					v-for="(image, i) in unsplashPhotos.results"
					:key="i"
					style="transition: transform 300ms"
					class="show-overlay"
				>
					<div class="unsplash-images">
						<img :src="image.urls.small" :alt="image.alt_description" />
						<div class="unsplash-overlay">
							<div class="overlay not-hover-overlay" v-if="selectedPhotos.includes(image)"></div>
							<div class="overlay hover-overlay"></div>
							<div class="tools not-hover-overlay" v-if="selectedPhotos.includes(image)">
								<div class="add-tool">
									<button
										style="display:flex"
										type="button"
										title="Add to collection"
										class="add-collection decoration-none btn-add isIncludes"
										@click="addPhoto(image)"
									>
										<svg
											class="add-svg"
											version="1.1"
											viewBox="0 0 32 32"
											width="32"
											height="32"
											aria-hidden="false"
										>
											<path d="M28.1 5.2L11.7 21.6l-7.8-7.8-3.4 3.3 11.2 11.2L31.5 8.6z" />
										</svg>
										<span style="margin-left:4px">Added</span>
									</button>
								</div>
							</div>
							<div class="tools hover-overlay">
								<div class="add-tool">
									<button
										type="button"
										title="Add to collection"
										:class="['add-collection decoration-none btn-add']"
										@click="addPhoto(image)"
									>
										<svg
											class="add-svg"
											v-if="selectedPhotos.includes(image)"
											version="1.1"
											viewBox="0 0 32 32"
											width="32"
											height="32"
											aria-hidden="false"
										>
											<path d="M3.5 13.6h25v4.9h-25z" />
										</svg>
										<svg
											class="add-svg"
											v-else
											version="1.1"
											viewBox="0 0 32 32"
											width="32"
											height="32"
											aria-hidden="false"
										>
											<path d="M14 3h4v26h-4zM29 14v4h-26v-4z" />
										</svg>
									</button>
								</div>
							</div>
							<div class="footer-overlay">
								<div class="profile hover-overlay">
									<span class="profile-2">
										<div class="mr-8">
											<span class="profile-3">
												<div class="profile-display-image" style="width: 32px; height: 32px;">
													<a href="/@henmankk">
														<img
															class="profile-image"
															:src="image.user.profile_image.small"
															:alt="'Go to '+ image.user.name + '\'s profile'"
														/>
													</a>
												</div>
											</span>
										</div>
										<div>
											<span class="profile-3">
												<a
													class="profile-name _1ByhS _4kjHg _1_w0v _3XJBh decoration-none _2zITg"
													:href="image.user.links.html"
												>{{image.user.name}}</a>
											</span>
										</div>
									</span>
								</div>
							</div>
						</div>
					</div>
				</stack-item>
			</stack>
			<div class="text-center">
				<div v-if="loading">
					<div class="lds-ellipsis" v-if="loading">
						<div></div>
						<div></div>
						<div></div>
						<div></div>
					</div>
				</div>
				<p v-if="noMore">No more</p>
			</div>
		</div>
	</div>
</template>

<script>
	import { Stack, StackItem } from "vue-stack-grid";
	export default {
		props: {
			search: {
				type: String
			}
		},
		components: {
			Stack,
			StackItem
		},
		data() {
			return {
				unsplashPhotos: [],
				selectedPhotos: [],
				newSearch: "",
				page: 1,
				loading: false
			};
		},
		watch: {
			search(newVal, oldVal) {
				if (newVal !== oldVal) {
					this.$emit("newSearchVal", newVal);
				}
			}
		},
		computed: {
			noMore() {
				if (this.unsplashPhotos.results) {
					return (
						this.unsplashPhotos.total <=
						this.unsplashPhotos.results.length
					);
				}
			},
			disabled() {
				return this.loading || this.noMore;
			}
		},
		methods: {
			async fetch() {
				this.loading = true;
				await axios
					.get("/api/request/unsplash", {
						params: {
							search: this.search,
							page: this.page
						}
					})
					.then(response => {
						this.unsplashPhotos = response.data;
						this.$emit("unsplashData", response.data);
						this.loading = false;
					})
					.catch(error => {
						console.log(error);
					});
			},
			async loadMore() {
				this.loading = true;
				await setTimeout(async () => {
					this.page++;
					await axios
						.get("/api/request/unsplash", {
							params: {
								search: this.search,
								page: this.page
							}
						})
						.then(response => {
							this.unsplashPhotos.results = [
								...this.unsplashPhotos.results,
								...response.data.results
							];
							this.loading = false;
						})
						.catch(error => {
							console.log(error);
						});
				}, 1000);
			},
			addPhoto(photo) {
				if (this.selectedPhotos.includes(photo)) {
					this.selectedPhotos.pop(photo);
				} else {
					this.selectedPhotos.push(photo);
				}
			},
			addSelectedPhotos() {
				this.$emit("addSelectedPhotos", this.selectedPhotos);
			}
		},
		mounted() {
			this.fetch();
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

.input-with-select {
	padding-bottom: 8px;
}

.unsplash-photos {
	max-height: 60vh;
	overflow: auto;
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
	border-radius: 12px;
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-overlay {
	animation-name: Qed4z;
	animation-timing-function: ease-in;
	animation-duration: 0.3s;
}

.unsplash-overlay .tools {
	position: absolute;
	top: 20px;
	padding-right: 20px;
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

.add-collection:hover {
	color: #111;
	fill: currentColor;
	background-color: #fff;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.add-collection {
	color: #767676;
	fill: currentColor;
	background-color: hsla(0, 0%, 100%, 0.9);
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

.footer-overlay {
	position: absolute;
	bottom: 20px;
	padding-right: 20px;
	padding-left: 20px;
	width: 100%;
	display: flex;
	pointer-events: none;
	height: 32px;

	.profile {
		flex: 1;
		min-width: 0;
		margin-right: 8px;
	}
}

.hover-overlay {
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.profile-2 {
	pointer-events: auto;
	display: inline-flex;
	align-items: center;
	max-width: 100%;
}

.profile-3,
.profile-2 {
	position: relative;
}

.mr-8 {
	margin-right: 8px;
}

.profile-3,
.profile-2 {
	position: relative;
}

.profile-display-image {
	position: relative;
	display: inline-block;
	line-height: 1;
	border-radius: 50%;
	overflow: hidden;
	vertical-align: middle;
	background-color: rgba(0, 0, 0, 0.1);
}

.profile-image {
	vertical-align: unset;
	width: 100%;
}

img {
	vertical-align: middle;
}

a {
	background-color: transparent;
}

.profile-name {
	display: block;
	font-size: 15px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	line-height: 1.35;
	opacity: 0.8;
	transition: opacity 0.1s ease-in-out;
	line-height: 30px;
	color: #fff;
	transform: translateZ(0);
	text-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
}

img {
	width: 100%;
	height: auto;
	border-radius: 12px;
}
.isIncludes {
	color: #fff !important;
	fill: currentColor !important;
	background-color: #3cb46e;
}
.prepend-btn {
	color: #fff !important;
	background-color: #3cb46e !important;
}
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