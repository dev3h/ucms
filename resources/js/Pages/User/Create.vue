<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full my-[15px] flex justify-start items-center">
                <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">{{$t('button.save')}}</el-button>
                <el-button type="danger" size="large" @click="goBack()">{{$t('button.cancel')}}</el-button>
            </div>
            <div class="w-full">
                <el-form class="w-full grid grid-col lg:grid-cols-2 gap-5" ref="form" :model="formData" :rules="rules"
                         label-position="top">

                    <div class="col-span-1">
                        <el-form-item :label="$t('column.common.name')" class="title--bold" prop="name" :error="getError('name')"
                                      :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" clearable />
                        </el-form-item>
                    </div>

                    <div class="col-span-1">
                        <el-form-item :label="$t('input.common.email')" class="title--bold" prop="email" :error="getError('email')"
                                      :inline-message="hasError('email')">
                            <el-input size="large" v-model="formData.email" clearable />
                        </el-form-item>
                    </div>
                    <div class="col-span-1">
                        <el-form-item :label="$t('sidebar.role')" class="title--bold" prop="role_id" :error="getError('role_id')"
                                      :inline-message="hasError('role_id')">
                            <el-select
                                v-model="formData.role_id"
                                :placeholder="$t('input.common.select')"
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
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";

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
                role_id: null
            },
            actions: [],
            rules: {
                name: baseRuleValidate(this.$t),
                email: baseRuleValidate(this.$t),
                role_id: baseRuleValidate(this.$t),
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
                    name: 'breadcrumb.create-user',
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
