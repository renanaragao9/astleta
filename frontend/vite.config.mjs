import { fileURLToPath, URL } from 'node:url';

import { PrimeVueResolver } from '@primevue/auto-import-resolver';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite';
import { defineConfig } from 'vite';

export default defineConfig({
    optimizeDeps: {
        noDiscovery: true
    },
    plugins: [
        vue(),
        Components({
            resolvers: [PrimeVueResolver()]
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
                    ui: ['primevue', '@primeuix/themes']
                }
            }
        }
    }
});
