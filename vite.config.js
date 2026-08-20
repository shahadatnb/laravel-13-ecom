import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'
import path from 'path'

export default defineConfig({
  plugins: [
    vue()
  ],
  publicDir: path.resolve(__dirname, './spa/public'),
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./spa/src', import.meta.url))
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    headers: {
      'Access-Control-Allow-Origin': '*'
    },
    proxy: {
      '/api': {
        target: 'http://laravel-13-sawf.test',
        changeOrigin: true
      }
    }
  },
  build: {
    outDir: path.resolve(__dirname, './public/build'),
    emptyOutDir: false,
    manifest: true,
    rollupOptions: {
      input: 'spa/src/main.js'
    }
  }
})
