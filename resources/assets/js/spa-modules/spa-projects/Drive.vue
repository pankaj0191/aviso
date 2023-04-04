<template>
	<div>
		<el-row :gutter="20">
			<el-col :md="6">
				<el-card>
					My Files
					<hr />
					<el-tree
						:data="data"
						icon-class="el-icon-arrow-right"
						:props="defaultProps"
						@node-click="handleNodeClick"
						v-if="!loading"
					>
						<span class="custom-tree-node" slot-scope="{ node, data }">
							<span class="catname" v-if="data.file">
								<i class="el-icon-document"></i>
								{{data.file | truncate(10, '...')}}
							</span>
							<span class="catname" v-else>
								<i class="el-icon-folder-opened"></i>
								{{node.label}}
							</span>
						</span>
					</el-tree>
					<div class="text-center" v-if="loading">
						<div class="lds-ellipsis">
							<div></div>
							<div></div>
							<div></div>
							<div></div>
						</div>
					</div>
				</el-card>
			</el-col>
			<el-col :md="18" style="padding:0 24px">
				<div class="files-row">
					<div class="file-head">Files</div>
					<div>
						<i :class="['el-icon-s-grid icon', (view == 'grid' && 'icon-color')]" @click="view = 'grid'"></i>
						<i :class="['fa fa-list icon', (view == 'list' && 'icon-color')]" @click="view = 'list'"></i>
					</div>
				</div>
				<transition name="el-zoom-in-top">
					<div v-if="view === 'list'">
						<table class="table">
							<thead>
								<tr>
									<th>Name</th>
									<th>Size</th>
									<th>Type</th>
									<th>Download</th>
								</tr>
							</thead>
							<tbody>
								<template v-for="(node, key) in currentNode">
									<tr :key="key" v-if="node.url">
										<td>
											<el-popover trigger="hover" placement="bottom" width="480">
												<div style="margin-bottom:8px">
													Open URL:
													<a
														target="_blank"
														class="title-popover-url"
														:href="`${spacePrefix}${node.url}`"
													>{{`${spacePrefix}${node.url}` | truncate(55, '...')}}</a>
												</div>
												<div class="image-list">
													<img height="300px" width="100%" :src="`${spacePrefix}${node.url}`" :alt="node.file" />
												</div>
												<span slot="reference">
													<i class="fa fa-file icon"></i>
													{{node.file}}
												</span>
											</el-popover>
										</td>
										<td>
											{{Number(node.size / (1000 * 1000)).toLocaleString(undefined, {
											minimumFractionDigits: 2,
											maximumFractionDigits: 2
											})}} MB
										</td>
										<td>{{node.type}}</td>
										<td>
											<i @click="downloadFile(node.url, node.file)" class="el-icon-download icon"></i>
										</td>
									</tr>
								</template>
							</tbody>
						</table>
					</div>
				</transition>
				<transition name="el-zoom-in-top">
					<div v-if="view === 'grid'">
						<el-row :gutter="12">
							<template v-for="(node, key) in currentNode">
								<el-col :md="8" class="custom-cards" :key="key" v-if="node.url" style="margin-bottom:12px">
									<el-card>
										<div class="header-card">
											<div class="title-card">{{node.file | truncate(20, '...')}}</div>
											<div class="type-card">{{node.type}}</div>
										</div>
										<div class="image-card">
											<img width="100%" height="100%" :src="`${spacePrefix}${node.url}`" :alt="node.file" />
										</div>
										<div class="footer-card">
											<div>
												<div class="value-popover">
													{{Number(node.size / (1000 * 1000)).toLocaleString(undefined, {
													minimumFractionDigits: 2,
													maximumFractionDigits: 2
													})}} MB
												</div>
											</div>
											<div>
												<i @click="downloadFile(node.url, node.file)" class="el-icon-download icon"></i>
											</div>
										</div>
									</el-card>
								</el-col>
							</template>
						</el-row>
					</div>
				</transition>
			</el-col>
		</el-row>
	</div>
</template>

