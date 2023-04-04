<template>
    <div>
        <ul class="list">
            <li v-for="sub in project.sub_categories" :key="sub.id">
                <span class="dimension">{{parseFloat(sub.width)}} x {{parseFloat(sub.height)}}</span>
                {{sub.name}}
                <span v-if="sub.files.length > 0" class="template-download" @click="openTemplateDialog(sub)">(Download
                    template)</span>
            </li>
            <li class="custom-width" v-if="project.width > 0 && project.height > 0">
                <span class="dimension">{{parseFloat(project.width)}} x {{parseFloat(project.height)}}</span> Custom
                Size
            </li>
        </ul>
        <el-dialog width="40%" class="project-details-dialog" title="Templates" :visible.sync="templateDialog"
            append-to-body>
            <div class="file-style">
                <stack :column-min-width="192" :gutter-width="8" :gutter-height="8" monitor-images-loaded>
                    <stack-item v-for="(file, index) in current_template.files" :key="index"
                        style="transition: transform 300ms">
                        <div class="unsplash-images">
                            <img :src="`${spacePrefix}/${file.image}`" :alt="file.name" />
                            <div class="unsplash-overlay">
                                <div class="overlay not-hover-overlay" v-if="imageLoading.includes(file.id)"></div>
                                <div class="overlay hover-overlay"></div>
                                <div
                                    :class="['tools', (imageLoading.includes(file.id) ? 'not-hover-overlay' : 'hover-overlay')]">
                                    <div class="add-tool">
                                        <div class="add-collection" @click="downloadZipFile(file)">
                                            <div class="lds-ring" v-if="imageLoading.includes(file.id)">
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                            <svg v-else class="add-svg" version="1.1" viewBox="0 0 32 32" width="32"
                                                height="32" aria-hidden="false">
                                                <path
                                                    d="M25.8 15.5l-7.8 7.2v-20.7h-4v20.7l-7.8-7.2-2.7 3 12.5 11.4 12.5-11.4z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="file-details">
                            <div class="file-name">
                                <i class="el-icon-picture-outline"></i>
                                {{file.name | truncate(20, '...')}}
                            </div>
                            <div class="file-size">
                                <i class="el-icon-rank"></i>
                                {{Number(file.size / (1000 * 1000)).toLocaleString(undefined, {
										minimumFractionDigits: 2,
										maximumFractionDigits: 2
										})}} MB
                            </div>
                        </div>
                    </stack-item>
                </stack>
            </div>
            <span slot="footer" class="dialog-footer">
                <div>
                    <el-button class="canacel-creative" :disabled="loading" @click="templateDialog = false">Close
                    </el-button>
                </div>
            </span>
        </el-dialog>
    </div>
</template>

<script>
	import { Stack, StackItem } from "vue-stack-grid";

    export default {
        props: ['project'],
        components: {
            Stack,
            StackItem
        },
        data() {
            return {
                templateDialog: false,
				imageLoading: [],
                current_template: {},
                loading: false,
            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix
            }
        },
        methods: {
            openTemplateDialog(sub) {
                this.current_template = sub;
                this.templateDialog = true;
            },
            pushImageLoading(image) {
				if (this.imageLoading.includes(image)) {
					this.imageLoading.splice(this.imageLoading.indexOf(image), 1);
				} else {
					this.imageLoading.push(image);
				}
			},
            downloadZipFile(file) {
				this.imageLoading.push(file.id);
				axios({
					url: `/api/request/download-zip/${file.id}`,
					method: "GET",
					responseType: "blob",
					headers: {
						"Content-Type": "application/json"
					}
				})
					.then(response => {
						const url = window.URL.createObjectURL(
							new Blob([response.data])
						);
						const link = document.createElement("a");
						link.href = url;
						// link.download = "download";
						link.setAttribute("download", file.name);
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
						this.imageLoading.splice(
							this.imageLoading.indexOf(file.id),
							1
						);
					})
					.catch(() => {
						console.log("error occured");
						this.imageLoading.splice(
							this.imageLoading.indexOf(file.id),
							1
						);
					});
			},
        }
    }
