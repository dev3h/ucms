<template>
    <div class="flex h-screen bg-grayF5">
        <div class="w-full flex justify-center items-center flex-col">
            <div
                class="text-blueDark rounded-lg w-[400px] h-[500px] m-auto"
            >
                <div class="relative">
                    <div class="logo text-center mb-6 px-5">
                        <!--                        <img src="/images/logo.png" alt="logo" />-->
                        <h1>LOGO</h1>
                    </div>
                </div>
                <div class="px-5 form-login">
                    <div
                        class="text-zinc-800 text-2xl font-bold font-['Meiryo'] uppercase leading-[28.80px] text-center mb-[17px]"
                    >
                        ログイン
                    </div>
                    <el-form
                        ref="form"
                        :model="formData"
                        :rules="rules"
                        label-position="top"
                        @keypress.enter.prevent="doSubmit"
                    >
                        <el-form-item
                            label="ユーザー名"
                            prop="email"
                            :inline-message="hasError('email')"
                            :error="getError('email')"
                        >
                            <el-input
                                v-model="formData.email"
                                size="large"
                                placeholder="メールアドレスを入力"
                                clearable
                            />
                        </el-form-item>
                        <el-form-item
                            label="パスワード"
                            prop="password"
                            :inline-message="hasError('password')"
                            :error="getError('password')"
                        >
                            <el-input
                                v-model="formData.password"
                                size="large"
                                type="password"
                                show-password
                                placeholder="パスワードを入力"
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
                            ログイン
                        </el-button>
                        <div class="mt-4">
                            <div
                                class="text-sm underline cursor-pointer"
                                @click="openForgotPasswordForm"
                            >
                                パスワードをお忘れの場合はこちら
                            </div>
                        </div>
                    </div>
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
                        message: "この項目は必須です。",
                        trigger: "blur",
                    },
                ],
                password: [
                    {
                        required: true,
                        message: "この項目は必須です。",
                        trigger: "blur",
                    },
                ],
            },
            loadingForm: false,
        };
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
