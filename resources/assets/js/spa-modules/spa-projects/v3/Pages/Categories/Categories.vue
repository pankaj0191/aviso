<template>
    <div>
        <h3 class="hidden-sm-and-up text-center" style="text-transform:uppercase;font-weight:bold">Categories</h3>
        <categories :options="categories" />
        <project-types :options="subCategories" :categories="categories" />
    </div>
</template>

<script>
    import {
        mapGetters
    } from "vuex";
    import Categories from './components/Categories'
    import ProjectTypes from './components/ProjectTypes'

    export default {
        components: {
            Categories,
            ProjectTypes
        },
        data() {
            return {
                categories: [],
                subCategories: [],
            };
        },
        methods: {
            async handleAction(index, id, status) {
                this.actionLoading.push(id)
                try {
                    let {data} = await axios.post("/api/projects/active/category", {
                        status: status,
                        id: id
                    })
                    this.actionLoading.splice(this.actionLoading.indexOf(id), 1)
                    this.categories.splice(index, 1, data);
                } catch (error) {
                    this.actionLoading.splice(this.actionLoading.indexOf(id), 1)
                    console.log(error)
                }
            },
            async fetch() {
                try {
                    let {data} = await axios.get('/api/projects/categories', {
                        params: {
                            active: 1
                        }
                    })
                    this.categories = data.categories
                    this.subCategories = data.subCategories
                } catch (error) {
                    console.log(error)
                }
            }
        },
        computed: {
            ...mapGetters(["isBothRole", 'user'])
        },
        async mounted() {
            await this.fetch();
            // if (!this.isBothRole) {
            //     return this.$router.push({name:'not-auth'})
            // }
        }
    };
</script>
<style lang="scss" scoped>
    .name-avatar {
        display: flex;
        align-items: center;
    }

  
</style>