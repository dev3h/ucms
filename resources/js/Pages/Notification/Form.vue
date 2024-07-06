<template>
    <AdminLayout>
        <div id="notification-list" class="w-full h-full bg-white px-4 pb-[24px]">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="w-full my-[18px]">
                <el-form ref="form" :model="formData" :rules="rules" label-position="top">
                    <div class="w-full flex gap-[48px]">
                        <div class="min-w-[400px] w-[30%]">
                            <el-form-item label="Publishing Settings" prop="is_schedule" :error="getError('is_schedule')" :inline-message="hasError('is_schedule')">
                                <div class="ml-[18px]">
                                    <el-radio-group v-model="formData.is_schedule" size="large">
                                        <el-radio :value="0">Now</el-radio>
                                        <el-radio :value="1">Schedule</el-radio>
                                    </el-radio-group>
                                </div>
                            </el-form-item>
                            <div v-if="formData.is_schedule">
                                <el-form-item label="Publication start date" prop="published_at" :error="getError('published_at')" :inline-message="hasError('published_at')">
                                    <el-date-picker
                                        v-model="formData.published_at" type="datetime"
                                        size="large" placeholder="Select"
                                        format="YYYY/MM/DD HH:mm"
                                        value-format="YYYY/MM/DD HH:mm"
                                    />
                                </el-form-item>
                            </div>
                            <el-form-item label="Publication end date" prop="published_end_at" :error="getError('published_end_at')" :inline-message="hasError('published_end_at')">
                                <el-date-picker
                                    v-model="formData.published_end_at" type="datetime"
                                    size="large" placeholder="Select"
                                    format="YYYY/MM/DD HH:mm"
                                    value-format="YYYY/MM/DD HH:mm"
                                />
                            </el-form-item>
                            <el-form-item
                                label="Send to" prop="sender_type"
                                :error="getError('sender_type')"
                                :inline-message="hasError('sender_type')"
                                class="sender-type"
                            >
                                <el-select
                                    v-model="formData.sender_type"
                                    placeholder="Select" size="large"
                                    clearable filterable
                                    :suffix-icon="getCaretBottom"
                                >
                                    <el-option label="All users" :value="1" />
                                    <el-option label="Specific user" :value="2" />
                                </el-select>
                            </el-form-item>
                            <div v-if="formData.sender_type == 2" class="ml-[18px] list-user">
                                <el-form-item prop="user_ids" :error="getError('user_ids')" :inline-message="hasError('user_ids')">
                                    <div class="flex gap-[12px]" :class="{ 'w-full' : listUsers.length > 0 }">
                                        <div class="mt-[4px] cursor-pointer" @click="openAddMemberDialog()">
                                            <img src="/images/svg/add-member.svg" alt="">
                                        </div>
                                        <div class="flex-1">
                                            <div v-if="listUsers.length > 0" class="w-full grid grid-cols-3 gap-[8px]">
                                                <div
                                                    v-for="(item, index) in listUsers" :key="index"
                                                    class="bg-[#F5F5F5] px-[12px] rounded-[12px] flex items-center gap-[8px]"
                                                >
                                                    <div class="text-[12px] single-line-text" :title="item?.name">
                                                        {{ item?.name }}
                                                    </div>
                                                    <div class="cursor-pointer" @click="removeMember(index)">
                                                        <img src="/images/svg/remove-member.svg" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </el-form-item>
                            </div>
                        </div>
                        <div class="flex-1">
                            <el-form-item label="Title" prop="title" :error="getError('title')" :inline-message="hasError('title')">
                                <el-input size="large" v-model="formData.title"  placeholder=""/>
                            </el-form-item>
                            <el-form-item label="Content" prop="content" :error="getError('content')" :inline-message="hasError('content')">
                                <CKEditorComponent :contentProp="formData.content" @updateContent="handleInputEditor"/>
                            </el-form-item>
                        </div>
                    </div>
                </el-form>
                <div class="mt-5 w-full flex justify-center items-center">
                    <el-button
                        type="info" size="large"
                        class="button-min--width"
                        @click="goBack()"
                    >
                        Cancel
                    </el-button>
                    <el-button
                        :loading="loadingForm"
                        type="primary" size="large"
                        class="btn-basic button-min--width"
                        @click="doSubmit()"
                    >
                        Save
                    </el-button>
                </div>
            </div>
        </div>
        <AddMemberDialog
            ref="addMemberDialog"
            :notice-id="this.appRoute().params?.id"
            @add-member="addMember"
        />
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
import axios from '@/Plugins/axios'
import form from '@/Mixins/form.js'
import AddMemberDialog from '@/Components/AdminNotification/AddMemberDialog.vue';
import CKEditorComponent from '@/Components/Ckediter/Ckeditor.vue';
import { CaretBottom } from '@element-plus/icons-vue'

