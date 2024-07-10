<template>
    <AdminLayout>
        <div class="w-full h-full bg-white">
            <div class="w-full pt-3 pb-2 border-b-[1px] px-4">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>

            <div class="w-full px-4">
                <div class="w-full flex justify-between gap-2 my-2">
                    <div class="flex gap-2">
                        <div class="col-span-1">
                            <el-input v-model="filters.search" class="!w-80" size="large" placeholder="Search"
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
                                placeholder="Created at"
                                size="large"
                                class="!w-[185px]"
                                value-format="YYYY-MM-DD"
                                format="YYYY/MM/DD"
                                @change="filterData"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <DataTable v-loading="loadForm" :fields="fields" :items="items" :paginate="paginate" footer-center
                    paginate-background @page-change="changePage" @size-change="changeSize">
                </DataTable>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
import DataTable from '@/Components/Page/DataTable.vue'
import axios from '@/Plugins/axios'
import debounce from 'lodash.debounce'

export default {
    components: {AdminLayout, BreadCrumbComponent, DataTable },
    data() {
        return {
            items: [],
            filters: {
                page: Number(this.appRoute().params?.page ?? 1),
                limit: Number(this.appRoute().query?.limit ?? 10),
            },
            fields: [
                { key: 'created_at', label: 'Time', align: 'left', headerAlign: 'left' },
                { key: 'actor', label: 'Actor', align: 'left', headerAlign: 'left' },
                { key: 'event', label: 'Event', align: 'left', headerAlign: 'left' },
                { key: 'target', label: 'Target', align: 'center', headerAlign: 'center', fixed: 'right', minWidth: 200 },
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
                    route: this.appRoute('admin.audit-log.index'),
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
            await axios.get(this.appRoute("admin.api.audit-log.index", params)).then(response => {
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
        changeSize(value) {
            this.filters.page = 1
            this.filters.limit = value
            this.fetchData()
        },
    },
}
</script>
<style></style>