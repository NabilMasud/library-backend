import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h } from 'vue';
import { route as ziggyRoute } from 'ziggy-js';
import { DefineComponent } from 'vue';

// Extend globalThis to include the route property
declare global {
    interface GlobalThis {
        rute: (name: string, params?: any, absolute?: boolean) => string;
    }
}


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

interface ZiggyConfig {
    location: string;
    [key: string]: any; // Tambahkan properti lain jika diperlukan
}

createServer((page) =>
    createInertiaApp({
        page,
        render: renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) });

            // Configure Ziggy for SSR...
            const ziggyConfig = {
                ...(page.props.ziggy as ZiggyConfig),
                location: new URL((page.props.ziggy as ZiggyConfig).location),
                url: (page.props.ziggy as ZiggyConfig).url || '',
                port: (page.props.ziggy as ZiggyConfig).port || null,
                defaults: (page.props.ziggy as ZiggyConfig).defaults || {},
                routes: (page.props.ziggy as ZiggyConfig).routes || {},
            };

            // Create route function...
            const route = (name: string, params?: any, absolute?: boolean) => ziggyRoute(name, params, absolute, ziggyConfig);

            // Make route function available globally...
            app.config.globalProperties.route = route as any;

            // Make route function available globally for SSR...
            if (typeof window === 'undefined') {
                Object.defineProperty(globalThis, 'rute', {
                    value: route,
                    writable: true,
                    configurable: true,
                });
            }

            app.use(plugin);

            return app;
        },
    }),
);
