<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div
            class="text-center text-zinc-800 text-2xl font-bold mb-5"
        >
            {{$t('auth-page.confirm-forgot-password-page.title', {email: formData.email})}}
        </div>
        <div
            class="text-center text-neutral-600 text-sm font-normal leading-[21px]"
        >
            <p>{{$t('auth-page.confirm-forgot-password-page.content')}}</p>
        </div>
        <div
            class="w-full sm:max-w-md mt-5 px-6 pb-4 overflow-hidden"
        >
            <el-button
                type="primary"
                :loading="loadingForm"
                class="w-full mt-3 btn-gradient"
                size="large"
                @click.prevent="submit"
            >
                {{$t('button.resend-email')}}
            </el-button>
        </div>
        <div class="h-[21px] justify-start items-center inline-flex mt-[21px]">
            <Link
                class="text-center text-zinc-800 text-sm font-bold leading-[21px] cursor-pointer underline"
                :href="this.appRoute('admin.forgot-password.form')"
            >
                {{$t('auth-page.confirm-forgot-password-page.enter-email-again')}}
            </Link>
        </div>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios";
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";

export default {
    name: "ConfirmForgotPassword",
    mixins: [form],
    data() {
        return {
            formData: {
                email: this.appRoute().params.email,
            },
            rules: {
                email: baseRuleValidate(this.$t)
            },
            loadingForm: false,
        };
    },
    methods: {
        async submit() {
            this.loadingForm = true;
            const { data } = await axios.post(
                this.appRoute("admin.api.send-mail-reset-password"),
                this.formData
            );
            this.$message({ message: data?.message, type: "success" });
            this.loadingForm = false;
        },
    },
};
</script>
<style>
.el-form-item__label {
    font-weight: 900 !important;
}
</style>
