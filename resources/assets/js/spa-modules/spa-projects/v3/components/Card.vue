<template>
	<div class="tw-card">
		<slot name="header"></slot>
		<div class="tw-body">
			<slot name="body"></slot>
		</div>
		<div class="tw-footer" v-if="footer">
			<slot name="footer"></slot>
		</div>
	</div>
</template>

<script>
	export default {
		props: {
			footer: {
				default: true,
				type: Boolean
			}
		},
		name: "card"
	};
	/*
        <ProofloCard>
            <template #header></template>
            <template #body></template>
            <template #footer></template>
        </ProofloCard>
    */
</script>
