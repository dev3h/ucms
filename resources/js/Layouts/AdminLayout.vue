<template>
    <div class="admin-layout">
        <AdminHeader />

        <el-container>
            <Sidemenu v-if="isShowSideBar" @zoom-out-sidebar="hiddenMenu" />

            <el-container>
                <el-main
                    id="el-main"
                    style="background-color: #e9e9e9"
                    :class="{
                        'ml-[285px]': isZoomOutMenu && isShowSideBar,
                        'ml-[54px]': !isZoomOutMenu && isShowSideBar,
                    }"
                >
                    <slot />
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>

<script>
import AdminHeader from './Partials/Header.vue'
import Sidemenu from './Partials/Sidemenu.vue'

export default {
    components: {
        AdminHeader,
        Sidemenu,
    },
    props: {
        isShowSideBar: {
            type: Boolean,
            default: () => {
                return true
            },
        },
    },
    data() {
        return {
            isZoomOutMenu: true,
        }
    },
    methods: {
        hiddenMenu(value) {
            this.isZoomOutMenu = value
        },
    },
}
</script>
<style>
.admin-layout {
    font-family: 'Roboto', sans-serif;
    font-style: normal;
}

.admin-layout .el-aside {
    height: calc(100vh - 60px);
    /* border-right: 1px solid #d9d9d9; */
}

.admin-layout > .el-container {
    padding-top: 60px;
    min-height: 100vh;
}

.admin-layout .el-menu {
    border-right: none;
}

.admin-sidebar {
    max-height: calc(100vh);
    overflow-y: auto;
}

.admin-layout .el-menu {
    background-color: var(--tw-white) !important;
}

.admin-layout .el-sub-menu {
    margin-bottom: 10px;
}

.admin-layout .el-menu-item,
.admin-layout .el-sub-menu__title {
    color: var(--tw-black-400);
    --el-menu-item-height: 64px;
    font-weight: 500;
    font-size: 16px;
}

.admin-layout .el-sub-menu__title:hover {
    color: var(--tw-gray-300);
}

.admin-layout .el-menu-item.is-active {
    color: var(--tw-primary) !important;
    background-color: var(--tw-gray-300);
}

.admin-layout .el-sub-menu.is-active .el-sub-menu__title {
    color: var(--tw-gray-300) !important;
}

.admin-layout .submenu-inner {
    margin-left: 25px;
    border-left: 1px solid #d0d5dd;
}

.admin-layout .submenu-wrapper .submenu-inner .el-menu-item {
    padding-left: 5px !important;
    margin-left: 10px !important;
}

.admin-layout .el-sub-menu .el-menu-item {
    height: 34px;
    margin-bottom: 8px;
}

.admin-layout .el-menu-vertical {
    width: 283px;
}

.el-menu--collapse {
    width: 55px !important;
    padding: 0 !important;
}

.el-menu--collapse.collapse-is-close .el-menu-item .el-menu-tooltip__trigger,
.el-menu--collapse.collapse-is-close .el-sub-menu__title.el-tooltip__trigger {
    padding: 0 !important;
    display: flex;
    justify-content: center;
}

.admin-layout .el-menu-item {
    padding-left: 1.25rem !important;
}

.el-menu-vertical .el-submenu__title {
    display: flex;
    align-items: center;
    padding-left: 1.25rem !important;
}

.admin-layout .el-menu-item.is-active {
    color: var(--tw-black-400);
}

.admin-layout .el-menu-item.is-active .icon,
.admin-layout .el-sub-menu.is-opened .icon,
.admin-layout .el-sub-menu:hover .icon,
.admin-layout .el-sub-menu:focus .icon,
.admin-layout .el-menu-item:hover .icon,
.admin-layout .el-menu-item:focus .icon {
    filter:invert(19%) sepia(39%) saturate(2651%) hue-rotate(165deg) brightness(45%) contrast(102%)
}

.admin-layout .el-sub-menu .el-menu-item {
    color: black;
}

.admin-layout .el-sub-menu.is-active .el-menu-item.is-active {
    color: var(--tw-primary);
    background: var(--tw-blue-300);
}

.admin-layout .el-menu--popup-container .el-menu-item.is-active {
    --tw-pink-dark-color: #ff4d4f;
    color: var(--tw-pink-dark-color);
}

.admin-layout .el-submenu .el-menu-item {
    padding-right: 1.25rem;
}

.admin-layout .el-menu-item:hover,
.admin-layout .el-sub-menu.is-active.is-opened .el-menu-item.is-active {
    color: var(--tw-primary);
    cursor: pointer;
}

.admin-layout .el-sub-menu.is-opened,
.admin-layout .el-sub-menu.is-opened .submenu-wrapper,
.admin-layout .el-sub-menu.is-opened ul {
    color: var(--tw-primary);
    background: #ffffff;
}

.admin-layout .el-sub-menu.is-opened .submenu-wrapper {
    margin-bottom: 5px;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

.admin-layout .el-sub-menu.is-opened {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.admin-layout .el-sub-menu.is-opened .el-sub-menu__title {
    color: var(--tw-primary);
}

.admin-layout .el-menu-item:focus {
    cursor: pointer;
}

.admin-layout .el-menu--collapse .menu-item-text {
    display: none;
}

.admin-layout .el-menu--collapse .el-menu-item {
    padding: 0 !important;
    justify-content: center;
}

.admin-layout .el-menu--collapse .el-sub-menu.menu-item-custom {
    justify-content: center;
}

.admin-layout .collapse-is-close .el-sub-menu.is-active .el-sub-menu__title {
    padding-left: 8px;
}

.admin-layout .collapse-is-close .el-sub-menu__title:hover {
    background: none;
    color: var(--tw-pink-dark-color);
}

.admin-layout .el-image__wrapper {
    position: relative;
}

.admin-layout .el-menu-item-group__title {
    padding: 0 !important;
}

.admin-layout .header-custom {
    position: absolute;
    right: 0;
    top: 15px;
}

.admin-layout .el-menu--collapse .el-sub-menu.is-active {
    background: var(--tw-gray-300);
    border-radius: 8px;
    background-color: white;
}

.admin-layout .el-menu--collapse .el-sub-menu.is-active .icon {
    filter: invert(44%) sepia(86%) saturate(552%) hue-rotate(169deg) brightness(83%) contrast(97%);
}

.admin-layout #el-main {
    padding: 0 0 0 16px !important;
}
</style>
