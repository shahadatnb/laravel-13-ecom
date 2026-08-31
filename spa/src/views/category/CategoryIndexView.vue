<script setup>
import { onMounted } from 'vue'
import { useCategoryStore } from '@/stores/category'
import { getImageUrl } from '@/utils/image'

const categoryStore = useCategoryStore()

onMounted(() => {
  if (!categoryStore.categories.length) {
    categoryStore.fetchCategories()
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Categories</h1>

    <!-- Loading -->
    <div v-if="categoryStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="bg-white rounded-lg shadow-md overflow-hidden animate-pulse">
        <div class="w-full h-48 bg-gray-200"></div>
        <div class="p-4 space-y-2">
          <div class="h-5 bg-gray-200 rounded w-1/2"></div>
          <div class="h-3 bg-gray-200 rounded w-3/4"></div>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div v-else-if="!categoryStore.activeCategories.length" class="text-center py-16 bg-white rounded-lg shadow-md">
      <div class="text-5xl mb-4">📂</div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">No Categories Found</h2>
      <p class="text-gray-500">Categories will appear here once added from the admin panel.</p>
    </div>

    <!-- Categories grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <router-link
        v-for="category in categoryStore.activeCategories"
        :key="category.id"
        :to="`/category/${category.slug}`"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group"
      >
        <div class="w-full h-48 bg-gray-100 overflow-hidden">
          <img
            :src="getImageUrl(category.thumbnail || category.image || category.image_url)"
            :alt="category.name"
            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
          />
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-lg">{{ category.name }}</h3>
          <p class="text-gray-600 text-sm">{{ category.description || 'Browse products in this category' }}</p>
          <div class="mt-2">
            <span class="text-primary-600 text-sm font-semibold group-hover:underline">View Products →</span>
          </div>
        </div> </router-link>
    </div>
  </div>
</template>