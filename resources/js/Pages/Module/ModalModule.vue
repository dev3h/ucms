<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal" :fullscreen="fullscreen">
            <template #header>
                <DialogHeader :title="formType === 'add' ? $t('form.add') : $t('form.edit')" :isFullscreen="fullscreen" @toggleFullscreen="handleToggleFullScreen" />
            </template>
            <div class="w-full">
                <el-form class="w-full lg:flex flex-wrap gap-2" ref="form" :model="formData" :rules="rules"
                    label-position="top">

                    <div class="flex-1">
                        <el-form-item :label="$t('column.common.name')" class="title--bold" prop="name" :error="getError('name')"
                            :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" clearable />
                        </el-form-item>
                    </div>
                    <div class="flex-1">
                        <el-form-item :label="$t('column.common.code')" class="title--bold" prop="name" :error="getError('code')"
                                      :inline-message="hasError('code')">
                            <el-input size="large" v-model="formData.code" clearable />
                        </el-form-item>
                    </div>
                </el-form>
            </div>
            <div class="w-full my-[15px] flex justify-center items-center">
                <el-button type="danger" size="large" @click="closeModal">{{$t('button.cancel')}}</el-button>
                <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">{{$t('button.save')}}</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import form from "@/Mixins/form.js";
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";
import DialogHeader from "@/Components/Dialog/DialogHeader.vue";
export default {
    components: {DialogHeader},
    mixins: [form],
    props: {
        redirectRoute: {
            type: String,
            default: null,
        },
    },
    emits: ['add-success', 'update-success'],
    data() {
        return {
            formType: 'add',
            isShowModal: false,
            current_id: null,
            systems: [],
            formData: {
                id: null,
                name: null,
                code: null,
            },
            rules: {
                name: baseRuleValidate(this.$t),
                code: baseRuleValidate(this.$t),
            },
            fullscreen: false,
            loadingForm: false
        }
    },
    async created() {
        await this.getAllSubSystem()
    },
    methods: {
        async open(id) {
            if (id) {
                this.current_id = id
                this.formType = 'edit'
                await this.fetchData()
            }
            this.isShowModal = true
        },
        closeModal() {
            this.isShowModal = false
            this.current_id = null
            this.formData = {
                id: null,
                name: null,
                code: null,
            }
            this.$refs.form.resetFields()
            this.formType = 'add'
        },
        async submit() {
            this.loadingForm = true

            const { action, method } = this.prepareSubmit()
            const { status, data } = await axios[method](action, this.formData)
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            })
            this.loadingForm = false
            this.isShowModal = false
            this.$inertia.visit(this.redirectRoute)
        },
        async fetchData()
        {
            if (this.formType === 'edit') {
                this.loadingForm = true
                const { data } = await axios.get(this.appRoute('admin.api.module.show', this.current_id))
                this.formData = {
                    ...data?.data,
                }
                this.loadingForm = false
            }
        },
        async getAllSubSystem() {
            try {
                const response = await axios.get(this.appRoute('admin.api.subsystem.index'))
                this.subsystems = response?.data?.data
            } catch (error) {
                this.$message.error(error?.response?.data?.message)
            }
        },
        prepareSubmit() {
          let action = null;
          let method = 'post';
            if (this.formType === 'add') {
                action = this.appRoute('admin.api.module.store');
            } else {
                action = this.appRoute('admin.api.module.update', this.current_id);
                method = 'put';
            }
            return {action, method};
        },
        handleToggleFullScreen() {
            this.fullscreen = !this.fullscreen
        }
    },
}
</script>
