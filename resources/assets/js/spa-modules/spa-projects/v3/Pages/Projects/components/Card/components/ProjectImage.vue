<template>
    <div>
        <div v-if="project.active_revision.proofs.length && isBothRole || (project.active_revision.proofs.length && project.status !== 'draft')"
            class="img-thumb"
            v-bind:style="{'background-image': 'url(' + spacePrefix + '/' + (project.type != 'video' ? project.active_revision.proofs[0].project_files.path : project.active_revision.proofs[0].project_files.thumb_path) + ')'}">
        </div>
        <div v-else-if="project.created_role == 'client' && !isBothRole && !project.active_revision.proofs.length && project.type != 'website'"
            class="img-thumb amiright">
            <el-row type="flex" justify="center" align="middle" style="height: 100%">
                <el-col class="draft-project">
                    <i class="el-icon-info"></i>
                    <h4>Design Request</h4>
                </el-col>
            </el-row>
        </div>
        <div v-else-if="project.created_role == 'client' && project.status === 'draft' && project.type != 'website'"
            class="img-thumb amiright">
            <el-row type="flex" justify="center" align="middle" style="height: 100%">
                <el-col class="draft-project">
                    <i class="el-icon-document"></i>
                    <h4>Design Request</h4>
                </el-col>
            </el-row>
        </div>
        <div v-else-if="project.created_role != 'client' && !isBothRole && project.status === 'draft' && project.type != 'website'"
            class="img-thumb amiright">
            <el-row type="flex" justify="center" align="middle" style="height: 100%">
                <el-col class="draft-project">
                    <i class="el-icon-info"></i>
                    <h4>Your design will <br /> be available soon.</h4>
                </el-col>
            </el-row>
        </div>
        <div v-else-if="project.created_role != 'client' && !isBothRole && !project.active_revision.proofs.length && project.type != 'website'"
            class="img-thumb amiright">
            <el-row type="flex" justify="center" align="middle" style="height: 100%">
                <el-col class="draft-project">
                    <i class="el-icon-info"></i>
                    <h4>Your design will <br /> be available soon.</h4>
                </el-col>
            </el-row>
        </div>
        <div v-else class="img-thumb"
            v-bind:style="{ 'background-image': 'url(' + `${spacePrefix}/assets/img/placeholder.jpg` + ')' }"></div>
    </div>
</template>

<script>
    export default {
        props: ['project', 'isBothRole'],
        computed: {
            spacePrefix() {
                return spacePrefix
            }
        }
    }
</script>

<style lang="scss" scoped>
    img {
        width: 100%;
        display: block;
        width: 100%;
        height: auto;
    }

    .img-thumb {
        width: 100%;
        height: 184px;
        display: block;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        border-radius: 5px;
        background-color: #A0AEC0;
        color: #fff;
        text-align: center;
    }
</style>