<template>
	<div id="profile">
		<div v-if="!statusProfile" class="text-center">
			<div class="not-user">User not found</div>
			<router-link :to="{name: 'projects'}">
				<el-button>Back to home</el-button>
			</router-link>
		</div>
		<el-row :gutter="24" type="flex" justify="center" v-else>
			<el-col :xs="24" :sm="24" :md="16" :lg="12" :xl="12">
				<el-card class="profile-card">
					<div class="profile-main" v-if="!loading">
						<div class="profile-header">
							<div class="profile-avatar">
								<el-avatar v-if="userProfile.photo_url" :size="76" :src="`${userProfile.photo_url}`"></el-avatar>
								<el-avatar
									v-else
									:size="76"
									src="https://cube.elemecdn.com/9/c2/f0ee8a3c7c9638a54940382568c9dpng.png"
								></el-avatar>
							</div>
							<div class="profile-content">
								<div class="profile-name">{{userProfile.profiles.find(item => item.id == username).name}}</div>
								<div class="profile-email">
									<i class="el-icon-user"></i>
									{{userProfile.email}}
								</div>
							</div>
						</div>
						<template v-if="profile && profile.title">
							<div class="profile-description">
								<div
									class="profile-body"
									v-if="profile.profile_id == user.id && !showAs"
									@click="openDescEditDialog(profile)"
								>
									<div class="profile-body-title">{{profile.title}}</div>
									<p class="profile-body-pre-wrap">
										<span>{{more ? (profile.body.substring(0, 555) + '...') : profile.body}}</span>
										<el-link
											:underline="false"
											@click.stop="more = !more"
											type="primary"
										>{{more ? 'more' : 'less'}}</el-link>
									</p>
								</div>
								<div class="profile-body-not-user" v-else>
									<div class="profile-body-title">{{profile.title}}</div>
									<p class="profile-body-pre-wrap">
										<span>{{more ? (profile.body.substring(0, 555) + '...') : profile.body}}</span>
										<el-link
											:underline="false"
											@click.stop="more = !more"
											type="primary"
										>{{more ? 'more' : 'less'}}</el-link>
									</p>
								</div>
								<div class="description-edit" v-if="profile.profile_id == user.current_profile_id && !showAs">
									<el-button
										icon="el-icon-edit"
										@click.native="openDescEditDialog(profile)"
										size="small"
										type="primary"
										circle
									></el-button>
								</div>
							</div>
						</template>
						<div v-else>
							<div
								class="svg-profile-details"
								v-if="profile.profile_id == user.current_profile_id && !showAs"
								@click="openDescEditDialog(profile)"
							>
								<profile-details-svg />
							</div>
							<div class="svg-profile-details-not-user" v-else>
								<profile-details-svg />
							</div>
						</div>
						<hr />
						<div class="profile-footer">
							<ul class="profile-states">
								<li>
									<div class="states-number">
										<span class="hourly-number">${{profile && profile.hourly_rate ? profile.hourly_rate : 0}}</span>
										<div class="hourly-rate-edit" v-if="profile.profile_id == user.current_profile_id && !showAs">
											<el-button
												icon="el-icon-edit"
												@click.native="openHourlyDialog(profile)"
												size="small"
												type="primary"
												circle
											></el-button>
										</div>
									</div>
									<div class="states-name">Hourly rate</div>
								</li>
								<li>
									<!-- <div class="states-number">${{totalAmount()}}</div> -->
									<div class="states-number">$0</div>
									<div class="states-name">Total earned</div>
								</li>
								<li>
									<div class="states-number">{{totalProjects}}</div>
									<div class="states-name">Projects</div>
								</li>
								<li>
									<div class="states-number">{{hours() !== 0 ? hours() : minutes()}}</div>
									<div class="states-name">{{hours() !== 0 ? 'Hours' : 'Minutes'}} worked</div>
								</li>
							</ul>
						</div>
					</div>
					<div class="text-center" v-else>
						<div class="lds-ripple">
							<div></div>
							<div></div>
						</div>
					</div>
				</el-card>
				<el-card class="profile-card">
					<div slot="header" class="clearfix">
						<div class="feedback-header">
                            <div class="feedback-header-card">
							Work history and feedback
							<span
								v-if="userProfile.project_reviews && userProfile.project_reviews.filter(item => item.pivot.review_project == 1).length > 0"
							>({{userProfile.project_reviews.filter(item => item.pivot.review_project == 1).length}})</span>
						</div>
                        <div class="feedback-action" v-if="profile.profile_id == user.current_profile_id && !showAs">
                            <el-tooltip class="item" effect="dark" :content="`Review turn ${reviews ?'off':'on'}`" placement="top">
                                <el-switch @change="changeReview" v-model="reviews"></el-switch>
                            </el-tooltip>
                        </div>
                        </div>
					</div>
					<div class="profile-feedback" v-if="!loading">
						<div  v-for="(project, key) in userProfile.project_reviews" :key="key">
							<div class="feedback-project" v-if="project.pivot.review_project">
                                <div>
								<div class="feedback-header">
									<div class="feedback-title">{{project.name}}</div>
									<div class="rate-duration">
										<div class="feedback-rate">
											<el-rate
												v-model="project.rate"
												disabled
												show-score
												text-color="#ff9900"
												score-template="{value}"
											></el-rate>
										</div>
										<small
											class="feedback-duration"
										>{{defineDate(project.created_at)}} - {{defineDate(project.updated_at)}}</small>
									</div>
								</div>
								<div>
									<em class="pre-line-break">{{project.review}}</em>
								</div>
							</div>
							<div class="feedback-price">{{projectPrice(project)}}</div>
                            </div>
						</div>
					</div>
					<div class="text-center" v-else>
						<div class="lds-ripple">
							<div></div>
							<div></div>
						</div>
					</div>
				</el-card>
			</el-col>
			<el-col
				class="profile-right hidden-sm-and-down"
				:md="6"
				:lg="4"
				:xl="4"
				v-if="user.current_profile_id == profile.profile_id"
			>
				<router-link :to="{name: 'setting-profile'}" class="tw-text-primary">
					<el-button class="profile-settings">Profile Settings</el-button>
				</router-link>
				<div>
					<el-link
						:underline="false"
						v-if="!showAs"
						type="primary"
						@click="showAs = true"
					>View my profile as others see it</el-link>
					<el-link :underline="false" v-else type="primary" @click="showAs = false">Back to profile</el-link>
				</div>
			</el-col>
		</el-row>

		<!--Creative Brief Dialog-->
		<description @close="descriptionActive = false" :descriptionActive="descriptionActive" />
		<description-edit
			@close="descriptionEditActive = false"
			:data="description"
			:descriptionEditActive="descriptionEditActive"
		/>
		<hourly-update @close="hourlyActive = false" :data="profile" :hourlyActive="hourlyActive" />
	</div>
