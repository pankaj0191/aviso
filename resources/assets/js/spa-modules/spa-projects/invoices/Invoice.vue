<template>
	<div id="invoice-app">
		<div class="invoice-control">
			<div class="back-avatar">
				<router-link tag="div" :to="{name: 'time-trackers'}">
					<i class="el-icon-arrow-left icon"></i>
				</router-link>
				<router-link :to="{name: 'projects'}">
					<div class="avatar-logo">
						<img :src="`${spacePrefix}/assets/img/prooflo-small.png`" alt width="24px" />
					</div>
				</router-link>
			</div>
			<div>
				<el-link :underline="false" type="primary" class="tw-mr-6" @click="changeLogo" icon="el-icon-upload2">Invoice Logo</el-link>
				<input @change="editFile" type="file" class="tw-hidden" ref="logo" name="logo" id="logo">
				<el-link
					style="margin-right:12px"
					:underline="false"
					type="primary"
					@click="openPrint"
					icon="el-icon-printer"
				>Print</el-link>
				<el-link :underline="false" type="primary" @click="sendMail" icon="el-icon-position">Mail</el-link>
			</div>
		</div>
		<div class="text-center" v-if="loading" style="padding-top:76px">
			<div class="lds-ripple">
				<div></div>
				<div></div>
			</div>
		</div>
		<div v-if="!loading">
			<div class="invoice-header">
				<div class="logo">
					<img v-if="user.current_profile &&user.current_profile.profile_description" :src="`${spacePrefix}/${user.current_profile.profile_description.dark_logo}`" alt />
					<div class="desc"></div>
				</div>
				<div class="due-blanace">
					<div class="text">Blanace Due</div>
					<div class="amount" v-if="totalAmount() > tax">${{(totalAmount() - tax)}}</div>
					<div class="amount" v-else>${{(totalAmount())}}</div>
				</div>
			</div>
			<div class="invoice-body">
				<table class="table">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">Project Type</th>
							<th scope="col">Project Name</th>
							<th scope="col">Hourly Rate</th>
							<th scope="col">Duration</th>
							<th scope="col">Amount</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">1</th>
							<td>{{invoice.project ? invoice.project.type : 'Project is removed'}}</td>
							<td>{{invoice.project ? invoice.project.name : 'Project is removed'}}</td>
							<td>${{hourlyRate()}}</td>
							<td>{{duration()}}</td>
							<td>${{totalAmount()}}</td>
						</tr>
					</tbody>
				</table>
				<ul class="invoice-values">
					<li class="value-item">
						<div class="inv-title">Subtotal</div>
						<div class="inv-value">${{totalAmount()}}</div>
					</li>
					<li class="value-item">
						<div class="inv-title">Prooflo Tax</div>
						<div class="inv-value" v-if="totalAmount() > tax">${{tax}}</div>
						<div class="inv-value" v-else>${{0.00}}</div>
					</li>
					<li class="value-item">
						<div class="inv-title">Balance due</div>
						<div class="inv-value" v-if="totalAmount() > tax">${{(totalAmount())}}</div>
						<div class="inv-value" v-else>${{(totalAmount())}}</div>
					</li>
				</ul>
			</div>
			<div class="invoice-footer"></div>
		</div>
	</div>
</template>

<script>
	import moment from "moment";
	import {mapGetters} from 'vuex'
	export default {
		props: ["id"],
		data() {
			return {
				invoice: {},
				tax: "5",
				loading: false
			};
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
			},
			...mapGetters(["user"])
		},
		methods: {
			changeLogo() {
				this.$refs.logo.click();
			},
			async editFile(e) {
				e.preventDefault();
				
				if ( ! this.$refs.logo.files.length) {
					return;
				}

				var self = this;

                let file = e.target.files[0];
                let fd = new FormData();
                fd.append('logo', file);
				try {
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }
                    let {
                        data
                    } = await axios.post(`/api/invoices/change-logo`, fd, config)
					if(this.user.current_profile && this.user.current_profile.profile_description) {
						this.user.current_profile.profile_description = data
					}
                    this.$refs.logo.value = null;
                } catch (error) {
                    console.log(error.response.data.errors)
                }
            },
			openPrint() {
				window.print();
			},
			hourlyRate() {
				if (
					this.invoice.project &&
					this.invoice.project.users_hourly_rate.length > 0
				) {
					return this.invoice.project.users_hourly_rate.find(
						item => this.invoice.user.id == item.id
					).pivot.hourly_rate;
				} else {
					return 0.0;
				}
			},
			duration() {
				return moment.utc(this.invoice.duration * 1000).format("HH:mm:ss");
			},
			totalAmount() {
				return (
					(this.invoice.duration * this.hourlyRate()) /
					60 /
					60
				).toFixed(2);
			},
			fetch() {
				this.loading = true;
				axios
					.get(`/api/invoices/${this.id}`)
					.then(response => {
						this.loading = false;
						this.invoice = response.data;
					})
					.catch(error => {
						this.loading = false;
						console.log(error);
						// this.handle_error(error);
					});
			},
			sendMail() {
				this.loading = true;
				axios
					.post(`/api/invoices/send/${this.id}`)
					.then(response => {
						toastr["success"]('Mail send successfully', "Success");
						this.loading = false;
					})
					.catch(error => {
						this.loading = false;
						console.log(error);
						// this.handle_error(error);
					});
			}
		},
		mounted() {
			this.fetch();
			$(".with-navbar").css("padding-top", "0");
		}
	};
</script>

<style lang="scss" scoped>
#invoice-app {
	$white: #fafafa;
	$black: #545c64;
	$red: #ef5b5b;
	$green: #80b4a0;
	$grey: #c0c4cc;
	$border-color: rgba(0, 0, 0, 0.1);
	$box-shadow: 0 2px 12px 0 $border-color;
	.invoice-control {
		padding: 0 24px;
		box-shadow: $box-shadow;
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 64px;
		.back-avatar {
			display: flex;
			align-items: center;
		}
		.icon {
			margin-right: 12px;
			font-size: 24px;
			font-weight: 700;
			cursor: pointer;
		}
		.avatar-logo {
			background-color: #62b29e;
			padding: 1px 12px;
			border-radius: 100%;
			box-shadow: $box-shadow;
		}
	}
	@media print {
		.invoice-control {
			display: none;
		}
		.invoice-header {
			padding: 24px !important;
		}
		.invoice-body {
			padding: 24px !important;
			.invoice-values {
				padding: 24px 24px 0 24px !important;
			}
		}
	}
	.invoice-header {
		padding: 24px 144px;
		display: flex;
		justify-content: space-between;
		.logo {
			img {
				width: 220px;
			}
		}
		.due-blanace {
			text-align: right;
			.text {
				font-weight: 500;
			}
			.amount {
				font-size: 32px;
				font-weight: 700;
			}
		}
	}
	.invoice-body {
		padding: 24px 120px;
		.invoice-values {
			text-align: right;
			padding: 24px 48px 0 48px;
			.value-item {
				display: flex;
				justify-content: flex-end;
				.inv-title {
					width: 120px;
					font-weight: 500;
				}
				.inv-value {
					width: 120px;
					font-weight: 700;
				}
			}
		}
	}
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
}
</style>