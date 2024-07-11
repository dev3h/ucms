<template>
    <ActionSection>
        <template #content>
            <h3 v-if="twoFactorEnabled && ! confirming" class="text-lg font-medium">
                {{$t("my-page.2fa.2faEnabled")}}
            </h3>

            <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-medium">
                {{$t("my-page.2fa.complete2faSetup")}}
            </h3>

            <h3 v-else class="text-lg font-medium">
                {{$t("my-page.2fa.2faNotEnabled")}}
            </h3>

            <div class="mt-3 max-w-xl text-sm">
                <p>
                    {{$t("my-page.2fa.2faPrompt")}}
                </p>
            </div>

            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm">
                        <p v-if="confirming" class="font-semibold">
                            {{$t("my-page.2fa.finalize2fa")}}
                        </p>

                        <p v-else>
                            {{$t("my-page.2fa.2faActive")}}
                        </p>
                    </div>

                    <div class="mt-4 p-2 inline-block bg-white" v-html="qrCode" />

                    <div v-if="setupKey" class="mt-4 max-w-xl text-sm">
                        <p class="font-semibold">
                           {{$t('my-page.2fa.setup-key')}}: <span v-html="setupKey"></span>
                        </p>
                    </div>

                    <div v-if="confirming" class="mt-4">
                        <InputLabel for="code" value="Code" isRequired="true"  />

                        <TextInput
                            id="code"
                            v-model="confirmationForm.code"
                            type="text"
                            name="code"
                            class="block mt-1 w-1/2"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            @keyup.enter="confirmTwoFactorAuthentication"
                        />

                        <InputError :message="confirmationForm.errors.code" class="mt-2" />
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0 && ! confirming">
                    <div class="mt-4 max-w-xl text-sm">
                        <p class="font-semibold">
                            {{$t("my-page.2fa.saveRecoveryCodes")}}
                        </p>
                    </div>

                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 dark:bg-gray-900 dark:text-gray-100 rounded-lg">
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="! twoFactorEnabled">
                    <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                        <el-button type="primary" :class="{ 'opacity-25': enabling }"
                                   :disabled="enabling">{{$t("button.enable")}}</el-button>
                    </ConfirmsPassword>
                </div>

                <div v-else>
                    <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                        <el-button v-if="confirming" type="primary" :class="{ 'opacity-25': enabling }"
                                   :disabled="enabling">{{$t("button.confirm")}}</el-button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <el-button v-if="recoveryCodes.length > 0 && ! confirming">{{$t("button.re-recovery-code")}}</el-button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <el-button v-if="recoveryCodes.length === 0 && ! confirming" type="primary">{{$t("button.show-recovery-code")}}</el-button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <el-button v-if="confirming" class="ml-2" :class="{ 'opacity-25': disabling }"
                                   :disabled="disabling">{{$t("button.disable")}}</el-button>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <el-button v-if="!confirming" type="danger" class="ml-2" :class="{ 'opacity-25': disabling }"
                                   :disabled="disabling">{{$t("button.disable")}}</el-button>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>

<script setup>
import {ref, computed, watch, onBeforeMount} from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionSection from '@/Components/Page/ActionSection.vue';
import ConfirmsPassword from '@/Components/Page/ConfirmsPassword.vue';
import InputError from '@/Components/Page/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Page/TextInput.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => ! enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (! twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};

onBeforeMount(() => {
    if(page?.props?.auth?.user?.two_factor_enabled && !page?.props?.auth?.user?.two_factor_confirmed_at) {
        disableTwoFactorAuthentication();
    }
})

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = true;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
}

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};
</script>
