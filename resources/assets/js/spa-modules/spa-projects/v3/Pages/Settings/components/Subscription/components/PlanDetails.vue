<template>
	<div>
		<div class="tw-text-md tw-font-semibold tw-text-gray-700">
			<span>${{plan.price}}</span>
			<!---->
			/ monthly
			<span v-if="plan.trial_days" class="tw-text-gray-400">({{plan.trial_days}} day trial)</span>
		</div>
		<div
			v-if="plan.description"
			class="tw-mt-4 tw-max-w-xl tw-text-normal tw-text-gray-600"
		>{{plan.description}}</div>
		<div class="tw-mt-4">
			<div class="tw-flex tw-items-center tw-my-3">
				<svg
					viewBox="0 0 20 20"
					fill="currentColor"
					class="tw-text-primary tw-opacity-75 tw-w-5 tw-h-5"
				>
					<path
						fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
						clip-rule="evenodd"
					/>
				</svg>
				<div class="tw-ml-2 tw-text-normal tw-text-gray-600">Unlimited projects</div>
			</div>
			<div class="tw-flex tw-items-center tw-my-3">
				<svg
					viewBox="0 0 20 20"
					fill="currentColor"
					class="tw-text-primary tw-opacity-75 tw-w-5 tw-h-5"
				>
					<path
						fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
						clip-rule="evenodd"
					/>
				</svg>
				<div class="tw-ml-2 tw-text-normal tw-text-gray-600">Unlimited Client</div>
			</div>
			<div class="tw-flex tw-items-center tw-my-3" v-if="plan.teams_count > 0">
				<svg
					viewBox="0 0 20 20"
					fill="currentColor"
					class="tw-text-primary tw-opacity-75 tw-w-5 tw-h-5"
				>
					<path
						fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
						clip-rule="evenodd"
					/>
				</svg>
				<div class="tw-ml-2 tw-text-normal tw-text-gray-600">
					Team
					<span class="tw-font-semibold">({{plan.teams_count}})</span>
				</div>
			</div>
			<div class="tw-flex tw-items-center tw-my-3" v-if="plan.teams_members_count > 0">
				<svg
					viewBox="0 0 20 20"
					fill="currentColor"
					class="tw-text-primary tw-opacity-75 tw-w-5 tw-h-5"
				>
					<path
						fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
						clip-rule="evenodd"
					/>
				</svg>
				<div class="tw-ml-2 tw-text-normal tw-text-gray-600">
					Members
					<span class="tw-font-semibold">({{plan.teams_members_count}})</span> / Team
				</div>
			</div>
			<div class="tw-flex tw-items-center tw-my-3" v-if="plan.storage_capacity > 0">
				<svg
					viewBox="0 0 20 20"
					fill="currentColor"
					class="tw-text-primary tw-opacity-75 tw-w-5 tw-h-5"
				>
					<path
						fill-rule="evenodd"
						d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
						clip-rule="evenodd"
					/>
				</svg>
				<div class="tw-ml-2 tw-text-normal tw-text-gray-600">
					Storage
					<span class="tw-font-semibold">({{plan.storage_capacity}}GB)</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props: ["plan"]
	};
</script>