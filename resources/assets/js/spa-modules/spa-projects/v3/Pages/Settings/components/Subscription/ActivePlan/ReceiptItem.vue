<template>
	<el-row :gutter="20" class="tw-py-2">
		<el-col :span="8">
			<a target="_blank" class="tw-text-primary tw-hover-text-primary tw-cursor-pointer tw-font-semibold" :href="receipt.hosted_invoice_url">{{ receipt.date | spark-date }}</a>
		</el-col>
		<el-col :span="8">
			<div v-if="receipt.total > 0">{{ (receipt.total / 100) | currency }}</div>
			<div v-else>0</div>
		</el-col>
		<el-col :span="8">
			<a target="_blank" :href="receipt.invoice_pdf">
				<el-button class="tw-btn-default">
					<i class="fa fa-btn fa-file-pdf-o"></i>Download PDF
				</el-button>
			</a>
		</el-col>
	</el-row>
</template>

<script>
	export default {
		props: ["receipt"]
	};
</script>

<style>
</style>