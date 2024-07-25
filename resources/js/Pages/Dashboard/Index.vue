<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <el-row :gutter="16">
                <el-col :span="8">
                    <el-card>
                        <div>
                            <div class="flex flex-col gap-1">
                                <span>Daily active users</span>
                                <StatisticTransition :start-number="0" :end-number="100" :duration="1000" />
                            </div>
                            <div class="statistic-footer">
                                <div class="footer-item flex items-center gap-1">
                                    <span>than yesterday</span>
                                    <span class="text-green-500 flex items-center gap-1 w-fit">
                                      24%
                                      <img src="/images/svg/arrow-up.svg" alt="">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </el-card>
                </el-col>
                <el-col :span="8">
                    <el-card>
                        <el-statistic :value="693700">
                            <template #title>
                                <div style="display: inline-flex; align-items: center">
                                    Monthly Active Users
                                </div>
                            </template>
                        </el-statistic>
                        <div class="statistic-footer">
                            <div class="footer-item flex items-center gap-1">
                                <span>month on month</span>
                                <span class="text-red-500 flex items-center gap-1 w-fit">
                                  12%
                                    <img src="/images/svg/arrow-down.svg" alt="">
                                </span>
                            </div>
                        </div>
                    </el-card>
                </el-col>
                <el-col :span="8">
                    <el-card>
                        <el-statistic :value="72000" title="New transactions today">
                            <template #title>
                                <div style="display: inline-flex; align-items: center">
                                    New transactions today
                                </div>
                            </template>
                        </el-statistic>
                        <div class="statistic-footer">
                            <div class="footer-item flex items-center gap-1">
                                <span>than yesterday</span>
                                <span class="text-green-500 flex items-center gap-1 w-fit">
                                  16%
                                    <img src="/images/svg/arrow-up.svg" alt="">
                                 </span>
                            </div>
                        </div>
                    </el-card>
                </el-col>
            </el-row>

        </div>

    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js';
import StatisticTransition from '@/Components/StatisticTransition/Index.vue';

export default {
    components: {AdminLayout, BreadCrumbComponent, StatisticTransition },
    data() {
        return {
            items: [],
            number: 0,
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
