<template>
	<div>
		<div
			class="el-form-item__error"
			v-for="(error, key) in serverErrors"
			:key="key"
		>{{error}}</div>
	</div>
</template>

<script>
	export default {
		props: ["serverErrors"]
	};
</script>