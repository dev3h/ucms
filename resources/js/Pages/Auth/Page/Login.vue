<template>
    <div class="flex mt-9 bg-grayF5">
        <div class="w-full flex flex-col">
            <div
                class="text-blueDark rounded-lg w-[400px] h-[500px] m-auto mb-20"
            >
                <div class="relative">
                    <div class="logo flex justify-center mb-6 px-5">
                        <img src="/images/logo.svg" alt="logo" />
                    </div>
                </div>
                <div class="px-5 form-login">
                    <div
                        class="text-zinc-800 text-2xl font-bold font-['Meiryo'] uppercase leading-[28.80px] text-center mb-[17px]"
                    >
                        Login
                    </div>
                    <el-form
                        ref="form"
                        :model="formData"
                        :rules="rules"
                        label-position="top"
                        @keypress.enter.prevent="doSubmit"
                    >
                        <el-form-item
                            label="Email"
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
                            label="Password"
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
                            login
                        </el-button>
                        <div class="mt-4">
                            <div
                                class="text-sm underline cursor-pointer"
                                @click="openForgotPasswordForm"
                            >
                                Click here if you have forgotten your password
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="w-[500px] mx-auto">
                <h2 class="uppercase text-center font-bold text-3xl">Login with SNS</h2>
                <div class="flex justify-center mt-4 h-10" >
                    <a :href="route('admin.socialite.redirect', 'google')" class="!w-[200px] flex h-full shadow-md items-center gap-2 px-4 py-3 hover:bg-gray-50">
                            <img src="/images/logo_google.png" alt="" class="h-full object-cover">
                        Google
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios.js";

export default {
    name: "Login",
    mixins: [form],
    data() {
        return {
            formData: {
                email: null,
                password: null,
                remember: false,
            },
            rules: {
                email: [
                    {
                        required: true,
                        message: "This field is required",
                        trigger: ['blur', 'change'],
                    },
                ],
                password: [
                    {
                        required: true,
                        message: "This field is required",
                        trigger: ['blur', 'change'],
                    },
                ],
            },
            loadingForm: false,
            errors: null
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
    methods: {
        async submit() {
            this.loadingForm = true;
            const response = await axios.post(
                this.appRoute("admin.api.login.handle"),
                this.formData
            );
            this.$inertia.visit(response?.data?.data);
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
