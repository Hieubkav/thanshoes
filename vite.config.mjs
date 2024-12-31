import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    resolve: {
        alias: {
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    plugins: [
        vue(
            {
                refresh: true,
            }
        ),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                ...refreshPaths,
                'app/Livewire/**',
            ]
        }),
    ],
});