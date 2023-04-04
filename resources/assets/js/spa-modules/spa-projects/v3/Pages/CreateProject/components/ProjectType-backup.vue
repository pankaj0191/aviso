<template>
    <div>
        <el-col>
            <el-row class="choose-project-hader">
                <el-alert title type="info" center>
                    <h3>
                        <font>SELECT PROJECT TYPE</font>
                    </h3>
                    <h5>
                        <font>What project below do you want to start?</font>
                    </h5>
                </el-alert>
            </el-row>
            <el-row class="choose-project-body" type="flex" align="middle" justify="center">
                <!-- Graphic Design  -->
                <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
                    <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
                        <div class="rollover">
                            <div class="rollover-body">
                                <el-button @click="chooseProject('design')" class="roll-btn">CREATE DESIGN PROJECT
                                </el-button>
                            </div>
                            <div class="text-center" style="position: absolute; bottom: 20px;">
                                <div class="type-body">
                                    <span class="type-info">
                                        <strong>Used for print design & digital design</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="w3-shadow-body">
                            <div class="img-thumb"
                                v-bind:style="{ 'background-image': 'url(' + `${spacePrefix}/assets/img/design-thumbnail.png` + ')' }">
                            </div>
                            <div class="text-center">
                                <div style="margin-top: 25px">
                                    <span class="type-title">
                                        <strong>GRAPHIC DESIGN</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-col>

                <!-- Website Design  -->
                <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
                    <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
                        <div class="rollover">
                            <div class="rollover-body">
                                <el-button @click="chooseProject('website')" class="roll-btn">CREATE WEBSITE PROJECT
                                </el-button>
                            </div>
                            <div class="text-center" style="position: absolute; bottom: 20px; width: 100%">
                                <div class="type-body">
                                    <span class="type-info">
                                        <strong>Used for live website url links</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="w3-shadow-body">
                            <div class="img-thumb"
                                v-bind:style="{ 'background-image': 'url(' + `${spacePrefix}/assets/img/website-thumbnail.png` + ')' }">
                            </div>

                            <div class="text-center">
                                <div style="margin-top: 25px">
                                    <span class="type-title">
                                        <strong>WEBSITE DESIGN</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-col>

                <!-- Video Design  -->
                <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
                    <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
                        <div class="rollover">
                            <div class="rollover-body">
                                <el-button @click="chooseProject('video')" class="roll-btn">CREATE VIDEO PROJECT
                                </el-button>
                            </div>
                            <div class="text-center" style="position: absolute; bottom: 20px; width: 100%">
                                <div class="type-body">
                                    <span class="type-info">
                                        <strong>Used for live video url links</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="w3-shadow-body">
                            <div class="img-thumb"
                                v-bind:style="{ 'background-image': 'url(' + `${spacePrefix}/assets/img/video-thumbnail.jpg` + ')' }">
                            </div>

                            <div class="text-center">
                                <div style="margin-top: 25px">
                                    <span class="type-title">
                                        <strong>VIDEOGRAPHY</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-col>
            </el-row>
        </el-col>
        <el-col style="margin-top: 30px">
            <el-row type="flex" align="middle" justify="center">
                <router-link :to="{ name: 'projects' }">
                    <el-button>Cancel</el-button>
                </router-link>
            </el-row>
        </el-col>
    </div>
</template>

<script>
    export default {
        data() {
            return {

            }
        },
        computed: {
            spacePrefix() {
				return spacePrefix;
			},
        },
        methods: {
            chooseProject(type) {
                this.$emit('chooseProject', type)
            },
        }
    }
</script>
<style lang="scss" scoped>
.choose-project-body {
	margin-top: 35px;
}
@media (max-width: 500px) {
	.choose-project-body {
		display: block;
	}
	.choose-project-body .w3-hover-shadow.w3-center {
		margin-left: 0;
		margin-right: 0;
	}
}

.w3-hover-shadow {
	border: 1px solid #c5c3c3 !important;
	border-radius: 5px;
	position: relative;
	height: 250px;
	margin: 0 20px;
}

.w3-shadow-body {
	cursor: pointer;
	position: relative;
	height: 100%;
	width: 100%;
	padding: 50px;
}

.rollover {
	width: 100%;
	height: 100%;
	z-index: 999;
	position: absolute;
	background: rgba(0, 0, 0, 0.6);
	opacity: 0;
	/*transition: 0.1s*/
}

.rollover-body {
	width: 100%;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
}

.w3-hover-shadow:hover .rollover {
	opacity: 1;
	/*transition: 0.1s*/
}

.el-alert--info {
	text-align: center;
	background-color: transparent;
}

.roll-btn {
	background-color: rgba(0, 0, 0, 0.7);
	color: #fff;
	font-weight: bold;
}


.img-thumb {
	width: auto;
	height: 135px;
	background-repeat: no-repeat;
	background-position: center;
	background-size: contain;
	border-radius: 5px;
}


.type-title {
	margin-bottom: 5px;
	font-size: 13px;
	color: #8f9091;
}

.type-info {
	margin-bottom: 5px;
	font-size: 12px;
	color: #fff;
}

.type-body {
	padding: 0 10px;
	line-height: 1;
}

@media (max-width: 550px) {
	.choose-project-hader .el-alert h4 {
		font-size: 16px;
	}
}
</style>