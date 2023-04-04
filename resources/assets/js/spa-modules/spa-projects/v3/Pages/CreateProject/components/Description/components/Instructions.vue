<template>
    <div>
        <div class="instruction-title">Use sentences or paragraphs below to share your request.</div>
        <p class="instruction-p">
            When you
            press enter, we'll create a new line for you. After you're done, we'll take each line
            and create a request checklist for your designer.
        </p>
        <div id="next_detail_task" v-for="(ins, key) in instructions" :key="key">
            <el-alert type="error" style="margin-bottom: 8px;" v-if="ins.error">
                {{"To add a new line you have to fill 5 letters at least"}}</el-alert>
            <div class="detail_task_form tw-flex tw-items-start">
                <el-checkbox 
                style="padding-top: 7px;"
                        disabled
                    ></el-checkbox>
                <div class="detail_task_field el-textarea tw-px-2"
                    style="margin: 0px -9.34375px 0px 0px; height: 60px; width: 100%;">
                    <textarea autocomplete="off" placeholder="Enter request instructions" v-model="ins.text"
                        @keydown.enter.exact.prevent @blur="removeEmptyString(ins, $event)"
                        @keyup.enter.exact="instructionAdd(ins)" @keydown.enter.shift.exact="newline(ins.text)"
                        @keydown.delete.exact="instructionDelete(ins, $event)" class="el-textarea__inner"
                        spellcheck="false" style="min-height: 32px;" :ref="`instruction${key}`"></textarea>
                </div>
            </div>
        </div>
        <div class="add_new_detail_line" @click="newInstruction()">
            <div class="add_new_detail_task_lines form-control">Press Enter for another line</div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['project', 'project_id', 'instructions'],
        data() {
            return {}
        },
        methods: {
            removeEmptyString(ins, e) {
                //
            },
            instructionDelete(value, e) {
                self = this;
                if (this.instructions.length > 1) {
                    if (value.text.length == 0) {
                        let index = self.instructions.indexOf(value);
                        self.instructions.splice(index, 1);
                        e.preventDefault();
                        self.$nextTick(() => {
                            self.$refs[`instruction${index - 1}`][0].focus();
                        });
                    }
                }
            },
            instructionAdd(value) {
                self = this;

                if (value.text.length > 4) {
                    const data = {
                        project_id: this.project_id,
                        instructions: this.instructions
                    };
                    value.error = "";
                    axios
                        .post("/api/request/instruction", data)
                        .then(response => {
                            if (response.data.status == 1) {
                                toastr["success"](response.data.message, "Success");
                                let ins = this.instructions.push({
                                    text: "",
                                    error: ""
                                });
                                this.$nextTick(() => {
                                    self.$refs[`instruction${ins - 1}`][0].focus();
                                });
                            } else {
                                toastr["error"](response.data.errors[0], "Error");
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                } else {
                    return (value.error =
                        "To add a new line you have to fill 5 letters at least");
                }
            },
            newInstruction() {
                self = this;
                let inst = this.instructions[this.instructions.length - 1];
                if (inst.text && inst.text.length > 4) {
                    inst.error = "";
                    let ins = this.instructions.push({
                        text: "",
                        error: ""
                    });
                    this.$nextTick(() => {
                        self.$refs[`instruction${ins - 1}`][0].focus();
                    });
                } else {
                    return (inst.error =
                        "To add a new line you have to fill 5 letters at least");
                }
            },
            newline(value) {
                value = `${value}\n`;
            },
        },
        created() {
            if (
                this.project.project_assets &&
                this.project.project_assets.length > 0
            ) {
                this.uploadAssets = true;
                this.fileList = this.project.project_assets;
            }
            if (this.project.creative_brief != null) {
                this.project.creative_brief.length > 0 && (this.includeText = true);
            }
        }
    }
</script>
<style lang="scss" scoped>
    $white: #fafafa;
    $black: #545c64;
    $red: #ef5b5b;
    $green: #80b4a0;
    $grey: #c0c4cc;
    $border-color: rgba(0, 0, 0, 0.1);
    $box-shadow: 0 2px 12px 0 $border-color;

    // instruction 
    .instruction-title {
        margin-bottom: 4px;
        font-weight: 600;
    }
    .instruction-p {
        margin-bottom: 24px;
    }

    .detail_task_field {
        border: none !important;
        border-bottom: 1px solid #ccc !important;
    }

    .add_new_detail_task_lines {
        height: 52px;
        color: #a5a5a5;
        border: none !important;
        border-radius: 0;
        border-bottom: 1px dotted #d7d7d7 !important;
    }

    .add_new_detail_line {
        cursor: pointer;
    }
</style>

<style lang="scss">
    .detail_task_field {
        .el-textarea__inner {
            border: none !important;
            // border-bottom: 1px solid #ccc !important;
            padding: 5px 12px;
            border-radius: 0;
        }

        .el-textarea__inner:focus {
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),
                0 0 8px rgba(57, 202, 154, 0.6);
            border-color: #39ca86;
        }
    }
</style>