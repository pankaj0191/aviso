<template>
	<div>
		<div class="request-name">
			<div class="container">{{project.name}}</div>
		</div>
		<div class="mobile-stepper">
			<el-row>
				<el-col :span="8">
					<div class="mobile-step mobile-step-next next" v-if="mobileStepperName().next.active == 0">
						<div class="mobile-icon mobile-icon-next">{{mobileStepperName().prevTo.active}}</div>
						{{mobileStepperName().prevTo.name}}
					</div>
				</el-col>
				<el-col :span="8">
					<div class="mobile-step mobile-step-prev prev" v-if="mobileStepperName().prev.active != 0">
						<div class="mobile-icon mobile-icon-prev">{{mobileStepperName().prev.active}}</div>
						{{mobileStepperName().prev.name}}
					</div>
				</el-col>
				<el-col :span="8">
					<div class="mobile-step mobile-step-current current">
						<div class="mobile-icon mobile-icon-current">{{mobileStepperName().current.active}}</div>
						{{mobileStepperName().current.name}}
					</div>
				</el-col>
				<el-col :span="8">
					<div class="mobile-step mobile-step-next next" v-if="mobileStepperName().next.active != 0">
						<div class="mobile-icon mobile-icon-next">{{mobileStepperName().next.active}}</div>
						{{mobileStepperName().next.name}}
					</div>
				</el-col>
				<el-col :span="8">
					<div class="mobile-step mobile-step-next next" v-if="mobileStepperName().prev.active == 0">
						<div class="mobile-icon mobile-icon-next">{{mobileStepperName().nextTo.active}}</div>
						{{mobileStepperName().nextTo.name}}
					</div>
				</el-col>
			</el-row>
		</div>
		<div class="container">
			<el-container class="container-width">
				<aside class="aside-stepper">
					<div class="right-stepper">
						<el-steps direction="vertical" finish-status="success" :active="active">
							<el-step title="Name"></el-step>
							<el-step title="Dimensions"></el-step>
							<el-step title="Description"></el-step>
							<el-step title="Photos"></el-step>
							<el-step title="Summary"></el-step>
						</el-steps>
					</div>
				</aside>
				<div class="left-stepper">
					<el-card class="card-request">
						<el-button
							@click="active--"
							:disabled="fetchLoading"
							type="text"
							icon="el-icon-arrow-left"
							v-if="active > 0"
						>Back</el-button>
						<div class="lds-ellipsis" v-if="fetchLoading">
							<div></div>
							<div></div>
							<div></div>
							<div></div>
						</div>
						<transition name="el-zoom-in-center">
							<keep-alive>
								<component
									v-if="!fetchLoading"
									:categories="categories"
									:project_id="project_id"
									v-bind:is="currentTabComponent"
									:active="active"
									:project="project"
									:freelancers="freelancers"
									@update_active="updateActive"
									@save_draft="saveDraft"
									ref="childComponent"
								></component>
							</keep-alive>
						</transition>
					</el-card>
				</div>
			</el-container>
		</div>
	</div>
</template>

