<template>
    <div>
        <el-row class="include-section-assets">
            <el-col :md="24" class="include-text">
                <div class="include-text-title">Does your design need to include text?</div>
                <div class="switch-include-text">
                    <slot name="switch" ></slot>
                </div>
            </el-col>
        </el-row>
        <el-row v-show="creative_brief && creative_brief.length > 0 || includeText">
            <el-col :md="24">
                <p class="text-muted">
                    Include text copy
                    <span class="font-italic">exactly</span>
                    as you'd like it to appear in your design
                </p>
                <slot></slot>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    export default {
        props: ['includeText', 'creative_brief'],
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