<template>
    <div id="new-project-card">
        <router-link :to="{ name: 'create-project', params: {role: 'project'} }" class="add-project" v-if="isBothRole">
            <i class="el-icon-plus color-theme add-project-icon"></i>
            <span class="new-project-title">New Project</span>
        </router-link>
        <router-link :to="{ name: 'create-project', params: {role: 'request'} }" class="add-project" v-else>
            <i class="el-icon-plus color-theme add-project-icon"></i>
            <span class="new-project-title">Request Project</span>
        </router-link>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
    export default {
        computed: {
            ...mapGetters(['isBothRole'])
        }
    }
</script>

<style lang="scss" scoped>
    #new-project-card {

        .add-project {
            text-decoration: none;
            --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            background-color: #fff;
            border-radius: 12px;
            width: 100%;
            min-height: 300px;
            display: flex;
            height: 300px;
            align-items: center;
            justify-content: center;
            color: #81b4a1;
            flex-direction: column;
            cursor: pointer;
            font-size: 20px;

            .add-project-icon {
                font-weight: 600;
                width: 70px;
                display: flex;
                color: #fff;
                align-items: center;
                justify-content: center;
                font-size: 30px;
                height: 70px;
                background-color: #81b4a1;
                border-radius: 100%;
                margin-bottom: 16px;
            }

            .new-project-title {
                font-weight: 400;
            }
        }

        .add-project:hover {
            background-color: #F0FFF4;
            text-decoration: none;
            color: #81b4a1;
        }
    }
</style>