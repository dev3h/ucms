<template>
    <AdminLayout>
        <div class="w-full h-full bg-white">
            <div class="w-full pt-3 pb-2 border-b-[1px] px-4">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full py-[12px] pr-4">
                <div class="mt-2 border-b-[1px] border-[#8A8A8A] flex gap-[4px] px-4">
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 1,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 1,
                        }"
                        @click="changeTab(1)"
                    >
                        Actions
                    </div>
                    <div
                        class="text-center px-[12px] py-[4px] rounded-t-[4px] cursor-pointer"
                        :class="{
                            'bg-primary text-white': tabActive === 2,
                            'bg-[#F4F4F4] text-[#8A8A8A]': tabActive !== 2,
                        }"
                        @click="changeTab(2)"
                    >
                        Subsystems
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
            <div class="w-full" v-if="tabActive === 1">
                <ActionTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 2">
                <SubsystemTab :id="id" />
            </div>
            <div class="w-full" v-if="tabActive === 3">
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
import GeneralTab from "@/Pages/Module/GeneralTab.vue";
import SubsystemTab from "@/Pages/Module/SubsystemTab.vue";
import ActionTab from "@/Pages/Module/ActionTab.vue";
export default {
    components: {ActionTab, SubsystemTab, GeneralTab, AdminLayout, BreadCrumbComponent },
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
                    route: this.appRoute("admin.module.index"),
                },
                {
                    name: this.id,
                    route: "",
                },
            ];
        },
    },
    created() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get(
                    this.appRoute("admin.api.module.show", this.id)
                );
                if(response) {
                    this.formData = response?.data?.data;
                }
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            }
        },
        goBack() {
            this.$inertia.visit(this.appRoute("admin.role.index"));
        },
        changeTab(tab) {
            this.tabActive = tab;
        },
    },
};
</script>
<style></style>
