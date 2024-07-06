<template>
    <AdminLayout>
        <div class="w-full h-full bg-white">
            <div class="w-full pt-3 pb-2 border-b-[1px] px-4">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full py-[12px] pr-4 ">
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
                        Users
                    </div>
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 3,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 3,
                        }"
                        @click="changeTab(3)"
                    >
                        General
                    </div>
                </div>
            </div>

<!--            <div v-if="tabActive === 1">-->
<!--                <div class="my-4">-->
<!--                    <h2 class="text=3xl">Permissions <span class="text-red-500">*</span></h2>-->
<!--                </div>-->
<!--                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">-->
<!--                    <el-card-->
<!--                        v-for="template in templatePermission"-->
<!--                        :key="template?.id"-->
<!--                        class="bg-blue-100 flex-1 p-3"-->
<!--                    >-->
<!--                        <template #header>-->
<!--                            <h2 class="uppercase font-bold text-3xl">Hệ thống {{ template?.name }}</h2>-->
<!--                        </template>-->

<!--                        <div-->
<!--                            v-if="template?.subsystems?.length > 0"-->
<!--                            v-for="subsystem in template?.subsystems"-->
<!--                            :key="subsystem?.id"-->
<!--                            class="ml-5 border border-black54 mb-5 p-2"-->
<!--                        >-->
<!--                            <h2>subsystem {{ subsystem?.name }}</h2>-->
<!--                            <div-->
<!--                                v-if="subsystem?.modules?.length > 0"-->
<!--                                v-for="module in subsystem?.modules"-->
<!--                                :key="module?.id"-->
<!--                                class="ml-5"-->
<!--                            >-->
<!--                                <h2>module {{ module?.name }}</h2>-->
<!--                                <div class="flex flex-wrap gap-2">-->
<!--                                    <div-->
<!--                                        v-if="module?.actions?.length > 0"-->
<!--                                        v-for="action in module?.actions"-->
<!--                                        :key="action?.id"-->
<!--                                        class="ml-5"-->
<!--                                    >-->
<!--                                        <div class="flex gap-1 items-center">-->
<!--                                            <h2 class="font-bold">-->
<!--                                                {{ action?.name }}-->
<!--                                            </h2>-->
<!--                                            <el-checkbox-->
<!--                                                v-model="action.checked"-->
<!--                                                size="large"-->
<!--                                                @change="handleCheckChange(action)"-->
<!--                                            />-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </el-card>-->
<!--                </div>-->
<!--            </div>-->
            <div class="w-full" v-if="tabActive === 1">
                <PermissionsTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 3">
                <GeneralTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 2">
                <UsersTab :id="id" />
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
import GeneralTab from "@/Pages/Role/GeneralTab.vue";
import UsersTab from "@/Pages/Role/UsersTab.vue";
import PermissionsTab from "@/Pages/Role/PermissionsTab.vue";
export default {
    components: {PermissionsTab, UsersTab, GeneralTab, AdminLayout, BreadCrumbComponent },
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
                    name: "Edit role",
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
