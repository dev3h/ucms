<template>
    <div
        class="notification-item border border-gray-E9 p-4 cursor-pointer mt-3 first:mt-0 shadow-sm hover:bg-neutral-100
        transition-all duration-300"
        :class="{
            'font-semibold': !notification?.is_read,
        }"
        @click.prevent="onMarkAsRead()"
    >
        <div class="notification-created text-xs flex gap-x-[6px]">
            <img src="/images/svg/clock.svg" alt="">
            <div>
                <span>{{ notification?.created_at }}</span>
                <span
                    v-if="!notification?.is_read"
                    class="notification_unread ml-2 w-2 h-2 rounded-full bg-green2D inline-block"
                />
            </div>
        </div>
        <div v-if="notification?.data?.content" class="notification-content mt-2 text-base">
            {{ notification?.data?.content }}
        </div>
    </div>
</template>

<script>
import axios from '@/Plugins/axios'

export default {
    props: {
        notification: {
            type: Object,
            default: () => {},
        },
        admin: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['notification-read'],
    methods: {
        onMarkAsRead() {
            if (this.notification?.is_read) {
                this.$inertia.visit(this.appRoute('master.company.show', this.notification?.data?.company_id))
                return
            }
            axios.get(this.appRoute('master.api.notification.read', this.notification?.id))
                .then(({data}) => {
                    this.$emit('notification-read', data?.data)
                })
                .catch((error) => {
                    this.$message({ message: error?.message, type: 'error' })
                })
            this.$inertia.visit(this.appRoute('master.company.show', this.notification?.data?.company_id))
        },
    },
}
</script>
