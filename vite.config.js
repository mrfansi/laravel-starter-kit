import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'platform/Admin/resources/css/app.css',
                'platform/Admin/resources/js/app.js'
            ],
            refresh: [
                'resources/views/**/*',
                'platform/*/resources/views/**/*'
            ],
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
    },
    resolve: {
        alias: {
            '@admin': '/platform/Admin/resources'
        }
    }
});
