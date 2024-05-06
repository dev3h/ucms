<template>
    <el-header class="bg-primary text-[white] fixed top-0 left-0 right-0 z-[10]">
        <div class="h-full flex items-center">
            <Link :href="getRouteRedirect" class="inline-block w-fit">
                <img src="/images/logo-white.svg" alt="logo" class="!h-12 object-cover" />
            </Link>
        </div>
        <slot name="page-header" />
        <div class="header-custom w-1/2 flex justify-end gap-6">
            <div v-if="pathSubmenu[1] === 'master'" id="notification-button"
                class="ml-auto flex items-center px-2 cursor-pointer relative" @click="toggleNotifications()">
                <span v-if="notifications_unread > 0"
                    class="text-[11px] leading-[10px] rounded-full absolute right-0 top-0 p-1 bg-red-500 text-white mx-auto">
                    {{ notifications_unread > 99 ? '99+' : notifications_unread }}
                </span>
                <img :src="'/images/svg/notification-icon.svg'" class="ml-2" />
            </div>
            <div class="mr-5">
                <el-dropdown trigger="click" class="h-full" @command="handleCommand">
                    <div class="el-dropdown-link flex items-center justify-center text-white">
                        <span class="mr-2 text-lg">{{ user?.name }}</span>
<!--                        <el-avatar :size="32" :src="user?.icon_url" />-->
                        <img :src="'/images/svg/down.svg'" class="ml-2" />
                    </div>
                    <template #dropdown>
                        <el-dropdown-menu class="w-48 !p-4">
                            <el-dropdown-item command="changeProfile">
                                <div class="flex items-center">
                                    <img src="/images/svg/profile-icon.svg" class="mr-2" />
                                    <span class="whitespace-nowrap">Profile</span>
                                </div>
                            </el-dropdown-item>
                            <el-dropdown-item command="changeApplication">
                                <div class="flex items-center">
                                    <img src="/images/svg/application-icon.svg" class="mr-2" />
                                    <span class="whitespace-nowrap">Application</span>
                                </div>
                            </el-dropdown-item>
                            <el-dropdown-item command="logout">
                                <div class="flex items-center" @click="dialogVisible = true">
                                    <img src="/images/svg/log-out-icon.svg" class="mr-2" />
                                    <span class="whitespace-nowrap mt-1">Logout</span>
                                </div>
                            </el-dropdown-item>
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>
        </div>
    </el-header>
    <NotificationPopup ref="notificationPopup" @update-unread="handleUpdateUnread" />
</template>

<script>
import NotificationPopup from '@/Components/Notification/Index.vue'
import { router } from '@inertiajs/vue3'

export default {
    name: 'AdminHeader',
    components: { NotificationPopup },
    props: {
        breadCrumb: {
            type: Object || Array,
            default: () => { },
        },
    },
    data() {
        return {
            pathSubmenu: window.location.pathname.split('/'),
            notifications: [],
            notifications_unread: 0,
            paginate: [],
            filter: {
                page: 1,
                limit: 20,
            },
        }
    },
    computed: {
        user() {
            return this.$page?.props?.auth?.user ?? {}
        },
        getRouteRedirect() {
            return this.appRoute('admin.system.index')
        },
    },
    methods: {
        toggleNotifications() {
            this.$refs.notificationPopup.toggle()
        },
        handleUpdateUnread(value) {
            this.notifications_unread = value
        },
        openProfileForm() {
            this.$inertia.visit(this.appRoute('admin.profile'))
        },
        openApplicationForm() {
            this.$inertia.visit(this.appRoute('admin.application'))
        },
        openPasswordForm() {
            this.$inertia.visit(this.appRoute('admin.form-change-password'))
        },
        async doLogout() {
            router.post(this.appRoute('admin.logout'))
        },
        handleCommand(command) {
            switch (command) {
                case 'logout':
                    this.doLogout()
                    break
                case 'changeProfile':
                    this.openProfileForm()
                    break
                case 'changePassword':
                    this.openPasswordForm()
                    break
                case 'changeApplication':
                    this.openApplicationForm()
                    break
                default:
                    break
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
.el-dropdown-menu .el-dropdown-menu__item {
    padding: 9px 0 !important;
}

.el-dropdown-menu .el-dropdown-menu__item:not(:last-child) {
    border-bottom: 1px solid #D9D9D9 !important;
}
</style>
