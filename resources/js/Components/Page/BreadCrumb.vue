<template>
    <div class="text-[14px] py-[4px]">
        <div class="flex items-center">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item v-for="(item, key) in breadCrumb" :key="key">
                    <span
                        v-if="key == 2 || (key == 1 && breadCrumb.length == 2)"
                          :class="{
                            'is-show': key == 2 || (key == 1 && breadCrumb.length == 2)
                        }"
                    >
                        <!--{{ truncateString(item.name, 15) }}-->
<!--                        {{ $t(item?.name) }}-->
                        {{item?.name}}
                    </span>
                    <a
                        v-else
                        class="!cursor-pointer"
                        @click="changePath(item.route)"
                    >
                        <!--{{ truncateString(item.name, 15) }}-->
                        {{ $t(item.name) }}
                    </a>
                </el-breadcrumb-item>
            </el-breadcrumb>
        </div>
    </div>
</template>

<script>
export default {
    name: 'BreadCrumbComponent',
    props: {
        breadCrumb: {
            type: Object || Array,
            default: () => {},
        },
    },
    methods: {
        changePath(value) {
            if (value) {
                this.$inertia.visit(value)
            }
        },
        truncateString(str, maxLength) {
            if (String(str).length > maxLength) {
                return str.substring(0, maxLength) + '...'
            }
            return str
        },
    },
}
</script>
<style>
.admin-layout .el-breadcrumb {
    font-size: unset !important;
}
.admin-layout .el-breadcrumb .el-breadcrumb__item .el-breadcrumb__inner a {
    color: #55595c !important;
    font-weight: bold !important;
}
.admin-layout .el-breadcrumb .el-breadcrumb__item .el-breadcrumb__inner {
    font-weight: unset !important;
}
.admin-layout .el-breadcrumb .el-breadcrumb__item span.el-breadcrumb__inner span.is-show {
    color: var(--tw-primary) !important;
    font-weight: unset !important
}
.admin-layout .el-breadcrumb .el-breadcrumb__item .el-breadcrumb__inner a:hover {
    opacity: 0.85 !important;
}
</style>
