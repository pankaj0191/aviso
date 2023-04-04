P<template>
    <el-dialog :close-on-click-modal="false" :show-close="false" :destroy-on-close="true" :close-on-press-escape="false"
        class="project-details-dialog" :title="'Create Project Type'" :visible.sync="addDialog" width="30%">
        <el-row :gutter="12">
            <el-col style="margin-bottom: 12px" :span="24">
                <el-input required="required" type="text" autosize placeholder="Name" v-model="form.name"></el-input>
                <span class="text-danger" v-if="serverErrors && serverErrors.name">{{serverErrors.name[0]}}</span>
            </el-col>
            <el-col style="margin-bottom: 12px" :span="8">
                <el-input required="required" placeholder="Width" v-model="form.width">
                </el-input>
            </el-col>
            <el-col style="margin-bottom: 12px" :span="8">
                <el-input required="required" placeholder="Height" v-model="form.height">
                </el-input>
            </el-col>
            <el-col style="margin-bottom: 12px" :span="8">
                <el-select v-model="form.size_type" placeholder="Select Size Type">
                    <el-option label="Pixel" value="pixel"></el-option>
                    <el-option label="Inch" value="inch"></el-option>
                </el-select>
            </el-col>
            <el-col style="margin-bottom: 12px" :span="24">
                <el-select required="required" placeholder="Select Project Type" style="width:100%" v-model="form.category_id" filterable>
                    <el-option v-for="item in categoriesFilter" :key="item.id" :label="item.name" :value="item.id">
                    </el-option>
                </el-select>
                <span class="text-danger" v-if="serverErrors && serverErrors.category_id">{{serverErrors.category_id[0]}}</span>
            </el-col>
            <el-col required="required" style="margin-bottom: 12px;overflow:hidden" :span="24">
                <input type="file" class="hidden" @change="updatePhotoPreview" name="image" id="image" ref="image">
                <el-button
                    type="primary"
                    @click.native.prevent="selectNewPhoto"
                    class="tw-btn"
                    size="medium"
                >Upload Category Photo</el-button>
                <div class="tw-my-2">
                    <img v-if="form.image" :src="`${spacePrefix}/${form.image}`" width="120px" alt="" />
                </div>
                <span class="text-danger" v-if="serverErrors && serverErrors.image">{{serverErrors.image[0]}}</span>
                <avatar-upload field="photo"
                    @crop-success="cropSuccess"
                    @crop-upload-success="cropUploadSuccess"
                    @crop-upload-fail="cropUploadFail"
                    v-model="show"
                    langType="en"
                    :width="245"
                    :height="120"
                    ref="uploader"
                    url="/api/projects/project-type/image"
                    :params="params"
                    :headers="headers"
                    :withCredentials="true"
                    img-format="png"></avatar-upload>
            </el-col>
            <el-col style="margin-bottom: 12px" :span="24">
                <el-checkbox v-model="form.active">Active</el-checkbox>
            </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
            <div class="creative-footer">
                <el-button size="mini" @click="dialog(false)">Cancel</el-button>
                <el-button size="mini" type="primary" @click="hancelCreate" :loading="dialogLoading">Save
                </el-button>
            </div>
        </span>
    </el-dialog>
</template>

<script>
import AvatarUpload from 'vue-image-crop-upload/upload-2.vue';

export default {
        components: { AvatarUpload },
        props: ['categories', 'addDialog'],
        data() {
            return {
                dialogLoading: false,
                form: {
                    name: '',
                    category_id: '',
                    image: '',
                    width: '',
                    height: '',
                    active: true,
                    size_type: 'pixel',
                },
                serverErrors: null,
                show: false,
                photoPreview: null,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                params: {},
            }
        },
        computed: {
            categoriesFilter() {
                if (this.categories && this.categories.length > 0) {
                    return this.categories.filter(cat => cat.profiles.length > 0)
                } else {
                    return this.categories
                }
            },
            spacePrefix() {
                return spacePrefix
            },
        },
    methods: {
        getCookie(cName) {
                const name = cName + "=";
                const cDecoded = decodeURIComponent(document.cookie); //to be careful
                const cArr = cDecoded.split('; ');
                let res;
                cArr.forEach(val => {
                    if (val.indexOf(name) === 0) res = val.substring(name.length);
                })
                return res
            },
            dialog() {
                this.$emit('addDialog')
            },
            selectNewPhoto() {
				// this.$refs.photo.click();
                this.show = true

            },
            cropSuccess(imgDataUrl, field){
                this.imgDataUrl = imgDataUrl;
                this.loading = true;
			},
			/**
			 * upload success
			 *
			 * [param] jsonData  server api return data, already json encode
			 * [param] field
			 */
            cropUploadSuccess(jsonData, field) {
                // Tostar success
                if (jsonData.status == 1) {
                    toastr["success"](jsonData.message, "Success");
                    this.form.image = jsonData.data.url;
                    this.show = false;
                } else {
                    toastr["error"](jsonData.message, "Error");
                }
                this.$refs.uploader.setStep(1)
                this.loading = false;
			},
			/**
			 * upload fail
			 *
			 * [param] status    server api return error status, like 500
			 * [param] field
			 */
			cropUploadFail(status, field){
				console.log('-------- upload fail --------');
				console.log(status);
				console.log('field: ' + field);
			},
			updatePhotoPreview() {
				const reader = new FileReader();

				reader.onload = e => {
					this.photoPreview = e.target.result;
				};

				reader.readAsDataURL(this.$refs.photo.files[0]);
				this.photo = this.$refs.photo.files[0];
            },
            
            async hancelCreate() {
                let fd = new FormData();
                fd.append('image', this.form.image);
                fd.append('category_id', this.form.category_id);
                fd.append('name', this.form.name);
                fd.append('width', this.form.width);
                fd.append('height', this.form.height);
                fd.append('active', this.form.active);
                fd.append('size_type', this.form.size_type);

                this.dialogLoading = true
                try {
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }
                    let {
                        data
                    } = await axios.post("/api/projects/project-type", fd, config)
                    this.dialogLoading = false
                    this.$emit('addProjectType', data.data)
                    this.form = {
                        name: '',
                        category_id: '',
                        image: '',
                        width: '',
                        height: '',
                        active: false,
                        size_type: 'pixel',
                    }
                    this.$refs.image.value = null;

                    this.$emit('addDialog', false)
                } catch (error) {
                    this.serverErrors = error.response.data.errors
                    this.dialogLoading = false
                    console.log(error.response.data.errors)
                }
            },
        },
        created() {
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
        }
    }
</script>

<style lang="scss">
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;

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
                padding: 12px 24px;
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

    .creative-footer {
        padding: 0 12px;

        .project-info {
            text-align: left;
            display: flex;

            .avatar {
                margin-right: 12px;
                box-shadow: $box-shadow;
            }

            .owner-name {
                color: #2d3748;
                font-weight: 400;
            }

            .details {
                color: #a0aec0;

                span:not(:last-child) {
                    margin-right: 12px;
                }
            }
        }
    }
</style>