<template>
    <el-aside width="265" class="fixed top-[60px] bottom-0 left-0 z-[5]">
        <div class="relative h-full bg-[#fff]">
            <!-- Side bar -->
            <div class="admin-sidebar h-full relative pt-[60px]">
                <el-menu id="sidebar" class="el-menu-vertical" :class="{ 'collapse-is-close': !collapseAside }"
                    :default-active="defaultActive" :default-openeds="pathSubmenu" :unique-opened="true" :router="true"
                    :collapse="!collapseAside">
                    <template v-for="(menu, index) in menus" :key="index">
                        <el-menu-item v-if="!menu.subMenus" :index="menu.pathActive" class="menu-item-custom"
                            :class="checkActive(menu.pathActive) ? 'is-active ' : ''" @click="onMenuClick(menu)">
                            <el-image class="icon w-6 h-6 object-cover" :src="`/images/aside/${menu.icon}`" alt />
                            <template #title>
                                <span class="menu-item-text pl-2 font-bold">{{ menu.label }}</span>
                            </template>
                        </el-menu-item>

                        <el-sub-menu v-else :index="menu.pathActive" class="menu-item-custom"
                            :class="checkActive(menu.pathActive) ? 'is-active ' : ''"
                            popper-class="ml-[25px] border border-[#d0d5dd]">
                            <template #title>
                                <el-image v-if="menu.icon" class="icon w-6 h-6 object-cover"
                                    :src="`/images/aside/${menu.icon}`" alt />
                                <el-image v-else class="icon w-6 h-6 object-cover" />
                                <span class="menu-item-text pl-2">{{ menu.label }}</span>
                            </template>
                            <div class="submenu-wrapper">
                                <div class="submenu-inner">
                                    <template v-for="(subMenu, i) in menu.subMenus" :key="i">
                                        <el-menu-item :index="subMenu.pathActive" class="flex items-center" :class="subMenu.pathActive == pathSubmenuItem ||
                        subMenu.pathActive2 == pathSubmenuItem
                        ? 'is-active'
                        : ''
                        " @click="onMenuClick(subMenu)">
                                            <template #title>
                                                <span class="menu-item-text pl-5">{{ subMenu.label }}</span>
                                            </template>
                                        </el-menu-item>
                                    </template>
                                </div>
                            </div>
                        </el-sub-menu>
                    </template>
                </el-menu>
            </div>

            <div class="absolute left-[0] pl-[16px] top-[0] right-[0] pt-[16px] bg-[white] cursor-pointer"
                :class="!collapseAside ? 'right-4' : 'right-8'">
                <span v-if="!collapseAside" title="メニューを拡大する" @click.prevent="toggleCollapse">
                    <i class="ri-menu-unfold-line text-2xl text-primary" />
                </span>

                <span title="メニューを縮小する" @click.prevent="toggleCollapse">
                    <i v-if="collapseAside" class="ri-menu-fold-line text-3xl text-gray8A" />
                </span>
            </div>
        </div>
    </el-aside>
</template>

<script>
import { MASTER_MENUS, APP_MENUS } from '@/Store/Const/menu.js'
import { isArray, isEmpty } from 'lodash'

export default {
    emits: ['zoom-out-sidebar'],
    data() {
        return {
            menus: MASTER_MENUS,
            collapseAside: true,
            defaultActive: '',
            dialogVisible: false,
            pathSubmenu: [],
            pathSubmenuItem: '',
        }
    },
    created() {
        this.getCurrentUrl()
        if (this.pathSubmenu[1] === 'admin') {
            this.menus = MASTER_MENUS
        } else {
            let menuBusiness = APP_MENUS
            let excludeList = ['initial-message', 'message-advice', 'data-management', 'setting']
            if (this.$page?.props?.auth?.role !== 'admin_enterprise') {
                menuBusiness = menuBusiness.filter((item) => !excludeList.includes(item.pathActive))
            }
            // console.log(menuBusiness)
            this.menus = menuBusiness
        }
    },
    methods: {
        getCurrentUrl() {
            const pathname = window.location.pathname.split('/')
            const menus = this.menus.map((item) => item.pathActive)
            let currentUrl = pathname.filter((element) => menus.includes(element)).toString()

            this.defaultActive = currentUrl || 'dashboard'
            this.pathSubmenu = window.location.pathname.split('/')
            if (this.pathSubmenu[2] == 'my-account') {
                this.defaultActive = ''
            }
            this.pathSubmenuItem = window.location.pathname?.split('/')?.slice(0, 4).join('/')
        },
        toggleCollapse() {
            this.collapseAside = !this.collapseAside
            this.$emit('zoom-out-sidebar', this.collapseAside)
        },
        onMenuClick(menu) {
            this.$inertia.visit(this.appRoute(menu.route), { replace: true })
        },
        checkActive(pathActive) {
            if (isArray(pathActive) && !isEmpty(pathActive)) {
                return pathActive.includes(this.pathSubmenu[1])
            }
            return pathActive == this.pathSubmenu[1]
        },
    },
}
</script>
