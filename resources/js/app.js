import './bootstrap';
import '../css/app.css';
import '../css/custom.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ElementPlus from "element-plus";
import "element-plus/dist/index.css";
import 'remixicon/fonts/remixicon.css'
import { Link } from '@inertiajs/vue3'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ElementPlus)
            .use(ZiggyVue)
            .component('Link', Link)
            .mixin({ methods: { appRoute: route } })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
