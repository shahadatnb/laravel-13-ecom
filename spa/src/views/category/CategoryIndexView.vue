<script setup>
import { ref } from 'vue'
import { getImageUrl } from '@/utils/image'

const categories = ref([
  { id: 1, name: 'Electronics', slug: 'electronics', image: '/placeholder.jpg', description: 'Electronic products' },
  { id: 2, name: 'Clothing', slug: 'clothing', image: '/placeholder.jpg', description: 'Fashion and clothing' }
])
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Categories</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <router-link v-for="category in categories" :key="category.id" :to="`/category/${category.slug}`" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
        <img :src="getImageUrl(category.image)" :alt="category.name" class="w-full h-48 object-cover" />
        <div class="p-4">
          <h3 class="font-semibold text-lg">{{ category.name }}</h3>
          <p class="text-gray-600 text-sm">{{ category.description }}</p>
        </div>
      </router-link>
    </div>
  </div>
</template>