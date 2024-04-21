<template>
    <div>
        <el-dialog v-model="isShowDeleteForm" width="350" class="commonDeleteForm" :close-on-click-modal="false">
            <div class="w-4/5 form">
                <div class="form-body mb-4">
                    <img src="/images/svg/recycle-bin.svg" style="margin: 0 auto" />
                    <p class="text-center text-[#303133] bold my-4 text-lg font-bold">Are you sure you want to delete it?</p>
                </div>
                <div class="flex form-footer mt-[30px]">
                    <el-button class="w-1/2 !bg-[#8A8A8A] !text-white" @click="closeDeleteForm">Cancel</el-button>
                    <el-button class="text-white btnDelete w-1/2" type="danger" @click.prevent="deleteItem"
                        >OK</el-button
                    >
                </div>
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
            isShowDeleteForm: false,
            current_id: null,
        }
    },
    methods: {
        open(id) {
            if (id) {
                this.current_id = id
            }
            this.isShowDeleteForm = true
        },
        closeDeleteForm() {
            this.isShowDeleteForm = false
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

<style>
.commonDeleteForm .btnDelete {
    background-color: #ca192d !important;
    color: aliceblue !important;
}
.commonDeleteForm .form {
    margin: 0 auto;
}
.commonDeleteForm .form-footer {
    justify-content: center;
}
.commonDeleteForm .el-dialog__body {
    position: relative;
    padding: 0 16px 24px 16px;
}
.commonDeleteForm .el-dialog__body .close-deleteForm-button {
    position: absolute;
    top: 9px;
    right: 9px;
    cursor: pointer;
}
</style>
