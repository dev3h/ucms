<template>
    <div>
        <el-dialog v-model="isShowModal" width="350">
            <div class="w-full">
                <el-form class="w-full grid grid-cols-3 gap-5" ref="form" :model="formData" :rules="rules"
                    label-position="top">

                    <div class="col-span-1">
                        <el-form-item label="氏名" class="title--bold" prop="name" :error="getError('name')"
                            :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" />
                        </el-form-item>
                    </div>

                    <div class="col-span-1">
                        <el-form-item label="権限" class="title--bold" prop="role" :error="getError('role')"
                            :inline-message="hasError('role')">
                            <el-select placeholder="権限" size="large" v-model="formData.role">
                                <el-option v-for="role in roles" :key="role?.id" :label="showRole(role?.name)"
                                    :value="role?.id" />
                            </el-select>
                        </el-form-item>
                    </div>


                    <div class="col-span-1">
                        <el-form-item label="メールアドレス" class="title--bold" prop="email" :error="getError('email')"
                            :inline-message="hasError('email')">
                            <el-input size="large" v-model="formData.email" />
                        </el-form-item>
                    </div>
                </el-form>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
export default {
    props: {
        action: {
            type: String,
        },
        redirectRoute: {
            type: String,
            default: null,
        },
        type: {
            type: String,
        },
    },
    emits: ['delete-success', 'delete-action', 'add-action', 'update-action'],
    data() {
        return {
            isShowModal: false,
            current_id: null,
            formData: {
                id: null,
                name: null,
                code: null,
            },
            rules: {
                name: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                email: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                role: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
            },
            loadingForm: false
        }
    },
    methods: {
        open(id) {
            if (id) {
                this.current_id = id
            }
            this.isShowModal = true
        },
        closeDeleteForm() {
            this.isShowModal = false
            this.current_id = null
        },
        async deleteItem() {
            if (!this.action) {
                this.$emit('delete-action', this.current_id)
                this.isShowDeleteForm = false
            } else {
                try {
                    this.loadingForm = true
                    const { status, data } = await axios.delete(this.action + this.current_id)
                    this.$message({
                        type: status === 200 ? 'success' : 'error',
                        message: data?.message,
                    })
                    this.isShowDeleteForm = false
                    if (this.redirectRoute != null) {
                        this.$inertia.visit(this.appRoute(this.redirectRoute))
                    } else {
                        this.$emit('delete-success', this.current_id)
                    }
                } catch (e) {
                    this.$message({
                        type: 'error',
                        message: e?.response?.data?.message,
                    })
                }
            }
        },
        
    },
}
</script>