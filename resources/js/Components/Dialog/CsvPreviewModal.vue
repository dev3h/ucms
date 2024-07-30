<template>
    <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal" :fullscreen="fullscreen">
        <template #header>
            <DialogHeader :title="$t('button.preview-csv')" :isFullscreen="fullscreen" @toggleFullscreen="handleToggleFullScreen" />
        </template>
        <div v-if="csvContent.length">
            <el-form :model="csvContent" :rules="rules" ref="form">
                <el-table :data="csvContent" @selection-change="handleSelectionChange" ref="table">
                    <el-table-column type="selection" width="55"></el-table-column>
                    <el-table-column v-for="(header, index) in csvHeaders" :key="index" :label="header" :prop="header">
                        <template #default="scope">
                            <el-form-item :prop="`data.${scope.$index}.${header}`" :error="getError(`data.${scope.$index}.${header}`)" :inline-message="hasError(`data.${scope.$index}.${header}`)">
                                <el-input v-model="scope.row[header]" clearable />
                            </el-form-item>
                        </template>
                    </el-table-column>
                </el-table>
            </el-form>
        </div>
        <template #footer>
            <div class="flex justify-center">
                <el-button @click="closeModal">{{ $t('button.cancel') }}</el-button>
                <el-button :disabled="selectedRows.length === 0" type="primary" @click="doSubmit()">{{ $t('button.import') }}</el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script>
import DialogHeader from "@/Components/Dialog/DialogHeader.vue";
import form from "@/Mixins/form.js";

export default {
    mixins: [form],
    components: { DialogHeader },
    emits: ['import-success'],
    data() {
        return {
            fullscreen: false,
            isShowModal: false,
            csvContent: [],
            csvHeaders: [],
            selectedRows: [],
            rules: {
                'csvContent.*.name': [{ required: true, message: 'Name is required', trigger: 'blur' }],
                'csvContent.*.email': [
                    { required: true, message: 'Email is required', trigger: 'blur' },
                    { type: 'email', message: 'Invalid email format', trigger: 'blur' }
                ],
            }
        }
    },
    methods: {
        async open(csvHeaders, csvContent) {
            this.isShowModal = true
            this.csvHeaders = csvHeaders
            this.csvContent = csvContent
        },
        closeModal() {
            this.isShowModal = false
        },
        handleToggleFullScreen() {
            this.fullscreen = !this.fullscreen;
        },
        handleSelectionChange(selectedRows) {
            console.log(selectedRows)
            this.selectedRows = selectedRows;
        },
        async submit() {
            this.loadingForm = true;
            const selectedData = this.selectedRows;
            console.log(selectedData)
            const {status, data} = await axios.post(this.appRoute('admin.api.user.import-csv'), {data: selectedData});
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            });
            if (status === 200) {
                this.closeModal();
                this.$emit('import-success');
            }
            this.loadingForm = false;
        },
        prepareSubmit() {
            let action = this.appRoute('admin.api.system.store');
            let method = 'post';
            return {action, method};
        }
    }
}
</script>
