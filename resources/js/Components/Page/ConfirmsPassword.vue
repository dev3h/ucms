<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

         <el-dialog v-model="confirmingPassword" width="500" :close-on-click-modal="false" class="dialog-base"
                    :title="title">
            {{ content }}

            <div class="mt-4">
                <el-input ref="passwordInput" v-model="form.password" type="password"
                          show-password
                          clearable placeholder="Password" @keyup.enter="confirmPassword" />

                <InputError :message="form.error" class="mt-2" />
            </div>
            <template #footer>
                <div class="w-full flex justify-center items-center gap-3 bg-[#F5F5F5]">
                    <el-button type="info" class="!h-8" @click="closeModal()" >Cancel</el-button>
                    <el-button type="primary" class="!h-8" :disabled="form.processing"
                               @click="confirmPassword" >{{ button }}</el-button>
                </div>
            </template>
        </el-dialog>
    </span>
</template>

<script>
import axios from 'axios';
import DialogModal from './DialogModal.vue';
import InputError from './InputError.vue';
import TextInput from './TextInput.vue';

export default {
    components: {
        DialogModal,
        InputError,
        TextInput,
    },
    props: {
        title: {
            type: String,
            default: 'Confirm Password',
        },
        content: {
            type: String,
            default: 'For your security, please confirm your password to continue.',
        },
        button: {
            type: String,
            default: 'Confirm',
        },
    },
    data() {
        return {
            confirmingPassword: false,
            form: {
                password: '',
                error: '',
                processing: false,
            },
        };
    },
    methods: {
        startConfirmingPassword() {
            axios.get(route('password.confirmation')).then(response => {
                if (response.data.confirmed) {
                    this.$emit('confirmed');
                } else {
                    this.confirmingPassword = true;

                    setTimeout(() => this.$refs.passwordInput.focus(), 250);
                }
            });
        },
        confirmPassword() {
            this.form.processing = true;

            axios.post(route('password.confirm'), {
                password: this.form.password,
            }).then(() => {
                this.form.processing = false;

                this.closeModal();
                this.$nextTick().then(() => this.$emit('confirmed'));

            }).catch(error => {
                this.form.processing = false;
                this.form.error = error.response.data.errors.password[0];
                this.$refs.passwordInput.focus();
            });
        },
        closeModal() {
            this.confirmingPassword = false;
            this.form.password = '';
            this.form.error = '';
        },
    },
};
</script>

<!--<script setup>-->
<!--import { ref, reactive, nextTick } from 'vue';-->
<!--import DialogModal from './DialogModal.vue';-->
<!--import InputError from './InputError.vue';-->
<!--import PrimaryButton from './PrimaryButton.vue';-->
<!--import SecondaryButton from './SecondaryButton.vue';-->
<!--import TextInput from './TextInput.vue';-->

<!--const emit = defineEmits(['confirmed']);-->

<!--defineProps({-->
<!--    title: {-->
<!--        type: String,-->
<!--        default: 'Confirm Password',-->
<!--    },-->
<!--    content: {-->
<!--        type: String,-->
<!--        default: 'For your security, please confirm your password to continue.',-->
<!--    },-->
<!--    button: {-->
<!--        type: String,-->
<!--        default: 'Confirm',-->
<!--    },-->
<!--});-->

<!--const confirmingPassword = ref(false);-->

<!--const form = reactive({-->
<!--    password: '',-->
<!--    error: '',-->
<!--    processing: false,-->
<!--});-->

<!--const passwordInput = ref(null);-->

<!--const startConfirmingPassword = () => {-->
<!--    axios.get(route('password.confirmation')).then(response => {-->
<!--        if (response.data.confirmed) {-->
<!--            emit('confirmed');-->
<!--        } else {-->
<!--            confirmingPassword.value = true;-->

<!--            setTimeout(() => passwordInput.value.focus(), 250);-->
<!--        }-->
<!--    });-->
<!--};-->

<!--const confirmPassword = () => {-->
<!--    form.processing = true;-->

<!--    axios.post(route('password.confirm'), {-->
<!--        password: form.password,-->
<!--    }).then(() => {-->
<!--        form.processing = false;-->

<!--        closeModal();-->
<!--        nextTick().then(() => emit('confirmed'));-->

<!--    }).catch(error => {-->
<!--        form.processing = false;-->
<!--        form.error = error.response.data.errors.password[0];-->
<!--        passwordInput.value.focus();-->
<!--    });-->
<!--};-->

<!--const closeModal = () => {-->
<!--    confirmingPassword.value = false;-->
<!--    form.password = '';-->
<!--    form.error = '';-->
<!--};-->
<!--</script>-->


