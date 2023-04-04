<template>
    <el-dialog :close-on-click-modal="false" :show-close="false" :close-on-press-escape="false"
        class="project-details-dialog" :title="'Upload File to Project Type'" :visible.sync="fileDialog" width="30%">
        <el-row :gutter="12">
            <el-col style="margin-bottom: 12px" :span="24">
                <ul class="">
                    <div v-for="(file, key) in dialogData.files" :key="key">
                        <el-popover width="130" trigger="hover">
                            <img height="128px" width="100%" class="tw-mr-2" :src="`${spacePrefix}/${file.image}`"
                                alt />
                            <li slot="reference"
                                class="tw-cursor-pointer tw-flex tw-items-center tw-justify-between tw-mx-2 tw-my-2 tw-bg-gray-50 tw-py-3 tw-px-4 tw-rounded tw-shadow">
                                <div><img width="24px" v-if="file.image" class="tw-mr-2" :src="`${spacePrefix}/${file.image}`" alt /> <a
                                        class="color-success" target="_blank"
                                        :href="`${spacePrefix}/${file.file ? file.file : ''}`">{{file.name ? file.name : 'N/A' | truncate(20, '...') }}</a> <small
                                        class="tw-text-gray-400" v-if="file.size">
                                        {{humanFileSize(file.size, true)}}</small></div>
                                <div>
                                    <i class="el-icon-loading tw-mx-1" v-if="imageLoading.includes(file.id)"></i>
                                    <el-tooltip effect="dark" content="Download" v-else>
                                        <i class="el-icon-download tw-mx-1" @click="downloadZipFile(file)"></i>
                                    </el-tooltip>
                                    <el-tooltip effect="dark" content="Edit">
                                        <i class="el-icon-edit tw-mx-1" @click="editFileProjectType(file)"></i>
                                    </el-tooltip>
                                    <i class="el-icon-loading tw-mx-1" v-if="deleteLoading.includes(file.id)"></i>
                                    <el-tooltip effect="dark" content="Delete" v-else>
                                        <i class="el-icon-close tw-mx-1" @click="deleteFile(file)"></i>
                                    </el-tooltip>
                                </div>
                            </li>
                        </el-popover>
                    </div>
                </ul>
            </el-col>
            <el-col>
                <el-divider content-position="center">Upload New Temeplete</el-divider>
            </el-col>
            <el-col required="required" style="margin-bottom: 12px" :span="24">
                <el-row>
                    <el-col :span="12">
                        <label for="Image">Image</label>
                        <input type="file" @change="editImage" name="image" id="image" ref="image">
                        <span class="text-danger" v-if="serverErrors && serverErrors.image">{{serverErrors.image[0]}}</span>
                    </el-col>
                    <el-col :span="12" class="text-right">
                        <img v-if="!imageLocalPrev" :src="`${spacePrefix}/${imagePrev}`" width="76px" alt="">
                        <img v-else :src="`${imageLocalPrev}`" width="76px" alt="">
                    </el-col>
                </el-row>
            </el-col>
            <el-col required="required" style="margin-bottom: 12px" :span="24">
                <hr>
                <label for="fileUpload">File</label>
                <input type="file" @change="editFile" name="fileUpload" id="fileUpload" ref="fileUpload">
                <span class="text-danger" v-if="serverErrors && serverErrors.template">{{serverErrors.template[0]}}</span>
                <a class="tw-mt-3 color-success" target="_blank"
                    :href="`${spacePrefix}/${form.fileUpload}`">{{filePrev}}</a>
            </el-col>
        </el-row>
        <span slot="footer" class="dialog-footer">
            <div class="creative-footer">
                <el-button size="mini" @click="dialog(false)">Cancel</el-button>
                <el-button size="mini" v-if="form.id" type="primary" @click="handelSave(true)" :loading="dialogLoading">Update
                </el-button>
                <el-button size="mini" v-if="form.id" type="primary" @click="addNew" :loading="dialogLoading">New</el-button>
                <el-button size="mini" v-if="!form.id" type="primary" @click="handelSave(true)" :loading="dialogLoading">Save &
                    Continue
                </el-button>
                <el-button size="mini" v-if="!form.id" type="primary" @click="handelSave(false)" :loading="dialogLoading">Save
                </el-button>

            </div>
        </span>
    </el-dialog>
</template>

<script>
    import {
        Mixin
    } from '../../../../../mixin'
    export default {
        props: ['categories', 'fileDialog', 'dialogData'],
        mixins: [Mixin],
        data() {
            return {
                filesList: [],
                dialogLoading: false,
                form: {
                    id: '',
                    image: null,
                    fileUpload: null,
                },
                imagePrev: '',
                imageLocalPrev: '',
                filePrev: '',
                serverErrors: null,
                imageLoading: [],
                deleteLoading: [],
            }
        },
        watch: {
            fileDialog(val) {
                this.filesList = this.files
                this.form = {
                    fileUpload: null,
                    image: null,
                }
            }
        },
        computed: {
            spacePrefix() {
                return spacePrefix
            }
        },
        methods: {
            editImage(e) {
                const file = e.target.files[0];
                this.imageLocalPrev = URL.createObjectURL(file);
                this.form.image = file
            },
            editFile(e) {
                const file = e.target.files[0];
                this.form.fileUpload = file
            },
            editFileProjectType(file) {
                this.form.id = file.id
                this.imagePrev = file.image
                this.imageLocalPrev = ''
                this.filePrev = file.name
                this.form.fileUpload = file.file
            },
            dialog(status) {
                this.$emit('fileDialog', status)
            },
            addNew() {
                this.form = {
                    id: '',
                    image: null,
                    fileUpload: null,
                }
                this.imagePrev = ''
                this.filePrev = ''
            },
            async deleteFile(file) {
                this.deleteLoading.push(file.id)
                try {
                    let {
                        data
                    } = await axios.delete(`/api/projects/project-type/files/${file.id}`, )
                    this.dialogData.files.splice(this.dialogData.files.indexOf(data.id), 1)
                    this.deleteLoading.splice(
                        this.deleteLoading.indexOf(file.id),
                        1
                    );
                } catch (error) {
                    console.log(error)
                    this.deleteLoading.splice(
                        this.deleteLoading.indexOf(file.id),
                        1
                    );
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
            async handelSave(status) {
                let fd = new FormData();
                if (this.form.id) {
                    fd.append('id', this.form.id);
                }
                fd.append('image', this.form.image);
                fd.append('template', this.form.fileUpload);

                this.dialogLoading = true
                try {
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }
                    let {
                        data
                    } = await axios.post(`/api/projects/project-type/files/${this.dialogData.id}`, fd, config)
                    this.dialogLoading = false
                    if (this.form.id) {
                        let index = _.map(this.dialogData.files, 'id').indexOf(data.data.id)
                        this.dialogData.files.splice(index, 1, data.data)
                    } else {
                        this.dialogData.files.push(data.data);
                    }
                    this.form = {
                        id: '',
                        image: '',
                        fileUpload: '',
                    }
                    this.imagePrev = ''
                    this.imageLocalPrev = ''
                    this.filePrev = ''
                    this.$refs.image.value = null;
                    this.$refs.fileUpload.value = null;

                    this.$emit('fileDialog', status)
                } catch (error) {
                    this.serverErrors = error.response.data.errors
                    this.dialogLoading = false
                    console.log(error.response.data.errors)
                }
            },
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