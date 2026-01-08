import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { config } from 'dotenv';
import { resolve } from 'path';

// .env ファイルを読み込む
config({ path: resolve(__dirname, '.env') });

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    define: {
        // .env の APP_DEBUG をフロントエンドに注入
        // これにより、.env の APP_DEBUG で手動に切り替えて挙動を確認できる
        'import.meta.env.APP_DEBUG': JSON.stringify(process.env.APP_DEBUG === 'true'),
    },
});
