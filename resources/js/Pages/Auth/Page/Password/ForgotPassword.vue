<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div
            class="text-zinc-800 text-2xl font-bold uppercase leading-[28.80px] text-center"
        >
            {{ $t("auth-page.forgot-password-page.title") }}
        </div>
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 overflow-hidden">
            <el-form
                ref="form"
                :model="formData"
                :rules="rules"
                label-position="top"
                @keypress.enter="doSubmit"
            >
                <el-form-item
                    :label="$t('input.common.email')"
                    prop="email"
                    :inline-message="hasError('email')"
                    :error="getError('email')"
                >
                    <el-input
                        v-model="formData.email"
                        type="email"
                        size="large"
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
                    {{ $t("button.send") }}
                </el-button>
            </div>
        </div>
        <div class="h-[21px] justify-start items-center inline-flex mt-[21px]">
            <Link
                class="text-center text-zinc-800 text-sm font-bold leading-[21px] cursor-pointer underline"
                :href="this.appRoute('admin.login.form')"
            >
                {{ $t("auth-page.return-to-login") }}
            </Link>
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
        overTimeMail: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            formData: {
                email: null,
            },
            rules: {
                email: [
                    {
                        required: true,
                        message: this.$t('validate.required'),
                        trigger: ["blur","change"],
                    },
                ],
            },
            loadingForm: false,
        };
    },
    created() {
        if (this.overTimeMail === true) {
            this.formData.email = this.appRoute()?.params?.email;
            this.$message({
                message: "このパスワードリセットトークンは無効です。",
                type: "error",
            });
        }
    },
    methods: {
        async submit() {
            this.loadingForm = true;
            const { data, status } = await axios.post(
                this.appRoute("admin.api.send-mail-reset-password"),
                this.formData
            );
            console.log(data);
            this.$message({ message: data?.message, type: "success" });
            this.loadingForm = false;
            this.$inertia.visit(
                this.appRoute("admin.form-confirm-forgot-password", {
                    email: this.formData.email,
                })
            );
        },
    },
};
</script>
