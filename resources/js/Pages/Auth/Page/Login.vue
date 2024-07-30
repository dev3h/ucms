<template>
    <div class="flex items-center min-h-screen bg-grayF5 bg-gray-50">
        <el-card class="!max-w-[500px] w-full py-9 mx-auto">
            <div class="w-full flex flex-col">
                <div class="text-blueDark rounded-lg">
                    <div class="relative">
                        <div class="logo flex justify-center mb-6 px-5">
                            <img src="/images/logo.svg" alt="logo" class="h-[100px]"/>
                        </div>
                    </div>
                    <div class="px-5">
                        <div
                            class="text-zinc-800 text-2xl font-bold uppercase leading-[28.80px] text-center mb-9"
                        >
                            {{ loginTitle }}
                        </div>
                        <el-form
                            ref="form"
                            :model="formData"
                            :rules="rules"
                            label-position="top"
                            @keypress.enter.prevent="doSubmit"
                        >
                            <el-form-item
                                :label="$t('input.common.email')"
                                prop="email"
                                :inline-message="hasError('email')"
                                :error="getError('email')"
                            >
                                <el-input
                                    v-model="formData.email"
                                    size="large"
                                    clearable
                                />
                            </el-form-item>
                            <el-form-item
                                :label="$t('input.common.password')"
                                prop="password"
                                :inline-message="hasError('password')"
                                :error="getError('password')"
                            >
                                <el-input
                                    v-model="formData.password"
                                    size="large"
                                    type="password"
                                    show-password
                                    clearable
                                />
                            </el-form-item>
                        </el-form>

                        <div class="text-center">
                            <el-button
                                type="primary"
                                :loading="loadingForm"
                                class="w-full mt-3 btn-gradient"
                                size="large"
                                @click.prevent="doSubmit"
                            >
                                {{ $t('button.login') }}
                            </el-button>
                            <div class="mt-4">
                                <div
                                    class="text-sm underline cursor-pointer"
                                    @click="openForgotPasswordForm"
                                >
                                    {{ $t('auth-page.click-forgot-password') }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-10" v-if="pathSub?.[1] === 'admin'">
                            <h2 class="uppercase text-center font-bold text-3xl">{{ $t('auth-page.or') }}</h2>
                            <div class="border mt-4">
                                <a :href="route('admin.socialite.redirect', 'google')"
                                   class="w-full flex h-10 items-center justify-center gap-2 px-4 py-3 hover:bg-gray-50">
                                    <img src="/images/logo_google.png" alt="" class="h-8 object-cover">
                                    <span>{{$t('auth-page.sign-in-with-google')}}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </el-card>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios.js";
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";

export default {
    name: "Login",
    mixins: [form],
    data() {
        return {
            formData: {
                email: null,
                password: null,
                remember: false,
                platform: 'web'
            },
            rules: {
                email: baseRuleValidate(this.$t),
                password: baseRuleValidate(this.$t)
            },
            loadingForm: false,
            errors: null,
            pathSub: window.location.pathname.split('/'),
        };
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
    computed: {
      loginTitle() {
          return this.pathSub[1] === 'admin' ? this.$t('auth-page.admin-login-title') : this.$t('auth-page.login-title');
      }
    },
    methods: {
        async submit() {
            this.loadingForm = true;
            const response = await axios.post(
                this.appRoute("admin.api.login.handle"),
                this.formData
            );
            if(response?.data?.data?.firstLogin) {
                this.$inertia.visit(this.appRoute("admin.first-login.form"));
            }
            else if(response?.data?.data?.twoFactor) {
                this.$inertia.visit(this.appRoute("admin.two-factor.form"));
            } else {
                this.$inertia.visit(response?.data?.data);
            }
            this.loadingForm = false;
        },
        openForgotPasswordForm() {
            this.$inertia.visit(this.appRoute("admin.forgot-password.form"));
        },
    },
};
</script>
<style scoped>
:deep(.form-login .el-form-item) {
    margin-bottom: 1.7rem !important;
}
</style>
