import { defineConfig } from 'vite'

export default defineConfig({
    build: {
        rollupOptions: {
            input: {
                main: 'resources/js/index.js',
                style: 'resources/css/index.css',
            },
            output: {
                dir: 'resources/dist',
                entryFileNames: 'filament-dcs-server-stats.js',
                assetFileNames: 'filament-dcs-server-stats.css',
            },
        },
        outDir: 'resources/dist',
        emptyOutDir: true,
        manifest: false,
    },
})
