<template>
    <div id="project-loading-card">
        <spinner line-fg-color="#81b4a1" />
    </div>
</template>

<script>
    import Spinner from 'vue-simple-spinner'
    export default {
        components: {
            Spinner
        }
    }
</script>
<style lang="scss" scoped>
    #project-loading-card {
        width: 100%;
        min-height: 290px;
        display: flex;
        height: 290px;
        align-items: center;
        justify-content: center;
         --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        background-color: #fff;
        border-radius: 4px;
        margin-bottom: 24px;
    }
</style>