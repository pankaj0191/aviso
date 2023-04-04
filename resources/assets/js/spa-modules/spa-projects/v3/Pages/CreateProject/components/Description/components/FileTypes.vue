<template>
    <div class="file-type">
        <div style="padding: 0 24px">
            <div class="switch-file-type" v-for="(extention, key) in extentions" :key="key">
                <div v-if="!extention.children">
                    <el-checkbox v-model="formData.extention" :label="extention.name">{{extention.name}}</el-checkbox>
                </div>
                <div v-else>
                    <slot :extention="extention"></slot>
                    <el-checkbox v-model="extention.value">{{extention.name}}</el-checkbox>

                    <span style="padding-left:12px" v-if="extention.value">
                        <slot name="extentionAdobe" :extentions="extention.children"></slot>

                        (
                        <el-radio v-for="(child,index) in extention.children" :key="index"
                            v-model="formData.extentionAdobe" :label="child.value">{{child.name}}</el-radio>)
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['formData'],
        data() {
            return {
                extentions: [
					{
						name: ".jpg",
						value: true
					},
					{
						name: ".png",
						value: false
					},
					{
						name: ".pdf",
						value: false
					},
					{
						name: "Adobe",
						value: false,
						radio: "",
						children: [
							{
								name: "AI",
								value: "ai"
							},
							{
								name: "PSD",
								value: "psd"
							},
							{
								name: "INDD",
								value: "indd"
							}
						]
					}
				]
            }
        }

    }
</script>

<style lang="scss" scoped>
    .file-type-icon {
        font-size: 64px;
        margin-bottom: 24px;
    }

    .switch-file-type {
        padding-right: 24px;
        display: inline-block;
    }
</style>