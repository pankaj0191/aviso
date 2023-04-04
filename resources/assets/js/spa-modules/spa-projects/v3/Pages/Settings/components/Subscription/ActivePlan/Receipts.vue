<template>
	<ProofloCard :footer="false">
		<template #header>
			<div class="tw-flex tw-justify-between">
				<div class="tw-flex tw-justify-between">
					<h2 class="tw-px-6 tw-text-xl tw-font-semibold tw-text-gray-700">Receipts</h2>
				</div>
			</div>
		</template>
		<template #body>
			<div class="tw-text-center">
				<i v-if="receiptProccess" class="tw-py-6 el-icon-loading"></i>
			</div>
			<div v-for="receipt in receipts" :key="receipt.id">
				<ReceiptItem :receipt="receipt" />
			</div>
		</template>
	</ProofloCard>
</template>

<script>
	import ReceiptItem from "./ReceiptItem";
	export default {
		components: { ReceiptItem },

		/**
		 * The component's data.
		 */
		data() {
			return {
				receipts: [],
				receiptProccess: false
			};
		},

		/**
		 * Prepare the component.
		 */
		mounted() {
			Bus.$on("get-receipts", () => {
				this.getReceipts();
			});
			this.getReceipts();
		},

		methods: {
			/**
			 * Get the user's billing Receipts
			 */
			async getReceipts() {
				try {
					this.receiptProccess = true;

					let { data } = await axios.get("/settings/invoices");

					this.receipts = _.filter(data.data, invoice => {
						return invoice.total != 0;
					});

					this.receiptProccess = false;
				} catch (error) {
					this.receiptProccess = false;

					toastr["error"]("Internal server error");
				}
			}
		}
	};
</script>

<style>
</style>