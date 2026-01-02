import { fileURLToPath, URL } from 'node:url';

import { PrimeVueResolver } from '@primevue/auto-import-resolver';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';
import { createHtmlPlugin } from 'vite-plugin-html';

export default defineConfig({
    optimizeDeps: {
        noDiscovery: true
    },
    plugins: [
        vue(),
        Components({
            resolvers: [PrimeVueResolver()]
        }),
        createHtmlPlugin({
            minify: true,
            entry: '/src/main.ts',
            template: 'index.html',
            inject: {
                data: {
                    isProduction: process.env.NODE_ENV === 'production'
                }
            }
        })
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
            crypto: 'crypto-browserify'
        }
    },
    server: {
        host: process.env.NODE_ENV === 'development' ? '0.0.0.0' : false,
        port: 5173,
        sourcemapIgnoreList: (sourcePath) => {
            return sourcePath.includes('node_modules');
        },
        sourcemap: false,
        fs: {
            strict: true
        }
    },
    build: {
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true
            }
        },
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'pinia'],
                    ui: ['primevue', '@primeuix/themes'],
                    utils: ['axios', 'date-fns', 'lodash-es']
                }
            }
        }
    }
});
