<script setup>
import { RouterLink } from 'vue-router'
</script>

<template>
  <div class="min-h-[calc(100vh-200px)] flex items-center justify-center">
    <div class="text-center">
      <div class="text-9xl mb-4">😕</div>
      <h1 class="text-4xl font-bold mb-4">404 - Page Not Found</h1>
      <p class="text-gray-600 mb-8">The page you are looking for does not exist.</p>
      <RouterLink to="/" class="btn btn-primary">
        Go to Homepage
      </RouterLink>
    </div>
  </div>
</template>