<template>
	<el-container class="main-container">
		<el-header>
			<div class="nav-right" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
				<ul class="nav-left">
					<li>
						<el-tooltip class="item" effect="dark" content="Minimize Menu">
							<i class="el-icon-s-fold icon hidden-xs-only" @click="toggleCollapse"></i>
						</el-tooltip>
					</li>
					<li class="nav-left-item">
						<router-link
							class="dropdown-link"
							tag="a"
							:to="{name: 'create-project', params: {role: 'project'}}"
							v-if="isFreelancer && user"
						>
							<el-tooltip class="item" effect="dark" content="Add New Project">
								<i class="el-icon-circle-plus-outline icon"></i>
							</el-tooltip>
						</router-link>

						<router-link
							class="dropdown-link"
							tag="a"
							:to="{name: 'create-request'}"
							v-if="!isFreelancer && user"
						>
							<el-tooltip class="item" effect="dark" content="Add New Design Request">
								<i class="el-icon-circle-plus-outline icon"></i>
							</el-tooltip>
						</router-link>

						<!-- <el-dropdown trigger="click" placement="bottom-start" @command="handleCommandCreateProject">
							<span class="el-dropdown-link">
								<i class="el-icon-circle-plus-outline icon"></i>
							</span>
							<el-dropdown-menu slot="dropdown">
								<el-dropdown-item command="design">
									<i class="fa fa-globe"></i>
									Graphic Design
								</el-dropdown-item>
								<el-dropdown-item command="website">
									<i class="el-icon-picture-outline"></i>
									Website
								</el-dropdown-item>
								<el-dropdown-item command="video" disabled>
									<i class="el-icon-video-camera"></i>
									Videography
								</el-dropdown-item>
							</el-dropdown-menu>
						</el-dropdown>-->
					</li>
				</ul>
			</div>
			<div class="nav-center">
				<transition name="el-zoom-in-center">
					<el-autocomplete
						:autofocus="true"
						popper-class="my-autocomplete"
						style="width:100%"
						v-if="searchAble"
						placeholder="Type something"
						:fetch-suggestions="querySearch"
						:trigger-on-focus="false"
						@select="handleSearchSelect"
						prefix-icon="el-icon-search"
						v-model="search"
						size="small"
					>
						<template slot-scope="{ item }">
							<div class="autocomplete-item">
								<el-avatar
									v-if="item.active_revision.proofs.length > 0"
									shape="square"
									size="large"
									:src="`${spacePrefix}/` + item.active_revision.proofs[0].project_files.path"
								></el-avatar>
								<el-avatar
									v-else
									shape="square"
									size="large"
									src="https://cube.elemecdn.com/9/c2/f0ee8a3c7c9638a54940382568c9dpng.png"
								></el-avatar>
								<div class="autocomplete-content">
									<div class="project-name">{{ item.name }}</div>
									<div class="footer-content">
										<div class="project-company">{{ item.company }}</div>
										<div class="project-company">{{projectDate(item.created_at)}}</div>
									</div>
								</div>
							</div>
						</template>
					</el-autocomplete>
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
					<el-dropdown trigger="click">
						<span class="el-dropdown-link">
							<el-tooltip class="item" effect="dark" content="Prooflo Apps">
								<i class="el-icon-s-grid icon"></i>
							</el-tooltip>
						</span>
						<el-dropdown-menu style="width: 348px;" slot="dropdown">
							<el-row class="menu-row">
								<el-col :span="8" class="menu-content">
									<router-link tag="div" :to="{ name: 'time-trackers'}">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round"
											class="feather feather-play-circle"
										>
											<circle cx="12" cy="12" r="10" />
											<polygon points="10 8 16 12 10 16 10 8" />
										</svg>
										<div class="title">Time Tracker</div>
									</router-link>
								</el-col>
								<el-col :span="8" class="menu-content">
									<router-link tag="div" :to="{ name: 'calender'}">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round"
											class="feather feather-calendar"
										>
											<rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
											<line x1="16" y1="2" x2="16" y2="6" />
											<line x1="8" y1="2" x2="8" y2="6" />
											<line x1="3" y1="10" x2="21" y2="10" />
										</svg>
										<div class="title">Calender</div>
									</router-link>
								</el-col>
								<el-col :span="8" class="menu-content">
									<router-link tag="div" :to="{ name: 'drive'}">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round"
											class="feather feather-hard-drive"
										>
											<line x1="22" y1="12" x2="2" y2="12" />
											<path
												d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"
											/>
											<line x1="6" y1="16" x2="6.01" y2="16" />
											<line x1="10" y1="16" x2="10.01" y2="16" />
										</svg>
										<div class="title">Drive</div>
									</router-link>
								</el-col>
								<el-col :span="8" class="menu-content">
									<router-link tag="div" :to="{ name: 'teams'}">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round"
											class="feather feather-users"
										>
											<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
											<circle cx="9" cy="7" r="4" />
											<path d="M23 21v-2a4 4 0 0 0-3-3.87" />
											<path d="M16 3.13a4 4 0 0 1 0 7.75" />
										</svg>
										<div class="title">Team</div>
									</router-link>
								</el-col>
								<el-col :span="8" class="menu-content">
									<router-link tag="div" :to="{ name: 'clients'}">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round"
											class="feather feather-user"
										>
											<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
											<circle cx="12" cy="7" r="4" />
										</svg>
										<div class="title" v-if="isFreelancer">Clients</div>
										<div class="title" v-else>Freelancers</div>
									</router-link>
								</el-col>
								<el-col :span="8" class="menu-content">
									<router-link tag="div" :to="{ name: 'invoices'}">
										<svg
											xmlns="http://www.w3.org/2000/svg"
											width="48"
											height="48"
											viewBox="0 0 24 24"
											fill="none"
											stroke="currentColor"
											stroke-width="1.5"
											stroke-linecap="round"
											stroke-linejoin="round"
											class="feather feather-file-text"
										>
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
											<polyline points="14 2 14 8 20 8" />
											<line x1="16" y1="13" x2="8" y2="13" />
											<line x1="16" y1="17" x2="8" y2="17" />
											<polyline points="10 9 9 9 8 9" />
										</svg>
										<div class="title">Invoices</div>
									</router-link>
								</el-col>
							</el-row>
						</el-dropdown-menu>
					</el-dropdown>
				</li>
				<li
					class="nav-left-item notification"
					v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)"
				>
					<el-dropdown trigger="click">
						<span class="el-dropdown-link">
							<el-badge
								v-if="notifications && hasUnreadNotificationsCount > 0"
								:value="hasUnreadNotificationsCount"
								:max="99"
								class="item"
							>
								<i class="el-icon-bell icon" @click="markNotificationsAsRead"></i>
							</el-badge>
							<i class="el-icon-bell icon" v-else></i>
						</span>
						<el-dropdown-menu style="    width: 365px;" slot="dropdown" class="notification-dropdown">
							<div
								class="infinite-list"
								style="overflow:auto;max-height:300px;"
								v-infinite-scroll="loadMore"
								infinite-scroll-disabled="disabled"
							>
								<ul
									class="bordered-items"
									v-if="notifications && notifications.notifications.data.length > 0"
								>
									<router-link
										tag="li"
										:class="['notification', (notification.read == 2 && 'notification-read' )]"
										v-for="(notification,key) in notifications.notifications.data"
										:key="key"
										:to="`/${notification.action_url}`"
										@click.native="markNotificationsAsReadOne(notification.id)"
									>
										<div class="notification-content">
											<span class="feather-icon">
												<i :class=" notification.icon"></i>
											</span>
											<div class="notification-text">
												<span class="notification-title">{{notification.project}}</span>
												<small>{{notification.body}}</small>
											</div>
										</div>
										<small class="notification-ago">{{notificationAgo(notification.created_at)}}</small>
									</router-link>
								</ul>
								<ul class="bordered-items" v-else>
									<li class="bordered-items">
										<div class="notification-content">
											<h4 class="text-center">You not have notifications</h4>
										</div>
									</li>
								</ul>
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
						</el-dropdown-menu>
					</el-dropdown>
				</li>
				<li class="nav-left-item" v-if="windowWidth >= 576 || (windowWidth <= 576 && !searchAble)">
					<el-dropdown trigger="click" @command="handleCommand">
						<span class="el-dropdown-link">
							<div class="navbar-user">
								<div class="user-details" v-if="user.name">
									<div class="hidden-sm-and-down">
										<div class="user-name">{{user.name}}</div>
										<small v-if="isFreelancer && isSubscribed">Freelancer (subscribed)</small>
										<small v-if="isFreelancer && !isSubscribed">
											Freelancer
											<a href="/settings#/subscription" class="dropdown-link primary">upgrade</a>
										</small>
										<small v-if="!isFreelancer">Client</small>
									</div>
								</div>
								<el-avatar
									v-if="!user.photo_url"
									src="https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png"
								></el-avatar>
								<el-avatar v-if="user.photo_url" :src="user.photo_url"></el-avatar>
							</div>
						</span>
						<el-dropdown-menu slot="dropdown">
							<el-dropdown-item command="profile" v-if="isFreelancer"  icon="el-icon-user">Profile</el-dropdown-item>
							<el-dropdown-item command="spark" v-if="spark" icon="el-icon-menu">Spark</el-dropdown-item>
							<el-dropdown-item  command="nova" v-if="nova" icon="el-icon-menu">Prooflo Admin</el-dropdown-item>
							<el-dropdown-item command="impersonator" v-if="impersonator" icon="fa fa-fw fa-btn fa-user-secret">Back To My Account</el-dropdown-item>
							<el-dropdown-item command="settings" icon="fa fa-fw fa-btn fa-cog">Your Settings</el-dropdown-item>
							<el-dropdown-item command="logout" divided icon="el-icon-circle-plus-outline">Logout</el-dropdown-item>
						</el-dropdown-menu>
					</el-dropdown>
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

	export default {
		data() {
			return {
				isCollapse: false,
				impersonator: null,
				searchAble: false,
				search: "",
				loadingNotifications: false,
				notifications: null,
				loading: false
			};
		},
		computed: {
			...mapGetters([
				"user",
				"nova",
				"spark",
				"support",
				"isFreelancer",
				"isSubscribed",
				"windowBreakPoint",
				"projects"
			]),
			/**
			 * Determine if the user has any unread notifications.
			 */
			hasUnreadNotifications() {
				if (this.notifications) {
					return (
						_.filter(
							this.notifications.notifications.data,
							notification => {
								return notification.read == 0;
							}
						).length > 0
					);
				}

				return false;
			},
			/**
			 * Determine if the user has any unread notifications.
			 */
			hasUnreadNotificationsCount() {
				if (this.notifications) {
					return _.filter(
						this.notifications.notifications.data,
						notification => {
							return notification.read == 0;
						}
					).length;
				}
				return 0;
			},
			windowWidth() {
				return this.$store.state.users.windowWidth;
			},
			spacePrefix() {
				return spacePrefix;
			},
			noMore() {
				if (this.notifications && this.notifications.notifications.data) {
					return (
						this.notifications.notifications.total <=
						this.notifications.notifications.data.length
					);
				}
			},
			disabled() {
				return this.loading || this.noMore;
            },
		},
		methods: {
            handleCommand(command) {
                console.log(command)
                switch (command) {
                    case 'profile':
                        return this.$router.push({name: 'profile', params: {username: this.user.current_profile_id}})
                        break;
                    case 'settings':
                        return window.location.href = '/settings'
                        break;
                    case 'spark':
                        return window.location.href = '/spark/kiosk'
                        break;
                    case 'nova':
                        return window.location.href = '/nova'
                        break;
                    case 'impersonator':
                        return window.location.href = '/spark/kiosk/users/stop-impersonating'
                        break;
                    case 'logout':
                        return window.location.href = '/logout'
                        break;
                }
            },
			async loadMore() {
				this.loading = true;
				let current_page = this.notifications.notifications.current_page;
				await axios
					.get(`/notifications/fetch`, {
						params: {
							page: current_page + 1
						}
					})
					.then(response => {
						this.notifications.notifications.current_page =
							response.data.notifications.current_page;
						this.notifications.notifications.next_page_url =
							response.data.notifications.next_page_url;
						this.notifications.notifications.data = [
							...this.notifications.notifications.data,
							...response.data.notifications.data
						];
						this.loading = false;
					})
					.catch(error => {
						console.log(error);
					});
			},
			projectDate(date) {
				if (!date) return "";
				var utc = moment.utc(date).toDate();
				return moment(utc)
					.local()
					.format("MMMM DD, YYYY");
			},
			querySearch(queryString, cb) {
				if (Object.entries(this.projects).length > 0) {
					var results = queryString
						? this.projects.filter(this.createFilter(queryString))
						: this.projects;
					// call callback function to return suggestions
					console.log(results);
					cb(results);
				}
			},
			createFilter(queryString) {
				return project => {
					return (
						project.name
							.toLowerCase()
							.indexOf(queryString.toLowerCase()) === 0
					);
				};
			},
			handleSearchSelect(project) {
				this.openProofer(project.id, project.active_revision.id, project);
			},
			openProofer(project_id, revision_id, project) {
				this.$router.push({
					name: "proofer",
					params: {
						project_id: project_id,
						revision_id: revision_id,
						project: project
					}
				});
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
			},
			/**
			 * Get the application notifications.
			 */
			getNotifications() {
				this.loadingNotifications = true;

				axios.get("/notifications/fetch").then(response => {
					this.notifications = response.data;
					this.loadingNotifications = false;
				});
			},
			/**
			 * Mark the current notifications as read.
			 */
			markNotificationsAsRead() {
				if (!this.hasUnreadNotifications) {
					return;
				}

				axios.put("/notifications/read", {
					notifications: _.map(
						this.notifications.notifications.data,
						"id"
					)
				});

				this.notifications.notifications.data.forEach(notification => {
					notification.read = 1;
				});
			},
			/**
			 * Mark the current notifications as read.
			 */
			markNotificationsAsReadOne(id) {
				let countNotifi =
					_.filter(
						this.notifications.notifications.data,
						notification => {
							return notification.read != 2 && notification.id == id;
						}
					).length > 0;
				if (!countNotifi) {
					return;
				}

				axios.put("/notifications/read-one", {
					notifications: id
				});

				this.notifications.notifications.data.forEach(notification => {
					if (notification.id == id) {
						notification.read = 2;
					}
				});
			},
			notificationAgo(data) {
				return moment.utc(data).fromNow();
			}
		},
		mounted() {
			Echo.channel("notifiction-realtime").listen(
				"NotificationRealTime",
				e => {
					if (this.user) {
						if (e.notification.user_id == this.user.id) {
							this.notifications.notifications.data.unshift(
								e.notification
							);
							this.$notify({
								title: e.notification.project,
								message: e.notification.body,
								iconClass: e.notification.icon,
								position: "bottom-left"
							});
						}
					}
				}
			);
		},
		created() {
			this.impersonatorCheck();
			this.getNotifications();
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

.hideSidebar .main-container {
	margin-left: 54px;
}

.main-container {
	min-height: 100%;
	-webkit-transition: margin-left 0.28s;
	transition: margin-left 0.28s;
	margin-left: 220px;
	position: relative;

	.el-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		box-shadow: $box-shadow;
		background-color: #fff;

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
				color: $green;
				font-size: 16px;
				margin-right: 4px;
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
		background-image: linear-gradient(
			rgba(29, 33, 41, 0.04),
			rgba(29, 33, 41, 0.04)
		);
	}

	.notification-read {
		background-color: #edf2fa;
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
<style lang="scss">
.my-autocomplete {
	li {
		line-height: normal;
		padding: 7px;

		.autocomplete-item {
			display: flex;
			align-items: center;

			.autocomplete-content {
				width: 90%;
				margin-left: 8px;

				.project-name {
					font-weight: 500;
					font-size: 16px;
					color: #1a202c;
				}

				.footer-content {
					display: flex;
					justify-content: space-between;
					align-items: center;

					.project-company {
						font-size: 12px;
						color: #a0aec0;
					}
				}
			}
		}
	}
}
</style>