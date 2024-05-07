<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full my-[15px] flex justify-start items-center">
                <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">Save</el-button>
                <el-button type="info" size="large" @click="goBack()">Back</el-button>
            </div>
            <div class="w-full">
                <el-form class="w-full grid grid-cols-2 gap-5" ref="form" :model="formData" :rules="rules"
                         label-position="top">

                    <div class="col-span-1">
                        <el-form-item label="Name" class="title--bold" prop="name" :error="getError('name')"
                                      :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" clearable />
                        </el-form-item>
                    </div>

                    <div class="col-span-1">
                        <el-form-item label="Email" class="title--bold" prop="email" :error="getError('email')"
                                      :inline-message="hasError('email')">
                            <el-input size="large" v-model="formData.email" clearable />
                        </el-form-item>
                    </div>
                    <div class="col-span-1">
                        <el-form-item label="Role" class="title--bold" prop="role_id" :error="getError('role_id')"
                                      :inline-message="hasError('role_id')">
                            <el-select
                                v-model="formData.role_id"
                                placeholder="Select"
                                size="large"
                                clearable
                            >
                                <el-option
                                    v-for="role in roles"
                                    :key="role?.id"
                                    :label="role?.name"
                                    :value="role?.id"
                                />
                            </el-select>
                        </el-form-item>
                    </div>
                </el-form>
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
export default {
    components: { AdminLayout, BreadCrumbComponent },
    mixins: [form],
    props: {
        templatePermission: {
            type: Array,
            default: () => [],
        },
        roles: {
            type: Array,
            default: () => [],
        }
    },
    data() {
        return {
            formData: {
                name: null,
                code: null,
            },
            actions: [],
            rules: {
                name: [{ required: true, message: "This field is required。", trigger: ["blur", "change"] }],
                email: [{ required: true, message: "This field is required。", trigger: ["blur", "change"] }],
                role_id: [{ required: true, message: "This field is required。", trigger: ["blur", "change"] }],
            },
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
                    name: "Create new user",
                    route: "",
                },
            ];
        },
    },

    methods: {
        goBack() {
            this.$inertia.visit(this.appRoute("admin.user.index"));
        },
        async submit() {
            this.loadingForm = true;
            const response = await axios.post(
                this.appRoute("admin.api.user.store"),
                    this.formData,
            );
            this.$message({
                type: response.status === 200 ? "success" : "error",
                message: response.data.message,
            });
            this.$inertia.visit(this.appRoute("admin.user.index"));
            this.loadingForm = false;
        },
    },
};
</script>
<style></style>
