<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div class="mb-[30px]">
            <img :src="'/images/reset_pass.svg'" alt="" />
        </div>
        <div class="w-full sm:max-w-md px-6 py-2 overflow-hidden">
            <p class="text-[20px] font-bold py-3 w-full text-center">
                パスワード変更
            </p>
            <div class="mt-4 form-change-password">
                <el-form
                    ref="form"
                    :model="formData"
                    :rules="rules"
                    label-position="top"
                    @keypress.enter="doSubmit"
                >
                    <el-form-item
                        label="初期パスワード"
                        prop="old_password"
                        :inline-message="hasError('old_password')"
                        :error="getError('old_password')"
                    >
                        <el-input
                            v-model="formData.old_password"
                            type="password"
                            size="large"
                            show-password
                            clearable
                        />
                    </el-form-item>
                    <el-form-item
                        label="新しいパスワード"
                        prop="password"
                        :inline-message="hasError('password')"
                        :error="getError('password')"
                    >
                        <el-input
                            v-model="formData.password"
                            type="password"
                            size="large"
                            show-password
                            clearable
                        />
                    </el-form-item>
                    <el-form-item
                        label="新しいパスワード (再入力)"
                        prop="password_confirmation"
                        :inline-message="hasError('password_confirmation')"
                        :error="getError('password_confirmation')"
                    >
                        <el-input
                            v-model="formData.password_confirmation"
                            type="password"
                            size="large"
                            show-password
                            clearable
                        />
                    </el-form-item>
                </el-form>
                <div>
                    <el-button
                        type="primary"
                        :loading="loading"
                        class="w-full mt-3 btn-gradient"
                        size="large"
                        @click.prevent="doSubmit"
                    >
                        変更する
                    </el-button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios";

export default {
    name: "Login",
    mixins: [form],
    props: {
        user: Object,
    },
    data() {
        return {
            formData: {
                email: this.user?.email,
                token: this.appRoute()?.params?.token,
                old_password: null,
                password: null,
                password_confirmation: null,
            },
            rules: {
                old_password: [
                    {
                        required: true,
                        message: "この項目は必須です。",
                        trigger: ["change", "blur"],
                    },
                ],
                password: [
                    {
                        required: true,
                        message: "この項目は必須です。",
                        trigger: ["change", "blur"],
                    },
                ],
                password_confirmation: [
                    {
                        required: true,
                        message: "この項目は必須です。",
                        trigger: ["change", "blur"],
                    },
                ],
            },
            loading: false,
        };
    },
    methods: {
        async submit() {
            this.loadingForm = true;
            const { data, status } = await axios.post(
                this.appRoute("admin.api.password-first.change"),
                this.formData
            );
            this.$message({ message: data?.message, type: "success" });
            this.loadingForm = false;
            this.$inertia.visit(this.appRoute("admin.login.form"));
        },
    },
};
</script>
<style scoped>
:deep(.form-change-password .el-form-item) {
    margin-bottom: 1.7rem !important;
}
</style>