</template>

<script>
	import { mapGetters } from "vuex";
	import moment from "moment";
	import ProfileDetailsSvg from "./components/ProfileDetailsSvg";
	import Description from "./components/Description";
	import DescriptionEdit from "./components/DescriptionEdit";
	import HourlyUpdate from "./components/HourlyUpdate";
	export default {
		props: ["username"],
		components: {
			ProfileDetailsSvg,
			Description,
			DescriptionEdit,
			HourlyUpdate
		},
		data() {
			return {
				showAs: false,
				more: true,
				loading: false,
				descriptionActive: false,
				hourlyActive: false,
				descriptionEditActive: false,
				description: {},
                rate: 5.0,
                reviews: false
			};
        },
        watch: {
            reviewProjectNotify(val) {
                this.changeReviewVal()
            }
        },
		methods: {
            changeReviewVal() {
                this.reviews = this.reviewProjectNotify
            },
			projectPrice(data) {
				let total = [];

				Object.entries(data.project_timers).forEach(([key, val]) => {
					if (val.user_id == this.profile.id) {
						total.push(val.duration); // the value of the current key.
					}
				});

				let finalTotal = total.reduce(function(total, num) {
					return total + num;
				}, 0);

				return `$${(
					(finalTotal * data.pivot.hourly_rate) /
					60 /
					60
				).toFixed(2)}`;
			},
			defineDate(date) {
				if (!date) return "";
				var utc = moment.utc(date).toDate();
				return moment(utc)
					.local()
					.format("MMMM DD, YYYY");
			},
			openDescDialog() {
				this.descriptionActive = true;
			},
			openHourlyDialog() {
				this.hourlyActive = true;
			},
			openDescEditDialog(desc) {
				this.descriptionEditActive = true;
				this.description = desc;
			},
			fetch() {
				this.loading = true;
				this.$store.dispatch("profile", this.username).then(() => {
					this.loading = false;
				});
			},
			hours() {
				return moment.utc(this.hoursWorked * 1000).hours();
			},
			minutes() {
				return moment.utc(this.hoursWorked * 1000).minutes();
			},
			totalAmount() {
				if (!this.profile.hourly_rate) {
					return 0;
				}
				return (
					(this.hoursWorked * this.profile.hourly_rate) /
					60 /
					60
				).toFixed(2);
            },
            async changeReview(val) {
                try {
                    await this.$store.dispatch('updateNotificationReview', {review_project: val})
                this.$notify({
                            title: "Success",
							message: 'Review has been updated',
							type: "success"
                })
                } catch (error) {
                  console.log(error)  
                }
            }
		},
		computed: {
			...mapGetters([
				"profile",
				"userProfile",
				"user",
				"totalProjects",
				"hoursWorked",
                "statusProfile",
                'reviewProjectNotify'
			]),
			spacePrefix() {
				return spacePrefix;
			}
		},
		created() {
            // this.$store.dispatch('bootstrap');
            this.fetch();
            this.changeReviewVal()
		}
	};
