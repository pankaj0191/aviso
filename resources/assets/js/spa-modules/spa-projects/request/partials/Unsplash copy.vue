<template>
    <div>
        <div>
            <el-input placeholder="Search assets by keyword" @keyup.enter.native="fetch" v-model="search"
                class="input-with-select">
                <template slot="prepend" v-if="selectedPhotos.length > 0">{{selectedPhotos.length}}</template>
                <el-button slot="append" icon="el-icon-search" @click="fetch">Search</el-button>
            </el-input>
        </div>
        <div class="loader-inner ball-pulse text-center">
            <div></div>
            <div></div>
            <div></div>
        </div>
        <div class="unsplash-photos">
            <stack :column-min-width="300" :gutter-width="15" :gutter-height="15" monitor-images-loaded>
                <stack-item v-for="(image, i) in unsplashPhotos.results" :key="i" style="transition: transform 300ms">
                    <div class="unsplash-images">
                        <img :src="image.urls.small" :alt="image.alt_description" />
                        <div class="image-tools">
                            <div class="overlay _3nWK4"></div>
                            <div class="tools _3nWK4">
                                <div class="add-tool">
                                    <button type="button" title="Add to collection"
                                        class="_3W4BS _1jjdS _1CBrG _1WPby xLon9 LqSCP _17avz _3dBbn _2gYrT _1dqDa"><svg
                                            class="_2rdbO" version="1.1" viewBox="0 0 32 32" width="32" height="32"
                                            aria-hidden="false">
                                            <path d="M14 3h4v26h-4zM29 14v4h-26v-4z"></path>
                                        </svg></button>
                                </div>
                            </div>
                            <div class="kOmuh _1pUy3">
                                <div class="_3Iv8j _2YCx7 _3nWK4"><span class="_2aPXR">
                                        <div class="_2BNtU"><span class="_1JARO">
                                                <div class="ODWzM" style="width: 32px; height: 32px;"><a
                                                        href="/@henmankk"><img class="_1FdcY"
                                                            :src="image.user.profile_image.small"
                                                            :alt="'Go to '+ image.user.name + '\'s profile'"></a></div>
                                            </span></div>
                                        <div class="_80uCh"><span class="_1JARO"><a
                                                    class="_3XzpS _1ByhS _4kjHg _1_w0v _3XJBh _1CBrG _2zITg"
                                                    :href="image.user.links.html">{{image.user.name}}</a></span></div>
                                    </span></div>
                            </div>
                        </div>
                    </div>
                </stack-item>
            </stack>
            <!-- <el-row :gutter="12" class="grid-gallery">
                <el-col class="gallery-responsive" :md="8" v-for="(photo, index) in unsplashPhotos.results"
                    :key="photo.id">
                    <div class="image-container" @click="addPhoto(photo)">
                        <img class="img-responsive" v-if="photo.urls" :src="photo.urls.small" alt="">
                        <div class="middle-overlay">
                            <span class="image-icon"><i class="el-icon-check"></i> {{photo.user.name}}</span>
                        </div>
                        <div class="middle-overlay-added" v-if="selectedPhotos.includes(photo.id)">
                            <span class="image-icon"><i class="el-icon-check"></i> {{photo.user.name}}</span>
                        </div>
                    </div>
                </el-col>
                <el-col :md="24" class="text-center">
                      <el-button size="small" type="primary" @click="loadMore">Load more</el-button>

                </el-col>
            </el-row> -->
        </div>

    </div>
</template>

