<template>
    <AdminLayout>
        <div class="w-full h-full bg-white grid lg:grid-cols-2 grid-col gap-5 px-16 py-8">
            <!-- Profile -->
            <el-card>
                <div class="px-4 w-full">
                    <div>
                        <div class="flex flex-col gap-2">
                            <span>Name</span>
                            <span>{{user?.name}}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span>Email</span>
                            <span>{{user?.email}}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span>Role</span>
                            <span>{{$page.props.auth.role}}</span>
                        </div>
                    </div>
                </div>
            </el-card>
            <!-- Update password -->
            <el-card>
                <div class="w-full flex justify-center">
                    <div class="px-4 w-full">
                        <div class="text-[24px] font-bold mb-8">Change Password</div>
                        <div>
                            <el-form ref="form" :model="formData" :rules="rules" label-position="top">
                                <el-form-item
                                    label="Current Password" prop="current_password"
                                    :error="getError('current_password')"
                                    :inline-message="hasError('current_password')">
                                    <el-input
                                        v-model="formData.current_password" autocomplete="new-password"
                                        :size="'large'" show-password placeholder="" clearable/>
                                </el-form-item>
                                <el-form-item
                                    class="mt-8"
                                    label="New Password" prop="password" :error="getError('password')"
                                    :inline-message="hasError('password')">
                                    <el-input
                                        v-model="formData.password" autocomplete="new-password" :size="'large'"
                                        show-password placeholder="" clearable/>
                                </el-form-item>
                                <el-form-item
                                    class="mt-8"
                                    label="New password (confirmation)" prop="password_confirmation"
                                    :error="getError('password_confirmation')"
                                    :inline-message="hasError('password_confirmation')">
                                    <el-input
                                        v-model="formData.password_confirmation" autocomplete="new-password"
                                        :size="'large'" show-password placeholder="" clearable/>
                                </el-form-item>
                            </el-form>
                            <div class="flex justify-center mt-8">
                                <el-button
                                    type='primary' :loading="loadingForm" class="!w-40" size="large"
                                    @click="doSubmit">Update</el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>
            <!-- Integration account -->
            <el-card>
                <h2 class="uppercase font-bold mb-4">Linked Accounts</h2>
                <div>
                    <div>
                        <h3 class="text-lg mb-2">Google</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            <img src="/images/logo_google.png" alt="" width="30" height="30" class="object-cover">
                           <div>
                                <span v-if="isLinked('google')">
                                    Linked Email: {{ emailLink('google') }}
                                </span>
                               <span v-else>
                                Not Link
                                </span>
                           </div>
                            <div>
                                <el-button type="danger" v-if="isLinked('google')" @click="handleUnlinkIntegrationSocialite('google')">Unlink</el-button>
                                <a :href="route('admin.integration.socialite.redirect', 'google')" v-else>
                                    <el-button type="primary">
                                        Link
                                    </el-button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>
            <!-- 2FA -->
            <el-card>
                <h2 class="uppercase font-bold mb-4">Two Factor Authentication</h2>
                <TwoFactorAuthenticationForm class="h-full" />
            </el-card>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import axios from '@/Plugins/axios.js';
import form from "@/Mixins/form.js";
import TwoFactorAuthenticationForm from "./TwoFactorAuthenticationForm.vue";

export default {
    components: {TwoFactorAuthenticationForm, AdminLayout},
    mixins: [form],
    data() {
        return {
            linkedAccounts: [],
            loadingForm: false,
            formData: {
                current_password: '',
                password: '',
                password_confirmation: '',
            },
            rules: {
                current_password: [
                    { required: true, message: 'This field is required', trigger: ['blur', 'change'] },
                ],
                password: [
                    { required: true, message: 'This field is required', trigger: ['blur', 'change'] },
                ],
                password_confirmation: [
                    { required: true, message: 'This field is required', trigger: ['blur', 'change'] },
                ],
            },
        }
    },
    watch: {
        '$page.props.errors': {
            immediate: true,
            handler(value) {
                if (value && Object.keys(value).length > 0) {
                    this.$message.error(Object.values(value).join(', '));
                }
            }
        }
    },
    created() {
        this.getAllLinkedAccounts();
    },
    computed: {
        user() {
            return this.$page.props.auth.user;
        }
    },
    methods: {
        async getAllLinkedAccounts() {
            try {
                const response = await axios.get(this.appRoute('admin.api.get-all-integration-socialite'));
                this.linkedAccounts = response?.data?.data;
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            }
        },
        isLinked(provider) {
            const findIndexProvider = this.linkedAccounts?.findIndex(item => item?.provider_type === provider);
            return findIndexProvider !== -1;
        },
        emailLink(provider) {
            const findIndexProvider = this.linkedAccounts?.findIndex(item => item?.provider_type === provider);
            return this.linkedAccounts[findIndexProvider]?.email;
        },
        async handleUnlinkIntegrationSocialite(provider) {
            try {
                const findIndexProvider = this.linkedAccounts?.findIndex(item => item?.provider_type === provider);
                const response = await axios.delete(this.appRoute('admin.api.unlink-integration-socialite', this.linkedAccounts[findIndexProvider]?.provider_id));
                if(response) {
                    this.$message.success(response?.data?.message);
                    await this.getAllLinkedAccounts();
                }
            } catch (err) {
                console.log(err);
                this.$message.error(err?.response?.data?.message);
            }
        },
        async submit() {
            try {
                this.loadingForm = true;
                const response = await axios.post(this.appRoute('admin.api.change-password'), this.formData);
                if (response) {
                    this.$message.success(response?.data?.message);
                    this.$refs.form.resetFields();
                }
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            } finally {
                this.loadingForm = false;
            }
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
