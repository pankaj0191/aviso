<template>
    <div v-if="Object.entries(project).length">
        <router-view :project="project" @updateProject="updateProject"></router-view>
    </div>
</template>

<script>
export default {
    props: ['project_id', 'type'],
    data() {
        return {
            project: {},
            revision_id: '',
        }
    },
    methods: {
        async fetchProject() {
            try {
                let { data } = await axios.get(`/api/projects/details/${this.project_id}`)
                if (data.status == 1) {
                    this.project = data.data.project
                    this.revision_id = data.data.revision_id
                    this.checkType(data.data.project)
                }
            } catch (error) {
                toastr['error'](error.response.data.message, 'Error');
            }
        },
        checkType(project) {
            if (project.type == this.type) {
                return
            } else {
                return this.$router.push({name: 'exist-project-title', params: {type: project.type}})
            }
        },
        updateProject(project) {
            this.project = project
        }
    },
    created() {
        this.fetchProject()
    }
}
</script>

<style>

</style>