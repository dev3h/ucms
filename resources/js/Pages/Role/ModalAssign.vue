<template>
    <div>
        <el-dialog v-model="isShowModal" :close-on-click-modal="false" :before-close="closeModal">
            <template #header>
                <h2 class="text-2xl font-bold">{{ formType === 'permission' ? 'Assign permissions' : 'Assign users' }}</h2>
            </template>
            <div class="w-full">
                <div class="flex border">
                    <div class="flex-1 border-r">
                        <div class="h-12 border-b px-4 flex items-center">
                            <el-input v-model="filterTree"  size="large" placeholder="Search"
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
                            <span>{{permissionChecked?.length}} permissions added</span>
                        </div>
                        <div class="max-h-[300px] h-full overflow-y-scroll">
                            <div v-for="permission in permissionChecked" :key="permission?.id" class="hover:bg-gray-200">
                                <div class="flex items-start justify-between py-3 pl-4 pr-3">
                                    <div>{{ permission?.label }} ({{findSystemByPermission(permission?.code)}})</div>
                                    <div class="cursor-pointer flex h-full items-center">
                                        <img src="/images/svg/x-icon.svg" alt="" @click="handleRemovePermission(permission?.id)"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full my-[15px] flex justify-center items-center">
                <el-button type="info" size="large" @click="closeModal">Cancel</el-button>
                <el-button type="primary" size="large" :disabled="permissionChecked?.length === 0" @click="handleAssignPermission" :loading="loadingForm">{{ formType === 'permission' ? 'Assign permissions' : 'Assign users' }}</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'
import debounce from "lodash.debounce";
export default {
    props: {
        redirectRoute: {
            type: String,
            default: null,
        },
    },
    emits: ['assign-success'],
    data() {
        return {
            formType: 'permission',
            isShowModal: false,
            current_id: null,
            loadingForm: false,
            filters: {},
            defaultProps: {
                id: 'id',
                children: 'children',
                label: 'label',
                code: 'code'
            },
            dataFetched: false,
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
        async open(id, type) {
            if (id) {
                this.current_id = id
                this.formType = type
                await this.fetchData()
            }
            this.isShowModal = true
        },
        closeModal() {
            this.isShowModal = false
            this.current_id = null
            this.formType = 'null'
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
        newTreeTempl(data, type = 'SYSTEM') {
            return data?.map(item => {
                let children = null;
                if (item.subsystems && item.subsystems.length > 0) {
                    children = this.newTree(item.subsystems, 'SUBSYSTEM');
                } else if (item.modules && item.modules.length > 0) {
                    children = this.newTree(item.modules, 'MODULE');
                } else if (item.actions && item.actions.length > 0) {
                    children = this.newTree(item.actions, 'ACTION');
                }

                return {
                    id: `${type}_${item.id}`,
                    label: item.name,
                    children: children
                }
            })
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
        async fetchData()
        {
            try {
                if (this.formType === 'permission') {
                    const { data } = await axios.get(this.appRoute('admin.api.role.rest-permission', this?.current_id))
                    if(data?.data?.length > 0) {
                        this.data = this.newTree(data?.data)
                    }
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
        prepareSubmit() {
            let action = null;
            let method = 'post';
            if (this.formType === 'add') {
                action = this.appRoute('admin.api.system.store');
            } else {
                action = this.appRoute('admin.api.system.update', this.current_id);
                method = 'put';
            }
            return {action, method};
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
        async handleAssignPermission() {
            this.loadingForm = true
            const permissionIds = this.permissionChecked.map(item => item.id.split('_')[1]);
            const { status, data } = await axios.post(this.appRoute('admin.api.role.assign-permission', this.current_id), { permission_ids: permissionIds })
            this.$message({
                type: status === 200 ? 'success' : 'error',
                message: data?.message,
            })
            this.loadingForm = false
            this.isShowModal = false
            this.$emit('assign-success')
        }
    }
}
</script>