</script>

<style lang="scss" scoped>
$white: #fafafa;
$black: #545c64;
$red: #ef5b5b;
$green: #80b4a0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, 0.1);
$box-shadow: 0 2px 12px 0 $border-color;
    hr {
	padding: 0;
	margin: 8px 0;
	opacity: 0.5;
}

section:not(:last-child) {
	margin-bottom: 24px;
}

.title-with-icon,
.title-with-out-icon {
	color: #a0aec0;
	font-weight: 500;
}

.title-with-icon {
	font-size: 18px;
}
.title-with-out-icon {
	font-size: 16px;
	margin-bottom: 8px;
}

.include-text {
	padding-bottom: 12px;
	p {
		padding: 0 22px;
	}
}

.photo-text {
	padding: 12px 16px;
	p {
		padding: 0 22px;
	}
}

.icon {
	margin-right: 4px;
}

.list {
    padding: 8px 22px 0 22px;
    color: #718096;
    .dimension {
        color: #2d3748;
        font-weight: 600;
    }
    .template-download {
        font-weight: 500;
        margin-left: 4px;
        cursor: pointer;
        color: $green;
    }
}
.instruction-list {
    margin-bottom: 24px;
    color: #2d3748;
    font-weight: 400;
    padding: 0px 22px;
}
.assets-images {
    padding: 0 12px;
}
.photos-images {
    padding: 0 24px;
}
.list-with-icon {
    display: flex;
    margin-bottom: 12px;
    font-weight: 400;
    color: #2d3748;
    padding: 0 24px;
    li {
        margin-right: 24px;
    }
    .not-selected {
        color: #e53e3e;
    }
}
.adobe-title {
    color: #a0aec0;
    font-weight: 500;
    margin-bottom: 4px;
}

img {
	width: 100%;
	height: auto;
	border-radius: 4px;
}

