import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'web/app/themes/mawi-theme/assets/scripts/main.js'
            ],
            publicDirectory: 'web/app/themes/mawi-theme/assets',
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'web/app/themes/mawi-theme/assets'),
        },
    },
    build: {
        assetsDir: '.',
    }
});