<script>
    import {
        Stack,
        StackItem
    } from "vue-stack-grid";
    export default {
        props: {
            search: {
                type: String
            }
        },
        components: {
            Stack,
            StackItem
        },
        data() {
            return {
                unsplashPhotos: [],
                selectedPhotos: [],
                newSearch: '',
                page: 1
            }
        },
        watch: {
            search(newVal, oldVal) {
                if (newVal !== oldVal) {
                    this.$emit('newSearchVal', newVal)
                }
            }
        },
        methods: {
            async fetch() {
                await axios
                    .get("/api/request/unsplash", {
                        params: {
                            search: this.search,
                            page: this.page
                        }
                    })
                    .then(response => {
                        this.unsplashPhotos = response.data;
                        this.$emit('unsplashData', response.data)
                    })
                    .catch(error => {
                        console.log(error);
                        // this.handle_error(error);
                    });
            },
            async loadMore() {
                this.page++
                await axios
                    .get("/api/request/unsplash", {
                        params: {
                            search: this.search,
                            page: this.page
                        }
                    })
                    .then(response => {
                        this.unsplashPhotos.results = [...this.unsplashPhotos.results, ...response.data
                            .results
                        ];
                        // this.$emit('unsplashData', response.data)
                    })
                    .catch(error => {
                        console.log(error);
                        // this.handle_error(error);
                    });
            },
            addPhoto(photo) {
                if (this.selectedPhotos.includes(photo.id)) {
                    this.selectedPhotos.pop(photo.id)
                } else {
                    this.selectedPhotos.push(photo.id)
                }
            }
        },
        mounted() {
            this.fetch()
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

    .input-with-select {
        padding-bottom: 8px;
    }

    .unsplash-photos {
        height: 160vh;
        overflow: auto;
        background-color: #f5f7fa;
    }

    ._1Nk0C {
        position: relative;
    }

    ._3nWK4 {
        transition: opacity .1s ease-in-out, visibility .1s ease-in-out
    }

    ._1Nk0C:not(:hover) ._3nWK4 {
        visibility: hidden;
        opacity: 0
    }

    .overlay {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: linear-gradient(180deg, rgba(0, 0, 0, .34) 0, rgba(0, 0, 0, .338) 3.5%, rgba(0, 0, 0, .324) 7%, rgba(0, 0, 0, .306) 10.35%, rgba(0, 0, 0, .285) 13.85%, rgba(0, 0, 0, .262) 17.35%, rgba(0, 0, 0, .237) 20.85%, rgba(0, 0, 0, .213) 24.35%, rgba(0, 0, 0, .188) 27.85%, rgba(0, 0, 0, .165) 31.35%, rgba(0, 0, 0, .144) 34.85%, rgba(0, 0, 0, .126) 38.35%, rgba(0, 0, 0, .112) 41.85%, rgba(0, 0, 0, .103) 45.35%, rgba(0, 0, 0, .1) 48.85%, rgba(0, 0, 0, .103) 52.35%, rgba(0, 0, 0, .112) 55.85%, rgba(0, 0, 0, .126) 59.35%, rgba(0, 0, 0, .144) 62.85%, rgba(0, 0, 0, .165) 66.35%, rgba(0, 0, 0, .188) 69.85%, rgba(0, 0, 0, .213) 73.35%, rgba(0, 0, 0, .237) 76.85%, rgba(0, 0, 0, .262) 80.35%, rgba(0, 0, 0, .285) 83.85%, rgba(0, 0, 0, .306) 87.35%, rgba(0, 0, 0, .324) 90.85%, rgba(0, 0, 0, .338) 94.35%, rgba(0, 0, 0, .347) 97.85%, rgba(0, 0, 0, .35));
        pointer-events: none;
        border-radius: 12px;
        transition: opacity .1s ease-in-out, visibility .1s ease-in-out;
    }

    .image-tools {
        animation-name: Qed4z;
        animation-timing-function: ease-in;
        animation-duration: .3s;
    }

    .image-tools .tools {
        position: absolute;
        top: 20px;
        padding-right: 20px;
        padding-left: 20px;
        width: 100%;
        display: flex;
        pointer-events: none;
        height: 32px;
        transition: opacity .1s ease-in-out, visibility .1s ease-in-out;
    }

    .image-tools .tools .add-tool {
        display: flex;
        margin-left: auto;
        padding-left: 13px;
    }

    ._2gYrT {
        margin-left: 9px;
    }

    ._1dqDa {
        pointer-events: auto;
    }

    ._3dBbn {
        display: inline-flex;
    }

    ._3W4BS {
        color: #767676;
        fill: currentColor;
        background-color: hsla(0, 0%, 100%, .9);
        border: 1px solid transparent;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .06);
    }

    ._1WPby {
        height: 32px;
        padding: 0 11px;
        font-size: 14px;
        line-height: 30px;
    }

    ._1WPby,
    ._2bkxm {
        display: inline-block;
    }

    ._1jjdS {
        border: 1px solid transparent;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        transition: all .1s ease-in-out;
        text-align: center;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    ._1CBrG {
        text-decoration: none;
    }

    .xLon9 {
        font-weight: 500;
    }

    ._2rdbO {
        position: relative;
        top: -1px;
        width: 16px;
    }

    .kOmuh {
        position: absolute;
        bottom: 20px;
    }

    ._1pUy3 {
        padding-right: 20px;
        padding-left: 20px;
        width: 100%;
        display: flex;
        pointer-events: none;
        height: 32px;
    }

    ._3Iv8j {
        flex: 1;
        min-width: 0;
        margin-right: 8px;
    }

    ._3nWK4 {
        transition: opacity .1s ease-in-out, visibility .1s ease-in-out;
    }


    ._2aPXR {
        pointer-events: auto;
        display: inline-flex;
        align-items: center;
        max-width: 100%;
    }

    ._1JARO,
    ._2aPXR {
        position: relative;
    }

    ._2BNtU {
        margin-right: 8px;
    }

    ._1JARO,
    ._2aPXR {
        position: relative;
    }

    .ODWzM {
        position: relative;
        display: inline-block;
        line-height: 1;
        border-radius: 50%;
        overflow: hidden;
        vertical-align: middle;
        background-color: rgba(0, 0, 0, .1);
    }

    ._2-V32,
    a {
        color: #767676;
        transition: color .1s ease-in-out, opacity .1s ease-in-out;
        -webkit-text-decoration-skip: ink;
        text-decoration-skip-ink: auto;
    }

    ._1FdcY {
        vertical-align: unset;
        width: 100%;
    }

    img {
        vertical-align: middle;
    }

    ._1B083 {
        display: inline-flex;
        justify-content: center;
    }


    ._1WPby {
        height: 32px;
        padding: 0 11px;
        font-size: 14px;
        line-height: 30px;
    }

    ._1WPby,
    ._2bkxm {
        display: inline-block;
    }

    ._1jjdS {
        border: 1px solid transparent;
        border-radius: 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        transition: all .1s ease-in-out;
        text-align: center;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    ._1CBrG {
        text-decoration: none;
    }

    .xLon9 {
        font-weight: 500;
    }

    ._3d86A,
    ._3d86A:hover {
        box-shadow: none;
    }

    ._3d86A {
        border: none;
        padding: 0;
        background-color: transparent;
        text-align: inherit;
    }

    ._2-V32,
    a {
        color: #767676;
        transition: color .1s ease-in-out, opacity .1s ease-in-out;
        -webkit-text-decoration-skip: ink;
        text-decoration-skip-ink: auto;
    }

    a {
        background-color: transparent;
    }

    .Apljk {
        position: relative;
        top: -1px;
        width: 16px;
    }

    ._1_w0v {
        color: #fff;
        transform: translateZ(0);
        text-shadow: 0 1px 8px rgba(0, 0, 0, .1);
    }

    ._2zITg {
        line-height: 30px;
    }

    ._3XzpS {
        display: block;
        font-size: 15px;
    }

    ._4kjHg {
        line-height: 1.35;
    }

    ._1ByhS {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    ._1CBrG {
        text-decoration: none;
    }

    ._3XJBh {
        opacity: .8;
        transition: opacity .1s ease-in-out;
    }

    .grid-gallery {}

    .gallery-responsive {
        padding-top: 12px;
    }

    .img-responsive {
        display: block;
        max-width: 100%;
        height: auto;
    }

    .image-container {
        cursor: pointer;
        position: relative;
    }

    .image-container img {
        transition: 0.3s ease;
    }

    .image-container:hover .middle-overlay {
        opacity: 0.9;
    }

    .middle-overlay {
        position: absolute;
        // top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 48px;
        width: 100%;
        opacity: 0;
        transition: 0.5s ease;
        background-color: $green;
        cursor: pointer;
    }

    .middle-overlay-added {
        position: absolute;
        // top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 48px;
        width: 100%;
        opacity: .9;
        transition: 0.5s ease;
        background-color: $green;
        cursor: pointer;
    }

    .middle-overlay-added .image-icon,
    .middle-overlay .image-icon {
        color: white;
        font-size: 14px;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .ball-pulse>div:nth-child(1) {
        -webkit-animation: scale .75s -.24s infinite cubic-bezier(.2, .68, .18, 1.08);
        animation: scale .75s -.24s infinite cubic-bezier(.2, .68, .18, 1.08);
    }

    .ball-pulse>div {
        background: #39ca86;
    }

    .ball-pulse>div {
        background-color: #fff;
        width: 15px;
        height: 15px;
        border-radius: 100%;
        margin: 2px;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
        display: inline-block;
    }

    .ball-pulse>div:nth-child(2) {
        -webkit-animation: scale .75s -.12s infinite cubic-bezier(.2, .68, .18, 1.08);
        animation: scale .75s -.12s infinite cubic-bezier(.2, .68, .18, 1.08);
    }

    .ball-pulse>div:nth-child(3) {
        -webkit-animation: scale .75s 0s infinite cubic-bezier(.2, .68, .18, 1.08);
        animation: scale .75s 0s infinite cubic-bezier(.2, .68, .18, 1.08);
    }

    img {
        width: 100%;
        height: auto;
        border-radius: 12px;
    }
</style>