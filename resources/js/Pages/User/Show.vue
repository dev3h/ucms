<template>
    <AdminLayout>
        <div class="w-full h-full bg-white">
            <div class="w-full pt-3 pb-2 border-b-[1px] px-4">
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
                        Permissions
                    </div>
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 2,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 2,
                        }"
                        @click="changeTab(2)"
                    >
                        General
                    </div>
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 3,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 3,
                        }"
                        @click="changeTab(3)"
                    >
                        User logs
                    </div>
                </div>
            </div>
            <div class="w-full" v-if="tabActive === 1">
                <PermissionsTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 2">
                <GeneralTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 3">
                <UserLogsTab :id="id" />
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
import GeneralTab from "@/Pages/User/GeneralTab.vue";

import PermissionsTab from "@/Pages/User/PermissionsTab.vue";
import UserLogsTab from "@/Pages/User/UserLogsTab.vue";
export default {
    components: {UserLogsTab, PermissionsTab, GeneralTab, AdminLayout, BreadCrumbComponent },
    mixins: [form],
    props: {
        id: {
            type: Number,
            default: () => null,
        },
    },
    data() {
        return {
            templatePermission: null,
            tabActive: 1,
            actions: [],
            loadingForm: false,
        };
    },
    computed: {
        setbreadCrumbHeader() {
            let menuOrigin = searchMenu();
            return [
                {
                    name: menuOrigin?.label,
                    route: this.appRoute("admin.role.index"),
                },
                {
                    name: this.id,
                    route: "",
                },
            ];
        },
    },
    methods: {
        goBack() {
            this.$inertia.visit(this.appRoute("admin.role.index"));
        },
        async fetchRoleTemplate() {
            await axios
                .get(
                    this.appRoute("admin.api.role.template-permission", this.id)
                )
                .then((response) => {
                    this.templatePermission = response?.data?.data;
                })
                .catch((error) => {
                    this.$message.error(error?.response?.data?.message);
                });
        },
        handleCheckChange(action) {
            if (!this.actions.includes(action)) {
                this.actions.push(action);
            } else {
                this.actions = this.actions.filter((item) => item !== action);
            }
        },
        changeTab(tab) {
            this.tabActive = tab;
        },
    },
};
</script>
<style></style>
