<template>
    <div>
        <div class="w-full my-[15px] flex justify-start items-center">
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
                <el-form-item :label="$t('input.common.email')" class="title--bold" prop="email" :error="getError('email')"
                              :inline-message="hasError('email')">
                    <el-input size="large" v-model="formData.email" clearable />
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
                email: null,
            },
            rules: {
                name: baseRuleValidate(this.$t),
                email: baseRuleValidate(this.$t),
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
            this.$inertia.visit(this.appRoute("admin.role.index"));
        },
        async submit() {
            this.loadingForm = true;
            const response = await axios.put(
                this.appRoute("admin.api.user.update", this.id),this.formData);
            this.$message.success(response?.data?.message);
            this.loadingForm = false;
            this.actions = [];
        },
    },
}
</script>
