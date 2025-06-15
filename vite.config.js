import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: '127.0.0.1', // Разрешаем доступ с любых IP
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
          host: 'localhost', // Для HMR
        }
      },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/calculator-charts.js', 'resources/js/crudcomment.js', 'resources/js/addFavorite.js', 'resources/js/nutrition_charts.js', 'resources/js/calculate_product_quantity.js'],
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
});
