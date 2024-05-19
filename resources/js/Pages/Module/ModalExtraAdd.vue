<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal">
            <template #header>
                <h2 class="text-2xl font-bold">{{type === 'action' ? 'Add action' : 'Add subsystem'}}</h2>
            </template>
            <div class="w-full">
                <div class="flex border">
                    <div class="flex-1 border-r">
                        <div class="h-12 border-b px-4 flex items-center">
                            <el-input v-model="search"  size="large" placeholder="Search"
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
                            <span>{{dataExtra?.length}} {{type === 'action' ? 'actions' : 'subsystems'}} added</span>
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
                <el-button type="info" size="large" @click="closeModal">Cancel</el-button>
                <el-button type="primary" size="large" :disabled="dataExtra?.length === 0" @click="handleAddExtra" :loading="loadingForm">Add</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import debounce from "lodash.debounce";
export default {
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
            type: 'action'
        }
    },
    methods: {
        async open(id, type='action') {
            this.type = type
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
        async fetchData()
        {
            try {
                let route = this.appRoute('admin.api.module.rest-action', this?.current_id);
                if(this.type === 'subsystem') {
                    route = this.appRoute('admin.api.module.rest-subsystem', this?.current_id);
                }
                console.log(this.type);
                const { data } = await axios.get(route)
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
            let path = this.appRoute('admin.api.module.add-extra', this.current_id);
            if(this.type === 'subsystem') {
                path = this.appRoute('admin.api.module.add-extra-subsystem', this.current_id);
            }
            const { status, data } = await axios.post(path, { ids: this.dataExtra })
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            })
            this.loadingForm = false
            this.isShowModal = false
            this.dataExtra = [];
            this.$emit('add-success')
        }
    }
}
</script>