export default {
    name: "NotificationForm",
    components: { AdminLayout, BreadCrumbComponent, AddMemberDialog, CKEditorComponent },
    mixins: [form],
    data() {
        return {
            loadingForm: false,
            formData: {
                title: null,
                sender_type: null,
                posted_at: null,
                content: null,
                user_ids: [],
                is_schedule: 0,
                published_at: null,
                published_end_at: null,
            },
            rules: {
                title: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                content: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                sender_type: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
                posted_at: [{ required: true, message: 'This field is required', trigger: ['blur', 'change'] }],
            },
            listUsers: [],
        }
    },
    computed: {
        setbreadCrumbHeader() {
            let menuOrigin = searchMenu()
            return [
                {
                    name: menuOrigin?.label,
                    route: this.appRoute('admin.notification.index'),
                },
                {
                    name: this.appRoute().params.id ? 'Edit Notification' : 'Create Notification',
                    route: '',
                },
            ]
        },
        getCaretBottom() {
            return CaretBottom;
        }
    },
    async created() {
        if (this.appRoute().params.id) {
            await this.fetchData()
        }
    },
    methods: {
        async fetchData() {
            await axios.get(this.appRoute('admin.api.notification.show', this.appRoute().params.id))
                .then(({ data }) => {
                    let form = data?.data
                    this.formData.sender_type = form?.sender_type
                    this.formData.title = form?.title
                    this.formData.content = form?.content
                    this.formData.user_ids = form?.user_ids
                    this.formData.is_schedule = form?.is_schedule
                    this.formData.published_at = form?.published_at
                    this.formData.published_end_at = form?.published_end_at
                    this.listUsers = form?.users
                })
                .catch((error) => {
                    this.$message({ message: error?.message, type: 'error' })
                })
        },
        prepareForSubmit() {
            const formData = { ...this.formData }
            if (this.formData.sender_type != 2) {
                formData.user_ids = []
            }
            if (this.appRoute().params.id) {
                formData._method = 'PUT'
            }
            return {
                action: this.appRoute().params.id
                    ? this.appRoute('admin.api.notification.update', this.appRoute().params.id) : this.appRoute('admin.api.notification.store'),
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
                this.$inertia.visit(this.appRoute('admin.notification.index'))
            }
        },
        goBack() {
            return this.$inertia.visit(this.appRoute('admin.notification.index'))
        },
        openAddMemberDialog() {
            this.$refs.addMemberDialog.open(this.formData.user_ids)
        },
        addMember(userSelects) {
            for(let user of userSelects) {
                this.formData.user_ids.push(user.id)
                this.listUsers.push({
                    id: user.id,
                    name: user.name,
                })
            }
        },
        removeMember(index) {
            this.formData.user_ids.splice(index, 1)
            this.listUsers.splice(index, 1)
        },
        handleInputEditor(value) {
            this.formData.content = value
        }
    }
}
</script>
<style>
#notification-list .ck-editor__editable {
    min-height: 550px;
}
#notification-list .single-line-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: calc(100% - 20px);
}
#notification-list .el-form-item {
    margin-bottom: 24px !important;
}
#notification-list .sender-type.el-form-item {
    margin-bottom: unset !important;
}
#notification-list .list-user .el-form-item__error {
    position: unset !important;
}
</style>
