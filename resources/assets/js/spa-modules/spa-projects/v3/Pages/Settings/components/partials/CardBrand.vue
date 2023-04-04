<template>
	<component class="tw-mx-2" v-bind:is="creditCard(brand)"></component>
</template>

<script>
	import {
		Card,
		Visa,
		MasterCard,
		Discover,
		Amex,
		Diners,
		Jcb,
		UnionPay
	} from "../svg/credit-card/index";
	export default {
		props: ["brand"],
		components: {
			Card,
			Visa,
			MasterCard,
			Discover,
			Amex,
			Diners,
			Jcb,
			UnionPay
		},
		methods: {
			creditCard(brand) {
				switch (brand) {
					case "Visa":
						return "Visa";
						break;
					case "MasterCard":
						return "MasterCard";
						break;
					case "American Express":
						return "Amex";
						break;
					case "Discover":
						return "Discover";
						break;
					case "Diners Club":
						return "Diners";
						break;
					case "JCB":
						return "Jcb";
						break;
					case "UnionPay":
						return "UnionPay";
						break;
					default:
						return "Card";
						break;
				}
			}
		}
	};
</script>

<style>
</style>