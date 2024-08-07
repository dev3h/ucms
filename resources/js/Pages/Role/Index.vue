<template>
    <AdminLayout>
        <div class="w-full px-4 bg-white">
            <div class="w-full pt-3 pb-2">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>

            <div class="w-full">
                <div class="w-full flex justify-between items-center my-2">
                    <div class="w-80">
                        <el-input v-model="filters.name" class="w-full" size="large" :placeholder="$t('input.common.search')" clearable>
                            <template #prefix>
                                <img src="/images/svg/search-icon.svg" alt="" />
                            </template>
                        </el-input>
                    </div>
                    <el-button type="primary" size="large" @click="openCreate()">{{$t('button.add')}}</el-button>

                </div>
            </div>

            <div class="w-full">
                <DataTable v-loading="loadForm" :fields="fields" :items="items" :paginate="paginate" footer-center
                    paginate-background @page-change="changePage">
                    <template #assigned="{row}">
                        <span v-if="row?.assigned === 0">-</span>
                        <span v-else>{{row?.assigned}} {{$t('column.users')}}</span>
                    </template>
                    <template #action="{ row }">
                        <div class="flex justify-center items-center gap-x-[12px]">
                            <div class="cursor-pointer" @click="openShow(row?.id)">
                                <img src="/images/svg/eye-icon.svg" />
                            </div>
                            <div class="cursor-pointer" @click="openDeleteForm(row?.id)">
                                <img src="/images/svg/trash-icon.svg" />
                            </div>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
        <DeleteForm ref="deleteForm" @delete-action="deleteItem" />
        <ModalRole ref="modalRole" :redirectRoute="appRoute('admin.role.index')" />
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
import ModalRole from "./ModalRole.vue";
export default {
    components: {ModalRole, AdminLayout, BreadCrumbComponent, DataTable, DeleteForm },
    props: {
        roles: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            items: [],
            filters: {
                name: null,
                role: null,
                page: Number(this.appRoute().params?.page ?? 1),
            },
            fields: [
                { key: 'name', 'min-width': 400, label: this.$t('column.common.name'), align: 'left', headerAlign: 'left' },
                { key: 'code', 'min-width': 300, label: this.$t('column.common.code'), align: 'left', headerAlign: 'left' },
                { key: 'assigned', 'min-width': 300, label: this.$t('button.assign'), align: 'left', headerAlign: 'left'},
                { key: 'action', width: 200, label: '', align: 'center', headerAlign: 'center', fixed: 'right', minWidth: 200 },
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
                    route: this.appRoute('admin.role.index'),
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
            await axios.get(this.appRoute("admin.api.role.index", params)).then(response => {
                this.items = response?.data?.data
                this.paginate = response?.data?.meta
                this.loadForm = false
            }).catch(error => {
                console.log(error)
            })
        },
        changePage(value) {
            this.filters.page = value
            this.fetchData()
        },
        filterData: debounce(function () {
            this.fetchData()
        }, 500),
        openCreate() {
            this.$refs.modalRole.open()
        },
        openDeleteForm(id) {
            this.$refs.deleteForm.open(id)
        },
        async deleteItem(id) {
            await axios.delete(this.appRoute("admin.api.role.destroy", id)).then(response => {
                this.$message.success(response?.data?.message);
                this.fetchData()
            }).catch(error => {
                this.$message.error(error?.response?.data?.message)
            })
        },
        openShow(id) {
            this.$inertia.visit(this.appRoute('admin.role.show', id))
        }
    },
}
</script>
<style></style>
