import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

export default defineConfig({
  plugins: [
    vue(),
  ],
  // ভিট-কে spa ফোল্ডারের ভিতরের public ফোল্ডারটি লোকেট করে দেওয়া হলো
  publicDir: path.resolve(__dirname, 'public'),
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    cors: true,
    headers: {
      'Access-Control-Allow-Origin': '*'
    }
  },
  build: {
    // এটি বিল্ড ফোল্ডারে manifest.json ফাইল তৈরি করতে বাধ্য করবে
    manifest: true,
    outDir: '../public/build',
    emptyOutDir: false,
    manifest: true,
    rollupOptions: {
      input: 'src/main.js'
    }
  }
})
