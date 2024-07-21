<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal" :fullscreen="fullscreen">
            <template #header>
                <DialogHeader :title="$t('button.add')" :isFullscreen="fullscreen" @toggleFullscreen="handleToggleFullScreen" />
            </template>
            <div class="w-full">
                <div class="flex border">
                    <div class="flex-1 border-r">
                        <div class="h-12 border-b px-4 flex items-center">
                            <el-input v-model="search"  size="large" :placeholder="$t('input.common.search')"
                                      @input="filterData"
                                      clearable>
                                <template #prefix>
                                    <img src="/images/svg/search-icon.svg" alt=""/>
                                </template>
                            </el-input>
                        </div>
                        <div class="px-4 py-[6px] max-h-[300px] h-full overflow-y-scroll">
                            <el-checkbox-group v-model="dataExtra" class="flex flex-col">
                                <el-checkbox v-for="item in data" :key="item?.id" :label="item?.name" :value="item?.id" />
                            </el-checkbox-group>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="h-12 border-b px-4 flex items-center">
                            <span>{{dataExtra?.length}} {{ $t('sidebar.module') }} {{ $t('form.item-added') }}</span>
                        </div>
                        <div class="max-h-[300px] h-full overflow-y-scroll">
                            <div v-for="item in dataExtra" :key="item" class="hover:bg-gray-200">
                                <div class="flex items-start justify-between py-3 pl-4 pr-3">
                                    <div>{{ findNameById(item) }}</div>
                                    <div class="cursor-pointer flex h-full items-center">
                                        <img src="/images/svg/x-icon.svg" alt="" @click="handleRemoveItemChecked(item)"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full my-[15px] flex justify-center items-center">
                <el-button type="danger" size="large" @click="closeModal">{{$t('button.cancel')}}</el-button>
                <el-button type="primary" size="large" :disabled="dataExtra?.length === 0" @click="handleAddExtra" :loading="loadingForm">{{$t('button.add')}}</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import debounce from "lodash.debounce";
import DialogHeader from "@/Components/Dialog/DialogHeader.vue";
export default {
    components: {DialogHeader},
    props: {
        redirectRoute: {
            type: String,
            default: null,
        },
    },
    emits: ['add-success'],
    data() {
        return {
            isShowModal: false,
            current_id: null,
            loadingForm: false,
            search: "",
            originalData: [],
            dataExtra: [],
            data: [],
            filterTree: '',
            fullscreen: false,
        }
    },
    methods: {
        async open(id) {
            if (id) {
                this.current_id = id
                await this.fetchData()
            }
            this.isShowModal = true
        },
        closeModal() {
            this.isShowModal = false
            this.current_id = null
            this.dataExtra = []
            this.search = ""
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
            try {
                const { data } = await axios.get(this.appRoute('admin.api.subsystem.rest-module', this?.current_id))
                if(data?.data?.length > 0) {
                    this.originalData = data?.data
                    this.data = [...this.originalData]
                }
            } catch(e) {
               this.$message.error(e?.response?.data?.message)
            }
        },
        filterData: debounce(function () {
            if(this.search === "") {
                this.data = [...this.originalData];
            } else {
                this.data = this.originalData.filter(item => item.name.toLowerCase().includes(this.search.toLowerCase()));
            }
        }, 300),
        findNameById(id) {
            return this.data.find(item => item.id === id)?.name
        },
        handleRemoveItemChecked(id) {
            this.dataExtra = this.dataExtra.filter(item => item !== id)
        },
        async handleAddExtra() {
            this.loadingForm = true
            const { status, data } = await axios.post(this.appRoute('admin.api.subsystem.add-extra', this.current_id), { ids: this.dataExtra })
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            })
            this.loadingForm = false
            this.isShowModal = false
            this.dataExtra = [];
            this.$emit('add-success')
        },
        handleToggleFullScreen() {
            this.fullscreen = !this.fullscreen
        }
    }
}
</script>
