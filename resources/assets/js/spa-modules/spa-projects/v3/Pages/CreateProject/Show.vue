<template>
    <div>
        <el-row v-if="types.includes($route.params.type) || ($route.query.worker && role == 'request')">
            <router-view></router-view>
        </el-row>
        <div v-else>
            <project-type @chooseProject="chooseProject" v-if="role =='project'" />
            <create-request @chooseProject="chooseProject" v-if="role == 'request'" />
        </div>
    </div>
</template>

<script>
    import {
        mapGetters
    } from "vuex";
    import ProjectType from './components/ProjectCategory/ProjectType.vue';
    import CreateRequest from './components/CreateRequest.vue';

    export default {
        props: ['role'],
        components: {
            ProjectType,
            CreateRequest
        },
        data() {
            return {
                chooseProjectType: true,
                projectType: "",
                types: ['design', 'website', 'video']
            };
        },
        methods: {
            chooseProject(type) {
                this.projectType = type;
                if (type == 'website') {
                    this.$router.push({
                        name: 'project-title',
                        params: {
                            type: 'website'
                        }
                    })
                } else if (type == 'design') {
                    this.$router.push({
                        name: 'project-title',
                        params: {
                            type: 'design'
                        }
                    })
                } else {
                    this.$router.push({
                        name: 'project-title',
                        params: {
                            type: 'video'
                        }
                    })
                }
                this.chooseProjectType = false;
            },
        },
        computed: {
            spacePrefix() {
                return spacePrefix;
            },
            ...mapGetters(['currentRole'])
        },
        watch: {
            currentRole(val) {
                if (val == 'Client' && this.role == 'project') {
                    return this.$router.push({name:'not-auth'})
                } else if ((val == 'Freelancer' || val == 'Agency') && this.role == 'request') {
                    return this.$router.push({name:'not-auth'})
                }
            }
        },
    };
</script>