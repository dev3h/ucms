<template>
    <div>
        <div class="w-full my-[15px] flex justify-start items-center">
            <el-button type="primary" size="large" @click="doSubmit()" :loading="loadingForm">Update</el-button>
            <el-button type="info" size="large" @click="goBack()">Back</el-button>
        </div>
        <el-form class="w-full grid grid-cols-3 gap-5" ref="form" :model="formData" :rules="rules"
                 label-position="top">

            <div class="col-span-1">
                <el-form-item label="Name" class="title--bold" prop="name" :error="getError('name')"
                              :inline-message="hasError('name')">
                    <el-input size="large" v-model="formData.name" clearable />
                </el-form-item>
            </div>

            <div class="col-span-1">
                <el-form-item label="Code" class="title--bold" prop="code" :error="getError('code')"
                              :inline-message="hasError('code')">
                    <el-input size="large" v-model="formData.code" clearable />
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
            formData: {
                id: this.props?.id,
                name: null,
                code: null,
            },
            rules: {
                name: baseRuleValidate,
                code: baseRuleValidate,
            },
            loadingForm: false,
        };
    },
    created() {
        this.fetchData();
    },
    methods: {
        async fetchData() {
            try {
                const response = await axios.get(
                    this.appRoute("admin.api.role.show", this.id)
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
        async submit() {
            this.loadingForm = true;
            const response = await axios.put(
                this.appRoute("admin.api.role.update", this.id),
                {
                    ...this.formData,
                    actions: this.actions
                }
            );
            this.$message.success(response?.data?.message);
            this.loadingForm = false;
            this.actions = [];
        },
    },
}
</script>
