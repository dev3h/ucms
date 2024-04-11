<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4">

            <div>
                <h2 class="uppercase font-bold mb-4">Linked Accounts</h2>
                <div>
                    <div>
                        <h3 class="text-lg mb-2">Google</h3>
                        <div class="flex items-center gap-2">
                            <img src="/images/logo_google.png" alt="" width="30" height="30" class="object-cover">
                           <div>
                                <span v-if="isLinked('google')">
                                    Linked
                                    Email link: {{ emailLink('google') }}
                                </span>
                               <span v-else>
                                Not Link
                                </span>
                           </div>
                            <div>
                                <el-button type="primary" v-if="isLinked('google')" @click="handleUnlinkIntegrationSocialite('google')">Unlink</el-button>
                                <a :href="route('admin.integration.socialite.redirect', 'google')" v-else>
                                    <el-button type="info">
                                        Link
                                    </el-button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import axios from '@/Plugins/axios.js';

export default {
    components: { AdminLayout},
    data() {
        return {
            linkedAccounts: []
        }
    },
    watch: {
        '$page.props.errors': {
            immediate: true,
            handler(value) {
                if (value && Object.keys(value).length > 0) {
                    this.$message.error(Object.values(value).join(', '));
                }
            }
        }
    },
    created() {
        this.getAllLinkedAccounts();
    },
    methods: {
        async getAllLinkedAccounts() {
            try {
                const response = await axios.get(this.appRoute('admin.api.get-all-integration-socialite'));
                this.linkedAccounts = response?.data?.data;
            } catch (err) {
                this.$message.error(err?.response?.data?.message);
            }
        },
        isLinked(provider) {
            const findIndexProvider = this.linkedAccounts?.findIndex(item => item?.provider_type === provider);
            return findIndexProvider !== -1;
        },
        emailLink(provider) {
            const findIndexProvider = this.linkedAccounts?.findIndex(item => item?.provider_type === provider);
            return this.linkedAccounts[findIndexProvider]?.email;
        },
        async handleUnlinkIntegrationSocialite(provider) {
            try {
                const findIndexProvider = this.linkedAccounts?.findIndex(item => item?.provider_type === provider);
                const response = await axios.delete(this.appRoute('admin.api.unlink-integration-socialite', this.linkedAccounts[findIndexProvider]?.provider_id));
                if(response) {
                    this.$message.success(response?.data?.message);
                    await this.getAllLinkedAccounts();
                }
            } catch (err) {
                console.log(err);
                this.$message.error(err?.response?.data?.message);
            }
        }
    }
}
</script>

<style lang="scss" scoped>

</style>
