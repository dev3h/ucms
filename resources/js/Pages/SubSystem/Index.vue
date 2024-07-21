<template>
    <AdminLayout>
        <div class="w-full bg-white px-4">
            <div class="w-full pt-3 pb-2">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>

            <div class="w-full">
                <div class="w-full flex flex-wrap justify-between gap-2 my-2">
                    <div class="flex flex-wrap gap-2">
                        <div class="col-span-1">
                            <el-input v-model="filters.search" class="!w-[320px]" size="large" :placeholder="$t('input.common.search')"
                                      clearable @input="filterData">
                                <template #prefix>
                                    <img src="/images/svg/search-icon.svg" alt=""/>
                                </template>
                            </el-input>
                        </div>
                        <div class="flex-col">
                            <el-date-picker
                                v-model="filters.created_at"
                                type="date"
                                :placeholder="$t('column.common.created-at')"
                                size="large"
                                class="!w-[185px]"
                                value-format="YYYY-MM-DD"
                                format="YYYY/MM/DD"
                                @change="filterData"
                            />
                        </div>
                    </div>
                    <div>
                        <div class="w-full flex justify-between items-center">
                            <div>
                                <el-button type="primary" size="large" @click="openCreate()">{{$t('button.add')}}</el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <DataTable v-loading="loadForm" :fields="fields" :items="items" :paginate="paginate" footer-center
                    paginate-background @page-change="changePage">
                    <template #action="{ row }">
                        <div class="flex justify-center items-center gap-x-[12px]">
                            <div class="cursor-pointer" @click="openShow(row?.id)">
                                <img src="/images/svg/eye-icon.svg" />
                            </div>
<!--                            <div class="cursor-pointer" @click="openEdit(row?.id)">-->
<!--                                <img src="/images/svg/pen-icon.svg" />-->
<!--                            </div>-->
                            <div class="cursor-pointer" @click="openDeleteForm(row?.id)">
                                <img src="/images/svg/trash-icon.svg" />
                            </div>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
        <DeleteForm ref="deleteForm" @delete-action="deleteItem" />
        <ModalSubSystem ref="modalSubSystem" :redirectRoute="appRoute('admin.subsystem.index')" />
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
import DataTable from '@/Components/Page/DataTable.vue'
import axios from '@/Plugins/axios'
import DeleteForm from '@/Components/Page/DeleteForm.vue';
import debounce from 'lodash.debounce'
import ModalSubSystem from "./ModalSubSystem.vue";
export default {
    components: {ModalSubSystem, AdminLayout, BreadCrumbComponent, DataTable, DeleteForm },
    data() {
        return {
            items: [],
            filters: {
                page: Number(this.appRoute().params?.page ?? 1),
            },
            fields: [
                { key: 'name', 'min-width': 400, label: this.$t('column.common.name'), align: 'left', headerAlign: 'left' },
                { key: 'code', 'min-width': 300, label: this.$t('column.common.code'), align: 'left', headerAlign: 'left' },
                { key: 'created_at', 'min-width': 200, label: this.$t('column.common.created-at'), align: 'left', headerAlign: 'left' },
                { key: 'action', width: 200, label: "", align: 'center', headerAlign: 'center', fixed: 'right', minWidth: 200 },
            ],
            paginate: {},
            loadForm: false
        }
    },
    computed: {
        setbreadCrumbHeader() {
            let menuOrigin = searchMenu()
            return [
                {
                    name: menuOrigin?.label,
                    route: this.appRoute('admin.subsystem.index'),
                },
            ]
        }
    },
    async created() {
        await this.fetchData()
    },
    methods: {
        async fetchData(page = 1) {
            this.loadForm = true
            this.filters.page = page
            let params = { ...this.filters }
            await axios.get(this.appRoute("admin.api.subsystem.index", params)).then(response => {
                this.items = response?.data?.data
                this.paginate = response?.data?.meta
                this.loadForm = false
            }).catch(error => {
                this.$message.error(error?.response?.data?.message)
            })
        },
        changePage(page) {
            this.fetchData(page)
        },
        filterData: debounce(function () {
            this.fetchData()
        }, 500),
        openCreate() {
            this.$refs.modalSubSystem.open()
        },
        openEdit(id) {
            this.$refs.modalSubSystem.open(id)
        },
        openDeleteForm(id) {
            this.$refs.deleteForm.open(id)
        },
        async deleteItem(id) {
            await axios.delete(this.appRoute("admin.api.subsystem.destroy", id)).then(response => {
                this.$message.success(response?.data?.message);
                this.fetchData()
            }).catch(error => {
                this.$message.error(error?.response?.data?.message)
            })
        },
        openShow(id) {
            this.$inertia.visit(this.appRoute('admin.subsystem.show', id))
        }
    },
}
</script>
<style></style>
