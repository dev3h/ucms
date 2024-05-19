<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal">
            <template #header>
                <h2 class="text-2xl font-bold">Add</h2>
            </template>
            <div class="w-full">
                <el-form class="w-full flex flex-col gap-2" ref="form" :model="formData" :rules="rules"
                    label-position="top">
                    <div class="flex justify-between gap-3">
                        <div class="flex-1">
                            <el-form-item label="Name" class="title--bold" prop="name" :error="getError('name')"
                                          :inline-message="hasError('name')">
                                <el-input size="large" v-model="formData.name" clearable/>
                            </el-form-item>
                        </div>
                        <div class="flex-1">
                            <el-form-item label="Code" class="title--bold" prop="name" :error="getError('code')"
                                          :inline-message="hasError('code')">
                                <el-input size="large" v-model="formData.code" clearable/>
                            </el-form-item>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="is-required">
                            <label for="" class="el-form-item__label">Permissions</label>
                            <div class="flex border">
                                <div class="flex-1 border-r">
                                    <div class="h-12 border-b px-4 flex items-center">
                                        <el-input v-model="filterTree" size="large" placeholder="Search"
                                                  clearable>
                                            <template #prefix>
                                                <img src="/images/svg/search-icon.svg" alt=""/>
                                            </template>
                                        </el-input>
                                    </div>
                                    <div class="px-4 py-[6px] max-h-[300px] h-full overflow-y-scroll">
                                        <el-tree
                                            ref="treeRef"
                                            style="max-width: 600px"
                                            :props="defaultProps"
                                            :data="data"
                                            node-key="id"
                                            show-checkbox
                                            @check-change="handleCheckChange"
                                            :filter-node-method="filterNode"
                                        />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="h-12 border-b px-4 flex items-center">
                                        <span>{{ permissionChecked?.length }} permissions added</span>
                                    </div>
                                    <div class="max-h-[300px] h-full overflow-y-scroll">
                                        <div v-for="permission in permissionChecked" :key="permission?.id"
                                             class="hover:bg-gray-200">
                                            <div class="flex items-start justify-between py-3 pl-4 pr-3">
                                                <div>{{ permission?.label }}
                                                    ({{ findSystemByPermission(permission?.code) }})
                                                </div>
                                                <div class="cursor-pointer flex h-full items-center">
                                                    <img src="/images/svg/x-icon.svg" alt=""
                                                         @click="handleRemovePermission(permission?.id)"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-form>
            </div>
            <div class="w-full my-[15px] flex justify-center items-center">
                <el-button type="info" size="large" @click="closeModal">Cancel</el-button>
                <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">Save</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import form from "@/Mixins/form.js";
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";
import debounce from "lodash.debounce";
export default {
    mixins: [form],
    props: {
        redirectRoute: {
            type: String,
            default: null,
        },
    },
    emits: ['add-success', 'update-success'],
    data() {
        return {
            formType: 'add',
            isShowModal: false,
            formData: {
                id: null,
                name: null,
                code: null,
            },
            rules: {
                name: baseRuleValidate,
                code: baseRuleValidate,
            },
            loadingForm: false,
            defaultProps: {
                id: 'id',
                children: 'children',
                label: 'label',
                code: 'code'
            },
            data: [],
            filterTree: '',
            permissionChecked: []
        }
    },
    watch: {
        filterTree: debounce(function(val) {
            this.$refs.treeRef.filter(val)
        }, 300),
    },
    methods: {
        async open() {
            await this.getAllPermission()
            this.isShowModal = true
        },
        closeModal() {
            this.formData = {
                id: null,
                name: null,
                code: null,
            }
            this.$refs.form.resetFields()
            this.isShowModal = false
        },
        async submit() {
            this.loadingForm = true

            const { action, method } = this.prepareSubmit()
            const { status, data } = await axios[method](action, this.formData)
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            })
            this.loadingForm = false
            this.isShowModal = false
            this.$inertia.visit(this.redirectRoute)
        },
        prepareSubmit() {
          let action = null;
          let method = 'post';

          action = this.appRoute('admin.api.system.store');
          return {action, method};
        },
        newTree(data, type = 'SYSTEM') {
            return data?.map(item => {
                let children = null;
                let label = item?.name
                if (item?.permissions && item?.permissions.length > 0) {
                    children = this.newTree(item?.permissions, 'PERMISSION');
                    label = `${item?.name} (${item?.permissions?.length} permissions)`;
                }
                return {
                    id: `${type}_${item.id}`,
                    label,
                    children,
                    code: `${item?.code}`
                }
            })
        },
        findSystemByPermission($permissionCode)
        {
            const systemCode = $permissionCode.split('-')[0]
            return this.data.find(item => item?.code === systemCode)?.label?.split(' (')[0]
        },
        async getAllPermission()
        {
            try {
                const { data } = await axios.get(this.appRoute('admin.api.role.get-all-permission'))
                if(data?.data?.length > 0) {
                    this.data = this.newTree(data?.data)
                }
            } catch(e) {
                this.$message.error(e?.response?.data?.message)
            }
        },
        filterNode: (value, data) => {
            // TODO: filter nhưng không expand được thằng con nếu nhập đúng hết 1 system
            if (!value) return true
            return data?.label.toLowerCase().includes(value.toLowerCase())
        },
        handleCheckChange(data,checked,indeterminate) {
            const [type, id] = data.id.split('_');
            if (type === 'PERMISSION') {
                if (checked) {
                    this.permissionChecked.push({
                        id: data.id,
                        label: data.label,
                        code: data.code
                    })
                } else {
                    this.permissionChecked = this.permissionChecked.filter(item => item.id !== data.id)
                }
            } else {
                //  TODO: nếu mà ban đầu chưa expand mà check thì nó ko thêm vào permissionChecked
            }
        },
        handleRemovePermission(id) {
            this.permissionChecked = this.permissionChecked.filter(item => item.id !== id)
            this.$refs.treeRef.setChecked(id, false)
        },
    },
}
</script>
