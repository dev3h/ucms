<template>
    <AdminLayout>
        <div class="w-full h-full bg-white px-4 pb-[24px]">
            <div class="w-full pt-3 pb-2 border-b-[1px]">
                <BreadCrumbComponent :bread-crumb="setbreadCrumbHeader" />
            </div>
            <div class="mt-3 flex flex-col gap-[32px]">
                <div class="w-full grid grid-cols-3 gap-6">
                    <div class="col-span-1">
                        <h4 class="font-bold">Publish at:</h4>
                        <p>{{ data?.is_schedule == 1 ? data?.published_at : data?.created_at }}</p>
                    </div>
                    <div v-if="data?.published_end_at" class="col-span-1">
                        <h4 class="font-bold">Publish end at:</h4>
                        <p>{{ data?.published_end_at }}</p>
                    </div>
                </div>
                <div v-if="!(data?.sender_type == 2 && data?.users?.length == 0)">
                    <h4 class="font-bold">Send to:</h4>
                    <div v-if="data?.sender_type == 1">
                        All users
                    </div>
                    <div v-else class="mt-1 flex flex-wrap gap-[8px]">
                        <div
                            v-for="(item, index) in data?.users" :key="index"
                            class="bg-[#F5F5F5] px-[12px] py-[4px] rounded-[12px] text-[14px]"
                        >
                            {{ item.nickname }}
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold">Title:</h4>
                    <p>{{ data?.title }}</p>
                </div>
                <div>
                    <h4 class="font-bold">Content:</h4>
                    <ContentCkeditor :content="data?.content" />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
<script>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import BreadCrumbComponent from '@/Components/Page/BreadCrumb.vue';
import { searchMenu } from '@/Mixins/breadcrumb.js'
import axios from '@/Plugins/axios'
import ContentCkeditor from '@/Components/Ckediter/ContentCkeditor.vue';

export default {
    name: "NotificationShow",
    components: { AdminLayout, BreadCrumbComponent, ContentCkeditor },
    data() {
        return {
            data: {
                id: this.idAccount,
                name: null,
                email: null,
                role: null,
                created_at: null
            },
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
                    name: this.data?.title,
                    route: '',
                },
            ]

        }
    },
    async created() {
        await this.fetchData()
    },
    methods: {
        async fetchData() {
            await axios.get(this.appRoute("admin.api.notification.show", this.appRoute().params.id))
                .then(({ data }) => {
                    this.data = data?.data
                })
        },
    }
}
</script>
<style></style>
