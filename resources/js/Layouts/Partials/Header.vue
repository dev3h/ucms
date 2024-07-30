<template>
    <el-header class="bg-white text-[white] fixed top-0 right-0 z-[10] !backdrop-blur-lg shadow-sm" :class="{
                        'left-[285px]':  collapseAside,
                        'left-[54px]': !collapseAside,
                    }">
        <div class="flex items-center gap-2 w-fit h-full">
            <span @click.prevent="toggleCollapse" class="cursor-pointer flex items-center h-full">
            <i :class="collapseAside ? 'ri-menu-fold-line' : 'ri-menu-unfold-line'" class="text-3xl text-primary"/>
            </span>
        </div>
        <slot name="page-header" />
        <div class="header-custom w-1/2 flex justify-end gap-6">
            <div v-if="pathSubmenu[1] === 'admin'" id="notification-button"
                class="ml-auto flex items-center px-2 cursor-pointer relative" @click="toggleNotifications()">
                <span v-if="notifications_unread > 0"
                    class="text-[11px] leading-[10px] rounded-full absolute right-0 top-0 p-1 bg-red-500 text-white mx-auto">
                    {{ notifications_unread > 99 ? '99+' : notifications_unread }}
                </span>
                <img :src="'/images/svg/notification-icon.svg'" class="ml-2" alt="" />
            </div>
            <div class="mr-5">
                <el-dropdown trigger="click" class="h-full" @command="handleCommand">
                    <div class="el-dropdown-link flex items-center justify-center text-white gap-1">
                        <el-avatar :size="32">{{user?.name?.[0]}}</el-avatar>
                        <span class="mr-2 text-lg text-primary">{{ user?.name }}</span>
                        <img :src="'/images/svg/down.svg'" class="ml-2" alt="" />
                    </div>
                    <template #dropdown>
                        <el-dropdown-menu class="w-48 !p-4">
                            <el-dropdown-item command="changeProfile">
                                <div class="flex items-center">
                                    <img src="/images/svg/profile-icon.svg" class="mr-2" alt="" />
                                    <span class="whitespace-nowrap">{{$t('pop-menu.profile')}}</span>
                                </div>
                            </el-dropdown-item>
                            <el-dropdown-item command="changeApplication">
                                <div class="flex items-center">
                                    <img src="/images/svg/application-icon.svg" class="mr-2" alt="" />
                                    <span class="whitespace-nowrap">{{$t('pop-menu.application')}}</span>
                                </div>
                            </el-dropdown-item>
                            <el-dropdown-item command="logout">
                                <div class="flex items-center" @click="dialogVisible = true">
                                    <img src="/images/svg/log-out-icon.svg" class="mr-2" alt="" />
                                    <span class="whitespace-nowrap mt-1">{{$t('pop-menu.logout')}}</span>
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
import BreadCrumbComponent from "@/Components/Page/BreadCrumb.vue";

export default {
    name: 'AdminHeader',
    components: {BreadCrumbComponent, NotificationPopup },
    props: {
        collapseAside: Boolean
    },
    emits: ['toggle-collapse-aside'],
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
        }
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
        toggleCollapse() {
            this.$emit('toggle-collapse-aside', !this.collapseAside)
        }
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
