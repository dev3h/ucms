import './bootstrap';
import '../css/app.css';
import "../css/_variable.css";
import '../css/custom.css';
import "../css/custom_ckeditor.css";

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import ElementPlus from "element-plus";
import "element-plus/dist/index.css";
import 'remixicon/fonts/remixicon.css'
import { Link } from '@inertiajs/vue3'
import {createPinia} from "pinia";
import { createI18n } from 'vue-i18n'
import CKEditor from '@ckeditor/ckeditor5-vue'
import en from '@/Languages/en.json';
import vi from '@/Languages/vi.json';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()
const messages = {
    en,
    vi,
};
const i18n = createI18n({
    locale: 'vi',
    fallbackLocale: 'en',
    messages: messages
})

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ElementPlus)
            .use(CKEditor)
            .use(ZiggyVue)
            .use(pinia)
            .use(i18n)
            .component('Link', Link)
            .mixin({ methods: { appRoute: route } })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
