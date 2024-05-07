<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-16 py-8">
            <div class="grid grid-cols-3 gap-2">
                <el-card class="mb-8" v-for="application in userApplications" :key="application?.id">
                    <template #header>
                        <div class="card-header">
                            <span class="font-bold">{{ application?.name }}</span>
                        </div>
                    </template>
                    <div>
                        <h3 class="font-bold">Permission</h3>
                        <ul class="ml-2 mt-2 list-disc">
                            <li v-for="(permission, index) in application?.permissions" :key="index">
                                <span>{{ permission }}</span>
                            </li>
                        </ul>
                    </div>
                </el-card>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";

export default {
    components: {AdminLayout},
    props: {
        userApplications: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {

        }
    },
    computed: {
        user() {
            return this.$page.props.auth.user;
        }
    },
}
</script>

<style lang="scss" scoped>

</style>
