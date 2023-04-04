<template>
	<svg
		class="tw-flex-shrink-0 tw-mr-3 tw-h-6 tw-w-6"
        style="margin-left:-4px"
        :class="classes"
		xmlns="http://www.w3.org/2000/svg"
		fill="none"
		viewBox="0 0 24 24"
		stroke="currentColor"
		aria-hidden="true"
	>
		<path
			stroke-linecap="round"
			stroke-linejoin="round"
			stroke-width="2"
			d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
		/>
	</svg>
</template>


<script>
export default {
    props: ['classes']
}
</script>