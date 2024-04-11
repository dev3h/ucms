<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-grayF5"
    >
        <div
            class="text-center text-zinc-800 text-2xl font-bold font-['Meiryo'] uppercase"
            style="margin-bottom: 32px !important"
        >
            確認メールを送信しました
        </div>
        <div
            class="text-center text-neutral-600 text-sm font-normal font-['Meiryo'] leading-[21px]"
            style="margin-bottom: 56px !important"
        >
            {{
                formData.email
            }}から<br />パスワード変更用のリンクが送信されます。<br />新しいパスワードを設定してください。
        </div>
        <div
            class="bg-[#F0F2F5] w-[710px] h-[114px] bg-gray-100 flex-col justify-center items-center gap-2.5 inline-flex mb-4"
        >
            <div
                class="w-[292px] h-[22px] text-center text-neutral-800 text-sm font-bold font-['Meiryo'] leading-[16.80px]"
            >
                メールが届かない場合
            </div>
            <div
                class="w-[876px] text-center text-neutral-600 text-sm font-normal font-['Meiryo'] leading-[21px]"
            >
                ※当社からのメールをp受信できるようにドメイン指定受信で「cxcx.jp」を許可してください。<br />※メールドメインの「許可が完了したら「確認メールを再送信」ボタンからメールを再送信してください。
            </div>
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
                確認メールを再送信
            </el-button>
        </div>
        <div class="h-[21px] justify-start items-center inline-flex mt-[21px]">
            <Link
                class="text-center text-zinc-800 text-sm font-bold font-['Meiryo'] leading-[21px] cursor-pointer underline"
                :href="this.appRoute('admin.login.form')"
            >
                メールアドレスを再度入力
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
