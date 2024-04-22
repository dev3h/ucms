<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false">
            <template #header>
                <h2 class="text-2xl font-bold">{{ formType === 'add' ? 'Add' : 'Edit' }}</h2>
            </template>
            <div class="w-full">
                <el-form class="w-full flex gap-2" ref="form" :model="formData" :rules="rules"
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
                            <el-input size="large" v-model="formData.code" clearable />
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
            rules: {
                name: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                code: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
            },
            loadingForm: false
        }
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
                code: null,
            }
            this.isShowModal = false
            this.current_id = null
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
                const { data } = await axios.get(this.appRoute('admin.api.permission.show', this.current_id))
                this.formData = data?.data
                this.loadingForm = false
            }
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