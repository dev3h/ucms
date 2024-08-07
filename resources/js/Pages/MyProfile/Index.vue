<template>
    <AdminLayout>
        <div class="w-full h-full bg-white grid lg:grid-cols-2 grid-col gap-5 p-8">
            <!-- Profile -->
            <el-card>
                <template #header>
                    <div class="text-[24px] font-bold">{{$t('my-page.info')}}</div>
                </template>
                <div class="px-4 w-full">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="flex flex-col gap-2">
                            <span>{{$t('column.common.name')}}:</span>
                            <span>{{user?.name}}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span>{{$t('input.common.email')}}: </span>
                            <span>{{user?.email}}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            <span>{{$t('sidebar.role')}}:</span>
                            <span>{{$page.props.auth.role}}</span>
                        </div>
                    </div>
                </div>
            </el-card>
            <!-- Update password -->
            <el-card>
                <template #header>
                    <div class="text-[24px] font-bold">{{$t('my-page.change-password')}}</div>
                </template>
                <div class="w-full flex justify-center">
                    <div class="px-4 w-full">
                        <div>
                            <el-form ref="form" :model="formData" :rules="rules" label-position="top">
                                <el-form-item
                                    :label="$t('input.common.current-password')" prop="current_password"
                                    :error="getError('current_password')"
                                    :inline-message="hasError('current_password')">
                                    <el-input
                                        v-model="formData.current_password" autocomplete="new-password"
                                        :size="'large'" show-password placeholder="" clearable/>
                                </el-form-item>
                                <el-form-item
                                    class="mt-8"
                                    :label="$t('input.common.new-password')" prop="password" :error="getError('password')"
                                    :inline-message="hasError('password')">
                                    <el-input
                                        v-model="formData.password" autocomplete="new-password" :size="'large'"
                                        show-password placeholder="" clearable/>
                                </el-form-item>
                                <el-form-item
                                    class="mt-8"
                                    :label="$t('input.common.confirm-new-password')" prop="password_confirmation"
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
                                    @click="doSubmit">{{$t('button.update')}}</el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>
            <!-- Integration account -->
            <el-card>
                <template #header>
                    <h2 class="uppercase font-bold">{{$t('my-page.link-account')}}</h2>
                </template>
                <div>
                    <div>
                        <h3 class="text-lg mb-2">Google</h3>
                        <div class="flex flex-wrap items-center gap-2">
                            <img src="/images/logo_google.png" alt="" width="30" height="30" class="object-cover">
                           <div>
                                <span v-if="isLinked('google')">
                                    {{$t('button.link')}} Email: {{ emailLink('google') }}
                                </span>
                               <span v-else>
                                {{$t('button.not-link')}}
                                </span>
                           </div>
                            <div>
                                <el-button type="danger" v-if="isLinked('google')" @click="handleUnlinkIntegrationSocialite('google')">{{$t('button.unlink')}}</el-button>
                                <a :href="route('admin.integration.socialite.redirect', 'google')" v-else>
                                    <el-button type="primary">
                                        {{$t('button.link')}}
                                    </el-button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>
            <!-- 2FA -->
            <el-card>
                <template #header>
                    <h2 class="uppercase font-bold">{{$t('my-page.2fa.title')}}</h2>
                </template>
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
