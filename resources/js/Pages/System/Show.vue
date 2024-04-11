<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full my-[15px] flex justify-start items-center">
                <el-button type="primary" size="large" @click="openEdit()" :loading="loadingForm">編集する</el-button>
                <el-button type="danger" size="large" @click="openDeleteForm()">削除する</el-button>
            </div>

            <div class="w-full grid grid-cols-3 gap-6">
                <div class="col-span-1">
                    <h4 class="text-[#545454] font-medium">氏名:</h4>
                    <p class="font-bold">{{ datas?.name }}</p>
                </div>

                <div class="col-span-1">
                    <h4 class="text-[#545454] font-medium">権限:</h4>
                    <p class="font-bold">{{ showRole(datas?.role) }}</p>
                </div>



                <div class="col-span-1">
                    <h4 class="text-[#545454] font-medium">作成日:</h4>
                    <p class="font-bold">{{ datas?.created_at }}</p>
                </div>



                <div class="col-span-1">
                    <h4 class="text-[#545454] font-medium">メールアドレス:</h4>
                    <p class="font-bold">{{ datas?.email }}</p>
                </div>
            </div>
        </div>
        <DeleteForm ref="deleteForm" @delete-action="deleteAccount" />
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
import axios from '@/Plugins/axios'
import { findRole } from '@/Store/Const/role';
import DeleteForm from '@/Components/Page/DeleteForm.vue';
export default {
    components: { AdminLayout, BreadCrumbComponent, DeleteForm },
    props: {
        idAccount: {
            type: Number,
            default: () => null
        }
    },
    data() {
        return {
            datas: {
                id: this.idAccount,
                name: null,
                email: null,
                role: null,
                created_at: null
            },
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
                    name: this.datas.name,
                    route: '',
                },
            ]

        }
    },
    async created() {
        await this.fetchData()
    },
    methods: {
        showRole(value) {
            let role = findRole(value)
            return role?.label
        },
        async fetchData() {
            await axios.get(this.appRoute("admin.api.account.show", this.idAccount)).then(response => {
                let data = response?.data?.data
                this.datas.name = data?.name
                this.datas.role = data?.role
                this.datas.email = data?.email
                this.datas.created_at = data?.created_at
            })
        },
        openEdit() {
            this.$inertia.visit(this.appRoute('admin.account.update', this.idAccount))
        },
        openDeleteForm() {
            this.$refs.deleteForm.open(this.idAccount)
        },
        async deleteAccount(id) {
            await axios.delete(this.appRoute("admin.api.account.delete", id)).then(response => {
                this.$message.success(response?.data?.message);
                return this.$inertia.visit(this.appRoute('admin.account.index'))
            }).catch(error => {
                this.$message.error(error?.response?.data?.message)
            })
        },
    }
}
</script>
<style></style>
