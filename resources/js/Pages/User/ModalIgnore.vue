<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal" :fullscreen="fullscreen">
            <template #header>
                <DialogHeader :title="$t('form.ignore')" :isFullscreen="fullscreen" @toggleFullscreen="handleToggleFullScreen" />
            </template>
            <div class="w-full">
                <div class="flex border">
                    <div class="flex-1 border-r">
                        <div class="h-12 border-b px-4 flex items-center">
                            <el-input v-model="search"  size="large" :placeholder="$t('input.common.search')"
                                      @input="filterData" clearable>
                                <template #prefix>
                                    <img src="/images/svg/search-icon.svg" alt=""/>
                                </template>
                            </el-input>
                        </div>
                        <div class="px-4 py-[6px] max-h-[300px] h-full overflow-y-scroll">
                            <el-checkbox-group v-model="dataHandle" class="flex flex-col">
                                <el-checkbox v-for="item in data" :key="item?.id" :label="item?.name" :value="item?.id" :checked="item?.checked" @change="handleChangeIgnoreItem(item?.id)" />
                            </el-checkbox-group>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="h-12 border-b px-4 flex items-center">
                            <span>{{ dataHandle?.length }} {{ $t('sidebar.permission') }} {{$t("form.ignore")}}</span>
                        </div>
                        <div class="max-h-[300px] h-full overflow-y-scroll">
                            <div v-for="item in dataHandle" :key="item" class="hover:bg-gray-200">
                                <div class="flex items-start justify-between py-3 pl-4 pr-3">
                                    <div>{{ findNameById(item) }}</div>
                                    <div class="cursor-pointer flex h-full items-center">
                                        <img src="/images/svg/x-icon.svg" alt=""
                                             @click="handleRemoveItemChecked(item)"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="w-full my-[15px] flex justify-center items-center">
                <el-button type="danger" size="large" @click="closeModal">{{$t('button.cancel')}}</el-button>
                <el-button type="primary" size="large" @click="handleIgnorePermission" :loading="loadingForm">{{$t('button.update')}}</el-button>
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
    emits: ['assign-success'],
    data() {
        return {
            isShowModal: false,
            current_id: null,
            loadingForm: false,
            search: "",
            data: [],
            originalData: [],
            filterTree: '',
            permissionChecked: [],
            dataHandle: [],
            role_id: null,
            removeItemIgnore: [],
            fullscreen: false,
        }
    },
    watch: {
        filterTree: debounce(function(val) {
            this.$refs.treeRef.filter(val)
        }, 300),
    },
    methods: {
        async open(id) {
            if (id) {
                this.current_id = id
                await this.getRoleOfUser()
            }
            this.isShowModal = true
        },
        closeModal() {
            this.isShowModal = false
            this.current_id = null
            this.formType = 'null'
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
        async getRoleOfUser() {
            try {
                const response = await axios.get(
                    this.appRoute("admin.api.user.show", this.current_id)
                );
                if(response) {
                    this.role_id = response?.data?.data?.role_id;
                    if(this.role_id) {
                        await this.fetchData(this.role_id)
                    }
                }
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            }
        },
        async fetchData(role_id)
        {
            try {
                const { data } = await axios.get(this.appRoute('admin.api.user.role.all-permission', {
                    role_id: role_id,
                    user_id: this.current_id
                }))
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
            this.dataHandle = this.dataHandle.filter(item => item !== id)
            this.handleFilterRemoveItemIgnore(id)
        },
        handleFilterRemoveItemIgnore(id) {
            if (!this.dataHandle.includes(id)) {
                this.removeItemIgnore.push(id)
            } else {
                this.removeItemIgnore = this.removeItemIgnore.filter(item => item !== id)
            }
        },
        handleChangeIgnoreItem(id) {
            this.handleFilterRemoveItemIgnore(id)
        },
        prepareSubmit() {
            let action = null;
            let method = 'post';
            if (this.formType === 'add') {
                action = this.appRoute('admin.api.system.store');
            } else {
                action = this.appRoute('admin.api.system.update', this.current_id);
                method = 'put';
            }
            return {action, method};
        },
        async handleIgnorePermission() {
            this.loadingForm = true
            const { status, data } = await axios.put(this.appRoute('admin.api.user.ignore-permission-for-user-role', this.current_id), {
                permission_ids: this.dataHandle,
                remove_permission_ignore_ids: this.removeItemIgnore
            })
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            })
            this.loadingForm = false
            this.isShowModal = false
            this.$emit('assign-success')
        },
        handleToggleFullScreen() {
            this.fullscreen = !this.fullscreen
        }
    }
}
</script>
