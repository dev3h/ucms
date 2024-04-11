<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div
            class="text-zinc-800 text-2xl font-bold font-['Meiryo'] uppercase leading-[28.80px] text-center mb-[17px]"
        >
            新しいパスワードを変更する
        </div>
        <div class="w-full sm:max-w-md px-6 py-4 overflow-hidden">
            <div class="mt-4 form-change-password">
                <el-form
                    ref="form"
                    :model="formData"
                    :rules="rules"
                    label-position="top"
                    @keypress.enter="doSubmit"
                >
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
                <div class="text-center">
                    <el-button
                        type="primary"
                        :loading="loadingForm"
                        class="w-full mt-3 btn-gradient"
                        size="large"
                        @click.prevent="doSubmit"
                    >
                        申請する
                    </el-button>
                </div>
            </div>
        </div>
        <div class="h-[21px] justify-start items-center inline-flex mt-[21px]">
            <Link
                class="text-center text-zinc-800 text-sm font-bold font-['Meiryo'] leading-[21px] cursor-pointer underline"
                :href="this.appRoute('admin.login.form')"
            >
                ログイン画面に戻る
            </Link>
        </div>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios";

export default {
    name: "ResetPassword",
    mixins: [form],
    props: {
        token: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            formData: {
                email: this.appRoute()?.params?.email,
                password: null,
                password_confirmation: null,
                token: this.token,
            },
            rules: {
                email: [
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
            loadingForm: false,
        };
    },
    methods: {
        async submit() {
            this.loadingForm = true;
            const { data, status } = await axios.post(
                this.appRoute("admin.api.reset-password.update"),
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
