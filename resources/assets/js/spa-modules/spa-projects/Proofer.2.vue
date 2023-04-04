<template>
	<el-container
		class="page-proof"
		v-loading.fullscreen.lock="fullscreenLoading"
	>
		<!-- Proof Header -->
		<el-header v-if="!isMobile" class="proof-header">
			<el-container>
				<div class="el-aside header-left">
					<!-- Nav Dashboard -->
					<div class="nav-dashboard" @click="goToDashboard()">
						<i class="menu-icon el-icon-menu"></i>
						<span class="menu-text">
							<i class="el-icon-arrow-left"></i>
							DASHBOARD<span class="tw-text-gray-400 tw-capitalize tw-absolute tw-ml-3">{{project.name}}</span>
						</span>
					</div>
				</div>

				<el-main class="header-main">
					<el-tooltip
						:content="
							pluginInstalled
								? 'Capture website image with plugin'
								: 'Install plugin'
						"
					>
						<el-button
							type="Primary"
							v-if="project.project_type == 'website'"
							@click="
								pluginInstalled
									? captureScreen()
									: installProofloPlugin()
							"
							>P</el-button
						>
					</el-tooltip>
					<!-- Select Revision -->
					<el-select
						v-if="project.project_type != 'website'"
						v-model="current_revision.id"
						@change="goToRevision()"
						class="select-revision"
					>
						<el-option
							v-for="version in project.versions"
							:key="version.value"
							:label="version.label"
							:value="version.id"
						></el-option>
					</el-select>

					<template v-if="project.versions">
						<!-- Freelancer buttons -->
						<template v-if="current_role == 'freelancer' || current_role == 'agency'">
							<el-button
								v-if="project.project_status == 'progress'"
								type="primary"
								icon="el-icon-upload"
								@click="openNewRevisionDialog"
								>Upload new proof</el-button
							>
                            <span
								v-if="
									project.project_type == 'design' &&
									project.project_status == 'revision'
								"
                                class="tw-text-primary"
								><i class="el-icon-position"></i> New Proofs Sent</span
							>
							<el-button
								v-if="
									project.project_status == 'approved' ||
									project.project_status == 'completed'
								"
								type="primary"
								icon="el-icon-upload"
								@click="showFinalFilesDialog = true"
								>Upload Final Files</el-button
							>

							<el-button
								v-if="project.project_status == 'approved'"
								type="primary"
								@click="moveToCompleted"
								>Move to Delivered</el-button
							>
						</template>

						<!-- Client buttons -->
						<template v-if="current_role == 'client'">
							<el-button
								v-if="
									project.project_type != 'website' &&
									project.project_status == 'revision'
								"
								icon="el-icon-position"
								@click="showSendCorrectionsDialog = true"
								>Send Corrections</el-button
							>
							<span
								v-if="project.project_status == 'progress'"
                                class="tw-text-primary"
                                >
                                <i class="el-icon-position"></i>
                                Corrections Sent</span>
							<el-button
								v-if="project.project_status == 'revision'"
								type="primary"
								icon="el-icon-circle-check"
								@click="approveReview()"
								>Approve</el-button
							>
							<el-button
								v-if="
									project.project_status == 'approved' ||
									project.project_status == 'completed'
								"
								type="primary"
								icon="el-icon-download"
								@click="showFinalFilesDialog = true"
								>Download Final Files</el-button
							>
						</template>
					</template>
				</el-main>

				<el-aside class="header-right" width="auto">
					<div class="collaborators">
						<!-- Clients List -->
						<el-tooltip
							v-for="client in project.clients"
							:key="client.id"
							:content="`${client.name} | ${client.pivot.role}`"
							class="tw-relative"
							popper-class="tw-capitalize"
						>
							<el-popconfirm
								confirmButtonText="OK"
								@confirm="
									handleDeleteClient(client)
								"
								cancelButtonText="No, Thanks"
								icon="el-icon-info"
								iconColor="red"
								title="Are you sure to delete this?"
							>
								<img
									slot="reference"
									class="collaborator user-avatar"
									:src="client.photo_url"
								/>
								<i class="el-icon-check client-icon" v-if="client.pivot.role == 'client'" slot="reference"></i>
							</el-popconfirm>
						</el-tooltip>

						<!-- Add new client -->
						<el-tooltip content="Add New Client">
							<el-button
								class="user-avatar add-button"
								@click="showNewClientDialog = true"
							>
								<i class="el-icon-plus"></i>
							</el-button>
						</el-tooltip>
                        <el-tooltip :content="`Poke ${currentRole == 'Client' ? 'Collaborators' : 'Client'}`">
                            <el-popconfirm
								confirmButtonText="OK"
								@confirm="handlePoke()"
								cancelButtonText="No, Thanks"
								icon="el-icon-info"
								title="Are you sure you want to poke?"
							>
                                <el-button
                                    class="user-avatar add-button tw-mr-2" slot="reference"
                                >
                                    <i class="el-icon-thumb"></i>
                                </el-button>
                            </el-popconfirm>
						</el-tooltip>
						<div class="tracker" style="margin-right: 8px">
							<el-tooltip content="Copy project hash">
								<el-popover placement="top" width="auto" trigger="click">
								<div><i class="el-icon-success" style="color:#80b4a0"></i> Hash copied to
									clipboard</div>
									<el-button size="mini" slot="reference" class="tw-mr-4"
										@click="clipboardLink(project.project_hash)">
										<svg width="18" enable-background="new 0 0 2447.6 2452.5" viewBox="0 0 2447.6 2452.5" xmlns="http://www.w3.org/2000/svg"><g clip-rule="evenodd" fill-rule="evenodd"><path d="m897.4 0c-135.3.1-244.8 109.9-244.7 245.2-.1 135.3 109.5 245.1 244.8 245.2h244.8v-245.1c.1-135.3-109.5-245.1-244.9-245.3.1 0 .1 0 0 0m0 654h-652.6c-135.3.1-244.9 109.9-244.8 245.2-.2 135.3 109.4 245.1 244.7 245.3h652.7c135.3-.1 244.9-109.9 244.8-245.2.1-135.4-109.5-245.2-244.8-245.3z" fill="#36c5f0"/><path d="m2447.6 899.2c.1-135.3-109.5-245.1-244.8-245.2-135.3.1-244.9 109.9-244.8 245.2v245.3h244.8c135.3-.1 244.9-109.9 244.8-245.3zm-652.7 0v-654c.1-135.2-109.4-245-244.7-245.2-135.3.1-244.9 109.9-244.8 245.2v654c-.2 135.3 109.4 245.1 244.7 245.3 135.3-.1 244.9-109.9 244.8-245.3z" fill="#2eb67d"/><path d="m1550.1 2452.5c135.3-.1 244.9-109.9 244.8-245.2.1-135.3-109.5-245.1-244.8-245.2h-244.8v245.2c-.1 135.2 109.5 245 244.8 245.2zm0-654.1h652.7c135.3-.1 244.9-109.9 244.8-245.2.2-135.3-109.4-245.1-244.7-245.3h-652.7c-135.3.1-244.9 109.9-244.8 245.2-.1 135.4 109.4 245.2 244.7 245.3z" fill="#ecb22e"/><path d="m0 1553.2c-.1 135.3 109.5 245.1 244.8 245.2 135.3-.1 244.9-109.9 244.8-245.2v-245.2h-244.8c-135.3.1-244.9 109.9-244.8 245.2zm652.7 0v654c-.2 135.3 109.4 245.1 244.7 245.3 135.3-.1 244.9-109.9 244.8-245.2v-653.9c.2-135.3-109.4-245.1-244.7-245.3-135.4 0-244.9 109.8-244.8 245.1 0 0 0 .1 0 0" fill="#e01e5a"/></g></svg>
									</el-button>
								</el-popover>
							</el-tooltip>
						</div>
					</div>
				</el-aside>
			</el-container>
		</el-header>
		<!-- Proof Header End -->

		<!-- Mobile Header -->
		<el-header v-else class="mobile-header">
			<!-- Revision Header -->
			<el-container v-if="mobileStatus == 'revision'">
				<el-aside width="70px">
					<div class="nav-dashboard" @click="goToDashboard()">
						<i class="menu-icon el-icon-menu"></i>
						<span class="menu-text">
							<i class="el-icon-arrow-left"></i>
						</span>
					</div>
				</el-aside>
				<el-main>
					<div class="revision-buttons">
						<el-select
							v-if="project.project_type != 'website'"
							v-model="current_revision.id"
							@change="goToRevision()"
							class="select-revision"
							size="small"
						>
							<el-option
								v-for="version in project.versions"
								:key="version.value"
								:label="'RR ' + version.value"
								:value="version.id"
							></el-option>
						</el-select>
						<el-button
							v-if="
								current_role == 'freelancer' || current_role == 'agency' ||
								(current_role == 'client' &&
									current_proof.project_files &&
									current_proof.project_files.user_id ==
										current_user.id &&
									project.project_type == 'website')
							"
							type="danger"
							icon="el-icon-delete"
							class="delete-button"
							@click="deleteProof(current_proof)"
							size="mini"
							plain
						></el-button>
						<template v-if="project.versions">
							<!-- Freelancer buttons -->
							<template v-if="current_role == 'freelancer' || current_role == 'agency'">
								<el-button
									v-if="project.project_status == 'progress'"
									icon="el-icon-upload"
									@click="openNewRevisionDialog"
									size="small"
								></el-button>
								<el-button
									v-if="
										project.project_status == 'approved' ||
										project.project_status == 'completed'
									"
									icon="el-icon-upload"
									@click="showFinalFilesDialog = true"
								></el-button>
							</template>

							<!-- Client buttons -->
							<template v-if="current_role == 'client'">
								<el-button
									v-if="
										project.project_type != 'website' &&
										current_revision.status_revision ==
											'revision'
									"
									@click="showSendCorrectionsDialog = true"
									type="default"
									icon="el-icon-position"
									class="submit-button"
									size="small"
								></el-button>
								<el-button
									v-if="
										current_revision.status_revision ==
										'revision'
									"
									type="primary"
									icon="el-icon-check"
									class="approve-button"
									@click="approveReview()"
									size="small"
								></el-button>
								<el-button
									v-if="
										current_revision.status_revision ==
										'approved'
									"
									type="primary"
									icon="el-icon-check"
									size="small"
									>Approved</el-button
								>

								<el-button
									v-if="
										current_revision.status_revision ==
										'completed'
									"
									type="primary"
									icon="el-icon-download"
									@click="showFinalFilesDialog = true"
									size="small"
								></el-button>
							</template>
						</template>
						<template
							v-if="
								project.project_status == 'revision' &&
								(current_role == 'freelancer' || current_role == 'agency' || 
									(current_role == 'client' &&
										project.project_type == 'website'))
							"
						>
							<el-button
								type="danger"
								icon="el-icon-upload"
								class="upload-button"
								@click="openUploadDialog"
								size="small"
							></el-button>
						</template>
					</div>
				</el-main>
				<el-aside width="60px" class="text-center">
					<div class="user">
						<el-button
							class="user-avatar add-button"
							@click="showNewClientDialog = true"
						>
							<i class="el-icon-plus"></i>
						</el-button>
					</div>
				</el-aside>
			</el-container>

			<!-- Canvas Header -->
			<el-container v-if="mobileStatus == 'canvas'">
				<el-aside width="70px">
					<div
						class="nav-dashboard"
						@click="mobileStatus = 'revision'"
					>
						<i class="menu-icon fa fa-square"></i>
						<span class="menu-text">
							<i class="el-icon-arrow-left"></i>
						</span>
					</div>
				</el-aside>
				<el-main>
					<div class="canvas-tools">
						<el-button
							icon="el-icon-edit"
							:type="pen_active ? 'primary' : ''"
							@click="togglePen()"
							class="canvas-tool"
							circle
						></el-button>

						<el-slider
							v-model="zoom"
							:min="3"
							:max="400"
							:change="setZoom()"
							class="zoom-slider"
						></el-slider>
						<!-- FitScreen Tool -->
						<el-tooltip content="Fit proof to window vertically">
							<el-button
								icon="fa fa-arrows-v"
								@click="fitImageSize('fitscreen')"
								class="canvas-tool"
								circle
							></el-button>
						</el-tooltip>
					</div>
				</el-main>
			</el-container>

			<!-- Comments Header -->
			<el-container v-if="mobileStatus == 'comments'">
				<el-aside width="70px">
					<div
						class="nav-dashboard"
						@click="mobileStatus = 'revision'"
					>
						<i class="menu-icon fa fa-square"></i>
						<span class="menu-text">
							<i class="el-icon-arrow-left"></i>
						</span>
					</div>
				</el-aside>
				<el-main>
					<div class="comments-header">
						<i class="el-icon-chat-dot-square"></i>
						<span>Comments</span>
					</div>
				</el-main>
				<el-aside width="70px">
					<div class="nav-dashboard" @click="mobileStatus = 'canvas'">
						<i class="menu-icon el-icon-edit"></i>
						<span class="menu-text">
							<i class="el-icon-arrow-right"></i>
						</span>
					</div>
				</el-aside>
			</el-container>
		</el-header>
		<!-- Mobile Header End -->

		<el-container class="proof-container">
			<!-- Left Sidebar -->
			<div v-if="!isMobile" class="el-aside sidebar-left">
				<div class="el-header">
					<div class="progress-area">
						<!-- Progress bar -->
						<el-progress
							:percentage="project.percentage"
							:text-inside="true"
							:stroke-width="17"
							class="project-progress"
						></el-progress>
						<div class="progress-info">
							<span class="text-success">{{
								project.solved_issues
							}}</span>
							OF
							<span class="text-danger">{{
								project.total_issues
							}}</span>
							ISSUES COMPLETED
						</div>
						<!-- Team members -->
						<div class="team-members">
							<el-tooltip
								v-for="member in project.teamMembers"
								:key="member.id"
								:content="member.name"
							>
								<img
									class="user-avatar"
									:src="member.photo_url"
								/>
							</el-tooltip>
						</div>
					</div>
					<template
						v-if="
							project.project_status == 'revision' &&
							(current_role == 'freelancer' || current_role == 'agency' ||
								(current_role == 'client' &&
									project.project_type == 'website'))
						"
					>
						<div
							v-if="project.project_type == 'website'"
							class="buttons-area"
						>
							<el-button-group class="button-group">
								<el-tooltip
									:content="
										pluginInstalled
											? 'Capture image with plugin'
											: 'Install plugin'
									"
								>
									<el-button
										class="upload-button"
										@click="
											pluginInstalled
												? captureScreen()
												: installProofloPlugin()
										"
									>
										<img
											:src="`${spacePrefix}/assets/img/p.png`"
											alt="p"
										/>
									</el-button>
								</el-tooltip>
								<el-tooltip
									content="Upload image from computer"
								>
									<el-button
										icon="el-icon-upload"
										class="upload-button"
										@click="openUploadDialog"
									></el-button>
								</el-tooltip>
							</el-button-group>
						</div>
					</template>
					<el-select
						v-model="sortItem"
						clearable
						placeholder="Select"
						class="buttons-area top-section"
					>
						<el-option
							v-for="item in sortItems"
							:key="item.value"
							:label="item.label"
							:value="item.value"
						></el-option>
					</el-select>
				</div>

				<el-main>
					<!-- Proof thumb list -->
					<el-card
						v-for="(proof, index) in sortCurrentProof"
						:key="proof.id"
						@click.native="goToProof(proof)"
						class="proof-thumb"
						:class="{ active: current_proof.id == proof.id }"
					>
						<span class="proof-number">{{ proof.page_num }}</span>
						<img
							class="thumb-image"
							:src="`${spacePrefix}/${proof.project_files.thumb_path}?v${proof.project_files.id}`"
						/>
						<div class="buttons-area">
							<el-button
								v-if="
									current_role == 'freelancer' || current_role == 'agency' ||
									(current_role == 'client' &&
										proof.project_files.user_id ==
											current_user.id &&
										project.project_type == 'website')
								"
								type="danger"
								icon="el-icon-delete"
								class="delete-button"
								@click="deleteProof(proof)"
								size="mini"
								plain
							></el-button>
							<el-button
								type="danger"
								icon="el-icon-download"
								class="download-button"
								@click="saveAsPdf([proof])"
								size="mini"
								plain
							></el-button>
						</div>
						<div class="review-comment">
							<el-button-group>
								<el-button
									v-if="getReviewCount(proof)"
									:type="
										current_role == 'client'
											? 'info'
											: 'warning'
									"
									icon="el-icon-warning"
									>{{ getReviewCount(proof) }}</el-button
								>
								<el-button
									v-if="getunreadComments(proof)"
									type="danger"
									icon="fa fa-commenting"
									>{{ getunreadComments(proof) }}</el-button
								>
							</el-button-group>
						</div>
						<div
							v-if="proof.status == 'approved'"
							class="proof-check"
						>
							<i class="el-icon-check"></i>
						</div>
					</el-card>
				</el-main>
				<div class="buttons-area creative-brief">
					<div style="padding-bottom: 4px" v-if="pdfLoading">
						<el-progress
							:percentage="saveAsPdfProgress"
							:color="colors"
						></el-progress>
					</div>
					<el-tooltip content="View Creative Brief">
						<el-button
							class="user-avatar add-button"
							@click="openCreativeBriefDialog"
						>
							<i class="el-icon-document"></i>
						</el-button>
					</el-tooltip>
					<el-tooltip content="Save As PDF">
						<el-button
							class="user-avatar add-button"
							:loading="pdfLoading"
							@click="saveAsPdf(current_revision.proofs)"
						>
							<i class="el-icon-download" v-if="!pdfLoading"></i>
						</el-button>
					</el-tooltip>
					<el-tooltip
						v-if="project.project_type != 'website'"
						content="Upload Image to slide deck"
					>
						<el-button
							class="user-avatar add-button"
							@click="openUploadDialog"
						>
							<i class="el-icon-upload"></i>
						</el-button>
					</el-tooltip>
				</div>
			</div>
			<!-- Left Sidebar End -->

			<!-- Proof Main -->
			<el-main class="proof-main">
				<!-- Canvas Stage -->
				<div id="stage" class="removeScroll" :class="handActive ? 'stage-hand' : 'stage-pen'"></div>

				<div
					class="controls"
					id="controls"
					v-if="['video', 'website'].includes(project.project_type) && current_proof && current_proof.project_files.file_type == 'video'"
				>
					<div class="control-timeline">
						<div
							class="timeline"
							id="timeline"
							v-on:mouseover="onMouseOverTimeline"
							v-on:mouseleave="onMouseLeaveTimeline"
						>
							<div
								class="current-time"
								:style="{ width: percent + '%' }"
							></div>
							<div class="current-point">
								<img
									src="/img/circle_player.png"
									:style="{ left: percent + '%' }"
									v-on:mousedown="setCurrentTime"
								/>
							</div>

							<div class="points">
								<div
									v-for="p in points"
									class="point"
									:class="{ active: p.active }"
									:style="point(p)"
									@click="showIssueGroup(p)"
									:key="'#' + p.label"
								>
									<span>{{ p.label }}</span>
									<div
										class="timeline-marker"
										:style="timelineMarker(p)"
									>
										<div
											class="resize"
											@click.stop
											@mousedown="resizePoint($event, p)"
										></div>
									</div>
								</div>
							</div>
						</div>
						<div class="points-wrap"></div>
						<div class="control-btn">
							<el-button
								:icon="!isPlay ? 'fa fa-play' : 'fa fa-pause'"
								@click="play"
								class="control"
							>
							</el-button>
							<el-button
								icon="fa fa-volume-up"
								@click="mute"
								class="control"
							>
							</el-button>
							<el-slider
								v-model="volume"
								:min="1"
								:max="100"
								@change="setVolume"
								class="volume-slider"
							></el-slider>
							<div class="text-time">
								{{ this.toHHMMSS(timeVideo) }}/{{
									this.toHHMMSS(fullTime)
								}}
							</div>
						</div>
					</div>
				</div>

				<div v-if="current_proof.project_files && current_proof.project_files.file_type != 'video'">
					<div v-if="!isMobile" class="canvas-tools">
						<div class="pen-tool">
							<!-- Draw Tool -->
							<el-button
								icon="el-icon-edit"
								:type="pen_active ? 'primary' : ''"
								@click="togglePen()"
								class="canvas-tool"
								circle
							></el-button>
							<!-- Tool Info -->
							<div v-if="showTooltip" class="tool-tip">
								SELECT TO ADD CORRECTION(S)
							</div>
						</div>

						<div class="pen-tool">
							<!-- Draw Tool -->
							<el-button
								icon="fa fa-hand-paper-o"
								:type="handActive ? 'primary' : ''"
								@click="toggleHand"
								class="canvas-tool"
								circle
							>
							</el-button>
						</div>

						<!-- Zoom Slider -->
						<el-slider
							v-model="zoom"
							:min="5"
							:max="400"
							:change="setZoom()"
							show-input
							class="zoom-slider"
						></el-slider>

						<!-- FitWidth Tool fixed this Shane -->
						<el-tooltip content="Fit proof to window">
							<el-button
								icon="fa fa-arrows-h"
								@click="fitImageSize('fullwidth')"
								class="canvas-tool"
								circle
							></el-button>
						</el-tooltip>

						<!-- FitScreen Tool -->
						<el-tooltip content="Fit proof to window vertically">
							<el-button
								icon="fa fa-arrows-v"
								@click="fitImageSize('fitscreen')"
								class="canvas-tool"
								circle
							></el-button>
						</el-tooltip>

						<!-- Rotate Tool -->
						<el-tooltip content="Rotate proof">
							<el-button
								v-if="project.project_type != 'website'"
								icon="el-icon-refresh"
								@click="rotateImage()"
								class="canvas-tool"
								circle
							></el-button>
						</el-tooltip>

						<!-- Toggle Buttons -->
						<el-button
							icon="el-icon-picture"
							class="toggle-button left"
							@click="toggleSidebar('left')"
						></el-button>
						<el-button
							icon="fa fa-bars"
							class="toggle-button right"
							@click="toggleSidebar('right')"
						></el-button>
					</div>

					<!-- Pagination -->
					<div v-if="!isMobile" class="proof-pagination">
						<el-pagination
                        v-if="current_revision.proofs"
                        background
                        layout="prev, pager, next"
                        :page-size="1"
                        :total="current_revision.proofs.length"
                        :current-page="current_revision.proofs.indexOf(current_proof) + 1"
                        @current-change="handlePageChange"
                        >
					</el-pagination>
					</div>
				</div>
				<div v-else>
					<div v-if="!isMobile" class="canvas-tools">
						<div class="pen-tool">
							<!-- Draw Tool -->
							<el-button
								icon="el-icon-edit"
								:type="pen_active ? 'primary' : ''"
								@click="togglePen()"
								class="canvas-tool"
								circle
							>
							</el-button>
							<!-- Tool Info -->
							<div v-if="showTooltip" class="tool-tip">
								SELECT TO ADD CORECTION(S)
							</div>
						</div>

						<div class="pen-tool">
							<!-- Draw Tool -->
							<el-button
								icon="fa fa-hand-paper-o"
								:type="handActive ? 'primary' : ''"
								@click="toggleHand"
								class="canvas-tool"
								circle
							>
							</el-button>
						</div>
					</div>
				</div>

				<!-- Mobile Cover -->
				<div
					v-if="isMobile && mobileStatus == 'revision'"
					@click="enableStageEditable()"
					class="canvas-overwrap"
				>
					<h5 class="tip-edit">TAP FOR EDIT MODE</h5>
				</div>
			</el-main>
			<!-- Proof Main End -->

			<!-- Right Sidebar -->
			<div
				v-show="!isMobile || mobileStatus == 'comments'"
				class="el-aside sidebar-right"
				:class="{ 'mobile-sidebar': isMobile }"
			>
				<div class="issue-list">
					<div class="tw-px-4 tw-py-4 tw-flex tw-items-center tw-justify-between tw-bg-gray-200">
						<router-link :to="{name: 'my-tasks'}" class="tw-flex tw-hover-text-primary tw-items-center tw-text-primary no-underline tw-rounded-md tw-shadow tw-px-4 tw-py-2 tw-bg-gray-50">
							<i class="el-icon-check tw-mr-2"></i>
							<div>My Tasks</div>
						</router-link>
						<div class="tw-flex tw-items-center">
							<span
								v-if="
									project.timeTracker &&
									project.timeTracker.end === null &&
									project.timeTracker.start &&
									(current_role == 'freelancer' || current_role == 'agency')
								"
								>{{ trackerTimer() }}</span
							>
							<span v-else-if="current_role == 'freelancer' || current_role == 'agency'">{{
								projectDuration()
							}}</span>
							<el-tooltip
								v-if="
									project.timeTracker &&
									project.timeTracker.end === null &&
									project.timeTracker.start &&
									(current_role == 'freelancer' || current_role == 'agency')
								"
								content="Stop Tracker"
							>
								<div
									class="time-tracker-stop stop-tracker"
									@click="stopTracker()"
								>
									<i
										class="fa fa-stop icon-time-tracker-stop"
									></i>
								</div>
							</el-tooltip>
							<el-tooltip
								v-else-if="current_role == 'freelancer' || current_role == 'agency'"
								content="Start Tracker"
							>
								<div
									class="time-tracker"
									@click="startTracker()"
								>
									<i class="fa fa-play icon-time-tracker"></i>
								</div>
							</el-tooltip>
						</div>
					</div>
					<!-- New Issue Form -->
					<el-card
						v-show="newIssueForm.active"
						class="new-issue"
						shadow="never"
					>
						<el-form
							:model="newIssueForm"
							ref="newIssueForm"
							@submit.native.prevent="addNewIssue()"
						>
							<el-form-item
								prop="description"
                                class="new-issue-input"
								:rules="[
									{
										required: true,
										message: 'Description is required',
									},
								]"
							>
								<el-input
									type="textarea"
                                    
									autosize
									ref="newIssueForm_description"
									v-model="newIssueForm.description"
									@keydown.native.enter.prevent="
										addNewIssue()
									"
									@keydown.native.esc.prevent="
										cancelNewIssue()
									"
									placeholder="Type comment here"
								></el-input>
							</el-form-item>
							<el-form-item class="form-buttons">
								<center>
									<el-upload
										class="upload-dialog"
										name="photos"
                                        :headers="headers"
										action="/api/files/upload"
										ref="upload_new_issue"
										:data="uploadFormData"
										:file-list="uploadFileList"
										:on-success="onIssueImageUploaded"
										:auto-upload="false"
										multiple
									>
										<el-button
											slot="trigger"
											size="mini"
											icon
											>Attach file</el-button
										>
										<el-button
											size="mini"
											@click="cancelNewIssue()"
											class="cancel-button"
											>Cancel</el-button
										>
										<el-button
											type="primary"
											size="mini"
											@click="addNewIssue()"
											>OK</el-button
										>
									</el-upload>
								</center>
							</el-form-item>
						</el-form>
					</el-card>
					<div
						class="no-corrections"
						v-if="
							current_proof &&
							current_proof.issues &&
							current_proof.issues.length == 0 &&
							!newIssueForm.active
						"
					>
						<div class="no-corrections-content">
							There are no corrections or comments
						</div>
						<i
							class="el-icon-chat-line-square no-corrections-icon"
						></i>
					</div>

					<!-- Issue List -->
					<template v-if="current_proof && current_proof.issues">
						<el-card
							v-for="(issue, index) in reverse(
								current_proof.issues
							)"
							:key="issue.id"
							class="issue-box issue-display"
							:class="issue.status"
							shadow="never"
						>
							<el-row class="box-header">
								<el-col :span="4">
									<el-badge
										v-if="
											issue.unread_comments &&
											issue.unread_comments.length > 0
										"
										:value="issue.unread_comments.length"
										class="item"
									>
										<el-tooltip :content="`${issue.user.name} | Show Comments`">
											<div @mouseover="commentHover.push(issue.id)" @mouseleave="commentHover = []">

											<img
												v-if="!commentHover.includes(issue.id)"
												@click="showIssueDetails(issue)"
												class="user-avatar"
												:src="issue.user.photo_url"
											/>
											<el-button
												v-else
												class="nav-button"
												icon="el-icon-chat-dot-square"
												@click="showIssueDetails(issue)"
												circle
											></el-button>
											</div>
										</el-tooltip>
									</el-badge>
									<el-tooltip v-else :content="`${issue.user.name}`">
										<div @mouseover="commentHover.push(issue.id)" @mouseleave="commentHover = []">
											<img
												v-if="!commentHover.includes(issue.id)"
												@click="showIssueDetails(issue)"
												class="user-avatar"
												:src="issue.user.photo_url"
											/>
											<el-button
												v-else
												class="nav-button"
												icon="el-icon-chat-dot-square"
												@click="showIssueDetails(issue)"
												circle
											></el-button>
										</div>
									</el-tooltip>
								</el-col>
								<el-col :span="16" class="text-center">
									<template
										v-if="
											project.project_type == 'website' &&
											(current_role == 'freelancer' || current_role == 'agency') &&
											issue.status == 'done'
										"
									>
										<el-button class="check-button"
											>Approved</el-button
										>
									</template>
									<template
										v-else-if="
											project.project_type == 'website' &&
											current_role == 'client' &&
											issue.status == 'todo'
										"
									>
										<el-dropdown split-button type="default">In Review
											<el-dropdown-menu slot="dropdown">
												<el-dropdown-item @click.native="
													toggleIssueStatus(
														issue,
														'done'
													)
												" icon="el-icon-circle-check">Approve</el-dropdown-item>
											</el-dropdown-menu>		
										</el-dropdown>
									</template>
									<template v-else>
										<el-checkbox
											v-model="issue.isCheck"
											:label="
												project.project_type ==
													'website' &&
												issue.status != 'todo'
													? issue.status == 'review'
														? current_role ==
														'client'
															? 'Approve'
															: 'In Review'
														: 'Approved'
													: issue.status
											"
											:disabled="
												project.project_type ==
													'design' && 
												(current_role == 'client' || current_role == 'collaborator')
													? true
													: false
											"
											@change="toggleIssueStatus(issue)"
											class="check-button"
											border
										></el-checkbox>
									</template>
								</el-col>
								<el-col :span="4" class="text-right">
									<el-button
										@click="
											project.project_type != 'video'
												? showIssueGroup(issue.group)
												: showIssueGroup(issue)
										"
                                        :style="backGroupColor(issue)"
										class="issue-label "
										circle
										>{{ issue.label }}</el-button
									>
								</el-col>
							</el-row>
							<el-row class="box-header">
								<!-- <el-col :span="4">
									<el-tooltip :content="issue.user.name">
										<img
											class="user-avatar"
											:src="issue.user.photo_url"
										/>
									</el-tooltip>
								</el-col> -->
								<el-col :span="23">
									<div class="datetime tw-px-2 tw-mb-2">
										{{ issue.created_at | utc_to_local }}
									</div>
									<div
										class="description tw-mb-2"
										trigger="click"
										@click="editIssue(issue, index)"
									>
										<span
											v-html="urlify(issue.description)"
										></span>
									</div>
									<div
										class="datetime"
										v-if="['video', 'website'].includes(project.project_type) && current_proof.project_files && current_proof.project_files.file_type == 'video'"
									>
										Time point:
										{{
											issue.time_video
												? toHHMMSS(issue.time_video)
												: toHHMMSS(0)
										}}
									</div>
									<div
										class="datetime"
										v-if="
											['video', 'website'].includes(project.project_type) && current_proof.project_files && current_proof.project_files.file_type == 'video' &&
											issue.length_video &&
											issue.length_video > 0
										"
									>
										Time point end:
										{{
											issue.time_video
												? toHHMMSS(
														parseFloat(
															issue.time_video
														) +
															parseFloat(
																issue.length_video
															)
												  )
												: toHHMMSS(0)
										}}
									</div>
									<div class="edit-issue">
										<el-input
											type="textarea"
											:autosize="{
												minRows: Math.round(
													issue.description.length /
														20
												),
											}"
											v-model="issue.description"
											@keydown.native.enter.prevent="
												updateIssue(issue, index)
											"
											class="edit-description"
											placeholder="Type comment here"
											autofocus
										></el-input>
										<div class="text-right">
											<el-upload
												class="upload-dialog"
												name="photos"
												action="/api/files/upload"
                                                :headers="headers"
												:ref="
													'upload_issue_' + issue.id
												"
												:data="uploadFormData"
												:file-list="uploadFileList"
												:on-success="
													onIssueImageUploaded
												"
												:auto-upload="false"
												multiple
											>
												<el-button
													size="mini"
													type="danger"
													class="update-button"
													icon="el-icon-delete"
													@click="deleteIssue(issue)"
													plain
												></el-button>
												<el-button
													slot="trigger"
													size="mini"
													type="info"
													icon="fa fa-paperclip"
													plain
												></el-button>
												<el-button
													type="primary"
													size="mini"
													class="update-button"
													@click="
														updateIssue(
															issue,
															index
														)
													"
													>OK</el-button
												>
											</el-upload>
										</div>
									</div>
									<template v-if="issue.files">
										<div
											v-for="(file,
											fileKey) in issue.files"
											class="attach-image"
											:key="fileKey"
										>
											<img
												:src="`${spacePrefix}/${file.thumb_path}`"
											/>
											<div class="attach-overlay">
												<i
													class="el-icon-zoom-in attach-button"
													@click="
														openImagePreview(
															`${spacePrefix}/${file.thumb_path}`
														)
													"
												></i>
												<a
													class="attach-button"
													@click="
														handleFileDownload(
															file.thumb_path,
															fileKey
														)
													"
												>
													<i
														class="el-icon-download"
													></i>
												</a>
												<i
													class="el-icon-delete attach-button"
													@click="
														deleteImage(
															current_issue,
															file,
															'issue'
														)
													"
												></i>
											</div>
										</div>
									</template>
								</el-col>
								<el-col :span="1">
									<el-dropdown
										v-if="issue.user_id == current_user.id"
										trigger="click"
										class="more-button"
									>
										<span class="el-dropdown-link">
											<i class="el-icon-more"></i>
										</span>
										<el-dropdown-menu slot="dropdown">
											<el-dropdown-item
												v-if="
													current_role == 'client' &&
													issue.status != 'done'
												"
												@click.native="
													toggleIssueStatus(
														issue,
														'done'
													)
												"
												>Approve</el-dropdown-item
											>
											<el-dropdown-item
												@click.native="
													edqitIssue(issue, index)
												"
												>Edit</el-dropdown-item
											>
											<el-dropdown-item
												@click.native="
													deleteIssue(issue)
												"
												>Delete</el-dropdown-item
											>
										</el-dropdown-menu>
									</el-dropdown>
								</el-col>
							</el-row>
							<div class="tw-relative">
								<div class="tw-absolute tracker-design tw-flex tw-items-center" :style="project.project_type == 'website' ? '' : 'top:-34px;'">
									<div v-if="issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start" class="tw-ml-8 tw-bg-gray-50 tw-font-medium tw-rounded-lg tw-text-sm tw-shadow tw-py-1 tw-px-6"
										:class="{'time-counts-active': (issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start)}">
										<span>{{trackerTimerIssue(issue.time_tracker)}}</span>
										
									</div>
									<div v-else-if="isBothRole" class="tw-ml-8 tw-bg-gray-50 tw-font-medium tw-rounded-lg tw-text-sm tw-shadow tw-py-1 tw-px-6"
										:class="{'time-counts-active': (issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start)}">
										<span>{{issueDuration(issue)}}</span>
									</div>
									<el-tooltip
										v-if="isBothRole && issue.time_tracker && issue.time_tracker.end === null && issue.time_tracker.start"
										content="Stop Tracker" placement="bottom">
										<div class="stop-tracker" @click="stopIssueTracker(issue)">
											<i class="fa fa-stop icon-time-tracker-stop"></i>
										</div>
									</el-tooltip>
									<el-tooltip v-else-if="isBothRole" content="Start Tracker" placement="bottom" class="">
										<div class="play-tracker" @click="startIssueTracker(issue)"><i class="fa fa-play icon-time-tracker"></i></div>
									</el-tooltip>
								</div>
							</div>
							<div v-if="project.project_type == 'website'" class="tw-mt-4 tw-bg-gray-50 tw-py-4 tw-px-6 tw-text-gray-500 tw-flex tw-justify-between items-center issue-footer">
							<div>
								<el-dropdown trigger="click">
									<span class="el-dropdown-link">
										<i class="el-icon-user tw-p-2 tw-mr-2 issue-icons" v-if="!issue.assign_id"></i>
											<el-tooltip v-if="issue.assign" :content="issue.assign.name">
												<span class="tw-mr-2 tw-relative">
													<el-avatar style="vertical-align:middle" :src="issue.assign.photo_url" :size="28" />
												</span>
											</el-tooltip>
									</span>
									<el-dropdown-menu slot="dropdown">
										<el-dropdown-item v-for="(user, key) in project.users" :key="key">
											<div class="tw-flex tw-items-center" @click="assignTask(user, issue)">
												<div class="tw-flex tw-items-center">
													<el-avatar :src="user.photo_url" class="tw-mr-2" :size="24" />
													{{user.name}}
												</div>
												<i class="el-icon-success tw-ml-6" v-if="issue.assign_id && issue.assign_id == user.id"></i>
											</div>
											</el-dropdown-item>
									</el-dropdown-menu>
								</el-dropdown>
								<el-dropdown trigger="click">
									<span class="el-dropdown-link">
										<i class="el-icon-collection-tag tw-p-2 tw-mr-2 issue-icons" v-if="!issue.tag"></i>
										<el-tooltip v-if="issue.tag">
											<div slot="content" class="tw-capitalize">{{issue.tag}}</div>
											<i v-if="issue.tag == 'high'" class="status-dot high-dot tw-mr-2"></i>
											<i v-if="issue.tag == 'medium'" class="status-dot medium-dot tw-mr-2"></i>
											<i v-if="issue.tag == 'low'" class="status-dot low-dot tw-mr-2"></i>
										</el-tooltip>
									</span>
									<el-dropdown-menu slot="dropdown">
										<el-dropdown-item>
											<div @click="issuePriority('high', issue)">
												<span class="high-dot"></span>High <i class="el-icon-success tw-ml-6" v-if="issue.tag && issue.tag == 'high'"></i>
											</div>
										</el-dropdown-item>
										<el-dropdown-item>
											<div @click="issuePriority('medium', issue)">
												<span class="medium-dot"></span>Medium <i class="el-icon-success tw-ml-6" v-if="issue.tag && issue.tag == 'medium'"></i>
											</div>
										</el-dropdown-item>
										<el-dropdown-item>
											<div @click="issuePriority('low', issue)">
												<span class="low-dot"></span>Low <i class="el-icon-success tw-ml-6" v-if="issue.tag && issue.tag == 'low'"></i>
											</div>
										</el-dropdown-item>
									</el-dropdown-menu>
								</el-dropdown>
								<!-- <i class="el-icon-date tw-p-2 tw-mr-2 issue-icons"></i> -->
								<el-date-picker style="width:auto"
									v-model="issueDate"
									type="date"
									@change="issueChangeDate(issue)"
									class="issue-date"
									placeholder="Pick a day">
								</el-date-picker> 
								<small v-if="issue.end_date" :class="issue.end_date | dateToFromNowDaily | dateColor(issue.end_date)">{{issue.end_date | dateToFromNowDaily}}</small>
							</div>
							<div v-if="project.project_type == 'website'" class="tw-flex tw-items-center tw-text-gray-500">
								<div class="font-small tw-cursor-pointer tw-mr-1 issue-likes" :class="(JSON.parse(issue.likes) instanceof Array && JSON.parse(issue.likes).length > 0 ? ' issue-likes-flex ' : ' issue-likes-none ') + (JSON.parse(issue.likes) instanceof Array && JSON.parse(issue.likes).includes(user.id) ? ' issue-like-active' : '')" @click="addLike(issue)">
									{{JSON.parse(issue.likes) && JSON.parse(issue.likes).length > 0 ? JSON.parse(issue.likes).length : ''}}
									<svg class="svg-issue-icon" fill="currentcolor" focusable="false" viewBox="0 0 24 24"><path d="M23.1,9.2C22.4,8.4,21.5,8,20.5,8h-4.2V4.7c0-1.6-0.8-3.2-2.1-4.2C13.6,0,12.7-0.1,12,0.1c-0.8,0.2-1.4,0.7-1.8,1.5L7.3,8 H2.7C1.2,8,0,9.2,0,10.7v8.7C0,20.8,1.2,22,2.7,22H7h1h11.2c1.7,0,3.2-1.2,3.4-2.9l1.2-7C24.1,11,23.8,10,23.1,9.2z M2.7,20 C2.3,20,2,19.7,2,19.3v-8.7C2,10.3,2.3,10,2.7,10H7v10H2.7z M21.9,11.7l-1.2,7C20.6,19.5,20,20,19.2,20H9V9.2l3-6.8 C12.1,2.1,12.3,2,12.4,2C12.6,2,12.8,2,13,2.1c0.8,0.6,1.3,1.6,1.3,2.6V10h6.2c0.4,0,0.8,0.2,1.1,0.5C21.9,10.9,22,11.3,21.9,11.7z"></path></svg>
								</div>
								<div v-if="issue.comments.length" class="font-small tw-cursor-pointer" @click="showIssueDetails(issue)">
									{{issue.comments.length}}
									<svg class="svg-issue-icon" fill="currentcolor" focusable="false" viewBox="0 0 24 24"><path d="M4.2,24.1c-0.2,0-0.3,0-0.5-0.1c-0.3-0.2-0.5-0.5-0.5-0.9v-5.2C1.1,16.1,0,13.7,0,11c0-5,4-9,9-9h6c5,0,9,4,9,9 c0,5-4,9-9,9h-4.1l-6.3,3.9C4.5,24,4.3,24.1,4.2,24.1z M9,4c-3.9,0-7,3.1-7,7c0,2.2,1,4.2,2.8,5.6C5,16.8,5.2,17,5.2,17.4v3.9 l5-3.1c0.2-0.1,0.3-0.2,0.5-0.2H15c3.9,0,7-3.1,7-7c0-3.9-3.1-7-7-7H9z"></path></svg>
								</div>
							</div>
						</div>
						</el-card>
					</template>
				</div>

				<!-- Issue Details -->
				<transition name="comments-slide">
					<div v-if="isIssueDetails" class="issue-details">
						<!-- Issue Box -->
						<el-card
							class="issue-box current-issue"
							:class="current_issue.status"
							shadow="never"
						>
							<el-row class="box-header-comment">
								<el-col :span="4">
									<el-button
										class="nav-button"
										icon="el-icon-arrow-right"
										@click="hideIssueDetails()"
										circle
									></el-button>
								</el-col>
								<el-col :span="16" class="text-center">
									<template
										v-if="
											project.project_type == 'website' &&
											(current_role == 'freelancer' || current_role == 'agency') &&
											current_issue.status == 'done'
										"
									>
										<el-button class="check-button"
											>Approved</el-button
										>
									</template>
									<template
										v-else-if="
											project.project_type == 'website' &&
											current_role == 'client' &&
											current_issue.status == 'todo'
										"
									>
										<el-dropdown split-button type="default">In Review
											<el-dropdown-menu slot="dropdown">
												<el-dropdown-item @click.native="toggleIssueStatus(
														current_issue,
														'done'
													)
												" icon="el-icon-circle-check">Approve</el-dropdown-item>
											</el-dropdown-menu>		
										</el-dropdown>
									</template>
									<template v-else>
										<el-checkbox
											v-model="current_issue.isCheck"
											:label="
												project.project_type ==
													'website' &&
												current_issue.status != 'todo'
													? current_issue.status ==
													  'review'
														? current_role ==
														  'client'
															? 'Approve'
															: 'In Review'
														: 'Approved'
													: current_issue.status
											"
											:disabled="
												project.project_type ==
													'design' &&
												current_role == 'client'
													? true
													: false
											"
											@change="
												toggleIssueStatus(current_issue)
											"
											class="check-button"
											border
										></el-checkbox>
									</template>
								</el-col>
								<el-col :span="4" class="text-right">
									<el-button
										:type="
											current_issue.owner_type ==
											'freelancer'
												? 'info'
												: 'danger'
										"
										@click="
											showIssueGroup(current_issue.group)
										"
										class="issue-label"
										circle
										>{{ current_issue.label }}</el-button
									>
								</el-col>
							</el-row>
							<el-row>
								<el-col :span="4">
									<el-tooltip
										:content="current_issue.user.name"
									>
										<img
											class="user-avatar"
											:src="current_issue.user.photo_url"
										/>
									</el-tooltip>
								</el-col>
								<el-col :span="19">
									<div class="datetime" :class="current_issue.status == 'done' ? 'tw-text-white' : '' ">
										{{
											current_issue.created_at
												| utc_to_local
										}}
									</div>
									<div
										class="description"
										@click="editIssue(current_issue, -1)"
									>
										<span
											v-html="
												urlify(
													current_issue.description
												)
											"
										></span>
									</div>
									<div
										class="datetime"
										v-if="['video', 'website'].includes(project.project_type) && current_proof.project_files && current_proof.project_files.file_type == 'video'"
									>
										Time point:
										{{
											current_issue.time_video
												? toHHMMSS(
														current_issue.time_video
												  )
												: toHHMMSS(0)
										}}
									</div>
									<div
										class="datetime"
										v-if="
											['video', 'website'].includes(project.project_type) && current_proof.project_files && current_proof.project_files.file_type == 'video' &&
											current_issue.length_video &&
											current_issue.length_video > 0
										"
									>
										Time point end:
										{{
											current_issue.time_video
												? toHHMMSS(
														parseFloat(
															current_issue.time_video
														) +
															parseFloat(
																current_issue.length_video
															)
												  )
												: toHHMMSS(0)
										}}
									</div>
									<div class="edit-issue">
										<el-input
											type="textarea"
											:autosize="{
												minRows: Math.round(
													current_issue.description
														.length / 20
												),
											}"
											v-model="current_issue.description"
											@keydown.native.enter.prevent="
												updateIssue(current_issue, -1)
											"
											class="edit-description"
											placeholder="Type comment here"
											autofocus
										></el-input>
										<div class="text-right">
											<el-upload
												class="upload-dialog"
												name="photos"
                                                :headers="headers"
												action="/api/files/upload"
												:ref="
													'upload_current_issue_' +
													current_issue.id
												"
												:data="uploadFormData"
												:file-list="uploadFileList"
												:on-success="
													onIssueImageUploaded
												"
												:auto-upload="false"
												multiple
											>
												<el-button
													slot="trigger"
													type="info"
													size="mini"
													icon="fa fa-paperclip"
													plain
												></el-button>
												<el-button
													size="mini"
													type="danger"
													class="update-button"
													icon="el-icon-delete"
													@click="
														deleteIssue(
															current_issue
														)
													"
													plain
												></el-button>
												<el-button
													type="primary"
													size="mini"
													class="update-button"
													@click="
														updateIssue(
															current_issue,
															-1
														)
													"
													>OK</el-button
												>
											</el-upload>
										</div>
									</div>
									<template v-if="current_issue.files">
										<div
											v-for="(file,
											key) in current_issue.files"
											class="attach-image"
											:key="file.id"
										>
											<img
												:src="`${spacePrefix}/${file.thumb_path}`"
											/>
											<div class="attach-overlay">
												<i
													class="el-icon-zoom-in attach-button"
													@click="
														openImagePreview(
															`${spacePrefix}/${file.thumb_path}`
														)
													"
												></i>
												<a
													class="attach-button"
													@click="
														handleFileDownload(
															file.thumb_path,
															key
														)
													"
												>
													<i
														class="el-icon-download"
													></i>
												</a>
												<i
													class="el-icon-delete attach-button"
													@click="
														deleteImage(
															current_issue,
															file,
															(type = 'issue')
														)
													"
												></i>
											</div>
										</div>
									</template>
								</el-col>
								<el-col :span="1">
									<el-dropdown
										v-if="
											current_issue.user_id ==
											current_user.id
										"
										trigger="click"
										class="more-button"
									>
										<span class="el-dropdown-link">
											<i class="el-icon-more"></i>
										</span>
										<el-dropdown-menu slot="dropdown">
											<el-dropdown-item
												v-if="
													current_role == 'client' &&
													current_issue.status !=
														'done'
												"
												@click.native="
													toggleIssueStatus(
														current_issue,
														'done'
													)
												"
												>Approve</el-dropdown-item
											>
											<el-dropdown-item
												@click.native="
													editIssue(current_issue, -1)
												"
												>Edit</el-dropdown-item
											>
											<el-dropdown-item
												@click.native="
													deleteIssue(current_issue)
												"
												>Delete</el-dropdown-item
											>
										</el-dropdown-menu>
									</el-dropdown>
								</el-col>
							</el-row>
						</el-card>

						<!-- New Comment Form -->
						<el-card class="new-issue" shadow="never">
							<el-form
								:model="newCommentForm"
								ref="newCommentForm"
								@submit.native.prevent="addNewComment()"
							>
								<el-form-item prop="description">
									<el-input
										type="textarea"
										@keydown.native.enter.prevent="
											addNewComment()
										"
										v-model="newCommentForm.description"
										placeholder="Type comment here"
										autosize
									></el-input>
								</el-form-item>
								<el-form-item class="form-buttons">
									<el-upload
										class="upload-dialog"
										name="photos"
                                        :headers="headers"
										action="/api/files/upload"
										ref="upload_new_comment"
										:data="uploadFormData"
										:file-list="uploadFileList"
										:on-success="onCommentImageUploaded"
										:auto-upload="false"
										multiple
									>
										<el-button
											slot="trigger"
											size="mini"
											icon="fa fa-paperclip"
										></el-button>
										<el-button
											type="primary"
											size="mini"
											@click="addNewComment()"
											class="add-button"
											>OK</el-button
										>
									</el-upload>
								</el-form-item>
							</el-form>
						</el-card>

						<!-- Comment List -->
						<el-card
							v-for="(comment, index) in reverse(
								current_issue.comments
							)"
							:key="comment.id"
							class="issue-box comment-box"
							shadow="never"
						>
							<el-row>
								<el-col :span="4">
									<el-tooltip :content="comment.user.name">
										<img
											class="user-avatar"
											:src="comment.user.photo_url"
										/>
									</el-tooltip>
								</el-col>
								<el-col :span="19">
									<div class="author-name">
										{{ comment.user.name }}
									</div>
									<div class="datetime">
										{{ comment.created_at | utc_to_local }}
									</div>
									<div
										class="description"
										@click="editComment(comment, index)"
									>
										<span
											v-html="urlify(comment.description)"
										></span>
									</div>
									<div class="edit-issue">
										<el-input
											type="textarea"
											:autosize="{
												minRows: Math.round(
													comment.description.length /
														20
												),
											}"
											v-model="comment.description"
											@keydown.native.enter.prevent="
												updateComment(issue, index)
											"
											class="edit-description"
											placeholder="Type comment here"
											autofocus
										></el-input>
										<div class="text-right">
											<el-upload
												class="upload-dialog"
												name="photos"
                                                :headers="headers"
												action="/api/files/upload"
												:ref="
													'upload_comment_' +
													comment.id
												"
												:data="uploadFormData"
												:file-list="uploadFileList"
												:on-success="
													onCommentImageUploaded
												"
												:auto-upload="false"
												multiple
											>
												<el-button
													slot="trigger"
													type="info"
													size="mini"
													icon="fa fa-paperclip"
													plain
												></el-button>
												<el-button
													size="mini"
													type="danger"
													class="update-button"
													icon="el-icon-delete"
													@click="
														deleteComment(comment)
													"
													plain
												></el-button>
												<el-button
													type="primary"
													size="mini"
													class="update-button"
													@click="
														updateComment(
															comment,
															index
														)
													"
													>OK</el-button
												>
											</el-upload>
										</div>
									</div>
									<template v-if="comment.files">
										<div
											v-for="(file, key) in comment.files"
											class="attach-image"
											:key="file.id"
										>
											<img
												:src="`${spacePrefix}/${file.thumb_path}`"
											/>
											<div class="attach-overlay">
												<i
													class="el-icon-zoom-in attach-button"
													@click="
														openImagePreview(
															`${spacePrefix}/${file.thumb_path}`
														)
													"
												></i>
												<a
													class="attach-button"
													@click="
														handleFileDownload(
															file.thumb_path,
															key
														)
													"
												>
													<i
														class="el-icon-download"
													></i>
												</a>
												<i
													class="el-icon-delete attach-button"
													@click="
														deleteImage(
															comment,
															file,
															(type = 'comment')
														)
													"
												></i>
											</div>
										</div>
									</template>
								</el-col>
								<el-col :span="1">
									<el-dropdown
										v-if="
											comment.user_id == current_user.id
										"
										trigger="click"
										class="more-button"
									>
										<span class="el-dropdown-link">
											<i class="el-icon-more"></i>
										</span>
										<el-dropdown-menu slot="dropdown">
											<el-dropdown-item
												@click.native="
													editComment(comment, index)
												"
												>Edit</el-dropdown-item
											>
											<el-dropdown-item
												@click.native="
													deleteComment(comment)
												"
												>Delete</el-dropdown-item
											>
										</el-dropdown-menu>
									</el-dropdown>
								</el-col>
							</el-row>
						</el-card>
					</div>
				</transition>

				<!-- <div class="getting-started">
                    <el-button class="link-button">
                        <a href="https://prooflo.com/video-tutorials/" target="_blank">Getting Started | Video Tutorials</a>
                    </el-button>
				</div>-->
			</div>
			<!-- Right Sidebar End -->
		</el-container>

		<el-footer v-if="isMobile" class="mobile-footer" height="100px">
			<el-row type="flex">
				<el-col class="footer-right">
					<el-button
						icon="el-icon-chat-dot-square"
						class="toggle-comments"
						@click="toggleComments()"
						>COMMENTS</el-button
					>
				</el-col>
				<el-col class="footer-main">
					<el-card
						v-for="(proof, index) in sortCurrentProof"
						:key="proof.id"
						@click.native="loadProof(proof)"
						class="proof-thumb"
						:class="{ active: current_proof.id == proof.id }"
					>
						<!-- <img class="thumb-image" :src="'/storage/' + proof.project_files.thumb_path"> -->
						<span class="proof-number">{{ proof.page_num }}</span>
						<img
							class="thumb-image"
							:src="`${spacePrefix}/${proof.project_files.thumb_path}?v${proof.project_files.id}`"
						/>
						<el-button
							v-if="
								current_role == 'freelancer' || current_role == 'agency' ||
								(current_role == 'client' &&
									proof.project_files.user_id ==
										current_user.id)
							"
							type="danger"
							icon="el-icon-delete"
							class="delete-button"
							@click="deleteProof(proof)"
							size="mini"
							plain
						></el-button>
						<div class="review-comment">
							<el-button-group>
								<el-button
									v-if="getReviewCount(proof)"
									type="warning"
									icon="el-icon-warning"
									>{{ getReviewCount(proof) }}</el-button
								>
								<el-button
									v-if="getunreadComments(proof)"
									type="danger"
									icon="fa fa-commenting"
									>{{ getunreadComments(proof) }}</el-button
								>
							</el-button-group>
						</div>
						<div
							v-if="proof.status == 'approved'"
							class="proof-check"
						>
							<i class="el-icon-check"></i>
						</div>
					</el-card>
				</el-col>
			</el-row>
		</el-footer>

		<!-- Upload Dialog -->
		<el-dialog
			title="Upload New Image"
			:visible.sync="showUploadDialog"
			@close="onCloseUploadDialog"
			class="upload-dialog"
		>
			<pdf-converting
				v-if="pdfLoader"
				v-on:confirm="pdfLoader = false"
			></pdf-converting>
			<el-upload
				name="photos"
                :headers="headers"
				list-type="picture"
				action="/api/files/upload"
				:data="uploadFormData"
				:file-list="uploadFileList"
				:before-upload="handleUpload"
				:before-remove="beforeRemove"
				:on-success="handleSuccess"
				drag
				multiple
			>
				<i class="el-icon-upload"></i>
				<div class="el-upload__text">
					Drop file here or
					<em>click to upload</em>
				</div>
			</el-upload>
			<span slot="footer" class="dialog-footer">
				<el-button @click="handelDeleteAllUploaded()">Cancel</el-button>
				<el-button type="primary" @click="sendUploadFiles()"
					>Add File</el-button
				>
			</span>
		</el-dialog>

		<el-dialog
			:visible.sync="showCreativeBriefDialog"
			class="project-details-dialog"
		>
			<span slot="title" class="display-title">
				<div class="title-icon">
					<svg
						xmlns="http://www.w3.org/2000/svg"
						viewBox="0 0 24 24"
						fill="none"
						stroke="currentColor"
						stroke-width="1.5"
						stroke-linecap="round"
						stroke-linejoin="round"
						class="feather feather-image"
					>
						<rect
							x="3"
							y="3"
							width="18"
							height="18"
							rx="2"
							ry="2"
						/>
						<circle cx="8.5" cy="8.5" r="1.5" />
						<polyline points="21 15 16 10 5 21" />
					</svg>
				</div>
				<span class="title">{{ project.name }}</span>
			</span>
			<project-details :project="project" />
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">
					<div class="tw-flex tw-items-center" v-if="project.owner">
						<div class="project-info">
                            <el-avatar
							class="avatar"
							:src="project.owner.photo_url"
                            ></el-avatar>
                            <div>
                                <div class="owner-name">
                                    {{ project.owner.name }}
                                </div>
                                <div class="details">
                                    <span>#{{ project.job_number }}</span>
                                    <span>
                                        <i class="el-icon-date"></i>
                                        {{ projectDate(project.created_at) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="tw-mx-8 tw-text-left" v-if="currentRole === 'Agency'">
                            <div class="tw-text-sm tw-text-gray-500 tw-mb-2">Team Members</div>
                            <el-tooltip v-for="member in project.teamMembers" :key="member.id" class="item" effect="dark" :content="member.name">
                                <el-avatar  :src="member.photo_url" :size="24"></el-avatar>
                            </el-tooltip>
                        </div>
					</div>
					<!-- <el-button type="primary" @click="saveCreativeBrief">Save</el-button> -->
					<el-button
						class="canacel-creative"
						@click="showCreativeBriefDialog = false"
						>Close</el-button
					>
				</div>
			</span>
		</el-dialog>

		<!-- New Revision Dialog -->
		<el-dialog
			title="Upload New Proofs"
			:visible.sync="showNewRevisionDialog"
			:close-on-click-modal="false"
			:close-on-press-escape="false"
			:show-close="false"
			@close="onCloseUploadDialog"
			class="upload-dialog"
		>
			<h4 class="text-success">Send proofs for next Revision Round</h4>
			<pdf-converting
				v-if="pdfLoader"
				v-on:confirm="pdfLoader = false"
			></pdf-converting>
			<el-upload
				name="photos"
                :headers="headers"
				list-type="picture"
				action="/api/files/upload"
				:data="uploadFormData"
				:file-list="uploadFileList"
				:before-upload="handleUpload"
				:on-success="handleSuccess"
				drag
				multiple
			>
				<i class="el-icon-upload"></i>
				<div class="el-upload__text">
					Drop file here or
					<em>click to upload</em>
				</div>
			</el-upload>
			<span slot="footer" class="dialog-footer">
				<el-button @click="cancelNewRevision()">Cancel</el-button>
				<el-button type="primary" @click="sendNewRivision()"
					>Send Proof</el-button
				>
			</span>
		</el-dialog>

		<!-- Send Corrections Dialog -->
		<el-dialog
			:visible.sync="showSendCorrectionsDialog"
			class="send-corrections"
		>
            <div class="text-center">
                <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" width="256" height="256" viewBox="0 0 570 511.67482" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M879.99927,389.83741a.99678.99678,0,0,1-.5708-.1792L602.86963,197.05469a5.01548,5.01548,0,0,0-5.72852.00977L322.57434,389.65626a1.00019,1.00019,0,0,1-1.14868-1.6377l274.567-192.5918a7.02216,7.02216,0,0,1,8.02-.01318l276.55883,192.603a1.00019,1.00019,0,0,1-.57226,1.8208Z" transform="translate(-315 -194.16259)" fill="#3f3d56"/><polygon points="23.264 202.502 285.276 8.319 549.276 216.319 298.776 364.819 162.776 333.819 23.264 202.502" fill="#e6e6e6"/><path d="M489.25553,650.70367H359.81522a6.04737,6.04737,0,1,1,0-12.09473H489.25553a6.04737,6.04737,0,1,1,0,12.09473Z" transform="translate(-315 -194.16259)" fill="#80b4a0"/><path d="M406.25553,624.70367H359.81522a6.04737,6.04737,0,1,1,0-12.09473h46.44031a6.04737,6.04737,0,1,1,0,12.09473Z" transform="translate(-315 -194.16259)" fill="#80b4a0"/><path d="M603.96016,504.82207a7.56366,7.56366,0,0,1-2.86914-.562L439.5002,437.21123v-209.874a7.00817,7.00817,0,0,1,7-7h310a7.00818,7.00818,0,0,1,7,7v210.0205l-.30371.12989L606.91622,504.22734A7.61624,7.61624,0,0,1,603.96016,504.82207Z" transform="translate(-315 -194.16259)" fill="#fff"/><path d="M603.96016,505.32158a8.07177,8.07177,0,0,1-3.05957-.59863L439.0002,437.54521v-210.208a7.50851,7.50851,0,0,1,7.5-7.5h310a7.50851,7.50851,0,0,1,7.5,7.5V437.68779l-156.8877,66.999A8.10957,8.10957,0,0,1,603.96016,505.32158Zm-162.96-69.1123,160.66309,66.66455a6.1182,6.1182,0,0,0,4.668-.02784l155.669-66.47851V227.33721a5.50653,5.50653,0,0,0-5.5-5.5h-310a5.50653,5.50653,0,0,0-5.5,5.5Z" transform="translate(-315 -194.16259)" fill="#3f3d56"/><path d="M878,387.83741h-.2002L763,436.85743l-157.06982,67.07a5.06614,5.06614,0,0,1-3.88038.02L440,436.71741l-117.62012-48.8-.17968-.08H322a7.00778,7.00778,0,0,0-7,7v304a7.00779,7.00779,0,0,0,7,7H878a7.00779,7.00779,0,0,0,7-7v-304A7.00778,7.00778,0,0,0,878,387.83741Zm5,311a5.002,5.002,0,0,1-5,5H322a5.002,5.002,0,0,1-5-5v-304a5.01106,5.01106,0,0,1,4.81006-5L440,438.87739l161.28027,66.92a7.12081,7.12081,0,0,0,5.43994-.03L763,439.02741l115.2002-49.19a5.01621,5.01621,0,0,1,4.7998,5Z" transform="translate(-315 -194.16259)" fill="#3f3d56"/><path d="M602.345,445.30958a27.49862,27.49862,0,0,1-16.5459-5.4961l-.2959-.22217-62.311-47.70752a27.68337,27.68337,0,1,1,33.67407-43.94921l40.36035,30.94775,95.37793-124.38672a27.68235,27.68235,0,0,1,38.81323-5.12353l-.593.80517.6084-.79346a27.71447,27.71447,0,0,1,5.12353,38.81348L624.36938,434.50586A27.69447,27.69447,0,0,1,602.345,445.30958Z" transform="translate(-315 -194.16259)" fill="#80b4a0"/></svg>
                <h4>
                    Do you want to send the corrections on
                    <span class="text-success"
                        >Rivision Round {{ current_revision.version }}</span
                    >
                    ?
                </h4>
            </div>
			<span slot="footer" class="dialog-footer">
				<el-button @click="showSendCorrectionsDialog = false"
					>No</el-button
				>
				<el-button type="primary" @click="sendCorrections"
					>Yes</el-button
				>
			</span>
		</el-dialog>

		<el-dialog
			:visible.sync="showNewClientDialog"
			class="project-details-dialog"
			:width="windowWidth <= 768 && '60%' || '90%'"
		>
			<span slot="title" class="display-title">
				<div class="title-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
				</div>
				<span class="title">Invite Collaborators</span>
			</span>
			<!-- <add-new-clients :showNewClientDialog="showNewClientDialog" /> -->
			<share :project="project" :project_id="project_id" type="proof" />
			<span slot="footer" class="dialog-footer">
				<div class="creative-footer">

					<!-- <el-button type="primary" @click="saveCreativeBrief">Save</el-button> -->
					<el-button class="canacel-creative" @click="showNewClientDialog = false">Close</el-button>
				</div>
			</span>
		</el-dialog>
		



		<!-- Image Preview Dialog -->
		<el-dialog :visible.sync="showImagePreviewDialog" class="image-preview">
			<img :src="imagePreviewUrl" />
		</el-dialog>

		<!-- Upload Final Files Dialog -->
		<final-files-dialog
			v-if="showFinalFilesDialog"
			:showFinalFilesDialog="showFinalFilesDialog"
			:currentRole="currentRole"
			:isBothRole="isBothRole"
			:project="project"
			:revisionId="revision_id"
			v-on:closeFinalFilesDialog="showFinalFilesDialog = false"
			v-on:filesSended="finalFilesSended"
            @approveProject="approveProject"
            @approvedSuccessfully="approvedSuccessfully"
			class="final-files-dialog"
		></final-files-dialog>

        <el-dialog class="success-dialog" width="70%" :show-close="false" :visible.sync="showSuccessfullyDialog">
            <div>
                <div class="title-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>
                <div class="title">Thanks you for your review! Any final files will be uploaded shortly.</div>
            </div>
            <el-button class="save-success" type="primary" @click="showSuccessfullyDialog = false">Close</el-button>
        </el-dialog>

		<!--Prooflo Simplified Dialog-->
		<plugin-install-dialog
			v-if="promptPluginInstall"
			:promptPluginInstall="promptPluginInstall"
			:pluginInstalled="pluginInstalled"
			v-on:continue="promptPluginInstall = false"
			v-on:takeScreenshots="captureScreen"
			v-on:installPlugin="installProofloPlugin"
		></plugin-install-dialog>
	</el-container>
</template>


<script>
	import Konva from "konva";
	import { Rate } from "element-ui";
    import pdfMake from "pdfmake/build/pdfmake";
    import { mapGetters } from 'vuex'

	export default {
		props: ["project_id", "revision_id", "proof_id", "issue_id"],

		mixins: [],

		components: {
			"final-files-dialog": require("./partials/FinalFilesDialog"),
			"plugin-install-dialog": require("./partials/PluginInstallDialog"),
			"pdf-converting": require("./partials/PdfConverting"),
			"project-details": require("./partials/ProjectDetails"),
			"share": require("./v3/Pages/CreateProject/components/shared/Share.vue"),
		},

		data() {
			return {
				// Loading
				fullscreenLoading: false,
                showSuccessfullyDialog: false,
				pdfLoading: false,
				saveAsPdfProgress: 0,
				colors: [
					{ color: "#f56c6c", percentage: 20 },
					{ color: "#e6a23c", percentage: 40 },
					{ color: "#6f7ad3", percentage: 60 },
					{ color: "#1989fa", percentage: 80 },
					{ color: "#5cb87a", percentage: 100 },
				],

				// User Data
				current_user: Spark.state.user,
				current_role: "",

				// Project Data
				project: {},
				current_revision: {},
				current_proof: {},
				current_issue: {},
				current_comment: {},
				creative_brief: "",

				// Sort Items By Issue and comment
				sortItem: "",
				sortItems: [
					{
						label: "Default sort",
						value: "default",
					},
					{
						label: "Sort by issues",
						value: "issues",
					},
					{
						label: "Sort by messages",
						value: "messages",
					},
				],

				// Canvas Data
				stage: {},
				layer: {},
				proof_image: {},

				// Canvas Status
				zoom: 0,
				zoom_old: 0,
				pen_active: false,
				showTooltip: true,

				// Issue Data
				newIssueForm: {
					description: "",
					active: false,
				},
				isIssueDetails: false,
				newCommentForm: {
					description: "",
				},

				// Upload data
				uploadFileList: [],
				uploadFormData: {},
				imagePreviewUrl: "",
				pdfLoader: false,

				// Dialogs
				showUploadDialog: false,
				showSendCorrectionsDialog: false,
				showNewRevisionDialog: false,
				showNewClientDialog: false,
				showFinalFilesDialog: false,
				showCreativeBriefDialog: false,
				showImagePreviewDialog: false,


				// Mobile
				isMobile: false,
				mobileStatus: "revision",

				// Quill Editor
				editor_option: {},

				// Prooflo Simplified
				pluginInstalled: false,
				promptPluginInstall: false,

				now: 0,
				nowIssue: 0,

				anim: null,
				video: null,
				fullTime: 0,
				isPlay: false,
				isMuted: false,
				volume: 100,
				points: [],
				handActive: true,
				isCurrentTimeUpdate: false,
				isMouseOverTimeline: false,
				forThumbVideoClone: false,
				thumbLayerGroup: false,
				copyText: '',
				issueDate: '',
				commentHover: [],
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
			};
		},

		methods: {
			async assignTask(user, issue) {
				try {
					let payload = {
						user_id: user.id,
						issue_id: issue.id
					}
					let { data } = await axios.post('/api/proof/issue/assisng', payload);
					let index = this.current_proof.issues.map(issue => issue.id).indexOf(data.data.id)
					let proof = this.project.last_revision.proofs.find(proof => proof.id == this.current_proof.id)
					proof.issues.splice(index, 1, data.data)
					
				} catch (error) {
					console.log(error)
				}
			},
			async addLike(issue) {
				try {
					let payload = {
						issue_id: issue.id
					}
					let { data } = await axios.post('/api/proof/issue/like', payload);
					let index = this.current_proof.issues.map(issue => issue.id).indexOf(data.data.id)
					let proof = this.project.last_revision.proofs.find(proof => proof.id == this.current_proof.id)
					proof.issues.splice(index, 1, data.data)
					
				} catch (error) {
					console.log(error)
				}
			},
			async issueChangeDate(issue) {
				try {
					let payload = {
						date: this.issueDate,
						issue_id: issue.id
					}
					let { data } = await axios.post('/api/proof/issue/due-date', payload);
					let index = this.current_proof.issues.map(issue => issue.id).indexOf(data.data.id)
					let proof = this.project.last_revision.proofs.find(proof => proof.id == this.current_proof.id)
					proof.issues.splice(index, 1, data.data)
					
				} catch (error) {
					console.log(error)
				}
			},
			async issuePriority(status, issue) {
				try {
					let payload = {
						status: status,
						issue_id: issue.id
					}
					let { data } = await axios.post('/api/proof/issue/priority', payload);
					let index = this.current_proof.issues.map(issue => issue.id).indexOf(data.data.id)
					let proof = this.project.last_revision.proofs.find(proof => proof.id == this.current_proof.id)
					proof.issues.splice(index, 1, data.data)
					
				} catch (error) {
					console.log(error)
				}
			},
			clipboardLink(copy) {
                self = this
                this.$copyText(copy).then(
                    function (e) {
                        self.copyText = 'copied'
                        setTimeout(() => {
                            self.copyText = 'copy'
                        }, 1500);
                    },
                    function (e) {
                        self.copyText = 'can not copy'
                    })
            },
			projectDate(date) {
				if (!date) return "";
				var utc = moment.utc(date).toDate();
				return moment(utc).local().format("MMMM DD, YYYY");
			},
			shortDate(date) {
				if (!date) return "";
				var utc = moment.utc(date).toDate();
				return moment(utc).local().format("MMMM DD");
			},
			handleFileDownload(file, index) {
				fetch(`${this.spacePrefix}/${file}`)
					.then((response) => response.blob())
					.then((blob) => {
						let link = document.createElement("a");
						const blobURL = URL.createObjectURL(blob);
						link.href = blobURL;
						link.download = `${this.project.company}_${
							this.project.name
						}_RR${this.project.versions[0].value}_${index + 1}`;
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
					})
					.catch((err) => {
						console.log(err);
					});
			},
			stopIssueTracker(issue) {
                axios
                    .put(
                        `/api/projects/project-timers/${issue.time_tracker.id}?stop=true`, {
                            end: moment().format("YYYY-MM-DD HH:mm:ss"),
                        }
                    )
                    .then((response) => {
                        if (response.data.status) {
							if (!issue.totalTracker) issue.totalTracker = 0
                            issue.time_tracker = response.data.data;
                            issue.totalTracker = parseInt(issue.totalTracker) + response.data.data.duration;
                            this.project.totalTracker = parseInt(this.project.totalTracker) + response.data.data.duration;

                            this.$notify({
                                title: "Success",
                                message: response.data.message,
                                type: "success",
                            });

                        } else {
                            this.$notify({
                                title: "Error",
                                message: response.data.errors[0],
                                type: "error",
                            });
                        }
                    })
                    .catch((error) => {
                        this.$notify({
                            title: "Error",
                            message: "Something went wrong please try again later",
                            type: "error",
                        });
                    });
            },
			startIssueTracker(issue) {
                axios
                    .post(`/api/projects/project-timers`, {
                        // description: this.dialogData.description
                        project_id: this.project.id,
                        issue_id: issue.id,
                        start: moment().format("YYYY-MM-DD HH:mm:ss"),
                    })
                    .then((response) => {
                        if (response.data.status) {
                            issue.time_tracker = response.data.data;

                            this.$notify({
                                title: "Success",
                                message: response.data.message,
                                type: "success",
                            });

                        } else {
                            this.$notify({
                                title: "Error",
                                message: response.data.errors[0],
                                type: "error",
                            });
                        }
                    })
                    .catch((error) => {
                        this.$notify({
                            title: "Error",
                            message: "Something went wrong please try again later",
                            type: "error",
                        });
                    });
            },
			startTracker() {
				// this.tracker.push(project.id);
				this.fullscreenLoading = true;
				axios
					.post(`/api/projects/project-timers`, {
						// description: this.dialogData.description
						project_id: this.project.id,
						start: moment().format("YYYY-MM-DD HH:mm:ss"),
					})
					.then((response) => {
						this.fullscreenLoading = false;
						if (response.data.status) {
							this.loadInitialRevision(
								this.project_id,
								this.revision_id
							);
							// this.dialogVisible = false;
							this.project["timeTracker"] = response.data.data;
							// this.tracker.pop(project.id);
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success",
							});
						} else {
							this.fullscreenLoading = false;
							this.$notify({
								title: "Error",
								message: response.data.errors[0],
								type: "error",
							});
						}
					})
					.catch((error) => {
						this.fullscreenLoading = false;
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error",
						});
					});
			},
			stopTracker(project) {
				this.fullscreenLoading = true;
				axios
					.put(
						`/api/projects/project-timers/${this.project.timeTracker.id}?stop=true`,
						{
							// description: this.dialogData.description
							end: moment().format("YYYY-MM-DD HH:mm:ss"),
						}
					)
					.then((response) => {
						this.fullscreenLoading = false;
						if (response.data.status) {
							this.loadInitialRevision(
								this.project_id,
								this.revision_id
							);
							// this.dialogVisible = false;
							this.project["timeTracker"] = response.data.data;
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success",
							});
						} else {
							this.fullscreenLoading = false;
							this.$notify({
								title: "Error",
								message: response.data.errors[0],
								type: "error",
							});
						}
					})
					.catch((error) => {
						this.fullscreenLoading = false;
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error",
						});
					});
			},
			projectDuration() {
				return moment
					.utc(this.project.totalTracker * 1000)
					.format("HH:mm:ss");
			},
			issueDuration(issue) {
				if (issue) {
					return moment
					.utc(issue.totalTracker * 1000)
					.format("HH:mm:ss");
				}
			},
			trackerTimer() {
				var diff = this.now.diff(this.project.timeTracker.start);
				return moment.utc(diff).format("HH:mm:ss");
			},
			trackerTimerIssue(time) {
				if (this.nowIssue) {
                    var diff = this.nowIssue.diff(time.start);
                    return moment.utc(diff).format("HH:mm:ss");
                }
			},
			//////////////////
			// Handle Error //
			//////////////////
			handle_error(errors) {
				this.$notify.error({
					title: "Error",
					message: "An error occurs!",
				});
				this.fullscreenLoading = false;
			},

			////////////////////
			// Goto Functions //
			////////////////////

			goToDashboard() {
				// Goto Projects Page
				this.$router.push({ name: "projects" });
			},

			goToRevision() {
				// Go To Proofer Page
				this.$router.push({
					name: "proofer",
					params: {
						project_id: this.project_id,
						revision_id: this.current_revision.id,
					},
				});

				// Load Revision Project
				this.loadInitialRevision(this.project_id, this.current_revision.id);
			},

			goToProof(proof) {
				var project_id = this.project_id;
				var revision_id = this.current_revision.id;
				var proof_id = proof.id;

				// Go To Proofer Page
				this.$router.push({
					path: `/proofer/${project_id}/${revision_id}/${proof_id}`,
				});

				// Load Revision Project
				this.loadProof(proof);
			},

			goToIssue(issue) {
				var project_id = this.project_id;
				var revision_id = this.current_revision.id;
				var proof_id = this.current_proof.id;
				var issue_id = issue.id;

				// Go To Proofer Page
				this.$router.push({
					path: `/proofer/${project_id}/${revision_id}/${proof_id}/${issue_id}`,
				});

				// Load Revision Project
				// this.loadInitialRevision(this.project_id, this.current_revision.id);
			},

			/////////////////////////
			// Load Init Functions //
			/////////////////////////

			async loadInitialRevision(project_id, revision_id) {
				this.fullscreenLoading = true;
				await axios
					.get("/api/projects/load/" + project_id + "/" + revision_id)
					.then((response) => {
						if (response.data.status == 1) {
							// Init project, revision, creative brief
							this.project = response.data.data;
							this.current_revision = this.project.last_revision;
							this.creative_brief = this.project.creative_brief;
							document.title = `${this.project.name}`;
							// Init plugin installation for client
							if (
								this.project.firstOpen &&
								this.project.project_type == "website" &&
								this.current_role == "client"
							) {
								this.promptPluginInstall = true;
							}

							if (this.proof_id) {
								let findProof = this.current_revision.proofs.find(
									(proof) => proof.id == this.proof_id
								);
								if (findProof) {
									this.loadProof(findProof);
									findProof.issues.forEach((issue) => {
										if (issue.id == this.issue_id) {
											this.current_issue = issue;
											this.showIssueDetails(issue);
										}
									});
								} else {
									// Load first proof
									this.loadProof(this.current_revision.proofs[0]);
								}
							} else if (this.current_revision.proofs[0]) {
								// Load first proof
								this.loadProof(this.current_revision.proofs[0]);
							} else {
								this.fullscreenLoading = false;
							}
						} else {
							this.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});

				// Init upload form data
				this.uploadFormData = {
					file_type: "picture",
					project_id: this.project_id,
					user_id: this.current_user.id,
					comment_id: "",
					owner_type: "",
				};
			},

			loadProof(proof) {
				// Set current proof
				if (!proof) {
					this.fullscreenLoading = false;
					// this.$alert(
					// 	"You don't have proofs, please upload at lest one.",
					// 	"Alert",
					// 	{
					// 		confirmButtonText: "OK",
					// 	}
					// );
					this.$message({
						type: "warning",
						message: "You don't have proofs, please upload at lest one.",
					});
					return;
				}
				this.current_proof = proof;
				this.current_proof.issues.forEach((issue) => {
					if (
						this.project.project_type == "website" &&
						(this.current_role == "freelancer" || this.current_role == "agency")
					) {
						issue.isCheck = issue.status == "review";
					} else {
						issue.isCheck = issue.status == "done";
					}
				});
				this.initNewIssue();
				this.hideIssueDetails();

				// Init stage
				this.initializeStage(proof);
			},

			deleteProof(proof) {
				this.$confirm("This will permanently delete the proof. Continue?", {
					confirmButtonText: "OK",
					cancelButtonText: "Cancel",
					type: "warning",
				}).then(() => {
					var index = this.current_revision.proofs.indexOf(proof);
					axios
						.delete("/api/proof/delete_proof/" + proof.id)
						.then((response) => {
							if (response.data.status == 1) {
								this.current_revision.proofs.splice(index, 1);
								this.loadInitialRevision(
									this.project_id,
									this.revision_id
								);

								this.$message({
									type: "success",
									message: "Delete completed",
								});
							} else {
								self.handle_error(response.data.errors);
							}
						})
						.catch((error) => {
							console.log(error);
						});
				});
			},

			saveAsPdf(proofs) {
				this.saveAsPdfProgress = 0;
				this.pdfLoading = true;
				var self = this;
				convertToPdf(0);

				var page_width, page_height;
				var progressCallbackMethod = function (progress) {
					// self.saveAsPdfProgress = progress * 100
					// console.log(progress)
					// if (progress == 1) {
					//     console.log(self.saveAsPdfProgress, progress)
					//     setTimeout(() => {
					//     }, 3000);
					// }
				};
				var content = [];
				function convertToPdf(count) {
					if (count < proofs.length) {
						getDataUri(proofs[count], function (imgData) {
							content.push({
								image: imgData,
								headlineLevel: 1,
							});
							count++;
							setTimeout(() => {
								convertToPdf(count);
								self.saveAsPdfProgress = Math.round(
									(count / proofs.length) * 100
								);
							}, 500);
						});
					} else {
						try {
							let fileName = "document.pdf";
							if (self.project.project_type == "design") {
								let version = self.project.versions.find(
									(item) => item.id == self.current_revision.id
								);
								if (proofs.length == 1) {
									fileName = `${self.project.name}Image${self.current_proof.page_num}RR${version.value}.pdf`;
								} else {
									fileName = `${self.project.name}ImagesRR${version.value}.pdf`;
								}
							} else {
								if (proofs.length == 1) {
									fileName = `${self.project.name}Image${self.current_proof.page_num}.pdf`;
								} else {
									fileName = `${self.project.name}Images.pdf`;
								}
							}
							pdfMake
								.createPdf({
									content: content,
									pageMargins: [0, 0, 0, 0],
									pageSize: {
										width: page_width,
										height: page_height,
									},
								})
								.download(fileName);
							setTimeout(() => {
								self.pdfLoading = false;
							}, 5000);
						} catch (error) {
							console.log(error);
							setTimeout(() => {
								self.pdfLoading = false;
							}, 5000);
						}
					}
				}
				function getDataUri(proof, callback) {
					var image = new Image();
					image.crossOrigin = "anonymous";
					image.onload = function () {
						var canvas = document.createElement("canvas");

						canvas.width = this.naturalWidth;
						canvas.height = this.naturalHeight;

						page_width = this.naturalWidth;
						page_height = this.naturalHeight;

						canvas.getContext("2d").drawImage(this, 0, 0);
						callback(canvas.toDataURL("image/png"));
					};
					image.src = `https://cors-anywhere.herokuapp.com/${self.spacePrefix}/${proof.project_files.path}?v${proof.project_files.id}`;
				}
			},

			getCurrentRole(project_id) {
				axios
					.get("/api/auth/getCurrentRole/" + project_id)
					.then((response) => {
						if (response.data.status == 1) {
							this.current_role = response.data.data.user_role;
						} else {
							this.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},

			getunreadComments(proof) {
				let unread = 0;
				proof.issues.forEach((issue) => {
					if (issue.unread_comments)
						unread += issue.unread_comments.length;
				});
				return unread;
			},

			getReviewCount(proof) {
				let count = 0;
				proof.issues.forEach((issue) => {
					if (this.current_role == "freelancer") {
						if (issue.status == "todo") count++;
					}
					if (this.current_role == "client") {
						if (issue.status == "todo" || issue.status == "done")
							count++;
					}
				});
				return count;
			},

			////////////////////////////
			// Canvas Stage Functions //
			////////////////////////////

			initializeStage(proof) {
				var self = this;
				if (proof.project_files.file_type != "video") {
					this.pause();
					var imageObj = new Image();
					imageObj.src = `${self.spacePrefix}/${proof.project_files.path}`;
					imageObj.onload = function () {
						if (!proof.canvas_data) {
							self.stage = new Konva.Stage({
								container: "stage",
							});
							self.layer = new Konva.Layer({
								draggable: true,
							});
							self.stage.add(self.layer);
							self.proof_image = new Konva.Image();
							self.layer.add(self.proof_image);
						} else {
							self.stage = Konva.Node.create(
								proof.canvas_data,
								"stage"
							);
							self.layer = self.stage.get("Layer")[0];
							self.proof_image = self.layer.get("Image")[0];
							self.layer.getChildren((node) => {
								if (node.getClassName() == "Group") {
									self.enableAnchorsDrag(node);
									node.on("dragend", () => {
										self.saveStage();
									});
								}
							});
						}
						self.proof_image.setImage(imageObj);
						self.fitStageSize();
						self.fitImageSize();
						self.layer.batchDraw();
						self.initPen();
						self.listenScroll()
						self.fullscreenLoading = false;
					};
				} else {
					this.pause();
					self.video = document.createElement("video");
					self.video.src = `${self.spacePrefix}/${proof.project_files.path}`;
					// self.video.muted = true;
					// self.video.onloadeddata = function() {
					self.video.addEventListener("loadedmetadata", function () {
						if (!proof.canvas_data) {
							self.stage = new Konva.Stage({
								container: 'stage',
							});
							self.layer = new Konva.Layer({
								draggable: false,
							});
							self.stage.add(self.layer);
							self.proof_image = new Konva.Image({
								draggable: false,
							});
							self.layer.add(self.proof_image);
						} else {
							self.stage = Konva.Node.create(
								proof.canvas_data,
								"stage"
							); 
							self.layer = self.stage.get("Layer")[0];
							self.proof_image = self.layer.get("Image")[0];
							self.layer.getChildren((node) => {
								if (node.getClassName() == "Group") {
									node.hide();
								}
							});
						}


						self.proof_image.setImage(self.video);

						self.stage.getChildren((node) => {
							if (node.draggable()) {
								node.draggable(false);
								self.saveStage();
							}
						});

						self.proof_image.width(self.video.videoWidth);
						self.proof_image.height(self.video.videoHeight);
						self.fitStageSize();
						self.fitImageSize("fitscreen");
						// if (self.video.duration === Infinity) {
						// 	}
						self.fullTime = self.video.duration;

						if (document.getElementById("controls")) {
							document.querySelector(
								".control-timeline"
							).style.width =
								(self.zoom / 100) * self.proof_image.width() + "px";
							document.getElementById("controls").style.bottom =
								self.stage.height() -
								(self.layer.getAttr("y") +
									(self.proof_image.height() * self.zoom) / 100) -
								85 +
								"px";
							// document.querySelector(
							// 	".control-timeline"
							// ).style.marginLeft = self.layer.getAttr("x") + "px";
						}
						self.proof_image.setImage(self.video);
						self.layer.add(self.proof_image);
						self.stage.add(self.layer);
						self.points = proof.issues;

						self.anim = new Konva.Animation(function () {
							// do nothing, animation just need to update the layer
						}, self.layer);
						self.layer.draw();
						self.initPen();
						self.fullscreenLoading = false;
						self.play();
						self.removeScroll()
					});

					self.video.addEventListener("timeupdate", function () {
						if (!self.isCurrentTimeUpdate) return;
						self.fullTime = self.video.duration;

						self.layer.batchDraw();
						self.pause();
						self.isCurrentTimeUpdate = false;
					});
				}
			},
			onMouseOverTimeline(e) {
				if (this.isMouseOverTimeline) {
					return;
				}
				this.isMouseOverTimeline = true;
				if (!this.forThumbVideoClone) {
					this.forThumbVideoClone = this.video.cloneNode(true);
				}
				let self = this;
				let timeline = document.getElementById("timeline");
				let newLeft = 0;
				let timelineWidth = timeline.offsetWidth;
				let newCurrentTime = 0;
				this.onMouseMoveTimeline = function onMouseMove(event) {
					newLeft = event.clientX - timeline.getBoundingClientRect().left;
					if (newLeft < 0) {
						newLeft = 0;
					}
					if (newLeft > timelineWidth) {
						newLeft = timelineWidth;
					}
					newCurrentTime = parseFloat(
						(self.fullTime * newLeft) / timelineWidth
					);
					self.forThumbVideoClone.currentTime = newCurrentTime;

					let scale = 0.11;
					let width = self.proof_image.width() * scale;
					let height = self.proof_image.height() * scale;
					let textHeight = height * 0.35;
					let fontSize = parseInt(textHeight / 1.6);
					let currentX =
						self.proof_image.width() * (newLeft / timelineWidth) -
						width / 2;
					let newX =
						self.proof_image.width() - currentX < width
							? self.proof_image.width() - width
							: currentX < 0
							? 0
							: currentX;

					if (!self.thumbLayerGroup) {
						self.thumbLayerGroup = new Konva.Group({
							name: "thumb",
							draggable: true,
							x: newX,
							y: self.proof_image.height() - height - textHeight,
						});
						self.layer.add(self.thumbLayerGroup);
					} else {
						self.thumbLayerGroup.setAttr("x", newX);
					}
					let image = self.thumbLayerGroup.findOne(".image");
					if (!image) {
						image = new Konva.Image({
							image: self.forThumbVideoClone,
							x: 0,
							y: 0,
							width: width,
							height: height,
							name: "image",
						});
						self.thumbLayerGroup.add(image);
					} else {
						image.image(self.forThumbVideoClone);
					}

					let texts = {
						textWhite: "rgba(255, 255, 255, 0.75)",
						textBlack: "rgba(0, 0, 0, 0.5)",
					};
					for (let key in texts) {
						let text = self.thumbLayerGroup.findOne("." + key);
						if (!text) {
							text = new Konva.Text({
								text: self.toHHMMSS(newCurrentTime),
								x: 0,
								width: width,
								fontFamily:
									"'Open Sans', Helvetica, Arial, sans-serif",
								fontSize: fontSize,
								fontStyle: "normal",
								fill: texts[key],
								align: "center",
								name: key,
							});
							text.y(height + (textHeight - text.getHeight()) / 2);
							self.thumbLayerGroup.add(text);
						} else {
							text.text(self.toHHMMSS(newCurrentTime));
						}
					}

					self.layer.draw();
				};
				document.addEventListener("mousemove", this.onMouseMoveTimeline);
			},
			onMouseLeaveTimeline(e) {
				if (!this.isMouseOverTimeline) {
					return;
				}
				this.isMouseOverTimeline = false;
				document.removeEventListener("mousemove", this.onMouseMoveTimeline);
				if (this.thumbLayerGroup) {
					this.thumbLayerGroup.destroy();
					this.thumbLayerGroup = false;
					this.layer.draw();
				}
            },
            async handlePoke() {
                try {
                    let { data } = await axios.post('/api/projects/poke', {
                        id: this.project.id
                    });
                    console.log(data);
                    
                } catch (error) {
                    console.log(error);
                }
            },
			setCurrentTime(e) {
				let self = this;
				e.preventDefault();
				let thumb = e.target;
				let timeline = document.getElementById("timeline");
				let currentWidth = document.querySelector(".current-time");

				document.addEventListener("mousemove", onMouseMove);
				document.addEventListener("mouseup", onMouseUp);

				self.layer.getChildren((node) => {
					if (node.getClassName() == "Group") {
						node.hide();
					}
				});

				let newLeft = 0;
				let timelineWidth = timeline.offsetWidth;
				let newCurrentTime = 0;
				function onMouseMove(event) {
					newLeft = event.clientX - timeline.getBoundingClientRect().left;
					if (newLeft < 0) {
						newLeft = 0;
					}
					if (newLeft > timelineWidth) {
						newLeft = timelineWidth;
					}
					newCurrentTime = parseFloat(
						(self.fullTime * newLeft) / timelineWidth
					);
					thumb.style.left = newLeft + "px";
					currentWidth.style.width =
						(newLeft / timelineWidth) * 100 + "%";
					newCurrentTime = parseFloat(
						(self.fullTime * newLeft) / timelineWidth
					);
					self.video.currentTime = newCurrentTime;
				}

				function onMouseUp() {
					document.removeEventListener("mouseup", onMouseUp);
					document.removeEventListener("mousemove", onMouseMove);
					self.isCurrentTimeUpdate = true;
					// let newCurrentTime = parseFloat(self.fullTime * newLeft / timelineWidth);
					self.anim.frame.time = newCurrentTime * 1000;
					self.video.currentTime = newCurrentTime;
				}
			},

			toHHMMSS(nums) {
				var sec_num = parseInt(nums, 10);
				var hours = Math.floor(sec_num / 3600);
				var minutes = Math.floor((sec_num - hours * 3600) / 60);
				var seconds = sec_num - hours * 3600 - minutes * 60;

				if (hours < 10) {
					hours = "0" + hours;
				}
				if (minutes < 10) {
					minutes = "0" + minutes;
				}
				if (seconds < 10) {
					seconds = "0" + seconds;
				}
				return (hours == "00" ? "" : hours + ":") + minutes + ":" + seconds;
			},
			mute() {
				if (!this.isMuted) {
					this.video.muted = true;
				} else {
					this.video.muted = false;
				}
				this.isMuted = !this.isMuted;
			},
			setVolume(volume) {
				this.volume = volume;
				this.video.volume = this.volume / 100;
			},
			pause() {
				if (this.video) {
					this.video.pause();
					this.anim.stop();
					this.isPlay = false;
				}
			},

			play() {
				// console.log('play()')
				if (this.percent >= 100) {
					this.video.load()
				}
				if (!this.isPlay) {
					var playPromise = this.video.play();

					if (playPromise !== undefined) {
						playPromise
							.then((_) => {
								// Automatic playback started!
								// Show playing UI.
								this.isPlay = true;
								return this.video.play();
							})
							.then((_) => {
								this.layer.getChildren((node) => {
									if (node.getClassName() == "Group") {
										node.hide();
									}
								});
								this.anim.start();
							})
							.catch((error) => {
								// Auto-play was prevented
								// Show paused UI.
							});
					}
				} else {
					this.pause();
				}
			},

			saveStage() {
				var self = this;
				if (this.thumbLayerGroup) {
					this.thumbLayerGroup.destroy();
					this.thumbLayerGroup = false;
				}
				var form_data = {
					revision_id: self.current_proof.revision_id,
					id: self.current_proof.id,
					canvas_data: self.stage.toJSON(),
				};

				axios
					.post("/api/proof/save", form_data)
					.then((response) => {
						if (response.data.status == 1) {
							self.current_proof.canvas_data =
								response.data.data[0].canvas_data;
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},

			initPen() {
				var self = this;
				var is_dragging = false;

				////////////////
				// Drag Group //
				////////////////
                let mainColor = '#eb5053';
                if (self.current_role == 'freelancer') {
                    mainColor = '#4a5259';
                } else if (self.current_role == 'agency') {
                    mainColor = '#020202';
                } else if (self.current_role == 'collaborator') {
                    mainColor = '#19418b';
                }
				var colors = {
					main: mainColor,
					white: "#FAFAFA",
				};

				var bt_group = new Konva.Group({
					name: "bt-group",
					draggable: true,
				});

				var bt_outline = new Konva.Rect({
					name: "bt-outline",
					stroke: colors.white,
				});
				bt_group.add(bt_outline);

				var bt_rect = new Konva.Rect({
					name: "bt-rect",
					stroke: colors.main,
				});
				bt_group.add(bt_rect);

				var bt_anchors = [];
				for (var i = 0; i < 3; i++) {
					var bt_anchor = new Konva.Circle({
						name: "bt-anchor",
						fill: colors.main,
						stroke: colors.white,
						draggable: true,
					});

					bt_anchors.push(bt_anchor);
					bt_group.add(bt_anchor);
				}

				var bt_circle = new Konva.Circle({
					name: "bt-circle",
					fill: colors.main,
					stroke: colors.white,
				});
				bt_group.add(bt_circle);

				var bt_text = new Konva.Text({
					name: "bt-text",
					fill: colors.white,
				});
				bt_group.add(bt_text);

				/////////////////
				// Mouse Event //
				/////////////////

				self.layer.on("mousedown touchstart", function (e) {
					if (
						!self.pen_active ||
						e.target.getClassName() != "Image" ||
						self.newIssueForm.active
					)
						return;

					if (['video', 'website'].includes(self.project.project_type) && self.current_proof.project_files && self.current_proof.project_files.file_type == 'video') {
						self.pause();
					}

					this.draggable(false);
					is_dragging = true;

					startDrag(getMousePos(e));
				});

				self.layer.on("mousemove touchmove", function (e) {
					if (!is_dragging) return;

					updateDrag(getMousePos(e));
				});

				self.layer.on("mouseup touchend", function (e) {
					if (!is_dragging) return;

					if (self.project.project_type != "video") {
						this.draggable(true);
					}

					is_dragging = false;

					saveDrag();
				});

				////////////////////
				// Drag Functions //
				////////////////////

				function startDrag(pos) {
					var scale = self.layer.scaleX();

					var issues = self.current_proof.issues;
					var count = issues.length
						? Number(issues[issues.length - 1].label) + 1
						: 1;

					bt_group.setAttrs({
						x: pos.x,
						y: pos.y,
						label: count,
						time_video:
							['video', 'website'].includes(self.project.project_type) && self.current_proof.project_files && self.current_proof.project_files.file_type == 'video'
								? self.video.currentTime
								: 0,
					});

					bt_text.setAttrs({
						text: count,
						align: "center",
					});

					self.adjustScaleofGroup(bt_group);

					self.layer.add(bt_group);
				}

				function updateDrag(pos) {
					var width = pos.x - bt_group.x();
					var height = pos.y - bt_group.y();

					bt_rect.setAttrs({
						width: width,
						height: height,
					});

					bt_outline.setAttrs({
						width: width,
						height: height,
					});

					bt_anchors[0].x(width);
					bt_anchors[1].setAttrs({
						x: width,
						y: height,
					});
					bt_anchors[2].y(height);

					self.layer.batchDraw();
				}

				function saveDrag() {
					// Add group into layer
					var group = self.convertGroup(bt_group);
					self.enableAnchorsDrag(group);
					group.on("dragend", () => {
						self.saveStage();
					});
					self.layer.add(group);

					// Remove drag group
					bt_rect.setAttrs({
						width: 0,
						height: 0,
					});
					bt_outline.setAttrs({
						width: 0,
						height: 0,
					});
					bt_anchors.forEach((anchor) => {
						anchor.position({ x: 0, y: 0 });
					});
					bt_group.remove();

					// Draw
					self.layer.batchDraw();

					// Ready new group
					self.newIssueForm.group = group;
					// self.newIssueForm.active = true;//added to resolve vue not defined
					Vue.set(self.newIssueForm, "active", true);

					// Hide issue details
					self.hideIssueDetails();
					// Show right sidebar
					if ($(".sidebar-right").hasClass("collapsed")) {
						$(".sidebar-right").removeClass("collapsed");
					}
					// Focus on the input
					Vue.nextTick(function () {
						self.$refs.newIssueForm_description.focus();
					});

					// For mobile
					if (self.isMobile) self.mobileStatus = "comments";
				}

				function getMousePos(e) {
					var scale = self.zoom / 100;
					var pos = self.stage.getPointerPosition();

					pos = {
						x: (pos.x - self.layer.x()) / scale,
						y: (pos.y - self.layer.y()) / scale,
					};
					pos = self.convertPosition(pos);

					return pos;
				}
			},

			convertGroup(drag_group) {
				var group = drag_group.clone({
					id: "group_" + drag_group.getAttr("label"),
				});
				return group;
			},

			adjustScaleofGroup(group) {
				var scale = this.layer.scaleX();
				group.getChildren((node) => {
					switch (node.name()) {
						case "bt-rect":
							node.setAttrs({
								strokeWidth: 2 / scale,
							});
							break;
						case "bt-outline":
							node.setAttrs({
								strokeWidth: 6 / scale,
							});
							break;
						case "bt-circle":
							node.setAttrs({
								radius: 16 / scale,
								strokeWidth: 3 / scale,
							});
							break;
						case "bt-text":
							node.setAttrs({
								fontSize: 16 / scale,
								offsetX: 9 / scale,
								offsetY: 7 / scale,
								width: 18 / scale,
							});
							break;
						case "bt-anchor":
							node.setAttrs({
								radius: 4 / scale,
								strokeWidth: 2 / scale,
							});
							break;
					}
				});
			},

			enableAnchorsDrag(group) {
				var circles = group.get("Circle");

				var rect = group.get("Rect")[1];
				var outline = group.get("Rect")[0];
				var text = group.get("Text")[0];

				circles.forEach((circle, index) => {
					var indexes = [
						(index + 2) % 4,
						index % 2 ? (index + 3) % 4 : (index + 1) % 4,
						index % 2 ? (index + 1) % 4 : (index + 3) % 4,
					];

					circle.on("dragmove", function (e) {
						var pos = circles[indexes[0]].position();
						var current_pos = circle.position();

						rect.position(pos);
						rect.width(current_pos.x - pos.x);
						rect.height(current_pos.y - pos.y);

						outline.position(pos);
						outline.width(current_pos.x - pos.x);
						outline.height(current_pos.y - pos.y);

						circles[indexes[1]].setX(current_pos.x);
						circles[indexes[2]].setY(current_pos.y);

						if (index == 0) {
							text.y(circles[indexes[2]].y());
						} else if (index == 2) {
							text.x(circles[indexes[1]].x());
						}
					});
				});
			},

			convertPosition(pos) {
				switch (this.layer.rotation()) {
					case 90:
						pos = {
							x: pos.y,
							y: -pos.x,
						};
						break;
					case 180:
						pos = {
							x: -pos.y,
							y: pos.x,
						};
					case 270:
						pos = {
							x: -pos.y,
							y: pos.x,
						};
				}
				return pos;
			},

			convertLengths(width, height) {
				switch (this.layer.rotation()) {
					case 0:
						return { width: width, height: height };
					case 90:
						return { width: -height, height: width };
					case 180:
						return { width: -width, height: -height };
					case 270:
						return { width: height, height: -width };
				}
			},

			////////////////////////
			// Set size Functions //
			////////////////////////

			fitStageSize() {
				var container = this.isMobile
					? document.getElementsByClassName("proof-container")[0]
					: this.stage.container();

				this.stage.width(container.offsetWidth);
				this.stage.height(container.offsetHeight);
			},

			fitImageSize(option) {
				var self = this;

				var project_type = self.project.project_type;

				var stageWidth = self.stage.width();
				var stageHeight = self.stage.height() - 145;

				var imageWidth = self.proof_image.width();
				var imageHeight = self.proof_image.height();

				if (self.layer.rotation() == 90 || self.layer.rotation() == 270) {
					var temp = imageWidth;
					imageWidth = imageHeight;
					imageHeight = temp;
				}

				var scale =
					project_type == "website" || option == "fullwidth"
						? stageWidth / imageWidth
						: Math.min(
								stageWidth / imageWidth,
								stageHeight / imageHeight
						  );

				if (option == "fitscreen") {
					scale = Math.min(
						stageWidth / imageWidth,
						stageHeight / imageHeight
					);
				}

				self.zoom = Math.floor(scale * 100);
				self.zoom_old = self.zoom;
				scale = self.zoom / 100;

				var lengths = self.convertLengths(
					self.proof_image.width(),
					self.proof_image.height()
				);
				var pos = {
					x:
						project_type == "website"
							? 0
							: (stageWidth - lengths.width * scale) / 2,
					y:
						project_type == "website"
							? 60
							: (stageHeight - lengths.height * scale) / 2 + 60,
				};

				if (option == "fitscreen") {
					pos = {
						x: (stageWidth - lengths.width * scale) / 2,
						y: (stageHeight - lengths.height * scale) / 2 + 60,
					};
				}

				self.layer.setAttrs({
					scaleX: scale,
					scaleY: scale,
					x: pos.x,
					y: pos.y,
				});
				self.layer.batchDraw();
			},

			setZoom(zoom) {
				var self = this;

				if (zoom) self.zoom = zoom;

				var scale = self.zoom / 100;
				var old_scale = self.zoom_old / 100;

				if (typeof self.layer.scale !== "function") return;

				/////////////////
				// Zoom Center //
				/////////////////

				self.layer.scale({ x: scale, y: scale });

				var pos_bg = self.layer.position();
				var pos_center = {
					x: self.stage.width() / 2,
					y: self.stage.height() / 2,
				};

				var diff_scale = scale - old_scale;

				var diff = {
					x: (diff_scale / old_scale) * (pos_bg.x - pos_center.x),
					y: (diff_scale / old_scale) * (pos_bg.y - pos_center.y),
				};
				self.layer.move(diff);
				self.zoom_old = self.zoom;

				/////////////////////
				// Scale Of Groups //
				/////////////////////

				self.layer.getChildren((node) => {
					if (node.getClassName() == "Group") {
						self.adjustScaleofGroup(node);
					}
				});

				// Draw
				self.layer.batchDraw();
			},

			rotateImage() {
				this.layer.rotate(90);
				if (this.layer.rotation() == 360) this.layer.rotation(0);

				var scale = this.layer.scaleX();
				var width = this.proof_image.width() * scale;
				var height = this.proof_image.height() * scale;

				var diff = {
					x: -width + height,
					y: -height - width,
				};

				switch (this.layer.rotation()) {
					case 90:
						diff = {
							x: width + height,
							y: height - width,
						};
						break;
					case 180:
						diff = {
							x: width - height,
							y: height + width,
						};
						break;
					case 270:
						diff = {
							x: -width - height,
							y: -height + width,
						};
						break;
				}

				this.layer.move({
					x: diff.x / 2,
					y: diff.y / 2,
				});

				this.layer.batchDraw();
			},

			togglePen() {
				if (this.handActive) {
					this.handActive = !this.handActive;
				}
				this.pen_active = !this.pen_active;
				this.showTooltip = false;
			},

			toggleHand() {
				let self = this;
				if (self.pen_active) {
					self.pen_active = !self.pen_active;
				}
				self.handActive = !self.handActive;
				if (!self.handActive) {
					self.layer.draggable(false);
				}

				let is_dragging = false;

				self.layer.on("mousedown touchstart", function (e) {
					if (
						!self.handActive ||
						e.target.getClassName() != "Image" ||
						self.newIssueForm.active
					)
						return;

					this.draggable(true);
					is_dragging = true;
				});

				self.layer.on("dragmove", function (e) {
					if (!is_dragging) return;

					if (document.getElementById("controls")) {
						document.getElementById("controls").style.bottom =
							self.stage.height() -
							(this.getAttr("y") +
								(self.proof_image.height() * self.zoom) / 100) -
							60 +
							"px";
						document.querySelector(
							".control-timeline"
						).style.marginLeft = this.getAttr("x") + "px";
					}
				});

				self.layer.on("mouseup touchend", function (e) {
					is_dragging = false;
				});
			},

			handlePageChange(page_number) {
				// var test= this.current_revision.proofs.filter(item => {
				//     return item.page_num == page_number
				// })
				this.loadProof(this.current_revision.proofs[page_number - 1]);
			},

			/////////////////////
			// Issue Functions //
			/////////////////////

			reverse: function (array) {
				if (!array) return;
				return array.slice().reverse();
			},

			addNewIssue() {
				var self = this;

				self.$refs["newIssueForm"].validate((valid) => {
					if (valid) {
						self.fullscreenLoading = true;

						let time =
							self.project.project_type == "video"
								? this.timeVideo
								: 0;

						var form_data = {
							proof_id: self.current_proof.id,
							description: self.newIssueForm.description,
							group: self.newIssueForm.group.getAttr("id"),
							label: self.newIssueForm.group.getAttr("label"),
							owner_type: self.current_role,
							project_type: self.project.project_type,
							time_video: time,
						};
						axios
							.post("/api/proof/create_issue", form_data)
							.then((response) => {
								if (response.data.status == 1) {
									self.current_issue = response.data.data[0];
									self.current_issue.isCheck = false;
									self.current_issue.comments = [];

									// Add issue to the list
									self.current_proof.issues.push(
										self.current_issue
									);
									self.saveStage();

									// Init new issue form
									self.initNewIssue();

									// Update progress
									self.project.total_issues++;

									// Upload attach file
									self.uploadFormData.owner_type = "issue";
									self.uploadFormData.issue_id =
										response.data.data[0].id;
									self.$refs["upload_new_issue"].submit();

									self.fullscreenLoading = false;
								} else {
									self.handle_error(response.data.errors);
								}
							})
							.catch((error) => {
								console.log(error);
							});
					} else {
						return false;
					}
				});
			},

            backGroupColor(issue) {
                if (issue.owner_type == 'client') {
                    return 'background-color: #eb5053;color:#fff'
                } else if (issue.owner_type == 'freelancer') {
                    return 'background-color: #4a5259;color:#fff'
                } else if (issue.owner_type == 'collaborator') {
                    return 'background-color: #19418b;color:#fff'
                } else if (issue.owner_type == 'agency') {
                    return 'background-color: #020202;color:#fff'
                }
            },

			cancelNewIssue() {
				// Remove group from canvas
				this.newIssueForm.group.remove();
				this.layer.batchDraw();

				// Init new issue form
				this.initNewIssue();
			},

			initNewIssue() {
				this.newIssueForm.active = false;

				setTimeout(() => {
					this.$refs["newIssueForm"].resetFields();
					this.newIssueForm = {
						description: "",
					};
				}, 100);
			},

			toggleIssueStatus(issue, status) {
				var self = this;

				if (status) {
					issue.status = "done";
				} else {
					if (self.project.project_type == "website") {
						if (self.current_role == "freelancer" || self.current_role == "agency") {
							issue.status = issue.isCheck ? "review" : "todo";
						} else {
							issue.status = issue.isCheck ? "done" : "todo";
						}
					} else {
						issue.status = issue.isCheck ? "done" : "todo";
					}
				}

				var colors = {
					main:
						issue.status == "done"
							? "#80B4A0"
							: issue.owner_type == "freelancer"
							? "#545C64"
							: "#Ef5B5B",
					cover: issue.status == "done" ? "rgba(128,180,160, .5)" : "",
				};

				self.fullscreenLoading = true;
				axios
					.get(
						"/api/proof/change_issue_status/" +
							issue.id +
							"/" +
							issue.status +
							"/" +
							this.project.project_type
					)
					.then((response) => {
						if (response.data.status == 1) {
							this.current_proof.status =
								response.data.data[0].status;

							// Update canvas group
							var group = self.layer.get("#" + issue.group)[0];
							group.getChildren((node) => {
								if (node.name() == "bt-rect") {
									node.setAttrs({
										stroke: colors.main,
										fill: colors.cover,
									});
								} else if (node.getClassName() == "Circle") {
									node.setAttr("fill", colors.main);
								}
							});
							self.layer.batchDraw();
							self.saveStage();

							// Update progress bar
							if (issue.status == "done") {
								self.project.solved_issues++;
							}
							if (issue.status == "todo") {
								if (
									self.project.project_type == "website" &&
									self.current_role == "freelancer"
								) {
									//
								} else {
									self.project.solved_issues--;
								}
							}
							self.project.percentage = Math.round(
								(self.project.solved_issues /
									self.project.total_issues) *
									100
							);

							self.fullscreenLoading = false;
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						this.handle_error(error);
					});
			},

			showIssueGroup(group_id) {
				var self = this;

				if (self.project.project_type != "video") {
					// Set fullscreen
					var scale = self.stage.width() / self.proof_image.width();
					self.setZoom(Math.floor(scale * 100));

					var group = self.layer.get("#" + group_id)[0];
					var scale = self.zoom / 100;

					var lengths = self.convertLengths(
						group.x() * scale,
						group.y() * scale
					);

					var pos = {
						x: self.layer.x() + lengths.width,
						y: self.layer.y() + lengths.height,
					};

					var diff = {
						x: self.stage.width() / 2 - pos.x,
						y: self.stage.height() / 2 - pos.y,
					};

					moveScreen();

					var i = 0;
					function moveScreen() {
						var sec = 20;
						if (i == sec - 1) return;

						self.layer.move({
							x: diff.x / sec,
							y: diff.y / sec,
						});
						self.layer.batchDraw();

						i++;
						setTimeout(moveScreen, 1);
					}
				} else {
					let issue = group_id;
					self.isCurrentTimeUpdate = true;
					self.anim.frame.time = parseFloat(issue.time_video) * 1000;
					self.video.currentTime = issue.time_video;

					self.layer.getChildren((node) => {
						if (
							node.getClassName() == "Group" &&
							node.getAttr("time_video") >
								parseFloat(issue.time_video) - 0.5 &&
							node.getAttr("time_video") <
								parseFloat(issue.time_video) + 0.5
						) {
							node.show();
						} else if (
							node.getClassName() == "Group" &&
							node.getAttr("label") != issue.label
						) {
							node.hide();
						}
					});
					self.layer.batchDraw();
					self.showIssueDetails(issue);
				}
			},
			resizePoint(event, p) {
				event.stopImmediatePropagation();
				event.preventDefault();
				let timeline = document.getElementById("timeline");
				if (!timeline) {
					return;
				}
				let timeLineWidth = document.getElementById("timeline").clientWidth;
				let marker = event.currentTarget.parentElement;
				let self = this;
				let oldLength = p.length_video;
				this.$set(p, "active", true);

				document.addEventListener("mousemove", onMouseMove);
				document.addEventListener("mouseup", onMouseUp);

				function onMouseMove(event) {
					let shiftX = event.pageX - marker.getBoundingClientRect().left;
					let timeLengt = (self.fullTime * shiftX) / timeLineWidth;
					if (timeLengt < 0) {
						timeLengt = 0;
					} else if (timeLengt > self.fullTime - p.time_video) {
						timeLengt = self.fullTime - p.time_video;
					}
					p.length_video = timeLengt;
				}

				function onMouseUp() {
					self.$set(p, "active", false);
					document.removeEventListener("mouseup", onMouseUp);
					document.removeEventListener("mousemove", onMouseMove);
					if (oldLength != p.length_video) {
						var formData = {
							id: p.id,
							length_video: p.length_video,
						};
						axios
							.post("/api/proof/create_issue", formData)
							.then((response) => {
								if (response.data.status != 1) {
									p.length_video = oldLength;
									self.handle_error(response.data.errors);
								}
							})
							.catch((error) => {
								p.length_video = oldLength;
								console.log(error);
							});
					}
				}
			},
			point(p) {
				let styles = {};
				if (document.getElementById("timeline")) {
					let width = document.getElementById("timeline").clientWidth;
					styles.left = (p.time_video * width) / this.fullTime + "px";
					let length = (p.length_video * width) / this.fullTime;
					if (length > 22) {
						styles.width =
							(p.length_video * width) / this.fullTime + "px";
					}
				}
				return styles;
			},
			timelineMarker(p) {
				let styles = {
					width: 1,
				};
				if (document.getElementById("timeline")) {
					let width = document.getElementById("timeline").clientWidth;
					styles.width =
						Math.max(1, (p.length_video * width) / this.fullTime) +
						"px";
				}
				return styles;
			},
			showIssueDetails(issue) {
				var self = this;

				self.current_issue = issue;
				self.isIssueDetails = true;

				this.goToIssue(issue);

				axios
					.delete("/api/proof/delete_issue_unread_comments/" + issue.id)
					.then((response) => {
						if (response.data.status == 1) {
							issue.unread_comments = [];
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						self.handle_error(error);
					});
			},

			hideIssueDetails() {
				this.isIssueDetails = false;
				let project_id = this.project_id;
				let revision_id = this.current_revision.id;
				let proof_id = this.current_proof.id;

				// Go To Proofer Page
				this.$router.push({
					path: `/proofer/${project_id}/${revision_id}/${proof_id}`,
				});
			},

			editIssue(issue, index) {
				if (issue.user_id != this.current_user.id) return;
				this.current_issue = issue;

				if (index < 0) {
					$(".current-issue").addClass("editable");
				} else {
					$($(".issue-box")[index]).addClass("editable");
				}
			},

			deleteIssue(issue) {
				this.$confirm("This will permanently delete the issue. Continue?", {
					confirmButtonText: "OK",
					cancelButtonText: "Cancel",
					type: "warning",
				}).then(() => {
					var index = this.current_proof.issues.indexOf(issue);
					axios
						.delete("/api/proof/delete_issue/" + issue.id)
						.then((response) => {
							if (response.data.status == 1) {
								this.current_proof.issues.splice(index, 1);
								if (this.isIssueDetails == true) {
									this.isIssueDetails = false;
								}
								this.layer.get("#" + issue.group)[0].remove();
								this.saveStage();

								this.$message({
									type: "success",
									message: "Delete completed",
								});
							} else {
								self.handle_error(response.data.errors);
							}
						})
						.catch((error) => {
							console.log(error);
						});
				});
			},

			updateIssue(issue, index) {
				if (index < 0) {
					$(".current-issue").removeClass("editable");
				} else {
					$($(".issue-box")[index]).removeClass("editable");
				}

				var formData = {
					id: issue.id,
					description: issue.description,
				};
				axios
					.post("/api/proof/create_issue", formData)
					.then((response) => {
						if (response.data.status == 1) {
							this.uploadFormData.owner_type = "issue";
							this.uploadFormData.issue_id = issue.id;

							if (index < 0) {
								this.$refs[
									"upload_current_issue_" + issue.id
								].submit();
							} else {
								this.$refs["upload_issue_" + issue.id][0].submit();
							}
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},

			onIssueImageUploaded(response) {
				this.current_issue.files.push({
					id: response.data.id,
					path: response.data.path,
					thumb_path: response.data.thumb,
				});

				// this.uploadFileList = [];
			},


			///////////////////////
			// Comment Functions //
			///////////////////////
			addNewComment() {
				var self = this;

				var form_data = {
					project_id: self.project_id,
					revision_id: self.revision_id,
					issue_id: self.current_issue.id,
					description: self.newCommentForm.description,
				};
				self.$refs["newCommentForm"].resetFields();

				self.fullscreenLoading = true;
				axios
					.post("/api/proof/add_comment", form_data)
					.then((response) => {
						if (response.data.status == 1) {
							self.current_comment = response.data.data[0];
							self.current_issue.comments.push(response.data.data[0]);

							if (
								self.project.project_type == "website" &&
								self.current_issue.status != "todo"
							) {
								self.current_issue.isCheck = false;
								self.toggleIssueStatus(self.current_issue);
							}

							self.uploadFormData.owner_type = "comment";
							self.uploadFormData.comment_id =
								self.current_comment.id;
							self.$refs["upload_new_comment"].submit();

							this.fullscreenLoading = false;
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},

			onCommentImageUploaded(response) {
				this.current_comment.files.push({
					id: response.data.id,
					path: response.data.path,
					thumb_path: response.data.thumb,
				});

				// this.uploadFileList = [];
			},

			editComment(comment, index) {
				if (comment.user_id != this.current_user.id) return;
				this.current_comment = comment;
				$($(".comment-box")[index]).addClass("editable");
			},

			deleteComment(comment) {
				var self = this;
				this.$confirm(
					"This will permanently delete the comment. Continue?",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning",
					}
				).then(() => {
					var index = this.current_issue.comments.indexOf(comment);
					axios
						.delete("/api/proof/delete_comment/" + comment.id)
						.then((response) => {
							if (response.data.status == 1) {
								self.current_issue.comments.splice(index, 1);
								self.current_comment = {};
								this.loadInitialRevision(
									this.project_id,
									this.revision_id
								);
								this.$message({
									type: "success",
									message: "Delete completed",
								});
							} else {
								self.handle_error(response.data.errors);
							}
						})
						.catch((error) => {
							console.log(error);
						});
				});
			},

			updateComment(comment, index) {
				$($(".comment-box")[index]).removeClass("editable");
				this.fullscreenLoading = true;

				var formData = {
					id: comment.id,
					description: comment.description,
					issue_id: this.current_issue.id,
					revision_id: this.revision_id,
					project_id: this.project_id,
				};
				axios
					.post("/api/proof/add_comment", formData)
					.then((response) => {
						if (response.data.status == 1) {
							this.uploadFormData.owner_type = "comment";
							this.uploadFormData.comment_id = comment.id;
							this.uploadFormData.issue_id = this.current_issue.id;
							this.$refs["upload_comment_" + comment.id][0].submit();

							this.fullscreenLoading = false;
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
            },

            getCookie(cName) {
                const name = cName + "=";
                const cDecoded = decodeURIComponent(document.cookie); //to be careful
                const cArr = cDecoded.split('; ');
                let res;
                cArr.forEach(val => {
                    if (val.indexOf(name) === 0) res = val.substring(name.length);
                })
                return res
            },         

			//////////////////////
			// Dialog Functions //
			//////////////////////

			onCloseUploadDialog() {
				this.uploadFileList = [];
			},

			// Upload Dialog
			openUploadDialog() {
				this.uploadFormData.owner_type = "proof";
				this.showUploadDialog = true;
			},
			handleUpload(file) {
				if (file.type == "application/pdf") {
					this.pdfLoader = true;
				}
			},
			beforeRemove(file) {
				var file_id;
				if (file.response) {
					file_id = file.response.data.id;
				} else {
					file_id = file.id;
				}
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
						.delete(`/api/files/delete/${file_id}`, {
							params: {
								type: "proof"
							}
						})
						.then(response => {
							if (response.data.status == 1) {
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
			async handelDeleteAllUploaded() {
				if (this.uploadFileList.length > 0) {
					await axios
						.delete(`/api/files/delete`, {
							params: {
								type: "proof",
								files: this.uploadFileList
							}
						})
						.then(response => {
							if (response.data.status == 1) {
								this.$message({
									type: "success",
									message: "Delete completed"
								});
								this.showUploadDialog = false
							} else {
								this.handle_error(response.data.errors);
							}
						})
						.catch(error => {
							console.log(error);
						});
				} else {
					this.showUploadDialog = false
				}
			},
			handleSuccess(response) {
				var self = this;
				if (response.status == 1) {
					if (response.data.length) {
						response.data.forEach(function (element, index) {
							self.uploadFileList.push({
								name: "Converted Image-" + (index + 1),
								url: `${self.spacePrefix}/${element.path}`,
							});
						});
						setTimeout(function () {
							self.pdfLoader = false;
						}, 500);
					} else if (response.data) {
						self.uploadFileList.push({
							id: response.data.id,
							name: `Proof-${response.data.id}`,
							url: `${self.spacePrefix}/${response.data.path}`,
						});
					}
				} else {
					this.pdfLoader = false;
					toastr["error"](response.errors[0], "Error");
				}
			},
			async sendUploadFiles() {
				try {
					this.showUploadDialog = false;
					await this.loadInitialRevision(
						this.project_id,
						this.revision_id
					);
					setTimeout(() => {
						this.$router.push({
							name: "proofer_proof",
							params: {
								project_id: this.project_id,
								revision_id: this.revision_id,
								proof_id: this.project.last_revision.proofs[
									this.project.last_revision.proofs.length - 1
								].id,
							},
						});
						this.goToProof(
							this.project.last_revision.proofs[
								this.project.last_revision.proofs.length - 1
							]
						);
					}, 300);
					console.log(
						this.project.last_revision.proofs[
							this.project.last_revision.proofs.length - 1
						].id
					);
				} catch (error) {}
			},

			// Creative Brief Dialog
			openCreativeBriefDialog() {
				this.showCreativeBriefDialog = true;
			},
			saveCreativeBrief() {
				this.fullscreenLoading = true;
				let formData = {
					project_id: this.project_id,
					creative_brief: this.creative_brief,
				};
				axios
					.post("/api/projects/save-creative-brief", formData)
					.then((response) => {
						this.fullscreenLoading = false;
						if (response.data.status) {
							this.showCreativeBriefDialog = false;
							this.loadInitialRevision(
								this.project_id,
								this.revision_id
							);
							this.$notify({
								title: "Success",
								message: response.data.message,
								type: "success",
							});
						} else {
							this.$notify({
								title: "Error",
								message: response.data.errors,
								type: "error",
							});
						}
					})
					.catch((error) => {
						this.fullscreenLoading = false;
						this.$notify({
							title: "Error",
							message: "Something went wrong please try again later",
							type: "error",
						});
					});
			},
			clearEditor() {
				this.creative_brief = "";
			},
			resetDefault() {
				this.creative_brief = this.project.creative_brief;
			},

			// New Revision Dialog
			openNewRevisionDialog() {
				axios
					.post("/api/revisions/create", {
						project_id: this.project_id,
					})
					.then((response) => {
						if (response.data.status == 1) {
							this.uploadFormData.owner_type = "proof";
							this.showNewRevisionDialog = true;
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},
			sendNewRivision() {
				this.showNewRevisionDialog = false;
				this.fullscreenLoading = true;

				axios
					.get(
						"/api/projects/send_project/" +
							this.project_id +
							"/" +
							this.current_role
					)
					.then((response) => {
						if (response.data.status == 1) {
							this.fullscreenLoading = false;
							var revision_id = response.data.data[0];
							this.loadInitialRevision(this.project_id, revision_id);
							// this.$alert(
							// 	"Revision round has been sent by email successfully",
							// 	"Success",
							// 	{
							// 		confirmButtonText: "OK",
							// 	}
							// );

							this.$message({
								type: "success",
								message: "Revision round has been sent by email successfully",
							});
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},
			cancelNewRevision() {
				axios
					.delete("/api/revisions/delete/" + this.project_id)
					.then((response) => {
						if (response.data.status == 1) {
							this.showNewRevisionDialog = false;
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},

			sendCorrections() {
				let self = this;
				this.showSendCorrectionsDialog = false;
				this.fullscreenLoading = true;

				axios
					.get("/api/projects/submit_corrections/" + this.project_id)
					.then((response) => {
						if (response.data.status == 1) {
							// Identify project owner on Gist
							convertfox.identify(response.data.data.owner.id, {
								email: response.data.data.owner.email,
								name: response.data.data.owner.name,
							});

							// Trigger 'Send Corrections' event
							convertfox.track("Send Corrections", {
								userId: response.data.data.owner.id,
								projectId: this.project_id,
							});

							this.fullscreenLoading = false;
							this.current_revision.status_revision =
								response.data.data.revision.status_revision;
							this.project.project_status =
								response.data.data.revision.status_revision;
							this.$notify({
								title: "Success",
								message: "Corrections sent successfully",
								type: "success",
							});
						} else {
							self.handle_error(response.data.errors);
						}
					})
					.catch((error) => {
						console.log(error);
					});
			},

			// Move to Completed
			moveToCompleted() {
				this.$confirm(
					"Are you sure you want to move this project to Completed?",
					"Warning",
					{
						confirmButtonText: "Yes, move",
						cancelButtonText: "Cancel",
						type: "success",
						title: "",
					}
				).then(() => {
					axios
						.put(
							"/api/projects/change_status/" +
								this.project_id +
								"/completed"
						)
						.then((response) => {
							if (response.status === 200) {
								this.project.project_status = "completed";

								// this.$alert(
								// 	"Project successfully moved to Completed",
								// 	"",
								// 	{
								// 		confirmButtonText: "OK",
								// 	}
								// );
								this.$message({
									type: "success",
									message: "Project successfully moved to Completed",
								});
							} else {
								toastr["error"](response.error, "Error");
							}
						});
				});
			},

			openImagePreview(image_url) {
				this.imagePreviewUrl = image_url;
				this.showImagePreviewDialog = true;
			},

			deleteImage(item, file, type) {
				console.log(item);
				let self = this;
				this.$confirm("This will permanently delete the file. Continue?", {
					confirmButtonText: "OK",
					cancelButtonText: "Cancel",
					type: "warning",
				}).then(() => {
					axios
						.delete("/api/files/delete/" + file.id, {
							params: {
								type: type,
							},
						})
						.then((response) => {
							if (response.data.status == 1) {
								item.files.forEach(function (element, index) {
									if (element.id == file.id) {
										item.files.splice(index, 1);
										return;
									}
								});
								this.$message({
									type: "success",
									message: "Delete completed",
								});
							} else {
								this.handle_error(response.data.errors);
							}
						})
						.catch((error) => {
							console.log(error);
						});
				});
			},

			finalFilesSended(message) {
				this.showFinalFilesDialog = false;
				this.$notify({
					title: "Success",
					message: message,
					type: "success",
				});
			},

			////////////////////////////////
			// New Client Functions //
			////////////////////////////////

			handleDeleteClient(row) {
				this.$confirm(
					"This will permanently delete the client. Continue?",
					{
						confirmButtonText: "OK",
						cancelButtonText: "Cancel",
						type: "warning",
					}
				).then(() => {
					var index = this.project.clients.indexOf(row);
					axios
						.delete(
							`/api/projects/delete-team-member/${this.project_id}/${row.id}`
						)
						.then((response) => {
							if (response.data.status == 1) {
								this.project.clients.splice(index, 1);
								this.$message({
									type: "success",
									message: response.data.message,
								});
							} else {
								this.$message({
									type: "success",
									message: response.data.errors,
								});
							}
						})
						.catch((error) => {
							toastr["error"](
								"Something went wrong, please try again later",
								"Error"
							);
						});
				});
			},


			toggleSidebar(sidebar) {
				$(".sidebar-" + sidebar).toggleClass("collapsed");
				setTimeout(() => {
					this.fitStageSize();
				}, 300);
			},

			////////////////////////
			// Revision Functions //
			////////////////////////
            approveReview() {
                let self = this
                this.$confirm("Are you sure you want to approve this project?", {
                    confirmButtonText: "OK",
                    cancelButtonText: "Cancel",
                    type: "warning",
                }).then(() => {
                    if (this.project.project_type == 'website' && !this.rate && !this.review) {
                        this.showFinalFilesDialog = true
                    } else {
                        this.approveProject()
                    }
                });            
            },
			approveProject() {
                this.fullscreenLoading = true;
                var formData = {
                    project_id: this.project_id,
                    revision_id: this.revision_id,
                    decision: "approved",
                };
                axios
                    .post("/api/projects/approve_project", formData)
                    .then((response) => {
                        if (response.data.status == 1) {

                            if (this.project.project_type == 'website') {
                                this.showSuccessfullyDialog = true
                            }

                            // Identify client on Gist
                            convertfox.identify(response.data.data.client.id, {
                                email: response.data.data.client.email,
                                name: response.data.data.client.name,
                            });

                            // Trigger 'Approve Project' event
                            convertfox.track("Approve Project", {
                                userId: response.data.data.client.id,
                                projectId: this.project_id,
                            });

                            this.loadInitialRevision(
                                this.project_id,
                                this.revision_id
                            );
                            this.fullscreenLoading = false;

                            this.$notify({
                                title: "Success",
                                message:
                                    "The decision has been sent by email successfully",
                                type: "success",
                            });
                        } else {
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch((error) => {
                        self.handle_error(error);
                    });
			},

			//////////////////////
			// Mobile Functions //
			//////////////////////
			detectMobile() {
				var testExp = new RegExp(
					"Android | webOS | iPhone | iPad |" +
						"BlackBerry | Windows Phone" +
						"Opera Mini | IEMobile | Mobile" +
						"i"
				);
				if (testExp.test(navigator.userAgent)) this.isMobile = true;
			},

			enableStageEditable() {
				if (this.mobileStatus == "revision") this.mobileStatus = "canvas";
			},

			toggleComments() {
				this.mobileStatus =
					this.mobileStatus == "comments" ? "canvas" : "comments";
			},

			//////////////////////
			// jQuery Functions //
			//////////////////////

			jqueryFunctions() {
				// Listen Stage Size
				this.listenStageSize();

				// Listen Zoom Buttons
				this.listenZoomButtons();

				// if (this.current_proof.project_files && this.current_proof.project_files.file_type != 'video') {
					// Enable Scroll
					this.listenScroll();
				// }
			},

			listenStageSize() {
				var self = this;
				// var resizeTimer;

				$(window).on("resize", function () {
					// clearTimeout(resizeTimer);
					// resizeTimer = setTimeout(function() {
					//     self.fitStageSize()
					// }, 250);

					self.fitStageSize();
				});
			},

			listenZoomButtons() {
				var self = this;

				$(".el-input-number__increase").click(function (event) {
					event.stopImmediatePropagation();

					if (self.zoom % 50 == 0) self.zoom += 50;
					else self.zoom += 50 - (self.zoom % 50);
				});
				$(".el-input-number__decrease").click(function (event) {
					event.stopImmediatePropagation();

					if (self.zoom % 50 == 0) self.zoom -= 50;
					else self.zoom -= self.zoom % 50;

					if (self.zoom < 25) self.zoom = 25;
				});
			},

			removeScroll() {
				var self = this;

				let isFirefox = typeof InstallTrigger !== "undefined";
				var event = isFirefox ? "DOMMouseScroll" : "mousewheel";
				$('.removeScroll').off(event);
			},

			listenScroll() {
				var self = this;
				let isFirefox = typeof InstallTrigger !== "undefined";
				var event = isFirefox ? "DOMMouseScroll" : "mousewheel";
				$("#stage").bind(event, function (event) {
					event.stopImmediatePropagation();
					event.preventDefault();

					var scale = self.layer.scaleX();
					var d = -60 * scale;

					if (
						event.originalEvent.wheelDelta > 0 ||
						event.originalEvent.detail < 0
					) {
						d = -d;
					}

					self.layer.move({ y: d });
					self.layer.batchDraw();
				});
			},

			urlify(text) {
				var urlRegex = /(https?:\/\/[^\s]+)/g;
				return text.replace(urlRegex, function (url) {
					return '<a href="' + url + '" target="_blank">' + url + "</a>";
				});
			},

			///////////////////////////////////
			// Prooflo Simplified functions //
			/////////////////////////////////
			checkExtension() {
				var img,
					self = this;
				img = new Image();
				img.src =
					"chrome-extension://" +
					Spark.extension_id +
					"/images/Icon_Initial_32.png";
				img.onload = function () {
					self.pluginInstalled = true;
				};
				img.onerror = function () {
					self.pluginInstalled = false;
				};
			},

			captureScreen() {
				// Send a message to the extension via the prooflo_ext_invoker.
				var prooflo_ext_invoker = document.getElementById(
					"prooflo_ext_invoker"
				);

				// Get required data for Prooflo Extension
				axios
					.get(
						`/api/v1/extension-data/${this.current_user.id}/${this.project_id}`
					)
					.then((response) => {
						if (response.status == 200) {
							let data = response.data.data;
							let extensionData = {
								appUrl: data.app_url,
								url: this.project.websiteURI,
								loginToken: data.token,
								projectId: this.project_id,
								redirectUrl: data.redirect_url,
							};

							// Call Extension
							prooflo_ext_invoker.contentWindow.postMessage(
								JSON.stringify(extensionData),
								"*"
							);
						} else {
							this.$notify({
								title: "Error",
								message: "API exception error occurred",
								type: "success",
							});
						}
					})
					.catch((error) => {
						this.$notify({
							title: "Error",
							message: "API exception error occurred",
							type: "error",
						});
					});
			},

			installProofloPlugin() {
				this.promptPluginInstall = false;
				this.loadInitialRevision(this.project_id, this.revision_id);

				window.open(
					"https://chrome.google.com/webstore/detail/prooflo-simplified/" +
						Spark.extension_id,
					"_blank"
				);
			},
		},

		watch: {
			pluginInstalled() {
				if (this.pluginInstalled) {
					let iFrame =
						'<iframe style="display:none" id="prooflo_ext_invoker" src="chrome-extension://' +
						Spark.extension_id +
						'/invoker.html"></iframe>';
					$(".page-proof").append(iFrame);
				} else {
					$("#prooflo_ext_invoker").remove();
				}
			},
			percent(val) {
				if (val >= 100) {
					return this.pause()
				}
			}
		},

		computed: {
			spacePrefix() {
				return spacePrefix;
			},
			sortCurrentProof() {
				if (this.current_revision.proofs) {
					if (this.sortItem == "messages") {
						return this.current_revision.proofs.sort(
							(a, b) =>
								this.getunreadComments(b) -
								this.getunreadComments(a)
						);
					} else if (this.sortItem == "issues") {
						return this.current_revision.proofs.sort(
							(a, b) =>
								this.getReviewCount(b) - this.getReviewCount(a)
						);
					} else if (this.sortItem == "default") {
						return this.current_revision.proofs.sort(
							(a, b) => a.page_num - b.page_num
						);
					} else {
						return this.current_revision.proofs.sort(
							(a, b) => a.page_num - b.page_num
						);
					}
				}
			},
			percent() {
				if (this.timeVideo == 0 || this.fullTime == 0) {
					return 0;
				}
				return (this.timeVideo * 100) / this.fullTime;
			},
			timeVideo() {
				if (this.anim == null) {
					return 0;
				}
				return this.anim.frame.time / 1000;
            },
            ...mapGetters(['windowWidth','currentRole', 'isBothRole', 'user'])
		},

		created() {
			setInterval(() => {
				this.now = moment();
			}, 1000);
			setInterval(() => {
				this.nowIssue = moment();
			}, 1000);
			// Get current role
			this.getCurrentRole(this.project_id);
			// Init project
			this.loadInitialRevision(this.project_id, this.revision_id);
			// Detect Mobile
			this.detectMobile();
            // set to headers XSRF-TOKEN
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
    },

		mounted() {
			let self = this;

			// Remove navbar when from dashboard
			$(".with-navbar").css("padding", 0);
			$(".navbar").css("display", "none");

			// Check Prooflo Simplified extension status
			setInterval(function () {
				self.checkExtension();
			}, 1000);

			// jQuery Functions
			this.jqueryFunctions();
			// this.fitImageSize("fitscreen");
		},

		updated() {
			//
		},

		destroyed() {
			// Recover navbar when to dashboard
			$(".navbar").css("display", "block");
			if (this.project.project_type == "video") {
				this.pause();
			}
		},

		beforeRouteEnter(to, from, next) {
			//Check if project owner subscription is not expired
			if (to.params.project) {
				if (to.params.project.active) {
					next();
				} else {
					next(false);
				}
			} else {
				axios
					.get(`/api/projects/details/${to.params.project_id}`)
					.then((response) => {
						if (response.data.status) {
							if (response.data.data.active) {
								next();
							} else {
								window.location.href = "projects";
							}
						} else {
							window.location.href = "projects";
						}
					})
					.catch((error) => {
						window.location.href = "projects";
					});
			}
		},
	};
</script>


<style lang="scss">
#captureWrapper {
	display: none;
}

//////////////////////////
// Variables and Mixins //
//////////////////////////

$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$yellow: #e6ac3c;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;

@mixin transition($args...) {
	-webkit-transition: $args;
	-moz-transition: $args;
	-ms-transition: $args;
	-o-transition: $args;
	transition: $args;
}

.page-proof {
	height: 100%;
	background-color: $white;
}

//////////////////
// Proof Header //
//////////////////

.proof-header {
	padding: 0;
	box-shadow: 0 1px 12px $border-color;
	z-index: 2;

	.header-left {
		width: 200px;
	}
	.header-main {
		padding: 10px;
		text-align: center;
	}
}

// Nav Dashboard
.nav-dashboard {
	display: flex;
	align-items: center;
	padding: 15px;
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

.select-revision {
	margin-right: 10px;

	&:last-child {
		margin-right: 0;
	}
}

// User avatar
.user-avatar {
	width: 40px;
	height: 40px;
	border-radius: 50%;
	padding: 0;
}

// Collaborators
.collaborators {
	display: flex;
	align-items: center;
	flex-direction: row-reverse;
	padding: 10px;

	.collaborator {
		margin-left: 10px;
	}
	.add-button {
		padding: 0;
		width: 34px;
		height: 34px;
		margin-left: 0;
	}
}

//////////////////
// Left Sidebar //
//////////////////

.proof-container {
	height: calc(100% - 60px);
}

.sidebar-left {
	width: 200px;
	box-shadow: 1px 12px 12px $border-color;
	z-index: 2;
	display: flex;
	flex-direction: column;
	@include transition(0.3s);

	&.collapsed {
		margin-left: -200px;
	}
	.el-header {
		padding: 0;
	}
	.progress-area {
		padding: 15px;
		background-color: #f1f1f2;
	}
	.buttons-area {
		padding: 15px 15px 0 15px;
		background-color: $white;
		text-align: center;
		.pdf-button {
			margin-left: 0;
			margin-top: 10px;
		}
	}
	.creative-brief {
		padding: 15px;
	}
	.top-section {
		padding: 15px;
	}
	.el-main {
		padding: 0 15px;
	}
}

.project-progress {
	padding: 10px 0;

	.el-progress-bar__outer {
		background-color: $grey;
	}
	.el-progress-bar__inner {
		@include transition(0.5s);
	}
}

.progress-info {
	font-size: 12px;
	text-align: center;
	.text-danger,
	.text-success {
		font-weight: bold;
		font-size: 120%;
	}
}

.team-members {
	margin-top: 5px;

	.user-avatar {
		width: 35px;
		height: 35px;
		margin-right: -2px;
	}
}

.button-group {
	width: 100%;

	.upload-button {
		width: 50%;
		font-weight: bold;
		padding: 0;

		div {
			margin-bottom: 5px;
		}
		img {
			width: 24px;
		}
		i {
			font-size: 26px;
			color: $black;
		}
	}
}
.upload-button {
	border-style: dashed;
	border-width: 2px;
	width: 100%;
	height: 50px;
	i {
		font-size: 20px;
		vertical-align: middle;
	}
	span {
		vertical-align: middle;
		font-weight: 400;
	}
}

.creative-brief-button {
	border-style: dashed;
	border-width: 2px;
	width: 100%;
	height: 50px;
	i {
		font-size: 20px;
		vertical-align: middle;
	}
	span {
		vertical-align: middle;
		font-weight: 400;
	}
}

.proof-thumbs {
	height: 100%;
	overflow: auto;
	padding: 15px;
}

.proof-thumb {
	height: 150px;
	margin-bottom: 15px;
	border-width: 2px;
	cursor: pointer;
	position: relative;

	&:last-child {
		margin-bottom: 0;
	}
	&.active {
		border-color: $red;

		.proof-number {
			background-color: $red;
		}
	}
	.el-card__body {
		height: 100%;
		padding: 0px;
	}
	img {
		width: 100%;
		height: 100%;
		object-fit: contain;
	}
	.proof-number {
		padding: 2px 5px;
		background-color: $black;
		color: $white;
		position: absolute;
		top: 0;
		left: 0;
		border-bottom-right-radius: 3px;
		width: 25px;
		height: 25px;
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.buttons-area {
		opacity: 0;
		@include transition(0.3s);
		position: absolute;
		top: 0;
		right: 0;
		padding: 2px;
		background-color: transparent;
		.delete-button,
		.download-button {
			padding-left: 10px;
			padding-right: 10px;
		}
		.el-button + .el-button {
			margin-left: 5px;
		}
	}
	&:hover .buttons-area {
		opacity: 1;
	}
	.proof-unread {
		position: absolute;
		bottom: 0;
		right: 0;
		padding: 1px 7px;
		background-color: $red;
		color: $white;
		border-top-left-radius: 3px;
	}
	.review-comment {
		position: absolute;
		bottom: 10px;
		left: 0;
		width: 100%;
		text-align: center;
		.el-button {
			padding: 6px 12px;
			span {
				margin-left: 0;
			}
		}
	}
	.proof-check {
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background-color: rgba(0, 137, 82, 0.4);
		color: $white;
		font-size: 100px;
		display: flex;
		justify-content: center;
		align-items: center;
	}
}

////////////////
// Proof Main //
////////////////

.proof-main {
	position: relative;
	background-color: #ececec;
	padding: 0;
	overflow: hidden;
}

#stage {
	width: 100%;
	height: 100%;	
}

.stage-hand {
	canvas {
		cursor: grab;
		&:active {
			cursor: grabbing;
		}
	}
}

.stage-pen {
	canvas {
		cursor: cella;
	}
}

.canvas-tools {
	position: absolute;
	top: 0;
	width: 100%;
	height: 60px;
	background-color: rgba(236, 236, 236, 0.9);
	z-index: 2;
	display: flex;
	align-items: center;
	justify-content: center;

	.canvas-tool {
		min-width: 40px;
		margin-left: 20px;

		&:first-child {
			margin-left: 8px;
		}
	}
	.pen-tool {
		position: relative;
		.tool-tip {
			position: absolute;
			background-color: rgba(0, 0, 0, 0.5);
			color: $white;
			font-size: 11px;
			font-weight: 700;
			padding: 5px 10px;
			border-radius: 5px;
			width: 110px;
			text-align: center;
			margin-top: 10px;
			left: -35px;
			&:before {
				content: "";
				top: -15px;
				left: 50%;
				border: solid transparent;
				height: 0;
				width: 0;
				position: absolute;
				border-bottom-color: rgba(0, 0, 0, 0.5);
				border-width: 8px;
				margin-left: -9px;
			}
		}
	}
}

.volume-slider {
	width: 40px;
	// margin-left: 20px;
	margin-right: 20px;

	.el-slider__runway {
		// margin-right: 50px;
		// background-color: $white;
	}
	// .el-slider__input {
	// 	width: 110px;
	// }
}

.zoom-slider {
	width: 210px;
	margin-left: 20px;

	.el-slider__runway {
		margin-right: 120px;
		background-color: $white;
	}
	.el-slider__input {
		width: 110px;
	}
}

.proof-pagination,
.controls {
	position: absolute;
	bottom: 0;
	width: 100%;
	height: 85px;
	background-color: rgba(236, 236, 236, 0.9);
	z-index: 2;
	display: flex;
	align-items: center;
	justify-content: center;
}

.control-timeline {
	flex-direction: column;
}
.control-btn {
	background: #1b1b1b;
	display: inline-flex;
	width: 100%;
	align-items: center;
	height: 40px;
}
.points-wrap {
	background: #8e8e8e;
	height: 22px;
	width: 100%;
}
.timeline {
	background: #3e3e3e;
	height: 22px;
	width: 100%;
}
.current-time {
	background: #f56c6c;
	height: 100%;
}

.current-point {
	position: relative;
	margin-top: -22px;
	width: 100%;
	height: 22px;
	margin-left: -11px;
	> img {
		position: absolute;
		left: 0;
		z-index: 5;
	}
}

.controls {
	bottom: 200px;
	.control {
		min-width: 40px;
		margin-left: 0;
		padding: 0;
		border: none;
		background: none;
		> .fa {
			font-size: 25px;
		}
		&:first-child {
			margin-left: 0;
		}
	}
}

.points {
	margin-top: -22px;
	width: 100%;
	height: 22px;
	position: relative;
}
.point {
	position: absolute;
	width: 22px;
	height: 22px;
	border-radius: 100%;
	border: 2px solid $white;
	// text-align: center;
	cursor: pointer;
	> span {
		color: $white;
		font-weight: bold;
		margin-top: -1px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	&:hover {
		border-color: $yellow;
	}
	&.active {
		border-color: $yellow;
	}
	.timeline-marker {
		height: 22px;
		background: $white;
		position: relative;
		.resize {
			width: 5px;
			height: 5px;
			position: absolute;
			background: #3e3e3e;
			right: -2px;
			top: 8px;
			cursor: ew-resize;
		}
	}
}

.toggle-button {
	position: absolute;
	top: 0;
	width: 40px;
	height: 40px;
	font-size: 18px;
	padding: 5px;

	&.left {
		left: 0;
		margin-left: -2px;
		border-top-left-radius: 0;
		border-bottom-left-radius: 0;
	}
	&.right {
		right: 0;
		margin-right: -2px;
		border-top-right-radius: 0;
		border-bottom-right-radius: 0;
	}
}

///////////////////
// Right Sidebar //
///////////////////

.sidebar-right {
	width: 300px;
	box-shadow: -1px 12px 12px $border-color;
	z-index: 1;
	position: relative;
	overflow: hidden;
	@include transition(0.3s);

	&.collapsed {
		margin-right: -300px;
	}
	.comments-slide-enter-active,
	.comments-slide-leave-active {
		@include transition(0.3s);
	}
	.comments-slide-enter,
	.comments-slide-leave-to {
		transform: translateX(100%);
	}
}

.issue-list {
	height: 100%;
	overflow: auto;
}

.getting-started {
	position: absolute;
	bottom: 0;
	width: 100%;
	z-index: 999;
	padding: 5px;
	text-align: center;
	.link-button {
		box-shadow: $box-shadow;
	}
	a {
		color: inherit;
		text-decoration: none;
		&:hover {
			color: inherit;
		}
	}
}

.new-issue {
	padding-top: 10px;
    .el-form {
        .el-form-item {
            .new-issue-input {
                margin-bottom: 16px;
            }
        }
    }
	textarea {
		resize: none;
	}
	.form-buttons {
		margin-bottom: 0;
		text-align: right;

		.el-form-item__content {
			line-height: initial;
		}
		.add-button,
		.cancel-button {
			margin-left: 10px;
		}
	}
}

.issue-display {
	.el-card__body {
		padding: 15px 0 0 0 !important;
	}

}
.high-dot,.medium-dot,.low-dot {
	height: 8px;
	margin-right: 8px;
	width: 8px;
	border-radius: 50%;
	display: inline-block;
}
.high-dot {
	background-color: #EC6E6E;
}
.medium-dot {
	background-color: #F2A731;
}
.low-dot {
	background-color: #67c23a;
}
.status-dot {
	height: 24px;
	width: 24px;
	margin-right: 8px;
	border-radius: 50%;
	display: inline-block;
	vertical-align: middle;
}

.issue-footer {

	.issue-likes-none {
		display: none !important;
	}
	.issue-likes-flex {
		display: inline-flex !important;
	}
	&:hover {
		.issue-likes {
			display: inline-flex !important;
		}
	}

	.done & {
		background-color: rgba(0, 137, 82, 0.1) !important;
	}
	.review & {
		background-color: rgba(230, 162, 60, 0.1) !important;
	}

}

.issue-date {
	.el-input__inner {
		display: none;
	}
	.el-input__suffix {
		position: initial !important;
	}
	.el-input__prefix {
		position: initial !important;
		.el-input__icon {
			// border: 1px dashed rgba(107, 114, 128, 1);
			border-radius: 100%;
			color: #6b7280;
			line-height: normal;
			height: 28px;
			width: 28px;

			&:hover {
				border: 1px dashed rgba(55, 65, 81, 1);
				color: rgba(55, 65, 81, 1);
				background-color: rgba(243, 244, 246, 1);
			}

			.done &:hover {
				background-color: rgba(0, 137, 82, 0.1) !important;
			}
			.review & {
				background-color: rgba(230, 162, 60, 0.1) !important;
			}
		}
	}
}

.issue-box {
	position: relative;

	.el-card__body {
		padding: 15px;
	}

	// Box header
	.box-header,
	.box-header-comment {
		z-index: 2;
		margin-bottom: 15px;
	}

	// Box header
	.box-header {
		padding-left: 15px;
		padding-right: 15px;
	}

	.issue-icons {
		border: 1px dashed rgba(107, 114, 128, 1);
		border-radius: 100%;

		&:hover {
			border: 1px dashed rgba(55, 65, 81, 1);
			color: rgba(55, 65, 81, 1);
			background-color: rgba(243, 244, 246, 1);
		}
	}
	.font-small {
		font-size: 12px;
		height: 20px;
		line-height: 20px;
		align-items: center;
		border-radius: 6px;
		padding: 0 6px;
		box-sizing: border-box;
		flex-shrink: 0;
		justify-content: center;
		overflow: hidden;
		transition-duration: .2s;
		transition-property: background,border,box-shadow,color,fill;
		&.issue-like-active {
			color: #6296f1;
			fill: #6296f1;
			&:hover {
				color: #6296f1;
				fill: #6296f1;
			}
		}
		&:hover {
			background: rgba(243, 244, 246, 1);
			color: #151b26;
			fill: #151b26;
		}
	}
	
	.svg-issue-icon {
		margin-left: 4px;
		flex: 0 0 auto;
		width: 12px;
		height: 12px;
	}
	// Nav button
	.nav-button {
		width: 40px;
		height: 40px;
		padding: 0;
		box-shadow: $box-shadow;
	}
	// Check button
	.check-button {
		background-color: #fff;
		margin-bottom: 0;
		box-shadow: $box-shadow;
		text-transform: capitalize;
	}
	// Issue label
	.issue-label {
		width: 40px;
		height: 40px;
		padding: 0;
		box-shadow: $box-shadow;
	}
	// Description
	.description {
		margin-left: 5px;
		padding-left: 5px;
		padding-right: 5px;
		word-break: break-word;
		border-radius: 5px;
		&:hover {
			background-color: #f1f1f1;
		}
	}
	.attach-image {
		width: 140px;
		height: 90px;
		margin-top: 15px;
		margin-left: auto;
		overflow: hidden;
		border-radius: 5px;
		box-shadow: $box-shadow;
		position: relative;
		img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		.attach-overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			display: flex;
			justify-content: center;
			align-items: center;
			background-color: rgba(0, 0, 0, 0.2);
			opacity: 0;
			@include transition(0.3s);

			.delete-button {
				margin-left: 5px;
			}
			.attach-button {
				font-size: 20px;
				color: $white;
				font-weight: 700;
				margin-left: 5px;
				@include transition(0.3s);
				i {
					font-weight: 700;
				}
				&:first-child {
					margin-left: 0;
				}
				&:hover {
					cursor: pointer;
					color: $grey;
				}
			}
		}
		&:hover .attach-overlay {
			opacity: 1;
		}
		@media (max-width: 550px) {
			position: relative;
			width: 100%;
			height: unset;
			padding-top: 66%;
			img {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}
			.attach-overlay .attach-button {
				margin-left: 15px;
				&:first-child {
					margin-left: 0;
				}
			}
		}
	}
	&.done {
		background-color: rgba(0, 137, 82, 0.1) !important;
		.more-button i {
			color: $white;
		}
	}
	&.review {
		background-color: rgba(230, 162, 60, 0.1) !important;
		.more-button i {
			color: $white;
		}
	}
	.more-button {
		cursor: pointer;
		margin-left: -3px;
		margin-top: 5px;
		i {
			font-size: 22px;
			color: $black;
			color: #cecfcf;
			transform: rotate(-90deg);
		}
	}
	.el-col-19:hover + .el-col-1 .more-button {
		display: none;
	}
	.update-button {
		margin-left: 10px;
	}
	// Edit issue
	.edit-issue {
		display: none;

		textarea {
			resize: none;
		}
		.edit-description {
			margin-bottom: 15px;
		}
	}
	&.editable {
		.edit-issue {
			display: block;
		}
		.description {
			display: none;
		}
		.more-button {
			display: none;
		}
	}
	.datetime {
		font-size: 8px;
		text-transform: uppercase;
		margin-left: 5px;
		color: rgba(0, 0, 0, 0.75);
	}
	.author-name {
		margin-left: 5px;
		font-size: 13px;
	}
}

// Issue details
.issue-details {
	position: absolute;
	top: 0;
	width: 100%;
	height: 100%;
	background-color: #e8e8e8;
	z-index: 3;
	overflow: auto;
	.el-card {
		background-color: $white;
	}
}

////////////
// Dialog //
////////////
.upload-dialog {
	.el-dialog__body {
		text-align: center;
	}
	input[type="file"] {
		display: none;
	}
}
.project-details-dialog {
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

.final-upload {
	input[type="file"] {
		display: none;
	}
	.el-upload-dragger {
		width: 100%;
	}
}

.el-message-box__headerbtn {
	z-index: 10;
}

@media (max-width: 500px) {
	.el-dialog__wrapper .el-dialog {
		width: 90%;
		.el-dialog__body {
			text-align: center;
		}
	}
	.collaborator-dialog {
		.collaborators {
			flex-direction: unset;
			justify-content: center;
			.collaborator:first-child {
				margin-left: 0;
			}
		}
		.el-dialog__footer {
			padding-bottom: 120px;
		}
		.team-members {
			margin-top: 40px;
			text-align: left;
			padding: 20px;
			bottom: 0;
			position: absolute;
			width: 100%;
			height: 100px;
			.heading {
				line-height: 24px;
				font-size: 18px;
			}
		}
	}
}

.image-preview img {
	width: 100%;
}

////////////////////
// Mobile Version //
////////////////////

.mobile-header {
	padding: 0;
	box-shadow: 0 1px 12px $border-color;
	z-index: 2;

	.el-main {
		padding: 0;
	}

	.nav-dashboard {
		justify-content: center;

		.menu-icon {
			margin-right: 5px;
		}
	}
	.user {
		padding: 10px 15px;
		text-align: center;
	}

	// Revision buttons
	.revision-buttons {
		padding: 13px;
		display: flex;
		align-items: center;
		justify-content: center;

		.select-revision {
			width: 80px;
		}
	}

	// Canvas tools
	.canvas-tools {
		position: initial;
		background-color: $white;

		.zoom-slider {
			width: 100px;
		}
	}

	// Comments header
	.comments-header {
		text-align: center;
		padding: 16px 0;
		background-color: #e0dfdf;
		color: $white;
		font-weight: 400;
		font-size: 18px;

		span {
			margin-left: 10px;
		}
	}

	.upload-button {
		border-style: solid;
		border-width: 1px;
		width: unset;
		height: unset;
		i {
			font-size: 12px;
		}
	}
}

.mobile-footer {
	padding: 0;
	box-shadow: 0 -1px 12px $border-color;
	z-index: 2;

	.footer-main {
		padding: 10px;
		display: flex;
		flex-direction: row;
		overflow: auto;
	}
	.footer-right {
		width: 50px;
	}
	.proof-thumb {
		height: 80px;
		width: 100px;
		min-width: 100px;
		margin-bottom: 0;
		margin-right: 15px;
	}
	.toggle-comments {
		transform: rotate(-90deg);
		transform-origin: top left;
		position: relative;
		top: 100px;
		height: 50px;
		width: 100px;
		padding: 13px 6px;
		background-color: #f1f1f2;
		border-bottom-left-radius: 0;
		border-bottom-right-radius: 0;

		span {
			font-size: 12px;
			font-weight: 400;
			margin-right: 2px;
		}
	}
}

// Canvas
.canvas-overwrap {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, 0.4);
	display: flex;
	align-items: center;
	justify-content: center;

	.tip-edit {
		color: #6b6b6b;
		background-color: $white;
		padding: 20px;
		font-size: 18px;
		box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.24);
		border-radius: 8px;
	}
}

// Mobile sidebar
.mobile-sidebar {
	width: 100%;
	box-shadow: none;
}
@media (max-width: 500px) {
	.el-message-box {
		width: 90%;
	}
	.zoom-slider .el-slider__runway {
		background-color: $grey;
	}
	.proof-thumb {
		.delete-button {
			opacity: 1;
			background-color: $red;
			color: $white;
			width: 25px;
			height: 25px;
			display: flex;
			justify-content: center;
			font-weight: 500;
		}
		.review-comment {
			.el-button {
				padding: 2px 5px;
			}
		}
	}
	.el-dialog__wrapper.upload-dialog {
		.el-upload,
		.el-upload-dragger {
			width: 100%;
		}
	}
}

.no-corrections {
	display: flex;
	align-items: center;
	justify-content: center;
	height: 100%;
	flex-direction: column;
	text-align: center;
	.no-corrections-content {
		width: 70%;
		font-size: 16px;
		color: #babcbf;
	}
	.no-corrections-icon {
		font-size: 128px;
		color: #dfdfdf;
	}
}
.tracker {
	display: flex;
	align-items: center;
}
.time-tracker,
.time-tracker-stop {
	margin: 0 12px;
	display: table;
	width: 32px;
	border-radius: 100%;
	height: 32px;
	cursor: pointer;
	box-shadow: 0 2px 12px 0 rgba(82, 82, 82, 0.49);
}

.time-tracker {
	background: #4caf50;
}

.time-tracker-stop {
	background: #e20214;
}

.time-tracker:hover,
.time-tracker:hover {
	background: #4ec72f;
}
.time-tracker .icon-time-tracker,
.time-tracker-stop .icon-time-tracker-stop {
	display: table-cell;
    padding-left: 2px;
	vertical-align: middle;
	text-align: center;
	color: #fff;
}

.creative-footer {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 0 12px;
	.project-info {
		text-align: left;
		display: flex;
		.avatar {
			margin-right: 12px;
			box-shadow: $box-shadow;
		}
		.owner-name {
			color: #2d3748;
			font-weight: 400;
		}
		.details {
			color: #a0aec0;
			span:not(:last-child) {
				margin-right: 12px;
			}
		}
	}
}
.tracker-design {
	top: -12px;
    right: 16px;
	.play-tracker,
	.stop-tracker {
		--tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
		box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
		margin: 0 6px;
		display: table;
		width: 24px;
		font-size: 12px;
		border-radius: 100%;
		height: 24px;
		cursor: pointer;
	}

	.time-counts-active {
        color: #FFF5F5;
        background-color: #F56565;
    }
	
	.play-tracker {
		color: #e8fff6;
		background-color: #81b4a1;
	}
	
	.stop-tracker {
		background-color: #F56565;
		color: #FFF5F5;
	}
	
	.play-tracker .icon-time-tracker,
	.stop-tracker .icon-time-tracker-stop {
		display: table-cell;
        padding-left: 2px;
		vertical-align: middle;
		text-align: center;
		color: #fff;
	}
}
.final-files-dialog {
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
.client-icon {
	position: absolute;
    bottom: 0;
    right: 0;
    color: $white;
	width: 18px;
    height: 18px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 12px;
    border-radius: 100%;
    background: #7fb4a0;
}


</style>

<style scoped lang="scss">
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