<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal" :fullscreen="fullscreen">
            <template #header>
                <DialogHeader :title="formType === 'add' ? $t('form.add') : $t('form.edit')" :isFullscreen="fullscreen" @toggleFullscreen="handleToggleFullScreen" />
            </template>
            <div class="w-full">
                <el-form class="w-full flex flex-col gap-2" ref="form" :model="formData" :rules="rules"
                    label-position="top">

                    <div class="flex-1">
                        <el-form-item :label="$t('column.common.name')" class="title--bold" prop="name" :error="getError('name')"
                            :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" clearable />
                        </el-form-item>
                    </div>
                    <div class="flex-1 w-full">
                        <el-form-item :label="$t('column.common.code')" class="title--bold code" prop="code" :error="getError('code')"
                                      :inline-message="hasError('code')">
                        <div class="flex-1">
                            <h3 class="font-bold">System</h3>
                            <div>
                                <div v-for="system in codeTemplate" :key="system.id">
                                    <div class="flex items-center gap-1">
                                        <input type="radio" :value="system.code" v-model="systemCode" @change="handleChangeSystem(system.code)" />
                                        <label>{{ system.name }}</label>
                                    </div>
                                    <div v-if="systemCode && systemCode === system.code" class="ml-5">
                                        <h3 class="font-bold">Sub System</h3>
                                        <div v-for="subsystem in system.subsystems" :key="subsystem.id">
                                            <div class="flex items-center gap-1">
                                                <input type="radio" :value="subsystem.code" v-model="subsytemCode" @change="handleChangeSubSystem(subsystem.code)" />
                                                <label>{{ subsystem.name }}</label>
                                            </div>
                                            <div v-if="subsytemCode && subsytemCode === subsystem.code" class="ml-5">
                                                <h3 class="font-bold">Module</h3>
                                                <div v-for="module in subsystem.modules" :key="module.id">
                                                    <div class="flex items-center gap-1">
                                                        <input type="radio" :value="module.code" v-model="moduleCode" @change="handleChangeModule(module.code)" />
                                                        <label>{{ module.name }}</label>
                                                    </div>
                                                    <div v-if="moduleCode && moduleCode === module.code" class="ml-5">
                                                        <h3 class="font-bold">Action</h3>
                                                        <div v-for="action in module.actions" :key="action.id">
                                                            <div class="flex items-center gap-1">
                                                                <input type="radio" :value="action.code" v-model="actionCode" @change="handleChangeAction(action.code)"  />
                                                                <label>{{ action.name }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        </el-form-item>
                    </div>
                </el-form>

            </div>
            <template #footer>
                <div class="w-full flex justify-center items-center">
                    <el-button type="danger" size="large" @click="closeModal">{{$t('button.cancel')}}</el-button>
                    <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">{{$t('button.save')}}</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import form from "@/Mixins/form.js";
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
            formData: {
                id: null,
                name: null,
                code: null,
            },
            systemCode: null,
            subsytemCode: null,
            moduleCode: null,
            actionCode: null,
            codeTemplate: [],
            rules: {
                name: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                code: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
            },
            fullscreen: false,
            loadingForm: false
        }
    },
    created() {
        this.getCodeTemplateForPermission()
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
            this.formData = {
                id: null,
                name: null,
            }
            this.systemCode = null
            this.subsytemCode = null
            this.moduleCode = null
            this.actionCode = null
            this.isShowModal = false
            this.current_id = null
            this.$refs.form.resetFields()
            this.formType = 'add'
        },
        async submit() {
            this.loadingForm = true
            this.formData.code = `${this.systemCode}-${this.subsytemCode}-${this.moduleCode}-${this.actionCode}`
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
                const { data } = await axios.get(this.appRoute('admin.api.permission.show', this.current_id))
                this.formData = data?.data
                const code = this.formData.code.split('-')
                this.systemCode = code[0]
                this.subsytemCode = code[1]
                this.moduleCode = code[2]
                this.actionCode = code[3]
                this.loadingForm = false
            }
        },
        async getCodeTemplateForPermission()
        {
            this.loadingForm = true
            const { data } = await axios.get(this.appRoute('admin.api.permission.code-for-permission'))
            this.codeTemplate = data?.data
            this.loadingForm = false
        },
        handleCodePermission(systemCode, subsytemCode, moduleCode, actionCode)
        {
            this.formData.code = `${systemCode}-${subsytemCode}-${moduleCode}-${actionCode}`
        },
        handleChangeSystem(code)
        {
            this.subsytemCode = null
            this.moduleCode = null
            this.actionCode = null
            this.systemCode = code
            this.handleCodePermission(this.systemCode, this.subsytemCode, this.moduleCode, this.actionCode)
        },
        handleChangeSubSystem(code)
        {
            this.moduleCode = null
            this.actionCode = null
            this.subsytemCode = code
            this.handleCodePermission(this.systemCode, this.subsytemCode, this.moduleCode, this.actionCode)
        },
        handleChangeModule(code)
        {
            this.actionCode = null
            this.moduleCode = code
            this.handleCodePermission(this.systemCode, this.subsytemCode, this.moduleCode, this.actionCode)
        },
        handleChangeAction(code)
        {
            this.actionCode = code
            this.handleCodePermission(this.systemCode, this.subsytemCode, this.moduleCode, this.actionCode)
        },
        prepareSubmit() {
          let action = null;
          let method = 'post';
            if (this.formType === 'add') {
                action = this.appRoute('admin.api.permission.store');
            } else {
                action = this.appRoute('admin.api.permission.update', this.current_id);
                method = 'put';
            }
            return {action, method};
        },
        handleToggleFullScreen() {
            this.fullscreen = !this.fullscreen
        }
    }
}
</script>
<style scoped>
:deep(.code .el-form-item__content) {
    flex-direction: column-reverse;
    align-items: start;
    padding-left: 20px;
}
</style>
