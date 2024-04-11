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
                <el-form class="w-full grid grid-cols-3 gap-5" ref="form" :model="formData" :rules="rules"
                    label-position="top">

                    <div class="col-span-1">
                        <el-form-item label="氏名" class="title--bold" prop="name" :error="getError('name')"
                            :inline-message="hasError('name')">
                            <el-input size="large" v-model="formData.name" />
                        </el-form-item>
                    </div>

                    <div class="col-span-1">
                        <el-form-item label="権限" class="title--bold" prop="role" :error="getError('role')"
                            :inline-message="hasError('role')">
                            <el-select placeholder="権限" size="large" v-model="formData.role">
                                <el-option v-for="role in roles" :key="role?.id" :label="showRole(role?.name)"
                                    :value="role?.id" />
                            </el-select>
                        </el-form-item>
                    </div>


                    <div class="col-span-1">
                        <el-form-item label="メールアドレス" class="title--bold" prop="email" :error="getError('email')"
                            :inline-message="hasError('email')">
                            <el-input size="large" v-model="formData.email" />
                        </el-form-item>
                    </div>
                </el-form>
            </div>
        </div>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
import axios from '@/Plugins/axios'
import { findRole } from '@/Store/Const/role';
import form from '@/Mixins/form.js'
export default {
    components: { AdminLayout, BreadCrumbComponent },
    mixins: [form],
    props: {
        roles: {
            type: Array,
            default: () => []
        },
        idAccount: {
            type: Number,
            default: () => null
        }
    },
    data() {
        return {
            formData: {
                id: this.idAccount,
                name: null,
                email: null,
                role: null
            },
            rules: {
                name: [{ required: true, message: 'This field is required。', trigger: ['blur', 'change'] }],
                code: [{ required: true, message: 'This field is required。', trigger: ['blur', 'change'] }],
            },
            loadingForm: false
        }
    },
    computed: {
        setbreadCrumbHeader() {
            let menuOrigin = searchMenu()
            return [
                {
                    name: menuOrigin?.label,
                    route: this.appRoute('admin.account.index'),
                },
                {
                    name: this.appRoute().params.id ? 'アカウント更新' : ' 新規アカウント作成',
                    route: '',
                },
            ]

        }
    },
    async created() {
        if (this.idAccount) {
            await this.fetchData()
        }

    },
    methods: {
        showRole(value) {
            let role = findRole(value)
            return role?.label
        },
        async fetchData() {
            await axios.get(this.appRoute('admin.api.account.show', this.idAccount)).then(response => {
                let data = response?.data?.data
                this.formData.name = data?.name
                this.formData.role = data?.role_id
                this.formData.email = data?.email
            })
        },
        prepareForSubmit() {
            const formData = { ...this.formData }
            if (this.formData?.id) {
                formData._method = 'PUT'
            }
            return {
                action: this.formData?.id ? `admin/api/account/${this.formData?.id}/update` : 'admin/api/account/create',
                formData,
            }
        },
        async submit() {
            this.loadingForm = true
            const { action, formData } = this.prepareForSubmit()
            const { data, status } = await axios.post(action, formData)
            if (status == 200) {
                this.$message({ message: data?.message, type: status === 200 ? 'success' : 'error' })
                this.loadingForm = false
                this.$inertia.visit(this.appRoute('admin.account.index'))
            }
        },
        goBack() {
            return this.$inertia.visit(this.appRoute('admin.account.index'))
        }
    }
}
</script>
<style></style>