// loading
.lds-ellipsis {
	display: inline-block;
	position: relative;
	width: 80px;
	height: 50px;
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

.unsplash-images {
	position: relative;
}

.hover-overlay,
.not-hover-overlay {
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-images:not(:hover) .hover-overlay {
	visibility: hidden;
	opacity: 0;
}

.overlay {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	background-image: linear-gradient(
		180deg,
		rgba(0, 0, 0, 0.34) 0,
		rgba(0, 0, 0, 0.338) 3.5%,
		rgba(0, 0, 0, 0.324) 7%,
		rgba(0, 0, 0, 0.306) 10.35%,
		rgba(0, 0, 0, 0.285) 13.85%,
		rgba(0, 0, 0, 0.262) 17.35%,
		rgba(0, 0, 0, 0.237) 20.85%,
		rgba(0, 0, 0, 0.213) 24.35%,
		rgba(0, 0, 0, 0.188) 27.85%,
		rgba(0, 0, 0, 0.165) 31.35%,
		rgba(0, 0, 0, 0.144) 34.85%,
		rgba(0, 0, 0, 0.126) 38.35%,
		rgba(0, 0, 0, 0.112) 41.85%,
		rgba(0, 0, 0, 0.103) 45.35%,
		rgba(0, 0, 0, 0.1) 48.85%,
		rgba(0, 0, 0, 0.103) 52.35%,
		rgba(0, 0, 0, 0.112) 55.85%,
		rgba(0, 0, 0, 0.126) 59.35%,
		rgba(0, 0, 0, 0.144) 62.85%,
		rgba(0, 0, 0, 0.165) 66.35%,
		rgba(0, 0, 0, 0.188) 69.85%,
		rgba(0, 0, 0, 0.213) 73.35%,
		rgba(0, 0, 0, 0.237) 76.85%,
		rgba(0, 0, 0, 0.262) 80.35%,
		rgba(0, 0, 0, 0.285) 83.85%,
		rgba(0, 0, 0, 0.306) 87.35%,
		rgba(0, 0, 0, 0.324) 90.85%,
		rgba(0, 0, 0, 0.338) 94.35%,
		rgba(0, 0, 0, 0.347) 97.85%,
		rgba(0, 0, 0, 0.35)
	);
	pointer-events: none;
	border-radius: 6px;
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-overlay {
	animation-name: Qed4z;
	animation-timing-function: ease-in;
	animation-duration: 0.3s;
}

.unsplash-overlay .tools {
	position: absolute;
	top: 0px;
	padding-right: 2px;
	padding-left: 20px;
	width: 100%;
	display: flex;
	pointer-events: none;
	height: 32px;
	transition: opacity 0.1s ease-in-out, visibility 0.1s ease-in-out;
}

.unsplash-overlay .tools .add-tool {
	display: flex;
	margin-left: auto;
	padding-left: 13px;
}

// .add-collection:hover {
//         color: #111;
// fill: currentColor;
// background-color: #fff;
// box-shadow: 0 1px 3px rgba(0,0,0,.1);
// }

.add-collection {
	cursor: pointer;
	color: #fff;
	fill: currentColor;
	// background-color: hsla(0, 0%, 100%, .9);
	border: 1px solid transparent;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
	border: 1px solid transparent;
	border-radius: 4px;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
	transition: all 0.1s ease-in-out;
	text-align: center;
	-webkit-user-select: none;
	-ms-user-select: none;
	user-select: none;
	font-weight: 500;
	display: inline-flex;
	pointer-events: auto;
	margin-left: 9px;

	.add-svg {
		position: relative;
		top: -1px;
		width: 16px;
	}
}

.btn-add {
	height: 32px;
	padding: 0 11px;
	font-size: 14px;
	line-height: 30px;
	display: inline-block;
	outline: 0;
}
.btn-add:active {
	outline: 0;
}

.decoration-none {
	text-decoration: none;
}

.lds-ring {
	margin-top: 6px;
	display: inline-block;
	position: relative;
	width: 24px;
	height: 24px;
}
.lds-ring div {
	box-sizing: border-box;
	display: block;
	position: absolute;
	width: 18px;
	height: 18px;
	// margin: 8px;
	border: 2px solid #fff;
	border-radius: 50%;
	animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
	border-color: #fff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
	animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
	animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
	animation-delay: -0.15s;
}
@keyframes lds-ring {
	0% {
		transform: rotate(0deg);
	}
	100% {
		transform: rotate(360deg);
	}
}

.project-details-dialog {
	.el-dialog {
		box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
			0 10px 10px -5px rgba(0, 0, 0, 0.04);
		border-radius: 8px;
		.display-title {
			display: flex;
			align-items: center;
			.title-icon {
				width: 3.5rem;
				height: 3.5rem;
				margin-left: 0;
				margin-right: 0;
				display: flex;
				align-items: center;
				background: #ecfff1;
				border-radius: 9999px;
				flex-shrink: 0;
				justify-content: center;
				svg {
					width: 24px;
					height: 24px;
					color: #38a169;
				}
			}
			.title {
				margin-left: 1rem;
				margin-top: 0;
				text-align: left;
				line-height: 1.5rem;
				color: #161e2e;
				font-weight: 500;
				font-size: 16px;
			}
		}
		.el-dialog__body {
			padding: 30px 32px;
		}
		.el-dialog__footer {
			background-color: #f9fafb;
			border-radius: 8px;
			.canacel-creative {
				border-radius: 6px;
				border: none;
				box-shadow: 0px 2px 4px 2px rgba(0, 0, 0, 0.08);
			}
		}
	}
}

.file-style {
	.file-details {
		margin-top: 6px;
		.file-name {
			color: #1a202c;
			font-weight: 500;
		}
		.file-size {
			color: #a0aec0;
			font-size: 12px;
		}
	}
}
</style>