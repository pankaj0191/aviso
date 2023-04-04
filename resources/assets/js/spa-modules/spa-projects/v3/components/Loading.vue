<template>
  <div class="text-center" v-if="loading" style="padding-top:76px">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
</template>

<script>
export default {
    props: {
        loading: {
            type: Boolean,
            default: false
        }
    }
}
</script>

<style lang="scss" scoped>
    $white: #fafafa;
	$black: #545c64;
	$red: #ef5b5b;
	$green: #80b4a0;
	$grey: #c0c4cc;
	$border-color: rgba(0, 0, 0, 0.1);
	$box-shadow: 0 2px 12px 0 $border-color;
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
</style>