<template>
    <el-drawer v-model="drawer" custom-class="custom-class" @close="handleClosedNotification">
        <template #header>
            <div class="font-semibold text-2xl text-[#202020]">通知</div>
        </template>
        <template #default>
            <NotificationList v-if="notifications.length > 0" :unread="notifications_unread"
                :is-load-more="paginate.current_page < paginate.last_page" @load-more="handleLoadMore">
                <NotificationItem v-for="notification in notifications" :key="notification.id"
                    :notification="notification" :admin="admin" @notification-read="handleReadNotification" />
            </NotificationList>
            <template v-else>
                <el-empty description="データはありません。" />
            </template>
        </template>
    </el-drawer>
</template>

<script>
import NotificationList from '@/Components/Notification/NotificationList.vue'
import NotificationItem from '@/Components/Notification/NotificationItem.vue'
import axios from '@/Plugins/axios'
import { ElNotification } from 'element-plus'

export default {
    components: { NotificationList, NotificationItem },
    props: {
        direction: {
            type: String,
            default: 'ltr',
        },
        admin: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['update-unread'],
    data() {
        return {
            pathSubmenu: window.location.pathname.split('/'),
            drawer: false,
            notifications: [],
            notifications_unread: 0,
            filter: {
                page: 1,
                limit: 20,
            },
            paginate: {}
        }
    },
    created() {
        let userId = this.$page?.props?.auth?.user?.id
        this.fetchDataNotifications()
        // Echo.private(`App.Models.Admin.${userId}`).notification((notification) => {
        //     ElNotification({
        //         title: '通知',
        //         message: `${notification?.content}`,
        //         duration: 1500
        //     })

        //     // if (type === 'App\\Notifications\\RegisterBusinessNotification') {
        //     //     this.notifications.unshift(notice)
        //     //     this.notifications_unread++
        //     //     this.$emit('update-unread', this.notifications_unread)
        //     //     ElNotification({
        //     //         title: '通知',
        //     //         message: `${notice?.data?.content}`,
        //     //         duration: 1500
        //     //     })
        //     // }
        // })
    },
    methods: {
        toggle() {
            this.drawer = true
        },
        fetchDataNotifications() {
            // axios.get(this.appRoute('master.api.notification.index', this.filter))
            //     .then(({ data }) => {
            //         this.paginate = data?.meta
            //         this.notifications_unread = data?.notifications_unread
            //         this.$emit('update-unread', this.notifications_unread)
            //         data.data.forEach(item => {
            //             this.notifications.push(item)
            //         })
            //     })
        },
        handleReadNotification(notification) {
            let notice = this.notifications.find((item) => item.id == notification.id)
            notice.is_read = notification.is_read
            notice.is_read_at = notification.is_read_at
            this.notifications_unread -= 1
        },
        handleClosedNotification() {
            this.$emit('update-unread', this.notifications_unread)
        },
        handleLoadMore() {
            this.filter.page++
            this.fetchDataNotifications()
        },
    },
}
</script>

<style>
.el-overlay {
    /* z-index: 2002 !important; */
}

.el-drawer__header {
    margin-bottom: 10px !important;
}

@media screen and (max-width: 1028px) {
    .el-drawer {
        width: 50% !important;
    }
}

@media screen and (max-width: 767px) {
    .el-drawer {
        width: 100% !important;
    }
}
</style>
