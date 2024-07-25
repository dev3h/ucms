export const MASTER_MENUS = [
    {
        label: 'sidebar.dashboard',
        icon: "dashboard-icon.svg",
        route: "admin.dashboard.index",
        pathActive: "dashboard",
    },
    {
        label: 'sidebar.system',
        icon: "system-icon.svg",
        route: "admin.system.index",
        pathActive: "system",
    },
    {
        label: 'sidebar.subsystem',
        icon: "subsystem-icon.svg",
        route: "admin.subsystem.index",
        pathActive: "subsystem",
    },
    {
        label: 'sidebar.module',
        icon: "module-icon.svg",
        route: "admin.module.index",
        pathActive: "module",
    },
    {
        label: 'sidebar.action',
        icon: "action-icon.svg",
        route: "admin.action.index",
        pathActive: "action",
    },
    {
        label: 'sidebar.user',
        icon: "user-icon.svg",
        route: "admin.user.index",
        pathActive: "user",
    },
    {
        label: 'sidebar.role',
        icon: "role-icon.svg",
        route: "admin.role.index",
        pathActive: "role",
    },
    {
        label: 'sidebar.permission',
        icon: "permission-icon.svg",
        route: "admin.permission.index",
        pathActive: "permission",
    },
    {
        label: 'sidebar.audit-log',
        icon: "audit-log-icon.svg",
        route: "admin.audit-log.index",
        pathActive: "audit-log",
    },
    {
        label: 'sidebar.notification',
        icon: 'bell-icon.svg',
        route: 'admin.notification.index',
        pathActive: 'notification',
    },
    {
        label: 'マスターデータ管理',
        icon: 'master-data.svg',
        route: '',
        pathActive: 'master-data',
        subMenus: [
            {
                label: '商品カテゴリ管理',
                route: 'admin.master-data.category.index',
                pathActive: '/admin/master-data/category',
            },
        ]
    }
];

export const APP_MENUS = [
    {
        label: "Application",
        icon: "application-icon.svg",
        route: "app.application.index",
        pathActive: "application",
    },
];
