<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div
            class="text-center text-zinc-800 text-2xl font-bold font-['Meiryo'] uppercase mb-8"
        >
            A confirmation email has been sent
        </div>
        <div
            class="text-center text-neutral-600 text-sm font-normal font-['Meiryo'] leading-[21px]"
            style="margin-bottom: 56px !important"
        >
            Sent to {{formData.email }}
            <p>Click the link in the email to complete your password change.</p>
            <p>If you do not receive the confirmation email or have lost it, please resend it using the link below.</p>
        </div>
        <div
            class="w-full sm:max-w-md mt-10 px-6 pb-4 overflow-hidden"
        >
            <el-button
                type="primary"
                :loading="loadingForm"
                class="w-full mt-3 btn-gradient"
                size="large"
                @click.prevent="submit"
            >
                Resend confirmation email
            </el-button>
        </div>
        <div class="h-[21px] justify-start items-center inline-flex mt-[21px]">
            <Link
                class="text-center text-zinc-800 text-sm font-bold font-['Meiryo'] leading-[21px] cursor-pointer underline"
                :href="this.appRoute('admin.forgot-password.form')"
            >
                Enter email address again
            </Link>
        </div>
    </div>
</template>
<script>
import form from "@/Mixins/form";
import axios from "@/Plugins/axios";

export default {
    name: "ConfirmForgotPassword",
    mixins: [form],
    data() {
        return {
            formData: {
                email: this.appRoute().params.email,
            },
            rules: {
                email: [
                    {
                        required: true,
                        message: "This field is required",
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