<script>
	import Dimensions from "./Dimensions";
	import Description from "./Description";
	import Photos from "./Photos";
	import { mapGetters } from "vuex";
	import Summary from "./Summary";
	import Name from "./Name";
	export default {
		props: ["project_id", "user_id", "revision_id", "project_type"],
		components: {
			Dimensions,
			Description,
			Photos,
			Summary,
			Name,
		},
		data() {
			return {
				active: 1,
				currentTabComponent: "",
				nextLoading: false,
				categories: [],
				project: {},
				fetchLoading: false,
				freelancers: {},
			};
		},
		computed: {
			loadingCom() {
				return this.$refs.childComponent.saveLoading;
			},
			...mapGetters(["windowBreakPoint", "windowWidth"]),
		},
		methods: {
			mobileStepperName() {
				let data = {
					nextTo: {},
					next: {},
					current: {},
					prev: {},
					prevTo: {},
				};
				switch (this.active) {
					case 0:
						data.prev.active = 0;
						data.prev.name = "";
						data.current.active = 1;
						data.current.name = "Home";
						data.next.active = 2;
						data.next.name = "Dimensions";
						data.nextTo.active = 3;
						data.nextTo.name = "Description";
						return data;
						break;
					case 1:
						data.prev.active = 1;
						data.prev.name = "Home";
						data.current.active = 2;
						data.current.name = "Dimensions";
						data.next.active = 3;
						data.next.name = "Description";
						return data;
						break;
					case 2:
						data.prev.active = 2;
						data.prev.name = "Dimensions";
						data.current.active = 3;
						data.current.name = "Description";
						data.next.active = 4;
						data.next.name = "Photos";
						return data;
						break;
					case 3:
						data.prev.active = 3;
						data.prev.name = "Description";
						data.current.active = 4;
						data.current.name = "Photos";
						data.next.active = 5;
						data.next.name = "Summary";
						return data;
						break;
					case 4:
						data.prevTo.active = 3;
						data.prevTo.name = "Description";
						data.prev.active = 4;
						data.prev.name = "Photos";
						data.current.active = 5;
						data.current.name = "Summary";
						data.next.active = 0;
						data.next.name = "";
						return data;
						break;
				}
			},
			async fetch() {
				this.fetchLoading = true;
				await axios
					.get("/api/request/fetch", {
						params: {
							project_id: this.project_id,
						},
					})
					.then((response) => {
						this.fetchLoading = false;
						if (response.data.role == "freelancer") {
							this.$router.push({name: "projects"});
						}
						this.categories = response.data.categories;
						this.project = response.data.project;
						this.freelancers = response.data.freelancers;
						// this.loading = false;
					})
					.catch((error) => {
						console.log(error);
						this.fetchLoading = false;
					});
			},
			saveDraft(values) {
				this.saveLoading = true;
				const data = {
					status: "draft",
					...values,
				};
				axios
					.post("/api/request/draft", data)
					.then((response) => {
						this.saveLoading = false;
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
							this.$router.push({name: "projects"});
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch((error) => {
						this.saveLoading = false;
						console.log(error);
					});
			},
			onUpdate() {
				this.nextLoading = true;
				this.$refs.childComponent.submit();
				if (this.loadingCom) {
					this.nextLoading = false;
					this.active++;
				}
			},
			updateActive(data) {
				this.active = data.project.active_stepper;
				this.fetch();
			},
			handleWindowResize() {
				this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
			},
			activeComponent(value) {
				switch (value) {
					case 0:
						return (this.currentTabComponent = "Name");
						break;
					case 1:
						return (this.currentTabComponent = "Dimensions");
						break;
					case 2:
						return (this.currentTabComponent = "Description");
						break;
					case 3:
						return (this.currentTabComponent = "Photos");
						break;
					case 4:
						return (this.currentTabComponent = "Summary");
						break;
				}
			},
		},
		watch: {
			active(newVal, old) {
				this.activeComponent(newVal);
			},
			project() {
				if (this.project.active_stepper > 0) {
					this.active = this.project.active_stepper;
				} else {
					this.active = 1;
				}
			},
		},
		mounted() {
			this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
			$(".with-navbar").css("padding-top", "0");
		},
		async created() {
            await this.fetch();
			await this.activeComponent(this.active);
			window.addEventListener("resize", this.handleWindowResize);
		},
		destroyed() {
			window.removeEventListener("resize", this.handleWindowResize);
		},
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

@media (min-width: 576px) {
	.container-width {
		padding: 24px 64px 0 64px;
	}
	.card-request {
		padding: 0 24px;
	}
}

@media (max-width: 768px) {
	.aside-stepper {
		display: none;
	}
	.mobile-stepper {
		padding: 24px 0px;
		.mobile-step {
			display: flex;
			align-items: center;
			flex-direction: column;
		}
		.mobile-step-prev {
			color: $green;
			font-weight: 500;
		}
		.mobile-step-current {
			color: $black;
			font-weight: 700;
		}
		.mobile-step-next {
			color: $grey;
			font-weight: 500;
		}
		.mobile-icon-prev {
			background: $green;
		}
		.mobile-icon-current {
			background: $black;
		}
		.mobile-icon-next {
			background: $grey;
		}
		.mobile-icon {
			width: 32px;
			height: 32px;
			color: $white;
			border-radius: 100%;
			padding: 5px 12px;
			margin-bottom: 6px;
		}
	}
}

@media (min-width: 768px) {
	.mobile-stepper {
		display: none;
	}
}

.aside-stepper {
	width: 200px;
}

.request-name {
	height: 42px;
	font-size: 20px;
	font-weight: 500;
	color: #000;
	text-transform: capitalize;
	line-height: 2;
}
.right-stepper {
	height: 360px;
}
.left-stepper {
	width: 100%;
}
.continue-button {
	padding-top: 24px;
}

.lds-ellipsis {
	margin: 0 auto;
	display: block;
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