</script>

<style lang="scss" scoped>
#profile {
	$white: #fafafa;
	$black: #545c64;
	$red: #ef5b5b;
	$green: #80b4a0;
	$grey: #c0c4cc;
	$border-color: rgba(0, 0, 0, 0.1);
	$box-shadow: 0 2px 12px 0 $border-color;
	.profile-main {
		.profile-header {
			display: flex;
			margin-bottom: 24px;
			.profile-content {
				margin-left: 12px;
				.profile-name {
					line-height: 27px;
					font-size: 21px;
					font-weight: 400;
				}
				.profile-email {
					color: grey;
				}
			}
		}
		.profile-description {
			position: relative;
			.profile-body,
			.profile-body-not-user {
				margin-right: 30px;
				.profile-body-title {
					line-height: 24px;
					font-size: 18px;
					font-weight: 700;
				}
				.profile-body-pre-wrap {
					white-space: pre-wrap;
					line-height: 21px;
					word-wrap: break-word;
					word-break: break-word;
					font-weight: 400;
					position: relative;
					padding: 16px 0;
				}
			}
			.description-edit {
				visibility: hidden;
				position: absolute;
				top: 0 !important;
				right: -6px !important;
				z-index: 1000;
			}
		}

		.profile-description:hover {
			.profile-body {
				background-color: #f9f9f9;
			}
			.description-edit {
				visibility: visible;
			}
		}

		.svg-profile-details {
			cursor: pointer;
		}
		.svg-profile-details-not-user,
		.svg-profile-details {
			display: flex;
			justify-content: center;
			margin: 24px 0;
		}
		.svg-profile-details:hover {
			background-color: #f9f9f9;
		}
		.profile-footer {
			.profile-states {
				li {
					width: 135px !important;
					min-width: 135px !important;
					max-width: none;
					margin-bottom: 0;
					display: inline-block;
					padding-left: 5px;
					padding-right: 5px;
					.states-number {
						position: relative;
						line-height: 24px;
						font-size: 17px;
						font-weight: 500;
						.hourly-rate-edit {
							display: none;
							position: absolute;
							right: 37px;
							top: -4px;
						}
					}
					.states-number:hover {
						.hourly-number {
							background-color: #f9f9f9;
							cursor: pointer;
						}
						.hourly-rate-edit {
							display: inline-block;
						}
					}
					.states-name {
						color: #656565 !important;
					}
				}
			}
		}
	}
	.profile-feedback {
		.feedback-project {
			display: flex;
			justify-content: space-between;
		}
		.feedback-header {
			margin-bottom: 8px;
			.feedback-title {
				font-weight: 700;
				font-size: 16px;
				margin-bottom: 4px;
			}
			.rate-duration {
				display: flex;
				.feedback-rate {
					margin-right: 6px;
				}
				.feedback-duration {
					color: $grey;
					margin-left: 6px;
				}
			}
		}
		.pre-line-break {
			white-space: pre-line !important;
			word-wrap: break-word;
			word-break: break-word;
			font-style: italic;
			color: #000;
		}
		.feedback-price {
			font-weight: 700;
		}
    }
    .feedback-header{
            display: flex;
    justify-content: space-between;
    align-items: center;
	.feedback-header-card {
		line-height: 27px;
		font-size: 21px;
		font-weight: 400;
	}
    }
	.profile-right {
		.profile-settings {
			margin-bottom: 16px;
		}
	}
	.lds-ripple {
		display: inline-block;
		position: relative;
		width: 80px;
		height: 80px;
	}
	.lds-ripple div {
		position: absolute;
		border: 4px solid $green;
		opacity: 1;
		border-radius: 50%;
		animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;
	}
	.lds-ripple div:nth-child(2) {
		animation-delay: -0.5s;
	}
	@keyframes lds-ripple {
		0% {
			top: 36px;
			left: 36px;
			width: 0;
			height: 0;
			opacity: 1;
		}
		100% {
			top: 0px;
			left: 0px;
			width: 72px;
			height: 72px;
			opacity: 0;
		}
	}
	.not-user {
		font-size: 24px;
		font-weight: 500;
	}
}
</style>
<style lang="scss">
#profile {
	.profile-card {
		.el-card__body {
			padding: 24px;
		}
		margin-bottom: 24px;
	}
	.hourly-rate-edit {
		.el-button--small.is-circle {
			padding: 6px;
		}
	}
	.profile-desc-dialog {
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
	.feedback-rate {
		.el-rate__item {
			.el-rate__icon {
				margin-right: 0px !important;
			}
		}
		.el-rate__text {
			margin-left: 4px;
			font-weight: 500;
			color: #000 !important;
		}
	}
}
</style>