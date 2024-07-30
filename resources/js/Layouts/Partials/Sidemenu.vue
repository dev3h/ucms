<template>
    <el-aside width="265" class="fixed top-0 bottom-0 left-0 z-[5] !h-full">
        <div class="h-[60px] shadow-md fixed top-0 left-0 z-[10] bg-primary !backdrop-blur-2xl" :class="collapseAside ? 'w-[283px]' : 'w-[55px]'">
            <Link :href="getRouteRedirect" class="flex items-center h-full w-full gap-2">
                <img :src="collapseAside ? '/images/EPU.png' : '/images/logo-small.png'" alt="logo" class="object-cover" />
            </Link>
        </div>
        <div class="relative h-full bg-primary">
            <!-- Side bar -->
            <div class="admin-sidebar h-full relative pt-[60px]">
                <el-menu id="sidebar" class="el-menu-vertical" :class="{ 'collapse-is-close': !collapseAside }"
                         :default-active="defaultActive" :default-openeds="openMenus" :router="true"
                         :collapse="!collapseAside">
                    <template v-for="(menu, index) in menus" :key="index">
                        <el-menu-item v-if="!menu.subMenus" :index="menu.pathActive" class="menu-item-custom"
                                      :class="checkActive(menu.pathActive) ? 'is-active ' : ''" @click="onMenuClick(menu)">
                            <el-image class="icon w-6 h-6 object-cover" :src="`/images/aside/${menu.icon}`" alt="" />
                            <template #title>
                                <span class="menu-item-text pl-2 font-bold">{{ $t(menu.label) }}</span>
                            </template>
                        </el-menu-item>

                        <el-sub-menu v-else :index="menu.pathActive" class="menu-item-custom"
                                     :class="checkActive(menu.pathActive) ? 'is-active ' : ''"
                                     popper-class="border border-[#d0d5dd]">
                            <template #title>
                                <el-image class="icon w-6 h-6 object-cover" :src="`/images/aside/${menu.icon}`" alt="" />
                                <span class="menu-item-text pl-2">{{ $t(menu.label) }}</span>
                            </template>
                            <div class="submenu-wrapper">
                                <div class="submenu-inner">
                                    <template v-for="(subMenu, i) in menu.subMenus" :key="i">
                                        <el-menu-item :index="subMenu.pathActive" class="flex items-center"
                                                      :class="subMenu.pathActive == pathSubmenuItem || subMenu.pathActive2 == pathSubmenuItem ? 'is-active' : ''"
                                                      @click="onMenuClick(subMenu)">
                                            <el-image class="icon w-6 h-6 object-cover" :src="`/images/aside/${subMenu.icon}`" alt="" />
                                            <template #title>
                                                <span class="menu-item-text pl-5">{{$t(subMenu.label) }}</span>
                                            </template>
                                        </el-menu-item>
                                    </template>
                                </div>
                            </div>
                        </el-sub-menu>
                    </template>
                </el-menu>
            </div>
        </div>
    </el-aside>
</template>

<script>
import { MASTER_MENUS, APP_MENUS } from '@/Store/Const/menu.js'
import { isArray, isEmpty } from 'lodash'

export default {
    emits: ['zoom-out-sidebar'],
    props: {
        collapseAside: Boolean,
    },
    data() {
        return {
            menus: MASTER_MENUS,
            defaultActive: '',
            dialogVisible: false,
            pathSubmenu: [],
            pathSubmenuItem: '',
            openMenus: [], // New data property to store open menus
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
            this.menus = menuBusiness
        }
        this.openMenus = this.menus.map(menu => menu.pathActive) // Initialize openMenus with all menu paths
    },
    computed: {
        getRouteRedirect() {
            return this.appRoute('admin.system.index')
        },
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
            this.$inertia.visit(this.appRoute(menu.route), {replace: true})
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
