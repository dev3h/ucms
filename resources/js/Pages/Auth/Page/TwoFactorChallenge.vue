<template>
    <Head title="Two-factor Confirmation" />

    <AuthenticationCard>
        <template #logo>
            <img src="/images/logo.svg" alt="logo" />
        </template>

        <div class="mb-4 text-sm text-center">
            <template v-if="! recovery">
                {{$t('auth-page.2fa-challenge-page.title')}}
            </template>

            <template v-else>
                {{$t('auth-page.2fa-challenge-page.title-recovery')}}
            </template>
        </div>

        <form @submit.prevent="submit">
            <div v-if="! recovery">
                <InputLabel for="code" :value="$t('column.common.code')" />
                <TextInput
                    id="code"
                    ref="codeInput"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full"
                    autofocus
                    autocomplete="one-time-code"
                />
                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div v-else>
                <InputLabel for="recovery_code" :value="$t('input.recovery-code')" />
                <TextInput
                    id="recovery_code"
                    ref="recoveryCodeInput"
                    v-model="form.recovery_code"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError class="mt-2" :message="form.errors.recovery_code" />
            </div>

            <div class="flex flex-col justify-center mt-9 gap-4">
                <el-button type="primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">{{$t('button.verify')}}</el-button>
                <el-button class="text-sm text-gray-600 hover:text-gray-900 cursor-pointer !ml-0" @click.prevent="toggleRecovery">
                    <template v-if="! recovery">
                        {{$t('button.recovery-code')}}
                    </template>

                    <template v-else>
                        {{$t('button.auth-code')}}
                    </template>
                </el-button>

            </div>
        </form>
    </AuthenticationCard>
</template>

<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/Page/AuthenticationCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Page/TextInput.vue';
import PrimaryButton from "@/Components/Page/PrimaryButton.vue";

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>
