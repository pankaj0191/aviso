<template>
	<div id="simple-calendar-app">
		<el-card class="no-scroll-content">
			<calendar-view
				ref="calendar"
				:displayPeriodUom="calendarView"
				:show-date="showDate"
				:events="simpleCalendarEvents"
				enableDragDrop
				:eventTop="windowWidth <= 400 ? '2rem' : '3rem'"
				eventBorderHeight="0px"
				eventContentHeight="1.65rem"
				class="theme-default"
				@click-date="openAddNewEvent"
				@click-event="openEditEvent"
				@drop-on-date="eventDragged"
			>
				<div slot="header" class="calender-header">
					<el-row class="row-header">
						<!-- Month Name -->
						<el-col :md="8" class="add-side">
							<!-- Add new event button -->
							<el-button
								type="primary"
								icon="el-icon-plus"
								size="small"
								@click="promptAddNewEvent(new Date())"
							>Add</el-button>
						</el-col>
						<el-col :md="8" class="current-month">
							<span @click="updateMonth(-1)" class="chevron">
								<svg
									xmlns="http://www.w3.org/2000/svg"
									width="24px"
									height="24px"
									viewBox="0 0 24 24"
									fill="none"
									stroke="currentColor"
									stroke-width="2"
									stroke-linecap="round"
									stroke-linejoin="round"
									class="feather-chevron"
								>
									<polyline points="15 18 9 12 15 6" />
								</svg>
							</span>
							<span class="current-month-text">{{ showDate | month }}</span>
							<span @click="updateMonth(1)" class="chevron">
								<svg
									xmlns="http://www.w3.org/2000/svg"
									width="24px"
									height="24px"
									viewBox="0 0 24 24"
									fill="none"
									stroke="currentColor"
									stroke-width="2"
									stroke-linecap="round"
									stroke-linejoin="round"
									class="feather-chevron"
								>
									<polyline points="9 18 15 12 9 6" />
								</svg>
							</span>
						</el-col>
						<el-col :md="8" class="current-view">
							<template v-for="(view, index) in calendarViewTypes">
								<el-button
									v-if="calendarView === view.val"
									:key="String(view.val) + 'plain'"
									type="plain"
									@click="calendarView = view.val"
								>{{ view.label }}</el-button>
								<el-button
									v-else
									:key="String(view.val) + 'border'"
									type="primary"
									@click="calendarView = view.val"
								>{{ view.label }}</el-button>
							</template>
						</el-col>
					</el-row>
					<el-row class="label-row">
						<el-col class="lable-col">
							<!-- Labels -->
							<div class="lable-display">
								<div v-for="(label, index) in calendarLabels" :key="index" class="label-content">
									<div class="label-icon" :class="'bg-label-' + label.color"></div>
									<span>{{ label.text }}</span>
								</div>
								<div class="flex items-center mr-4 mb-2">
									<div class="label-icon"></div>
									<span>None</span>
								</div>
							</div>
						</el-col>
					</el-row>
				</div>
			</calendar-view>
		</el-card>

		<!-- ADD EVENT -->
		<el-dialog
			:visible.sync="activePromptAddEvent"
			:show-close="false"
			width="355px"
			class="add-event"
		>
			<span slot="title" class="display-title">
				<div style="display:flex">
					<div class="title-icon">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="24"
							height="24"
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
					</div>
					<span class="title">Add Event</span>
				</div>
				<div>
					<div
						v-if="labelLocal != 'none'"
						class="label-icon-adv"
						:class="'bg-label-' + labelColor(labelLocal)"
					></div>
					<span style="text-transform: capitalize;">{{ labelLocal }}</span>
				</div>
			</span>
			<div class="event-form">
				<div class="form-group">
					<el-select
						@change="changeProject"
						v-model="projectID"
						clearable
						placeholder="Select"
						style="width:100%"
					>
						<el-option v-for="item in projects" :key="item.id" :label="item.name" :value="item.id"></el-option>
					</el-select>
				</div>
				<div class="form-group">
					<small class="date-label">Start Date</small>
					<el-date-picker
						value-format="yyyy-MM-dd"
						style="width:100%"
						:picker-options="startPickerOptions"
						:disabled="disabledFrom"
						v-model="startDate"
						type="date"
						placeholder="Start Date"
					></el-date-picker>
				</div>
				<div class="form-group">
					<small class="date-label">End Date</small>
					<el-date-picker
						value-format="yyyy-MM-dd"
						style="width:100%"
						:picker-options="pickerOptions"
						v-model="endDate"
						type="date"
						placeholder="End Date"
					></el-date-picker>
				</div>
			</div>
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">
					<el-button type="primary" class="canacel-creative" @click="addEvent">Add Event</el-button>
					<el-button class="canacel-creative" @click="activePromptAddEvent = false">Close</el-button>
				</div>
			</span>
		</el-dialog>

		<!-- EDIT EVENT -->
		<el-dialog
			:visible.sync="activePromptEditEvent"
			:show-close="false"
			width="355px"
			class="add-event"
		>
			<span slot="title" class="display-title">
				<div style="display:flex">
					<div class="title-icon">
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="24"
							height="24"
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
					</div>
					<span class="title">Edit Event</span>
				</div>
				<div>
					<div
						v-if="labelLocal != 'none'"
						class="label-icon-adv"
						:class="'bg-label-' + labelColor(labelLocal)"
					></div>
					<span style="text-transform: capitalize;">{{ labelLocal }}</span>
				</div>
			</span>
			<div class="event-form">
				<small class="date-label">Project Name</small>
				<div class="form-group">
					<el-select v-model="projectID" clearable placeholder="Select" style="width:100%">
						<el-option v-for="item in projects" :key="item.id" :label="item.name" :value="item.id"></el-option>
					</el-select>
				</div>
				<div class="form-group">
					<small class="date-label">Start Date</small>
					<el-date-picker
						value-format="yyyy-MM-dd"
						style="width:100%"
						:picker-options="startPickerOptions"
						v-model="startDate"
						type="date"
						placeholder="Start Date"
					></el-date-picker>
				</div>
				<div class="form-group">
					<small class="date-label">End Date</small>
					<el-date-picker
						value-format="yyyy-MM-dd"
						style="width:100%"
						:picker-options="pickerOptions"
						v-model="endDate"
						type="date"
						placeholder="End Date"
					></el-date-picker>
				</div>
			</div>
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">
					<el-button type="primary" class="canacel-creative" @click="editEvent">Update</el-button>
					<el-button class="canacel-creative" @click="removeEvent">Remove</el-button>
					<el-button class="canacel-creative" @click="cancel">Close</el-button>
				</div>
			</span>
		</el-dialog>
	</div>
