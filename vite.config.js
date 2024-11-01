import path from "path";
import {defineConfig, loadEnv} from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig(({mode}) => {
    const env = loadEnv(mode, process.cwd());
    const url = new URL(env.VITE_APP_URL);

    return {
        server: {
            host: "0.0.0.0",
            port: 8080,
            strictPort: true,
            hmr: {
                host: "app." + url.hostname,
                port: 8080,
            }
        },
        plugins: [
            laravel({
                input: [
                    "resources/js/admin.jsx",
                    "resources/js/auth.jsx",
                    "resources/js/installer.jsx",
                    "resources/js/index.jsx",
                    "resources/js/pusher.js",
                ],
                refresh: true,
            }),
            react({
                babel: {
                    configFile: path.resolve(__dirname, ".babelrc.js")
                }
            }),
        ],
        resolve: {
            alias: {
                "@/scss": "/resources/scss",
                "@ziggy": "/vendor/tightenco/ziggy/src/js",
                "@/static": "/resources/static",
            }
        },
    }
});
