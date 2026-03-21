import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PvButton from 'primevue/button';
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import { createApp, h } from 'vue';
import type { DefineComponent } from 'vue';
import '../css/app.css';
import { initializeTheme } from '@/composables/useAppearance';

// 1. Import the Aura preset (or Lara if you prefer)
// eslint-disable-next-line import/order
import Aura from '@primevue/themes/aura';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);

        // 2. Add the theme configuration here
        app.use(PrimeVue, {
            ripple: true,
            theme: {
                preset: Aura,
                options: {
                    darkModeSelector: '.dark', // Ensures it works with Tailwind dark mode
                }
            }
        });

        app.use(ToastService);
        app.use(ConfirmationService);

        app.component('Toast', Toast);
        app.component('ConfirmDialog', ConfirmDialog);
        app.component('PvButton', PvButton);

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
