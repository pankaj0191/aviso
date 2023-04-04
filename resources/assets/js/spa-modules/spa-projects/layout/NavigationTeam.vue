<template>
	<div>
		<div v-for="(team, key) in teams" :key="key">
			<transition name="el-zoom-in-top" v-if="!isCollapse">
				<el-row v-if="team.name">
					<div class="team-header" style="padding: 4px 16px">
						<span class="labels-not-bold hidden-md-and-down">
							<div
								class="collabse-icon"
								role="button"
								v-if="teamShow.includes(team.id)"
								@click="spliceTeamShow(team)"
								aria-label="Expand"
								tabindex="0"
							>
								<svg class="MiniIcon" focusable="false" viewBox="0 0 24 24">
									<path
										d="M20.9,9c-0.2-0.6-0.8-1-1.4-1h-15C3.9,8,3.4,8.4,3.1,9C2.9,9.5,3,10.2,3.5,10.6l7.1,7.1c0.4,0.4,0.9,0.6,1.5,0.6c0.5,0,1.1-0.2,1.5-0.6l7.1-7.1C21,10.2,21.1,9.5,20.9,9z"
									/>
								</svg>
							</div>
							<div
								class="collabse-icon"
								role="button"
								v-if="!teamShow.includes(team.id)"
								@click="pushTeamShow(team)"
								aria-label="Expand"
								tabindex="0"
							>
								<svg class="MiniIcon RightCaretMiniIcon" focusable="false" viewBox="0 0 24 24">
									<path
										d="M17.5,10.7l-7.1-7.1C10,3.2,9.4,3,8.8,3.3c-0.6,0.2-1,0.8-1,1.4v14.9c0,0.6,0.4,1.2,1,1.4c0.2,0.1,0.4,0.1,0.6,0.1c0.4,0,0.8-0.2,1.1-0.5l7.1-7.1c0.4-0.4,0.6-0.9,0.6-1.5S17.9,11.1,17.5,10.7z"
									/>
								</svg>
							</div>
							<span class="team-name" @click="teamShowToggle(team)">{{team.name}}</span>
						</span>
						<div @click="showMembersDialog(team)" v-if="team.owner.id == user.id" class="plusIcon">
							<svg class="MiniIcon PlusMiniIcon" focusable="false" viewBox="0 0 24 24">
								<path
									d="M10,10V4c0-1.1,0.9-2,2-2s2,0.9,2,2v6h6c1.1,0,2,0.9,2,2s-0.9,2-2,2h-6v6c0,1.1-0.9,2-2,2s-2-0.9-2-2v-6H4c-1.1,0-2-0.9-2-2s0.9-2,2-2H10z"
								/>
							</svg>
						</div>
						<i
							class="fa fa-user-plus hidden-lg-and-up"
							@click="showMembersDialog(team)"
							style="color:white;font-size:16px"
						></i>
					</div>
					<transition name="el-zoom-in-top">
						<el-row
							type="flex"
							style="flex-wrap: wrap;padding: 0 20px"
							justify="start"
							v-if="team.users.length > 0 && teamShow.includes(team.id)"
						>
							<div v-for="member in team.users" :key="member.id" style="margin: 0 5px 5px 0;padding:0">
								<el-tooltip
									@click.native="showMemberProjects(member)"
									:content="member.name"
									placement="bottom"
									effect="light"
								>
									<!-- <el-avatar class="circle-head-team" :size="24" :src="member.photo_url"></el-avatar> -->
                                    <span class="tw-relative tw-inline-block">
                                        <img class="tw-h-6 tw-w-6 tw-rounded-full" :src="member.photo_url" alt="" />
                                        <span v-if="member.pivot.role == 'owner'" class="tw-absolute tw-top-0 tw-right-0 tw-block tw-h-1.5 tw-w-1.5 tw-rounded-full tw-ring-2 tw-ring-white" style="background-color: #4ade80;" />
                                    </span>
								</el-tooltip>
							</div>
						</el-row>
					</transition>
				</el-row>
			</transition>
			<el-row v-if="team.length == 0 && isFreelancer">
				<el-row>
					<el-col class="team-header new-team">
						<!--<span class="labels">TEAM</span>-->
						<a href="/settings#/teams">
							<el-tag class="create-team-btn" v-if="!isCollapse">
								<el-icon class="el-icon-s-custom"></el-icon>CREATE TEAM
							</el-tag>
							<el-tag class="create-team-btn-mobile">
								<img class="circle-head-team" :src="`${spacePrefix}/assets/img/create_team.png`" alt="create team" />
							</el-tag>
						</a>
					</el-col>
				</el-row>
			</el-row>
		</div>
	</div>
</template>

<script>
	import { mapGetters } from "vuex";

	export default {
        props: ['isCollapse'],
		data() {
			return {
				teamShow: JSON.parse(localStorage.getItem("teamShow"))
					? JSON.parse(localStorage.getItem("teamShow"))
					: []
			};
		},
		methods: {
			showMembersDialog(team) {
				this.$emit("showMembersDialog", team);
            },
            showMemberProjects(member){
				this.$emit("showMemberProjects", member);
            },
			teamShowToggle(team) {
				if (this.teamShow.includes(team.id)) {
					this.teamShow.splice(this.teamShow.indexOf(team.id), 1);
				} else {
					this.teamShow.push(team.id);
				}
				localStorage.setItem("teamShow", JSON.stringify(this.teamShow));
			},
			spliceTeamShow(team) {
				this.teamShow.splice(this.teamShow.indexOf(team.id), 1);
				localStorage.setItem("teamShow", JSON.stringify(this.teamShow));
			},
			pushTeamShow(team) {
				this.teamShow.push(team.id);
				localStorage.setItem("teamShow", JSON.stringify(this.teamShow));
			},
		},
		computed: {
			...mapGetters(["user", "isFreelancer", "teams"]),
			spacePrefix() {
				return spacePrefix;
			}
		},
	};
</script>

<style scoped lang="scss">
// Green circly on image
.circle-head-team {
    border-radius: 50%;
    border: 2px solid #80B4A0;
}
</style>