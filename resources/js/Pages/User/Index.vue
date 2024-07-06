<template>
    <AdminLayout>
        <div class="w-full h-full bg-white">
            <div class="w-full pt-3 pb-2 border-b-[1px] px-4">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>

            <div class="w-full">
                <div class="w-full flex justify-between items-center px-4 my-2">
                    <div class="flex gap-2">
                        <el-input v-model="filters.search" class="!w-80" size="large" placeholder="Search" clearable @input="filterData">
                            <template #prefix>
                                <img src="/images/svg/search-icon.svg" alt="" />
                            </template>
                        </el-input>
                        <el-select v-model="filters.role_id" class="!w-[240px]" size="large" placeholder="Select Role" clearable @change="fetchData">
                            <el-option
                                v-for="role in roles"
                                :key="role.id"
                                :label="role.name"
                                :value="role.id"
                            />
                        </el-select>
                        <el-date-picker
                            v-model="filters.created_at"
                            type="date"
                            placeholder="Created at"
                            size="large"
                            class="!w-[240px]"
                            value-format="YYYY-MM-DD"
                            format="YYYY/MM/DD"
                            @change="filterData"
                        />
                    </div>
                    <el-button type="primary" size="large" @click="openCreate()">Add</el-button>

                </div>
            </div>

            <div class="w-full">
                <DataTable v-loading="loadForm" :fields="fields" :items="items" :paginate="paginate" footer-center
                    paginate-background @page-change="changePage">
                    <template  #activity="{ row }">
                        <div class="w-5 h-5 rounded-full" :class="row?.activity === 'online' ? 'bg-green-500' : 'bg-gray-300'"></div>
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
        <DeleteForm ref="deleteForm" @delete-action="deleteAccount" />
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
export default {
    components: { AdminLayout, BreadCrumbComponent, DataTable, DeleteForm },
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
                page: Number(this.appRoute().params?.page ?? 1),
            },
            fields: [
                { key: 'name', width: 200, label: 'Name', align: 'left', headerAlign: 'left' },
                { key: 'email', width: 200, label: 'Email', align: 'left', headerAlign: 'left' },
                { key: 'role_name', width: 200, label: 'Role', align: 'left', headerAlign: 'left' },
                {key: 'last_seen', width: 200, label: 'Last seen', align: 'left', headerAlign: 'left'},
                {key: 'activity', width: 200, label: 'Activity', align: 'center', headerAlign: 'left'},
                { key: 'created_at', width: 200, label: 'Created At', align: 'left', headerAlign: 'left'},
                { key: 'action', label: 'Action', align: 'center', headerAlign: 'center', fixed: 'right', minWidth: 200 },
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
                    route: this.appRoute('admin.user.index'),
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
            await axios.get(this.appRoute("admin.api.user.index", params)).then(response => {
                this.items = response?.data?.data
                this.paginate = response?.data?.meta
                this.loadForm = false
            }).catch(error => {
                this.$message.error(error?.response?.data?.message)
                this.loadForm = false
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
            this.$inertia.visit(this.appRoute('admin.user.create'))
        },
        openDeleteForm(id) {
            this.$refs.deleteForm.open(id)
        },
        async deleteAccount(id) {
            await axios.delete(this.appRoute("admin.api.user.destroy", id)).then(response => {
                this.$message.success(response?.data?.message);
                this.fetchData()
            }).catch(error => {
                this.$message.error(error?.response?.data?.message)
            })
        },
        openShow(id) {
            this.$inertia.visit(this.appRoute('admin.user.show', id))
        }
    },
}
</script>
<style></style>
