<template>
    <div v-if="teamMembers.length > 0">
        <div v-for="member in teamMembers" :key="member.id" style="margin-top: 10px">
            <label class="notify-label">
                <div style="display: flex; align-items: center">
                    <img style="float: left" :src="member.photo_url"
                        class="img-circle special-img team-circle user-avatar">
                    <span class="member-emailaddress" style="margin-left: 20px">{{member.email}}</span>
                </div>
            </label>
            <div style="float: right; margin-left: 20px">
                <label class="switch">
                    <input type="checkbox" name="new_project" v-model="check" :value="member.id" :id="member.id" @change="modifyMemberAccess">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
    import {
        mapGetters
    } from 'vuex'
    export default {
        props: [
            'project_id', 'project'
        ],
        data() {
            return {
                check: [],
            }
        },
        computed: {
            ...mapGetters(['teamMembers'])
        },
        methods: {
            fetchData() {
                if (this.project && this.project.users) {
                    this.check =  _.map(this.project.users.filter(user => user.pivot.role == 'freelancer'), 'id')
                }
            },  
            modifyMemberAccess(event) {
                let formData = {
                    member_id: event.target.id,
                    project_id: this.project_id,
                };

                if (event.target.checked) {
                    axios
                        .post("/api/projects/add-team-member", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                toastr['success'](response.data.message, 'Success');
                            } else {
                                event.target.checked = false;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            event.target.checked = false;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                            this.handle_error({});
                        });
                } else {
                    axios
                        .delete("/api/projects/delete-team-member/" + this.project_id + '/' + event.target.id)
                        .then(response => {
                            if (response.data.status == 1) {
                                toastr['success'](response.data.message, 'Success');
                            } else {
                                event.target.checked = true;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            event.target.checked = true;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                        });
                }
            },
        },
        mounted() {
            this.fetchData()
        }
    }
</script>

<style lang="scss" scoped>
 /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 49px;
        height: 23px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        display: none;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #80b4a0;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #80b4a0;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .notify-body {
        text-align: center;
    }

    .notify-info {
        margin-bottom: 20px;
    }

    .notify-label {
        text-align: right;
        font-weight: normal;
    }

    @media (max-width: 550px) {
        .notify-label .member-emailaddress {
            margin-left: 10px !important;
        }
        .notify-label .user-avatar {
            margin: 0 !important;
        }
    }
</style>