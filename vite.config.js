import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'
import path from 'path'

export default defineConfig(({ command }) => ({
  // Production assets are served from /build/ (see @vite + public/build).
  // The build-time base makes lazy-chunk CSS preload URLs resolve under
  // /build/assets/ instead of /assets/ ("Unable to preload CSS" fix).
  // Dev server keeps base '/' so hotAsset URLs keep working.
  base: command === 'build' ? '/build/' : '/',
  root: path.resolve(__dirname, './spa'),
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
        target: 'http://laravel-13-ecom.test',
        changeOrigin: true
      }
    }
  },
  build: {
    outDir: path.resolve(__dirname, './public/build'),
    emptyOutDir: false,
    manifest: 'manifest.json',
    rollupOptions: {
      input: 'src/main.js'
    }
  }
}))
