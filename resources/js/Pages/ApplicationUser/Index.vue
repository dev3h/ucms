<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-16 py-8">
            <div class="grid lg:grid-cols-3 grid-cols md:grid-cols-2 gap-2 gap-5">
                <el-card v-for="application in userApplications" :key="application?.id">
                    <template #header>
                        <div class="card-header">
                            <span class="font-bold">{{ application?.name }}</span>
                        </div>
                    </template>
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
