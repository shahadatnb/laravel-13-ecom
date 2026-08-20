<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { getImageUrl } from '@/utils/image'
import { formatPrice } from '@/utils/currency'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()

const searchQuery = ref('')
const products = ref([])
const loading = ref(false)
const error = ref(null)

// Perform search via API
async function performSearch(query) {
  if (!query || !query.trim()) {
    products.value = []
    return
  }
  loading.value = true
  error.value = null
  try {
    const results = await productStore.searchProducts(query)
    products.value = results || []
  } catch (err) {
    error.value = err.message || 'Search failed. Please try again.'
    products.value = []
  } finally {
    loading.value = false
  }
}

// Handle search submission — update URL and trigger search
function handleSearch() {
  const q = searchQuery.value
  if (!q || !q.trim()) {
    products.value = []
    router.replace({ query: {} })
    return
  }
  // Update URL — the watch on route.query.q will call performSearch
  router.push({ query: { q: q.trim() } })
}

// Read query from URL on mount and trigger search
onMounted(() => {
  const q = route.query.q || ''
  searchQuery.value = q
  if (q.trim()) {
    performSearch(q)
  }
})

// Watch route query changes (e.g. user navigates back/forward, or search from main layout)
watch(
  () => route.query.q,
  (newQ) => {
    searchQuery.value = newQ || ''
    if (newQ?.trim()) {
      performSearch(newQ)
    } else {
      products.value = []
    }
  }
)


</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Search Products</h1>

    <!-- Search Input -->
    <div class="mb-8">
      <div class="flex max-w-2xl mx-auto">          <input
          v-model="searchQuery"
          type="search"
          autocomplete="search"
          name="search"
          aria-label="Search products"
          class="flex-1 px-5 py-3 border border-r-0 border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg"
          placeholder="Search products…"
          @keyup.enter="handleSearch"
        />          <button
          @click="handleSearch"
          class="px-8 py-3 bg-primary-600 text-white rounded-r-lg hover:bg-primary-700 font-semibold"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          Search
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-block w-12 h-12 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mb-4"></div>
      <p class="text-gray-500 text-lg">Searching products...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-16">
      <div class="text-6xl mb-4">😕</div>
      <h3 class="text-xl font-semibold text-gray-800 mb-2">Search Error</h3>
      <p class="text-red-500 mb-6">{{ error }}</p>
      <button
        @click="performSearch(searchQuery)"
        class="px-6 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
      >
        Try Again
      </button>
    </div>

    <!-- Empty State -->
    <div v-else-if="searchQuery && products.length === 0" class="text-center py-16">
      <div class="text-6xl mb-4">🔍</div>
      <h3 class="text-xl font-semibold text-gray-800 mb-2">No products found</h3>
      <p class="text-gray-500 text-lg mb-2">
        We couldn't find any results for "<strong>{{ searchQuery }}</strong>"
      </p>
      <p class="text-gray-400">Try a different search term or browse our categories.</p>
    </div>

    <!-- Initial State (no search yet) -->
    <div v-else-if="!searchQuery" class="text-center py-16">
      <div class="text-7xl mb-4">🔎</div>
      <h3 class="text-xl font-semibold text-gray-800 mb-2">Search our products</h3>
      <p class="text-gray-500 text-lg">Type a keyword above and press Enter or click Search.</p>
    </div>

    <!-- Results -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group"
      >
        <RouterLink :to="`/product/${product.slug}`">
          <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
            <img
              v-if="product.thumbnail || product.images?.length"
              :src="getImageUrl(product.thumbnail || product.images[0]?.image)"
              :alt="product.name"
              width="400"
              height="400"
              loading="lazy"
              class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
            />
            <span v-else class="text-6xl text-gray-300" aria-hidden="true">📦</span>
          </div>
        </RouterLink>
        <div class="p-4">
          <RouterLink :to="`/product/${product.slug}`">
            <h3 class="font-semibold text-gray-800 mb-1 line-clamp-1 group-hover:text-primary-600 transition-colors">
              {{ product.name }}
            </h3>
          </RouterLink>
          <p class="text-sm text-gray-400 mb-3 line-clamp-1">
            {{ product.short_description || product.description?.slice(0, 80) }}
          </p>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-xl font-bold text-gray-900">
                {{ formatPrice(product.sale_price || product.regular_price || product.price) }}
              </span>
              <span
                v-if="product.sale_price && product.regular_price"
                class="text-sm text-gray-400 line-through"
              >
                {{ formatPrice(product.regular_price) }}
              </span>
            </div>
            <span class="text-sm text-primary-600 font-medium">View →</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>