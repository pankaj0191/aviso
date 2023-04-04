<template>
	<div id="client-info">
		<div class="client-header">
			<i class="el-icon-edit icon"></i>
			<i class="el-icon-info icon" @click="goTo('info')"></i>
		</div>
		<div class="client-body">
			<div class="client-avatar">
				<el-avatar src="https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png" :size="96"></el-avatar>
			</div>
			<div class="client-content">
				<div class="client-name">Ahmed Mamdouh</div>
				<div class="client-email">gm.xerk@gmail.com</div>
			</div>
			<div class="client-states">
				<div class="client-design">
					<div class="state-title">Design</div>
					<div class="state-number">5</div>
				</div>
				<div class="client-website">
					<div class="state-title">Website</div>
					<div class="state-number">3</div>
				</div>
				<div class="client-video">
					<div class="state-title">Video</div>
					<div class="state-number">1</div>
				</div>
			</div>
		</div>
		<div class="client-footer">
			<el-row :gutter="20">
				<el-col :md="12" class="client-contact" v-if="active">
					<el-button style="width:100%" size="small" type="primary" @click="goTo('contact')">Contact</el-button>
				</el-col>
				<el-col :md="12" class="client-deactive" v-if="active">
					<el-button size="small" style="width:100%" type="danger" @click="active = false">Deactive</el-button>
				</el-col>
				<el-col :md="24" class="client-active" v-if="!active">
					<el-button size="small" style="width:100%" type="warning" @click="active = true">Active</el-button>
				</el-col>
			</el-row>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				active: true
			};
		},
		methods: {
			goTo(status) {
				this.$emit("goTo", status);
			}
		}
	};
</script>

<style lang="scss" scoped>
#client-info {
	$white: #fafafa;
	$black: #545c64;
	$red: #ef5b5b;
	$green: #80b4a0;
	$grey: #c0c4cc;
	$border-color: rgba(0, 0, 0, 0.1);
	$box-shadow: 0 2px 12px 0 $border-color;
	.icon {
		color: #a0aec0;
		margin-right: 4px;
		font-size: 18px;
		cursor: pointer;
	}
	.client-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 24px;
	}
	.client-body {
		.client-avatar {
			text-align: center;
			margin-bottom: 6px;
			.el-avatar {
				box-shadow: $box-shadow;
			}
		}
		.client-content {
			text-align: center;
			font-size: 14px;
			.client-name {
				color: #4a5568;
				font-weight: 500;
			}
			.client-email {
				color: #a0aec0;
			}
		}
		.client-states {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-top: 24px;
			margin-bottom: 24px;
			.state-title {
				color: #718096;
			}
			.state-number {
				color: #1a202c;
				font-size: 16px;
			}
		}
	}
}
</style>