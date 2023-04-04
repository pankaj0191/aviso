<template>
    <transition name="el-zoom-in-bottom">
        <div
            class="handle-screen tw-fixed tw-bottom-0 tw-inset-x-0 tw-pb-2"
            v-show="selected.length"
        >
            <div class="tw-px-2 banner-handle">
                <div class="tw-p-3 tw-rounded-lg tw-bg-primary tw-shadow-lg">
                    <div
                        class="tw-flex tw-items-center tw-justify-between tw-flex-wrap tw-text-white"
                    >
                        <div class="tw-w-0 tw-flex-1 tw-flex tw-items-center">
                            <div
                                class="tw-ml-3 tw-font-medium tw-text-white tw-truncate"
                            >
                                <span>
                                    Are you sure you want delete ({{
                                        selected.length
                                    }}) projects?
                                </span>
                            </div>
                        </div>
                            <div
                                @click="deleteProject"
                                class="tw-order-3 tw-flex-shrink-0"
                            >
                                <a
                                    href="#"
                                    class="tw-flex tw-items-center tw-justify-center tw-px-6 no-underline tw-py-2 tw-rounded-md tw-shadow-sm text-sm tw-font-medium tw-hover-text-primary tw-text-primary tw-bg-white hover-bg-primary"
                                >
                                    Confirm
                                </a>
                            </div>
                        <div class="tw-order-4 tw-flex-shrink-0 tw-ml-2">
                            <button
                                type="button"
                                @click="clearSelected"
                                class="tw-flex tw-p-2 tw-font-semibold tw-rounded-md tw-border-0 tw-text-white close-bottom"
                            >
                                <i class="el-icon-close tw-text-white"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
    import axios from 'axios';
    export default {
        props: ['selected', 'projectid'],
        methods: {
            clearSelected() {
                this.$emit('clearSelected');
            },
            async deleteProject() {
                this.$emit('loadingMethod', {
                    selected: this.selected,
                    status: true,
                });
                try {
                    let { data } = await axios.post(
                        '/api/projects/bulk/delete',
                        {
                            selected: this.selected,
                        }
                    );
                    if (data.status) {
                        this.$emit('loadingMethod', {
                            selected: this.selected,
                            status: false,
                        });
                        this.$store.commit(
                            'BULK_DELETE_PROJECT',
                            this.selected
                        );
                        this.clearSelected();
                        toastr['success'](data.message, 'Success');
                    } else {
                        this.$emit('loadingMethod', {
                            selected: this.selected,
                            status: false,
                        });
                    }
                } catch (error) {
                    console.log(error);
                    this.$emit('loadingMethod', {
                        selected: this.selected,
                        status: false,
                    });
                }
            },
        },
    };
</script>

<style lang="scss" scoped>
    .banner-handle {
        max-width: 80rem;
        margin-left: auto;
        margin-right: auto;
        .close-bottom {
            background-color: #fff0;
            &:hover {
                background-color: #fff5;
            }
        }
    }

    @media screen and (min-width: 766px) {
        .handle-screen {
            margin-left: 220px;
        }
    }

    @media screen and (max-width: 765px) {
        .handle-screen {
            margin-left: 54px;
        }
    }
</style>
