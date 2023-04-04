<template>
	<div>
		<div class="tw-text-lg tw-font-semibold tw-text-gray-900">Update Profile Information</div>
		<el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" class="demo-ruleForm">
			<ProofloCard>
				<template #body>
					<div v-if="stateLoading" class="tw-text-center">
						<i class="tw-py-6 el-icon-loading"></i>
					</div>
					<div v-else>
						<!-- Profile Photo -->
						<div>
							<!-- Profile Photo File Input -->
							<input type="file" class="hidden" ref="photo" @change="updatePhotoPreview" />
							<label for="photo" class="tw-pb-2 tw-block">Photo</label>
							<el-avatar v-show="!photoPreview" :src="user.photo_url" fit="fill" :size="76"></el-avatar>
							<el-avatar v-show="photoPreview" :src="photoPreview" :size="76"></el-avatar>
                            <avatar-upload field="photo"
                                @crop-success="cropSuccess"
                                @crop-upload-success="cropUploadSuccess"
                                @crop-upload-fail="cropUploadFail"
                                v-model="show"
                                langType="en"
                                :width="300"
                                :height="300"
                                ref="uploader"
                                url="/api/profile/update/user"
                                :params="params"
                                :headers="headers"
                                :withCredentials="true"
                                img-format="png"></avatar-upload>


							<div class="tw-pb-6 tw-pt-2">
								<el-button
									type="primary"
									@click.native.prevent="selectNewPhoto"
									class="tw-btn"
									size="medium"
								>SELECT A NEW PHOTO</el-button>
								<el-popconfirm
									confirm-button-text="OK"
									v-if="user.photo_url && !user.photo_url.includes('gravatar')"
									cancel-button-text="No, Thanks"
									icon="el-icon-info"
									@confirm="deletePhoto"
									icon-color="red"
									title="Are you sure to delete this?"
								>
									<el-button
										type="danger"
										class="tw-btn"
										:loading="loading"
										slot="reference"
										size="medium"
									>REMOVE PHOTO</el-button>
								</el-popconfirm>
							</div>
						</div>

						<el-form-item prop="name">
							<label for="name" class="tw-pb-0 tw-mb-0 tw-block tw-mx-0">Name</label>
							<el-input type="text" id="name" name="Name" v-model="ruleForm.name" autocomplete="off"></el-input>
						</el-form-item>
						<el-form-item prop="email">
							<label for="email" class="tw-pb-0 tw-mb-0 tw-block tw-mx-0">E-mail Address</label>
							<el-input type="text" id="email" name="email" v-model="ruleForm.email" autocomplete="off"></el-input>
						</el-form-item>
					</div>
				</template>
				<template #footer>
					<el-button
						class="tw-btn"
						:loading="loading"
						type="primary"
						@click="update('ruleForm')"
					>Update Profile Information</el-button>
				</template>
			</ProofloCard>
		</el-form>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import AvatarUpload from 'vue-image-crop-upload/upload-2.vue';
export default {
        components: {
            AvatarUpload,
        },
		data() {
			return {
                photoPreview: null,
                show: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                params: {},
				photo: "",
				ruleForm: {
					name: "",
					email: ""
				},
				rules: {
					name: [{ required: true }],
					email: [{ type: "email", required: true, trigger: "change" }]
				},
				formData: {},
				loading: false
			};
		},
		watch: {
			user(val) {
				this.ruleForm.name = val.current_profile.name;
				this.ruleForm.email = val.email;
			}
		},
		computed: {
			...mapGetters(["user", "stateLoading"])
		},
		methods: {
			update(formName) {
				this.loading = true;
				this.formData = new FormData();
				this.formData.append("photo", this.photo);
				this.formData.append("name", this.ruleForm.name);
				this.formData.append("email", this.ruleForm.email);
				const config = {
					headers: {
						"content-type": "multipart/form-data"
					}
				};
				this.$refs[formName].validate(async valid => {
					if (valid) {
						let { data } = await axios.post(
							`/api/profile/update/user`,
							this.formData,
							config
						);
						toastr["success"](data.message, "Success");
						this.$store.commit("UPDATE_USER", data);
						this.loading = false;
					} else {
						this.loading = false;
						return false;
					}
				});
			},
			selectNewPhoto() {
				// this.$refs.photo.click();
                this.show = true

            },
            getCookie(cName) {
                const name = cName + "=";
                const cDecoded = decodeURIComponent(document.cookie); //to be careful
                const cArr = cDecoded.split('; ');
                let res;
                cArr.forEach(val => {
                    if (val.indexOf(name) === 0) res = val.substring(name.length);
                })
                return res
            },    
            cropSuccess(imgDataUrl, field){
                this.imgDataUrl = imgDataUrl;
                this.loading = true;
			},
			/**
			 * upload success
			 *
			 * [param] jsonData  server api return data, already json encode
			 * [param] field
			 */
            cropUploadSuccess(jsonData, field) {
                // Tostar success
                if (jsonData.status == 1) {
                    toastr["success"](jsonData.message, "Success");
                    this.$store.commit("UPDATE_USER", jsonData);
                    this.show = false;
                } else {
                    toastr["error"](jsonData.message, "Error");
                }
                this.$refs.uploader.setStep(1)
                this.loading = false;
			},
			/**
			 * upload fail
			 *
			 * [param] status    server api return error status, like 500
			 * [param] field
			 */
			cropUploadFail(status, field){
				console.log('-------- upload fail --------');
				console.log(status);
				console.log('field: ' + field);
			},
			updatePhotoPreview() {
				const reader = new FileReader();

				reader.onload = e => {
					this.photoPreview = e.target.result;
				};

				reader.readAsDataURL(this.$refs.photo.files[0]);
				this.photo = this.$refs.photo.files[0];
			},

			async deletePhoto() {
				this.loading = true;
				try {
					const { data } = await axios.delete(
						"/api/profile/delete/photo"
					);
					toastr["success"](data.message, "Success");
					this.$store.commit("UPDATE_USER", data);
                    this.photoPreview = null
					this.loading = false;
				} catch (error) {
					this.loading = false;
				}
			}
		},
		mounted() {
			this.ruleForm.name = this.user.name;
			this.ruleForm.email = this.user.email;
		},
        created() {
            this.headers['X-XSRF-TOKEN'] = this.getCookie('XSRF-TOKEN')
        }
	};
</script>

<style>
</style>