<script>
	export default {
		data() {
			return {
				view: "grid",
				data: [],
				defaultProps: {
					children: "children",
					label: "label"
				},
				currentNode: [],
				loading: false
			};
		},
		methods: {
			handleNodeClick(data) {
				if (data.children) {
					let children = data.children.filter(child => child.url);
					this.currentNode = children;
				}
			},
			fetch() {
				this.loading = true;
				axios
					.get("/api/drive")
					.then(response => {
						this.loading = false;
						let companies = response.data.data.find(item => {
							return item.label == "projects";
						});
						let pictures = response.data.data.find(item => {
							return item.label == "pictures";
						});
						this.data = companies.children;
					})
					.catch(error => {
						this.loading = false;
					});
			},
			downloadFile(url, name) {
				axios({
					url: `/api/drive/download`,
					method: "POST",
					responseType: "blob",
					headers: {
						"Content-Type": "application/json"
					},
					data: {
						url: url
					}
				}).then(response => {
					const url = window.URL.createObjectURL(
						new Blob([response.data])
					);
					const link = document.createElement("a");
					link.href = url;
					// link.download = "download";
					link.setAttribute("download", name);
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
				});
			},
			copyURL(url) {
				// var copyText = document.getElementById("copyInput");
				// copyText.select();
				// // copyText.setSelectionRange(0, 99999)
				// document.execCommand("copy");
				// alert("Copied the text: " + copyText.value);
				let testingCodeToCopy = document.querySelector("#copy-input");
				testingCodeToCopy.setAttribute("type", "text"); // 不是 hidden 才能複製
				testingCodeToCopy.select();

				try {
					var successful = document.execCommand("copy");
					var msg = successful ? "successful" : "unsuccessful";
					alert("Testing code was copied " + msg);
				} catch (err) {
					alert("Oops, unable to copy");
				}

				/* unselect the range */
				testingCodeToCopy.setAttribute("type", "hidden");
				window.getSelection().removeAllRanges();
			}
		},
		filters: {
			truncate: function(text, length, suffix) {
				if (text.length > length) {
					return text.substring(0, length) + suffix;
				} else {
					return text;
				}
			}
		},
		computed: {
			spacePrefix() {
				return spacePrefix;
			}
		},
		mounted() {
			this.fetch();
		}
	};
</script>

<style lang="scss" scoped>
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;

.files-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 24px;

	.file-head {
		font-size: 16px;
		color: #a0aec0;
	}
}

.icon {
	color: #a0aec0;
	margin-right: 4px;
	font-size: 18px;
	cursor: pointer;
}

.icon-color {
	color: #1a202c;
}

.el-avatar {
	margin-left: 0.75rem !important;
	box-shadow: $box-shadow;
}

.value-popover {
	font-size: 12px;
	color: #a0aec0;
}

.title-popover-url {
	color: blue;
}

// loading
.lds-ellipsis {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 80px;
}

.lds-ellipsis div {
	position: absolute;
	top: 33px;
	width: 13px;
	height: 13px;
	border-radius: 50%;
	background: $green;
	animation-timing-function: cubic-bezier(0, 1, 1, 0);
}

.lds-ellipsis div:nth-child(1) {
	left: 8px;
	animation: lds-ellipsis1 0.6s infinite;
}

.lds-ellipsis div:nth-child(2) {
	left: 8px;
	animation: lds-ellipsis2 0.6s infinite;
}

.lds-ellipsis div:nth-child(3) {
	left: 32px;
	animation: lds-ellipsis2 0.6s infinite;
}

.lds-ellipsis div:nth-child(4) {
	left: 56px;
	animation: lds-ellipsis3 0.6s infinite;
}

@keyframes lds-ellipsis1 {
	0% {
		transform: scale(0);
	}

	100% {
		transform: scale(1);
	}
}

@keyframes lds-ellipsis3 {
	0% {
		transform: scale(1);
	}

	100% {
		transform: scale(0);
	}
}

@keyframes lds-ellipsis2 {
	0% {
		transform: translate(0, 0);
	}

	100% {
		transform: translate(24px, 0);
	}
}

.image-list {
	text-align: center;

	img {
		border-radius: 12px;
	}
}
</style>
<style lang="scss">
.custom-cards {
	.el-card {
		border-radius: 12px;

		.el-card__body {
			padding: 0;
		}

		.header-card {
			padding: 10px 20px 10px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
			font-size: 12px;

			.title-card {
				color: #718096;
			}

			.type-card {
				color: #a0aec0;
				text-transform: uppercase;
			}
		}

		.footer-card {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 20px 10px 20px;
			color: #a0aec0;
		}

		.image-card {
			height: 150px;

			img {
				object-fit: cover;
			}
		}
	}
}
</style>