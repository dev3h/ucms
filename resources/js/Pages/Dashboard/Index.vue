<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="flex justify-between gap-2 mt-5">
                <el-card class="flex-1">
                    <template #header>
                        <h2>Total Users</h2>
                    </template>
                    <div>
                        1
                    </div>
                </el-card>
                <el-card class="flex-1">
                    <template #header>
                        <h2>New users today</h2>
                    </template>
                    <div>
                        1
                    </div>
                </el-card>
                <el-card class="flex-1">
                    <template #header>
                        <h2>New users past 7 days</h2>
                    </template>
                    <div>
                        1
                    </div>
                </el-card>
            </div>
        </div>

    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
export default {
    components: {AdminLayout, BreadCrumbComponent },
    data() {
        return {
            items: [],
        }
    },
    computed: {
        setbreadCrumbHeader() {
            let menuOrigin = searchMenu()
            return [
                {
                    name: menuOrigin?.label,
                    route: this.appRoute('admin.system.index'),
                },
            ]
        }
    },
    async created() {
    },
    methods: {

    },
}
</script>
<style></style>
