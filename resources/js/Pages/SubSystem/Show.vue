<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full py-[12px] pr-4">
                <div class="mt-2 border-b-[1px] border-[#8A8A8A] flex gap-[4px]">
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 1,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 1,
                        }"
                        @click="changeTab(1)"
                    >
                        {{ $t('sidebar.module')}}
                    </div>
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 2,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 2,
                        }"
                        @click="changeTab(2)"
                    >
                        {{ $t('button.general')}}
                    </div>
                </div>
            </div>
            <div class="w-full" v-if="tabActive === 1">
                <ModuleTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 2">
                <GeneralTab :id="id" />
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import BreadCrumbComponent from "@/Components/Page/BreadCrumb.vue";
import { searchMenu } from "@/Mixins/breadcrumb.js";
import axios from "@/Plugins/axios";
import form from '@/Mixins/form.js'
import GeneralTab from "@/Pages/SubSystem/GeneralTab.vue";
import ModuleTab from "@/Pages/SubSystem/ModuleTab.vue";
export default {
    components: {ModuleTab, GeneralTab, AdminLayout, BreadCrumbComponent },
    mixins: [form],
    props: {
        id: {
            type: Number,
            default: () => null,
        },
    },
    data() {
        return {
            tabActive: 1,
        };
    },
    computed: {
        setbreadCrumbHeader() {
            let menuOrigin = searchMenu();
            console.log(menuOrigin?.label)
            return [
                {
                    name: menuOrigin?.label,
                    route: this.appRoute("admin.subsystem.index"),
                },
                {
                    name: this.id,
                    route: "",
                },
            ];
        },
    },
    methods: {
        changeTab(tab) {
            this.tabActive = tab;
        },
    },
};
</script>
<style></style>
