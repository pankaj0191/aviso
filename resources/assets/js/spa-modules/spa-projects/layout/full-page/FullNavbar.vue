<template>
	<el-container class="main-container">
		<el-header>
			<div class="nav-right" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
				<ul class="nav-left">
					<li class="nav-left-item">
						<router-link :to="{name: 'projects'}" tag="div">
							<div class="nav-dashboard-profile">
								<i class="menu-icon el-icon-menu"></i>
								<span class="menu-text hidden-xs-only">
									<i class="el-icon-arrow-left"></i>
									DASHBOARD
								</span>
							</div>
						</router-link>
					</li>
				</ul>
			</div>
			<div class="nav-center">
				<transition name="el-zoom-in-center">
					<input-search :searchAble="searchAble" :projects="projects" />
				</transition>
			</div>
			<ul class="nav-left">
				<li class="nav-left-item">
					<i
						v-if="projects && projects.length > 0"
						class="el-icon-search icon"
						@click="searchAble = !searchAble"
					></i>
				</li>
				<li class="nav-left-item" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
					<nav-apps :isBothRole="isBothRole" :currentRole="currentRole" />
				</li>
				<li
					class="nav-left-item notification"
					v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)"
				>
					<notification :user="user" />
				</li>
				<li class="nav-left-item" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
					<profile-menu />
				</li>
			</ul>
		</el-header>
		<el-main>
			<router-view></router-view>
		</el-main>
	</el-container>
</template>

<script>
	import { mapGetters } from "vuex";
	import ProfileMenu from "../../v3/layouts/nav/components/ProfileMenu.vue";
	import Notification from "../../v3/layouts/nav/components/Notification.vue";
	import NavApps from "../../v3/layouts/nav/components/NavApps.vue";
	import InputSearch from "../../v3/layouts/nav/components/InputSearch.vue";

	export default {
		components: { ProfileMenu, Notification, NavApps, InputSearch },
		data() {
			return {
				isCollapse: false,
				impersonator: null,
				searchAble: false,
				search: ""
			};
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
			},
			...mapGetters([
				"currentRole",
				"isBothRole",
				"user",
				"nova",
				"spark",
				"support",
				"isFreelancer",
				"isSubscribed",
				"windowBreakPoint",
				"projects"
			]),
			windowWidth() {
				return this.$store.state.users.windowWidth;
			}
		},
		methods: {
			handleWindowResize() {
				this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
			},
			toggleCollapse() {
				this.isCollapse = !this.isCollapse;
				this.$emit("isCollapse", this.isCollapse);
			},
			impersonatorCheck() {
				axios
					.get("/impersonator-check")
					.then(response => {
						if (response.data > 0) {
							this.impersonator = true;
						} else {
							this.impersonator = false;
						}
					})
					.catch(error => {
						console.log(error);
						// this.handle_error(error);
					});
			}
		},
		created() {
			this.impersonatorCheck();
			window.addEventListener("resize", this.handleWindowResize);
		},
		destroyed() {
			window.removeEventListener("resize", this.handleWindowResize);
		},
		mounted() {
			$(".with-navbar").css("padding-top", "0");
			this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
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

@mixin transition($args...) {
	-webkit-transition: $args;
	-moz-transition: $args;
	-ms-transition: $args;
	-o-transition: $args;
	transition: $args;
}

.hideSidebar .main-container {
	margin-left: 54px;
}

.el-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	box-shadow: $box-shadow;
	background-color: #fff;
	.nav-dashboard-profile {
		display: flex;
		align-items: center;
		padding: 15px 0;
		cursor: pointer;

		.menu-icon {
			font-size: 30px;
			margin-right: 15px;
			@include transition(0.3s);
		}
		.menu-text {
			font-weight: 400;
			@include transition(0.3s);
		}
		&:hover {
			.menu-icon {
				font-weight: bold;
			}
			.menu-text {
				margin-left: -7px;
			}
		}
	}
	.nav-left {
		display: flex;
		align-items: center;
		height: 100%;
		margin: 0;
		.notification {
			margin-right: 12px;
		}
		.nav-left-item {
			padding: 0 8px;
			.navbar-user {
				display: flex;
				align-items: center;
				.user-details {
					text-align: right;
					display: block !important;
					line-height: 1.25 !important;
					.user-name {
						font-weight: 600 !important;
					}
				}
				.el-avatar {
					margin-left: 0.75rem !important;
					box-shadow: $box-shadow;
				}
			}
		}
		.notification {
			.el-dropdown-menu {
				padding: 0;
				margin: 0;
			}
		}
	}
}

.icon {
	font-size: 24px;
	cursor: pointer;
}

.bordered-items {
	.notification {
		display: flex;
		justify-content: space-between;
		padding: 16px;
		cursor: pointer;
		.notification-content {
			display: flex;
			align-self: start;
			.feather-icon {
				user-select: none;
				.feather-message-square {
					margin-right: 4px;
					color: $green;
				}
			}
			.notification-text {
				margin: 0 6px;
				.notification-title {
					color: $green;
					font-weight: 500;
					display: block;
				}
			}
		}
		.notification-ago {
			margin-top: 4px;
			white-space: nowrap;
		}
	}
}

ul.bordered-items > li:not(:last-of-type):not([class*="shadow"]) {
	border-bottom: 1px solid #dae1e7;
}
.notification-dropdown {
	padding: 0;
	.notification:hover {
		background-color: #f7f7f7;
	}
}
.menu-row {
	padding: 16px;
	.menu-content {
		.router-link-exact-active {
			color: $green;
		}
		cursor: pointer;
		margin-bottom: 16px;
		text-align: center;
		.menu-icon {
			font-size: 48px;
		}
		.title {
			font-weight: 500;
			font-size: 14px;
		}
	}
}
.dropdown-link {
	color: #606266;
}
.dropdown-link:hover {
	color: $green;
}
.el-dropdown-menu__item:hover .dropdown-link {
	color: $green;
	text-decoration: none;
}
.primary {
	color: $green;
}
.nav-center {
	width: 50%;
}
@media only screen and (max-width: 576px) {
	.nav-center {
		width: 90%;
	}
}

@media only screen and (max-width: 1200px) and (min-width: 577px) {
	.nav-center {
		width: 40%;
	}
}
</style>