</template>

<script>
	import { CalendarView, CalendarViewHeader } from "vue-simple-calendar";
	require("vue-simple-calendar/static/css/default.css");
	import { mapGetters } from "vuex";

	export default {
		components: {
			CalendarView,
			CalendarViewHeader,
		},
		data() {
			return {
				startPickerOptions: {
					disabledDate: this.disabledStartDueDate,
					shortcuts: [
						{
							text: "Today",
							onClick(picker) {
								picker.$emit("pick", new Date());
							},
						},
						{
							text: "Yesterday",
							onClick(picker) {
								const date = new Date();
								date.setTime(date.getTime() - 3600 * 1000 * 24);
								picker.$emit("pick", date);
							},
						},
						{
							text: "A week ago",
							onClick(picker) {
								const date = new Date();
								date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
								picker.$emit("pick", date);
							},
						},
					],
				},
				pickerOptions: {
					disabledDate: this.disabledDueDate,
					shortcuts: [
						{
							text: "Today",
							onClick(picker) {
								picker.$emit("pick", new Date());
							},
						},
						{
							text: "Yesterday",
							onClick(picker) {
								const date = new Date();
								date.setTime(date.getTime() - 3600 * 1000 * 24);
								picker.$emit("pick", date);
							},
						},
						{
							text: "A week ago",
							onClick(picker) {
								const date = new Date();
								date.setTime(date.getTime() - 3600 * 1000 * 24 * 7);
								picker.$emit("pick", date);
							},
						},
					],
				},
				showDate: new Date(),
				disabledFrom: false,
				id: 0,
				projectID: "",
				startDate: "",
				endDate: "",
				labelLocal: "none",

				// langHe: he,
				// langEn: en,

				url: "",
				calendarView: "month",

				activePromptAddEvent: false,
				activePromptEditEvent: false,

				calendarViewTypes: [
					{
						label: "Month",
						val: "month",
					},
					{
						label: "Week",
						val: "week",
					},
					{
						label: "Year",
						val: "year",
					},
				],
			};
		},
		computed: {
			simpleCalendarEvents() {
				let projects = [];

				if (this.projects && this.projects.length > 0) {
					projects = this.projects.filter((item) => {
						return item.start != null && item.end != null;
					});
				}
				return projects.map(
					({ id, name, start, end, website_url, type }) => ({
						id,
						title: name,
						startDate: start,
						endDate: end,
						url: website_url,
						classes: `event-${this.labelColor(type)}`,
						label: type,
					})
				);
			},
			validForm() {
				return (
					this.title !== "" &&
					this.startDate !== "" &&
					this.endDate !== "" &&
					Date.parse(this.endDate) - Date.parse(this.startDate) >= 0 &&
					!this.errors.has("event-url")
				);
			},
			disabledDatesTo() {
				return {
					to: new Date(this.startDate),
				};
			},
			disabledDatesFrom() {
				return {
					from: new Date(this.endDate),
				};
			},
			calendarLabels() {
				return [
					{
						text: "Website",
						value: "website",
						color: "success",
					},
					{
						text: "Design",
						value: "design",
						color: "warning",
					},
					{
						text: "Video",
						value: "video",
						color: "danger",
					},
				];
			},
			labelColor() {
				return (label) => {
					if (label === "website") return "success";
					else if (label === "design") return "warning";
					else if (label === "video") return "danger";
					else if (label === "none") return "primary";
				};
			},
			windowWidth() {
				return this.$store.state.users.windowWidth;
			},
			...mapGetters(["projects", "isFreelancer", "isSubscribed"]),
		},
		methods: {
			changeProject() {
				this.labelLocal = this.projects.find(
					(item) => item.id == this.projectID
				).type;
			},
			disabledDueDate(time) {
				return moment(time.getTime()).format("YYYY-MM-DD") < this.startDate;
			},
			disabledStartDueDate(time) {
				return moment(time.getTime()).format("YYYY-MM-DD") > this.endDate;
			},
			addEvent() {
				this.saveLoading = true;
				const data = {
					start: this.startDate,
					end: this.endDate,
				};
				axios
					.post(`/api/calender/${this.projectID}`, data)
					.then((response) => {
						this.saveLoading = false;
						this.activePromptAddEvent = false;
						this.$store.dispatch("loadProjects");
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch((error) => {
						this.saveLoading = false;
						console.log(error);
					});
			},
			updateMonth(val) {
				this.showDate = this.$refs.calendar.getIncrementedPeriod(val);
			},
			clearFields() {
				this.title = this.endDate = this.url = "";
				this.id = 0;
				this.labelLocal = "none";
			},
			promptAddNewEvent(date) {
				this.disabledFrom = false;
				this.addNewEventDialog(date);
			},
			addNewEventDialog(date) {
				this.clearFields();
				this.startDate = moment(date).format("YYYY-MM-DD");
				this.endDate = moment(date).format("YYYY-MM-DD");
				this.activePromptAddEvent = true;
			},
			openAddNewEvent(date) {
				this.disabledFrom = true;
				this.addNewEventDialog(date);
			},
			openEditEvent(event) {
				const e = this.projects.find((item) => item.id == event.id);
				this.id = e.id;
				this.projectID = e.id;
				this.startDate = e.start;
				this.endDate = e.end;
				this.url = e.website_url;
				this.labelLocal = e.type;
				this.activePromptEditEvent = true;
			},
			editEvent() {
				this.saveLoading = true;
				const data = {
					start: this.startDate,
					end: this.endDate,
				};
				axios
					.post(`/api/calender/${this.projectID}`, data)
					.then((response) => {
						this.saveLoading = false;
						this.activePromptEditEvent = false;
						this.$store.dispatch("loadProjects");
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch((error) => {
						this.saveLoading = false;
						console.log(error);
					});
			},
			async removeEvent() {
				await axios
					.post(`/api/calender/remove/${this.projectID}`)
					.then((response) => {
						this.saveLoading = false;
						this.activePromptEditEvent = false;
						this.$store.dispatch("loadProjects");
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch((error) => {
						this.saveLoading = false;
						console.log(error);
					});
			},
			cancel() {
				this.activePromptEditEvent = false;
			},
			async eventDragged(event, date) {
				const draggedDateStart = new Date(date);

				const eventStartDate = new Date(event.startDate).getTime();
				const eventEndDate = new Date(event.endDate).getTime();

				const diff = draggedDateStart - eventStartDate;
				const newEndDate = new Date(eventEndDate + diff);
				console.log(draggedDateStart, newEndDate);

				const data = {
					start: moment(draggedDateStart).format("YYYY-MM-DD"),
					end: moment(newEndDate).format("YYYY-MM-DD"),
				};
				await axios
					.post(`/api/calender/${event.id}`, data)
					.then((response) => {
						this.$store.dispatch("loadProjects");
						if (response.data.status == 1) {
							toastr["success"](response.data.message, "Success");
						} else {
							toastr["error"](response.data.errors[0], "Error");
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},
			handleWindowResize() {
				this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
			},
		},
		mounted() {
			this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);
		},
		created() {
			// this.$store.registerModule("calendar", moduleCalendar);
			// this.$store.dispatch("calendar/fetchEvents");
			// this.$store.dispatch("calendar/fetchEventLabels");
			window.addEventListener("resize", this.handleWindowResize);
		},
		beforeDestroy() {
			// this.$store.unregisterModule("calendar");
			window.addEventListener("resize", this.handleWindowResize);
		},
	};
</script>

<style lang="scss">
@import "../../../sass/apps/simple-calendar.scss";
</style>
<style lang="scss" scoped>
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$success: #28c76f;
$warning: #f2a731;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;

.calender-header {
	margin-bottom: 16px;

	.row-header {
		display: flex;

		.add-side {
			display: flex;
			align-items: center;
		}

		.current-month {
			order: 999;
			display: flex;
			justify-content: flex-end;
			align-items: center;

			.current-month-text {
				font-weight: 500;
				font-size: 20px;
				margin-left: 0.75rem;
				margin-right: 0.75rem;
				white-space: nowrap;
			}

			.chevron {
				cursor: pointer;
				display: inline-flex;
				align-items: center;
				background: $green;
				color: $white;
				border-radius: 9999px;
				user-select: none;
				position: relative;

				.feather-chevron {
					width: 1.25rem;
					height: 1.25rem;
					margin: 4px;
				}
			}
		}

		.current-view {
			display: flex;
			justify-content: center;
		}
	}

	.label-row {
		display: flex;
		margin-top: 16px;

		.lable-col {
			display: flex;

			.lable-display {
				display: flex;
				flex-wrap: wrap;
				justify-content: flex;

				.label-content {
					display: flex;
					align-items: center;
					margin-right: 16px;
					margin-bottom: 8px;

					.label-icon {
						width: 0.75rem;
						height: 0.75rem;
						margin-right: 8px;
						border-radius: 9999px;
						display: inline-block;
					}
				}
			}
		}
	}
}

.label-icon-adv {
	width: 0.75rem;
	height: 0.75rem;
	margin-right: 4px;
	border-radius: 9999px;
	display: inline-block;
}
.bg-label-success {
	background-color: $success;
}

.bg-label-warning {
	background-color: $warning;
}

.bg-label-danger {
	background-color: $red;
}

.add-event {
	.el-dialog {
		box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
			0 10px 10px -5px rgba(0, 0, 0, 0.04);
		border-radius: 8px;

		.display-title {
			display: flex;
			align-items: center;
			justify-content: space-between;

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
				display: flex;
				align-items: center;
			}
		}

		.el-dialog__body {
			padding: 30px 32px;
		}

		.el-dialog__footer {
			background-color: #f9fafb;
			border-radius: 8px;
		}
	}
}

.canacel-creative {
	border-radius: 6px;
	border: none;
	box-shadow: 0px 2px 4px 2px rgba(0, 0, 0, 0.08);
}
</style>