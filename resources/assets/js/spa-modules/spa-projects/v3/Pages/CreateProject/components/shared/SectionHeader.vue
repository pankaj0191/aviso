<template>
  <div class="section-header">
                        <div class="section-title">{{title}}</div>
                        <div class="section-body">{{body}}</div>
                    </div>
</template>

<script>
export default {
    props: ['title', 'body']
}
</script>
<style lang="scss" scoped>
.section-header {
        margin-bottom: 24px;

        .section-title {
            font-size: 18px;
            color: #223345;
            font-weight: 700;
        }
    }
</style>