<template>
    <div>
        <el-row class="include-section-assets">
            <el-col :md="24" class="include-text">
                <div class="include-text-title">Do you have any assets to upload?</div>
                <div class="switch-include-text">
                    <slot name="switch"></slot>
                </div>
            </el-col>
            <el-col :md="24">
                <p class="text-muted">Content, photos, fonts, logos, etc.</p>
            </el-col>
        </el-row>
        <el-row class="add-files" v-if="uploadAssets">
            <el-col :md="24">
                <el-button plain size="small" icon="el-icon-plus" @click="addFilesDailog = true">Add
                    Files</el-button>
            </el-col>
            <el-col>
                <div class="unsplash-selected" v-show="fileList.length > 0">
                    <stack :column-min-width="96" :gutter-width="4" :gutter-height="4" monitor-images-loaded>
                        <stack-item v-for="(image, i) in fileList" :key="i" style="transition: transform 300ms">
                            <div class="unsplash-images">
                                <img :src="`${spacePrefix}/` + (image.url ? image.url : image.thumb)" :alt="image.id" />
                                <div class="unsplash-overlay">
                                    <div class="overlay hover-overlay"></div>
                                    <div class="tools hover-overlay">
                                        <div class="add-tool">
                                            <div class="add-collection" @click="removeSelected(image)">
                                                <svg class="add-svg" version="1.1" viewBox="0 0 32 32" width="32"
                                                    height="32" aria-hidden="false">
                                                    <path
                                                        d="M25.33 8.55l-1.88-1.88-7.45 7.45-7.45-7.45-1.88 1.88 7.45 7.45-7.45 7.45 1.88 1.88 7.45-7.45 7.45 7.45 1.88-1.88-7.45-7.45z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </stack-item>
                    </stack>
                </div>
            </el-col>
        </el-row>
        <el-dialog title="Add Files" :visible.sync="addFilesDailog" width="50%">
            <add-files v-if="addFilesDailog" :project_id="project.id" :revision_id="revision_id"
                :exists_files="fileList" project_type="design" @send-files="getFiles" />
        </el-dialog>
    </div>
</template>

<script>
    import {
        Stack,
        StackItem
    } from "vue-stack-grid";
    import AddFiles from "./../../../../../../request/partials/AddFiles";

    export default {
        props: ['project', 'uploadAssets', 'revision_id'],
        components: {
            Stack,
            StackItem,
            AddFiles
        },
        data() {
            return {
                addFilesDailog: false,
                fileList: [],
            }
        },
        methods: {
            getFiles(data) {
                this.fileList = [...this.fileList, ...data];
                this.addFilesDailog = false;
            },
            removeSelected(image) {
                let self = this;
                return this.$confirm(
                    "This will permanently delete the file. Continue?", {
                        confirmButtonText: "OK",
                        cancelButtonText: "Cancel",
                        type: "warning"
                    }
                ).then(() => {
                    axios
                        .delete(`/api/files/delete/${image.id}`, {
                            params: {
                                type: "assets"
                            }
                        })
                        .then(response => {
                            if (response.data.status == 1) {
                                this.fileList.splice(
                                    this.fileList.indexOf(image.id),
                                    1
                                );
                                this.$message({
                                    type: "success",
                                    message: "Delete completed"
                                });
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                });
            },
        },
        computed: {
            spacePrefix() {
                return spacePrefix
            },
        },
        created() {
            if (
                this.project.project_assets &&
                this.project.project_assets.length > 0
            ) {
                this.uploadAssets = true;
                this.fileList = this.project.project_assets;
            }
        }
    }
</script>
<style lang="scss" scoped>
    .include-section-assets {
        margin-top: 48px;
    }

    .include-text {
        display: flex;
        align-items: center;
        .include-text-title {
            font-weight: 600;   
        }

        .switch-include-text {
            margin-left: 16px;
        }
    }
</style>

<style lang="scss" scoped>
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;

    .add-files {
        margin-top: 24px;
    }

    .validate-text {
        margin-bottom: 8px;
        font-size: 12px;
        color: $red;
    }

    img {
        width: 100%;
        height: auto;
        border-radius: 6px;
    }

    .unsplash-selected {
        padding-top: 12px;
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

    .unsplash-images:hover .not-hover-overlay {
        visibility: hidden;
        opacity: 0;
    }

    .overlay {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: linear-gradient(180deg,
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
                rgba(0, 0, 0, 0.35));
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

    .continue-button {
        padding-top: 24px;
    }
</style>