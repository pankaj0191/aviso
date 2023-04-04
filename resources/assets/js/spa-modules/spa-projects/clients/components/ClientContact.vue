<template>
	<div id="client-contact">
		<div class="client-header">
			<i class="el-icon-arrow-left icon" @click="goTo('basic')"></i>
			<div class="client-avatar">
				<el-avatar src="https://cube.elemecdn.com/0/88/03b0d39583f48206768a7534e55bcpng.png" :size="48"></el-avatar>
			</div>
			<div class="client-content">
				<div class="client-name">Ahmed Mamdouh</div>
				<div class="client-email">gm.xerk@gmail.com</div>
			</div>
		</div>
		<div class="client-body">
			<div class="client-mail-subject">
				<el-input placeholder="Type your subject" v-model="subject" clearable></el-input>
			</div>
			<div class="client-mail-message">
				<el-input
					type="textarea"
					:autosize="{ minRows: 6, maxRows: 8}"
					placeholder="Type your message"
					v-model="message"
				></el-input>
			</div>
		</div>
		<div class="client-footer">
			<el-button style="width:100%" size="small" type="primary" @click="goTo('basic')">Send Message</el-button>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				subject: "",
				message: ""
			};
		},
		methods: {
			goTo(status) {
				this.$emit("goTo", status);
			},
			contact() {}
		}
	};
</script>

<style lang="scss" scoped>
#client-contact {
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
		font-size: 24px;
		cursor: pointer;
	}
	.client-header {
		display: flex;
		justify-content: flex-start;
		align-items: center;
		margin-bottom: 20px;
		.client-avatar {
			margin-right: 12px;
			.el-avatar {
				box-shadow: $box-shadow;
			}
		}
		.client-content {
			font-size: 12px;
			.client-name {
				color: #4a5568;
				font-weight: 500;
			}
			.client-email {
				color: #a0aec0;
			}
		}
	}
	.client-body {
		.client-mail-subject {
			margin-bottom: 12px;
		}
		.client-mail-message {
			margin-bottom: 20px;
		}
	}
}
</style>