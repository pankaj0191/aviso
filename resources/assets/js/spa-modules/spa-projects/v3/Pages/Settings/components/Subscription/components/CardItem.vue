<template>
	<el-row :gutter="24" class="tw-inline-flex tw-w-full">
		<el-col class="text-center" :xs="24" :sm="24" :md="24" :lg="8" :xl="8">
			<div class="tw-flex tw-items-center tw-justify-start tw-pl-6">
				<CardBrand :brand="card.brand" />
				<div class="tw-font-semibold tw-mr-2">{{card.name}}</div>
				<span class="tw-text-gray-400 tw-text-sm">({{card.exp_month}}/{{card.exp_year}})</span>
			</div>
		</el-col>
		<el-col class="text-center hidden-md-and-down" :lg="8" :xl="8">{{card.brand}} ({{card.last4}})</el-col>
		<el-col class="text-center hidden-md-and-down" :lg="8" :xl="8">
			<div v-if="defaultMethod == card.id" class="tw-text-primary">Default</div>
			<div v-else></div>
		</el-col>
	</el-row>
</template>

<script>
	import CardBrand from "../../partials/CardBrand.vue";

	export default {
		components: { CardBrand },
		props: {
			card: {
				type: Object
			},
            defaultMethod: {
                type: String
            }
		}
	};
</script>