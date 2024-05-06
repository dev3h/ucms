<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full my-[15px] flex justify-start items-center">
                <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm" :disabled="user.id === id">Update</el-button>
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
                                @change="handleChangeRole"
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
            <div class="my-4">
                <h2 class="text=3xl">Permissions <span class="text-red-500">*</span> </h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <el-card
                    v-for="template in templatePermission"
                    :key="template?.id"
                    class="bg-blue-100 flex-1 p-3"
                >
                    <template #header>
                        <h2 class="uppercase font-bold text-3xl">Hệ thống {{ template?.name }}</h2>
                    </template>

                    <div
                        v-if="template?.subsystems?.length > 0"
                        v-for="subsystem in template?.subsystems"
                        :key="subsystem?.id"
                        class="ml-5 border border-black54 mb-5 p-2"
                    >
                        <h2>subsystem {{ subsystem?.name }}</h2>
                        <div
                            v-if="subsystem?.modules?.length > 0"
                            v-for="module in subsystem?.modules"
                            :key="module?.id"
                            class="ml-5"
                        >
                            <h2>module {{ module?.name }}</h2>
                            <div class="flex flex-wrap gap-2">
                                <div
                                    v-if="module?.actions?.length > 0"
                                    v-for="action in module?.actions"
                                    :key="action?.id"
                                    class="ml-5"
                                >
                                    <div class="flex gap-1 items-center">
                                        <h2 class="font-bold">
                                            {{ action?.name }}
                                        </h2>
                                        <el-checkbox
                                            v-model="action.checked"
                                            :disabled="action?.disabled"
                                            size="large"
                                            @change="handleCheckChange(action)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-card>
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
        id: {
            type: Number,
            default: () => null,
        },
        roles: {
            type: Array,
            default: () => [],
        }
    },
    data() {
        return {
            templatePermission: null,
            formData: {
                id: this.props?.id,
                name: null,
                email: null,
                role_id: null
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
                    route: this.appRoute("admin.user.index"),
                },
                {
                    name: "Edit user",
                    route: "",
                },
            ];
        },
        user() {
            return this.$page.props.auth.user;
        }
    },
    created() {
        Promise.all([this.fetchUserTemplate(), this.fetchData()]);
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get(
                    this.appRoute("admin.api.user.show", this.id)
                );
                if(response) {
                    this.formData = response?.data?.data;
                }
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            }
        },
        goBack() {
            this.$inertia.visit(this.appRoute("admin.user.index"));
        },
        async fetchUserTemplate() {
            let action = this.appRoute("admin.api.user.template-permission", this.id);
            if (this.formData?.role_id) {
              action = `${this.appRoute("admin.api.user.template-permission", this.id)}?role_id=${this.formData?.role_id}`;
            }
            await axios.get(action)
                .then((response) => {
                    this.templatePermission = response?.data?.data;
                })
                .catch((error) => {
                    this.$message.error(error?.response?.data?.message);
                });
        },
       async submit() {
            this.loadingForm = true;
            const response = await axios.put(
                this.appRoute("admin.api.user.update", this.id),
                {
                    ...this.formData,
                    actions: this.actions
                }
            );
            this.$message.success(response?.data?.message);
            this.loadingForm = false;
            this.actions = [];
        },
        handleCheckChange(action) {
            if (!this.actions.includes(action)) {
                this.actions.push(action);
            } else {
                this.actions = this.actions.filter((item) => item !== action);
            }
        },
        handleChangeRole() {
            this.actions = [];
            this.fetchUserTemplate();
        },
    },
};
</script>
<style></style>
