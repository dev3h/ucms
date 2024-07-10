<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div class="mb-[30px]">
            <img :src="'/images/reset_pass.svg'" alt="" />
        </div>
        <div class="w-full sm:max-w-md px-6 py-2 overflow-hidden">
            <p class="text-[20px] font-bold py-3 w-full text-center">
                {{ $t("auth-page.change-password-first-page.title") }}
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
                        :label="$t('input.common.initial-password')"
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
                        :label="$t('input.common.new-password')"
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
                        :label="$t('input.common.confirm-new-password')"
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
                        {{ $t("button.update") }}
                    </el-button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios";
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";

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
                old_password: baseRuleValidate(this.$t),
                password: baseRuleValidate(this.$t),
                password_confirmation: baseRuleValidate(this.$t),
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
