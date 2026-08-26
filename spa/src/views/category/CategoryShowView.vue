<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useCategoryStore } from '@/stores/category'
import { useProductStore } from '@/stores/product'
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'

const route = useRoute()
const categoryStore = useCategoryStore()
const productStore = useProductStore()
const cartStore = useCartStore()
const toast = useToast()

const loading = ref(true)

onMounted(async () => {
  await fetchCategory()
})

async function fetchCategory() {
  loading.value = true
  try {
    await categoryStore.fetchCategoryWithProducts(route.params.slug)
  } finally {
    loading.value = false
  }
}

function addToCart(product) {
  cartStore.addItem(product)
  toast.success(`${product.name} added to cart!`)
}

function formatPrice(price) {
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 0,
  }).format(price || 0)
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="text-4xl">⏳</div>
      <p class="text-gray-600 mt-4">Loading category...</p>
    </div>

    <!-- Category Content -->
    <div v-else-if="categoryStore.currentCategory" class="bg-white rounded-lg shadow-md p-8">
      <h1 class="text-3xl font-bold mb-4">
        {{ categoryStore.currentCategory.name }}
      </h1>
      <p class="text-gray-600 mb-8">
        {{ categoryStore.currentCategory.description }}
      </p>

      <!-- Products Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div
          v-for="product in categoryStore.currentCategory.products"
          :key="product.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
        >
          <RouterLink :to="`/product/${product.slug}`">
            <div class="aspect-square bg-gray-200 flex items-center justify-center">
              <span class="text-6xl">📷</span>
            </div>
          </RouterLink>
          <div class="p-4">
            <RouterLink :to="`/product/${product.slug}`">
              <h3 class="font-semibold text-lg mb-2 hover:text-primary-600">
                {{ product.name }}
              </h3>
            </RouterLink>
            <div class="flex items-center justify-between">
              <span class="text-xl font-bold text-primary-600">
                {{ formatPrice(product.sale_price || product.regular_price) }}
              </span>
              <button @click="addToCart(product)" class="btn btn-primary btn-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                Add to Cart
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!categoryStore.currentCategory.products?.length" class="text-center py-12">
        <div class="text-4xl">📦</div>
        <p class="text-gray-600 mt-4">No products in this category.</p>
      </div>
    </div>

    <!-- Not Found -->
    <div v-else class="text-center py-12">
      <div class="text-4xl">😕</div>
      <p class="text-gray-600 mt-4">Category not found.</p>
      <RouterLink to="/categories" class="btn btn-primary mt-4">
        Browse Categories
      </RouterLink>
    </div>
  </div>
</template>