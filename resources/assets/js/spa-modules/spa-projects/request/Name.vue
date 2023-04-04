<template>
	<div class="text-center">
		<div class="el-input el-input--suffix">
			<input
				type="text"
				v-model="name"
				autocomplete="off"
				placeholder="* Name Your Request"
				class="el-input__inner"
			/>
		</div>
		<div class="prooflo-tip">
			<span class="tip">Prooflo Tip:</span> Include a design type in your name Like Brochure, Flyer, Facebook ad, etc.
		</div>
		<div class="prooflo-tip">
			<span class="example">Example:</span> Baseball Event Brochure
		</div>
		<div class="your-freelancer">
			<div v-if="freelancer">
				Your Freelancer is
				<span
					class="freelancer-name"
					@click="selectFreelancerShow = !selectFreelancerShow"
				>{{freelancer.name}}</span>
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
									v-model="freelancer"
									:label="item"
								>{{item.name}}</el-radio>
							</el-col>
						</el-row>
					</div>
				</transition>
			</div>
		</div>
		<div v-if="name.length > 4 && freelancer">
			<el-button type="primary" size="small" :loading="saveLoading" @click="updateProject()">Continue</el-button>
		</div>
	</div>
</template>

<script>
	import { mapGetters } from "vuex";

	export default {
		name: "Name",
		props: ["project", "freelancers"],
		data() {
			return {
				name: "",
				freelancer: "",
				haveFreelancer: false,
				selectFreelancerShow: false,
				project_id: "",
				revision_id: "",
				user_id: "",
				loading: false,
				saveLoading: false
			};
		},
		watch: {
			isBothRole() {
				if (this.isBothRole) {
					return this.$router.push({name: "projects"});
				} else {
					return false;
				}
			}
		},
		computed: {
			...mapGetters(["isBothRole"]),
			isClientRole() {
				if (this.isBothRole) {
					return this.$router.push({name: "projects"});
				} else {
					return false;
				}
			}
		},
		methods: {
			initValues() {
				if (Object.entries(this.project).length === 0) {
					return;
				} else {
					let { id, name, user_role_freelancer } = JSON.parse(
						JSON.stringify(this.project)
					);

                    this.name = name;
                    if (Object.values(this.freelancers).length > 0) {
                        this.freelancer = Object.values(this.freelancers).find(item => {
                            return item.id == user_role_freelancer[0].id;
                        });
                    }
				}
			},
			updateProject() {
				this.saveLoading = true;
				const data = {
					name: this.name,
					project_id: this.project.id,
					company: this.freelancer.company,
					email: this.freelancer.email,
					client_name: this.freelancer.name
				};
				this.saveLoading = true;
				axios
					.post("/api/request/name", data)
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
			this.initValues();
		},
		mounted() {
			$(".with-navbar").css("padding-top", "0");
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

.request-name {
	height: 42px;
	background: white;
	font-size: 20px;
	font-weight: 500;
	color: #000;
	text-transform: capitalize;
	line-height: 2;
}
.request-card {
	text-align: center;
	margin: 0 auto;
	margin-top: 24px;
}

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

.prooflo-tip {
	padding: 16px 0 0 0;
	width: 70%;
	margin: 0 auto;
	color: #a0a0a0;
	.tip {
		font-weight: 700;
		color: #81b4a1;
	}
	.example {
		font-weight: 700;
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

.jump-page {
	cursor: pointer;
	text-transform: capitalize;
	margin-top: 16px;
	font-weight: 600;
	font-size: 14px;
	color: $grey;
}

.lds-ellipsis {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 60px;
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