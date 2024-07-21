<template>
    <div>
        <div class="w-full my-5 flex justify-start items-center">
            <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">{{$t('button.update')}}</el-button>
            <el-button type="danger" size="large" @click="goBack()">{{$t('button.cancel')}}</el-button>
        </div>
        <el-form class="w-full grid grid-col lg:grid-cols-2 gap-5" ref="form" :model="formData" :rules="rules"
                 label-position="top">

            <div class="col-span-1">
                <el-form-item :label="$t('column.common.name')" class="title--bold" prop="name" :error="getError('name')"
                              :inline-message="hasError('name')">
                    <el-input size="large" v-model="formData.name" clearable />
                </el-form-item>
            </div>

            <div class="col-span-1">
                <el-form-item :label="$t('column.common.code')" class="title--bold" prop="code" :error="getError('code')"
                              :inline-message="hasError('code')">
                    <el-input size="large" v-model="formData.code" clearable />
                </el-form-item>
            </div>
            <div class="col-span-1">
                <el-form-item :label="$t('sidebar.system')" class="title--bold" prop="system_id" :error="getError('system_id')"
                              :inline-message="hasError('system_id')">
                    <el-select
                        v-model="formData.system_id"
                        placeholder="Select"
                        size="large"
                        clearable
                    >
                        <el-option
                            v-for="system in systems"
                            :key="system.id"
                            :label="system.name"
                            :value="system.id"
                        />
                    </el-select>
                </el-form-item>
            </div>
        </el-form>
    </div>
</template>

<script>
import form from "@/Mixins/form.js";
import axios from "@/Plugins/axios.js";
import baseRuleValidate from "@/Store/Const/baseRuleValidate.js";

export default {
    mixins: [form],
    props: {
        id: {
            type: Number,
            default: () => null,
        },
    },
    data() {
        return {
            systems: [],
            formData: {
                id: this.props?.id,
                name: null,
                code: null,
                system_id: null,
            },
            rules: {
                name: baseRuleValidate(this.$t),
                code: baseRuleValidate(this.$t),
                system_id: baseRuleValidate(this.$t),
            },
            loadingForm: false,
        };
    },
    created() {
        Promise.all([this.fetchData(), this.getAllSystem()]);
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get(
                    this.appRoute("admin.api.subsystem.show", this.id)
                );
                if(response) {
                    this.formData = {
                        ...response?.data?.data,
                        system_id: response?.data?.data?.system?.id,
                    };
                }
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            }
        },
        goBack() {
            this.$inertia.visit(this.appRoute("admin.subsystem.index"));
        },
        async getAllSystem() {
            try {
                const response = await axios.get(this.appRoute('admin.api.system.index'))
                this.systems = response?.data?.data
            } catch (error) {
                this.$message.error(error?.response?.data?.message)
            }
        },
        async submit() {
            this.loadingForm = true;
            const response = await axios.put(
                this.appRoute("admin.api.subsystem.update", this.id),this.formData
            );
            this.$message.success(response?.data?.message);
            this.loadingForm = false;
        },
    },
}
</script>
