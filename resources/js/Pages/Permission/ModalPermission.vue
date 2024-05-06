<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal">
            <template #header>
                <h2 class="text-2xl font-bold">{{ formType === 'add' ? 'Add' : 'Edit' }}</h2>
            </template>
            <div class="w-full">
                <el-form class="w-full flex flex-col gap-2" ref="form" :model="formData" :rules="rules"
                    label-position="top">

                    <div class="flex-1">
                        <el-form-item label="Name" class="title--bold" prop="name" :error="getError('name')"
                            :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" clearable />
                        </el-form-item>
                    </div>
                    <div class="flex-1">
                        <el-form-item label="Code" class="title--bold" prop="name" :error="getError('code')"
                                      :inline-message="hasError('code')">
                        <div>
                            <h3 class="font-bold">System</h3>
                            <div>
                                <div v-for="system in codeTemplate" :key="system.id">
                                    <input type="radio" :value="system.code" v-model="systemCode" @change="handleChangeSystem(system.code)" />
                                    <label>{{ system.name }}</label>
                                    <div v-if="systemCode && systemCode === system.code" class="ml-5">
                                        <h3 class="font-bold">Sub System</h3>
                                        <div v-for="subsystem in system.subsystems" :key="subsystem.id">
                                            <input type="radio" :value="subsystem.code" v-model="subsytemCode" @change="handleChangeSubSystem(subsystem.code)" />
                                            <label>{{ subsystem.name }}</label>
                                            <div v-if="subsytemCode && subsytemCode === subsystem.code" class="ml-5">
                                                <h3 class="font-bold">Module</h3>
                                                <div v-for="module in subsystem.modules" :key="module.id">
                                                    <input type="radio" :value="module.code" v-model="moduleCode" @change="handleChangeModule(module.code)" />
                                                    <label>{{ module.name }}</label>
                                                    <div v-if="moduleCode && moduleCode === module.code" class="ml-5">
                                                        <h3 class="font-bold">Action</h3>
                                                        <div v-for="action in module.actions" :key="action.id">
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
                        </el-form-item>
                    </div>
                </el-form>

            </div>
            <div class="w-full my-[15px] flex justify-center items-center">
                <el-button type="info" size="large" @click="closeModal">Cancel</el-button>
                <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">Save</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import form from "@/Mixins/form.js";
export default {
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
            },
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
        handleChangeSystem(code)
        {
            this.subsytemCode = null
            this.moduleCode = null
            this.actionCode = null
            this.systemCode = code
        },
        handleChangeSubSystem(code)
        {
            this.moduleCode = null
            this.actionCode = null
            this.subsytemCode = code
        },
        handleChangeModule(code)
        {
            this.actionCode = null
            this.moduleCode = code
        },
        handleChangeAction(code)
        {
            this.actionCode = code
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
        }
    },
}
</script>
<style scoped>
:deep(.el-radio-group) {
    display: block;
}
</style>
