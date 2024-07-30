<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal" :fullscreen="fullscreen">
            <template #header>
                <DialogHeader :title="$t('button.import-csv')" :isFullscreen="fullscreen" @toggleFullscreen="handleToggleFullScreen" />
            </template>
            <div class="w-full flex flex-col lg:flex-row gap-5">
                <el-card class="flex-1 cursor-pointer" @click="handleDownload">
                    <div class="flex flex-col gap-2 items-center">
                        <img src="/images/svg/download.svg" alt="" width="24" height="24">
                        <span>{{ $t('button.download-csv-template') }}</span>
                    </div>
                </el-card>
                <el-card class="flex-1 cursor-pointer" @click="triggerFileInput">
                    <div class="flex flex-col gap-2 items-center">
                        <img src="/images/svg/upload.svg" alt="" width="24" height="24">
                        <span>{{$t('button.upload-csv')}}</span>
                    </div>
                </el-card>
                <input type="file" ref="fileInput" style="display: none" @change="handleFileUpload" />
            </div>
            <CsvPreviewModal ref="csvPreviewModal" @import-success="handleImportSuccess" />
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import form from "@/Mixins/form.js";
import DialogHeader from "@/Components/Dialog/DialogHeader.vue";
import Papa from 'papaparse';
import CsvPreviewModal from "@/Components/Dialog/CsvPreviewModal.vue";


export default {
    mixins: [form],
    components: {CsvPreviewModal, DialogHeader},
    props: {
        redirectRoute: {
            type: String,
            default: null,
        },
    },
    emits: ['import-success'],
    data() {
        return {
            isShowModal: false,
            fullscreen: false,
            loadingForm: false,
            csvContent: [],
            csvHeaders: []
        }
    },
    methods: {
        async open() {
            this.isShowModal = true
        },
        closeModal() {
            this.isShowModal = false
            this.csvContent = []
            this.csvHeaders = []
        },
        handleToggleFullScreen() {
            this.fullscreen = !this.fullscreen
        },
        async handleDownload() {
            try {
                const response = await axios({
                    url: this.appRoute("admin.api.user.download-csv-template"),
                    method: 'GET',
                    responseType: 'blob',
                });

                const blob = new Blob([response.data], { type: 'text/csv;charset=utf-8;' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'template-user.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                this.$message.error(error?.response?.data?.message);
            }
        },
        triggerFileInput() {
            this.$refs.fileInput.click();
        },
        handleFileUpload(event) {
            const file = event.target.files[0];
            const validTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if (file && validTypes.includes(file.type)) {
                this.parseCSV(file);
            } else {
                this.$message.error(this.$t("message.invalid-csv-file"));
            }
        },
        parseCSV(file) {
            Papa.parse(file, {
                header: true,
                complete: (results) => {
                    console.log(results?.data)
                    const requiredHeaders = ['name', 'email', 'password'];
                    const originalHeaders = results.meta.fields;
                    const fileHeaders = originalHeaders.map(header => header.toLowerCase());
                    const missingHeaders = requiredHeaders.filter(header => !fileHeaders.includes(header));
                    const extraHeaders = fileHeaders.filter(header => !requiredHeaders.includes(header));

                    if (missingHeaders.length > 0 || extraHeaders.length > 0) {
                        this.$message.error(this.$t("message.invalid-csv-data"));
                    } else {
                        this.csvHeaders = originalHeaders;
                        this.csvContent = results.data;
                        this.$refs.csvPreviewModal.open(this.csvHeaders, this.csvContent);
                    }
                },
                error: (error) => {
                    this.$message.error('Error parsing CSV file: ' + error.message);
                }
            });
        },
        handleImportSuccess() {
            this.isShowModal = false;
            this.$emit('import-success');
        }
    },
}
</script>
