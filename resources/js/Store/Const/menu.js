export const MASTER_MENUS = [
    {
        label: 'sidebar.dashboard',
        icon: "dashboard-icon.svg",
        route: "admin.dashboard.index",
        pathActive: "dashboard",
    },
    {
        label: 'sidebar.system-components',
        icon: "system-icon.svg",
        route: '',
        pathActive: 'system-components',
        subMenus: [
            {
                label: 'sidebar.system',
                icon: "system-icon.svg",
                route: "admin.system.index",
                pathActive: "/admin/system-components/system",
            },
            {
                label: 'sidebar.subsystem',
                icon: "subsystem-icon.svg",
                route: "admin.subsystem.index",
                pathActive: "/admin/system-components/subsystem",
            },
            {
                label: 'sidebar.module',
                icon: "module-icon.svg",
                route: "admin.module.index",
                pathActive: "/admin/system-components/module",
            },
            {
                label: 'sidebar.action',
                icon: "action-icon.svg",
                route: "admin.action.index",
                pathActive: "/admin/system-components/action",
            },
        ]
    },
    {
        label: 'sidebar.user-management',
        icon: "user-icon.svg",
        route: '',
        pathActive: 'user-management',
        subMenus: [
            {
                label: 'sidebar.user',
                icon: "user-icon.svg",
                route: "admin.user.index",
                pathActive: "/admin/user-management/user",
            },
            {
                label: 'sidebar.role',
                icon: "role-icon.svg",
                route: "admin.role.index",
                pathActive: "/admin/user-management/role",
            },
            {
                label: 'sidebar.permission',
                icon: "permission-icon.svg",
                route: "admin.permission.index",
                pathActive: "/admin/user-management/permission",
            }
        ]
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
