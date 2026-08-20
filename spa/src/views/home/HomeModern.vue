<script setup>
import { ref, onMounted, computed } from 'vue'
import { useSiteStore } from '@/stores/site'
import { useCategoryStore } from '@/stores/category'
import { useRouter } from 'vue-router'
import ProductService from '@/services/ProductService'

const siteStore = useSiteStore()
const categoryStore = useCategoryStore()
const router = useRouter()

const featuredProducts = ref([])
const newProducts = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const [featured, newRes] = await Promise.all([
      ProductService.getFeatured(),
      ProductService.getNewArrivals()
    ])
    featuredProducts.value = featured.data?.data || []
    newProducts.value = newRes.data?.data || []
    if (!categoryStore.categories.length) {
      await categoryStore.fetchActiveCategories()
    }
  } catch (err) {
    console.error('Failed to load home data:', err)
  } finally {
    loading.value = false
  }
})

function formatPrice(price) {
  return new Intl.NumberFormat('en-BD', {
    style: 'currency',
    currency: 'BDT',
    minimumFractionDigits: 0,
  }).format(price || 0)
}

function goToProduct(slug) {
  router.push({ name: 'product.show', params: { slug } })
}

function goToCategory(slug) {
  router.push({ name: 'category.show', params: { slug } })
}
</script>

<template>
  <div>
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 -left-40 w-96 h-96 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -right-40 w-96 h-96 bg-accent-400 rounded-full blur-3xl"></div>
      </div>
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10">
        <div class="max-w-2xl">
          <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm text-primary-100 text-sm font-medium px-4 py-1.5 rounded-full mb-6 border border-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            New Season Collection
          </span>
          <h1 class="text-4xl md:text-6xl font-bold font-display text-white leading-tight mb-6">
            Discover the
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-200 to-accent-300">
              Best Deals
            </span>
            Online
          </h1>
          <p class="text-lg text-primary-100/80 mb-8 max-w-lg leading-relaxed">
            Shop the latest trends with amazing prices. Quality products, fast delivery, and exceptional customer service.
          </p>
          <div class="flex flex-wrap gap-4">
            <RouterLink :to="{ name: 'products.index' }" class="btn bg-white text-primary-800 hover:bg-primary-50 shadow-xl shadow-black/10 font-semibold">
              Shop Now
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </RouterLink>
            <RouterLink :to="{ name: 'categories.index' }" class="btn bg-white/10 text-white hover:bg-white/20 backdrop-blur-sm border border-white/20">
              Browse Categories
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Decorative wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 120L60 108C120 96 240 72 360 66C480 60 600 72 720 78C840 84 960 84 1080 78C1200 72 1320 60 1380 54L1440 48V120H0Z" fill="#fafafa"/>
        </svg>
      </div>
    </section>

    <!-- Features Strip -->
    <section class="bg-neutral-50 py-6">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div class="flex items-center gap-3 p-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-neutral-800">Free Shipping</p>
              <p class="text-xs text-neutral-500">On orders over ৳5000</p>
            </div>
          </div>
          <div class="flex items-center gap-3 p-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-neutral-800">Secure Payment</p>
              <p class="text-xs text-neutral-500">100% secure checkout</p>
            </div>
          </div>
          <div class="flex items-center gap-3 p-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-neutral-800">Easy Returns</p>
              <p class="text-xs text-neutral-500">7-day return policy</p>
            </div>
          </div>
          <div class="flex items-center gap-3 p-3">
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
              <p class="text-sm font-semibold text-neutral-800">24/7 Support</p>
              <p class="text-xs text-neutral-500">Dedicated support</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-12 md:py-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
          <div>
            <h2 class="section-title">Shop by Category</h2>
            <p class="text-neutral-500 mt-1">Browse our top categories</p>
          </div>
          <RouterLink :to="{ name: 'categories.index' }" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </RouterLink>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="category in categoryStore.categories.slice(0, 6)" :key="category.id"
            @click="goToCategory(category.slug)"
            class="group cursor-pointer bg-white rounded-2xl border border-neutral-100 p-5 text-center hover:shadow-lg hover:border-primary-200 transition-all duration-300 hover:-translate-y-1">
            <div class="w-14 h-14 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-3 group-hover:bg-primary-100 transition-colors">
              <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <h3 class="text-sm font-semibold text-neutral-800 group-hover:text-primary-700 transition-colors">{{ category.name }}</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-12 md:py-16 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
          <div>
            <h2 class="section-title">Featured Products</h2>
            <p class="text-neutral-500 mt-1">Handpicked items just for you</p>
          </div>
          <RouterLink :to="{ name: 'products.index' }" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </RouterLink>
        </div>

        <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="n in 4" :key="n" class="animate-pulse">
            <div class="bg-neutral-100 rounded-2xl aspect-square mb-4"></div>
            <div class="h-4 bg-neutral-100 rounded w-3/4 mb-2"></div>
            <div class="h-4 bg-neutral-100 rounded w-1/2"></div>
          </div>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="product in featuredProducts.slice(0, 8)" :key="product.id"
            @click="goToProduct(product.slug)"
            class="product-card cursor-pointer">
            <div class="aspect-square bg-neutral-50 relative overflow-hidden">
              <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div v-if="product.discount_price" class="absolute top-3 left-3 bg-accent-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg">
                SALE
              </div>
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold text-neutral-800 line-clamp-2 mb-2 group-hover:text-primary-700 transition-colors">{{ product.name }}</h3>
              <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold text-primary-700">{{ formatPrice(product.discount_price || product.price) }}</span>
                <span v-if="product.discount_price" class="text-sm text-neutral-400 line-through">{{ formatPrice(product.price) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Promo Banner -->
    <section class="py-12 md:py-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 rounded-3xl p-8 md:p-12 relative overflow-hidden">
          <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
          <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>
          <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
              <h2 class="text-2xl md:text-3xl font-bold font-display text-white mb-2">Special Offer</h2>
              <p class="text-primary-100/80">Get up to 30% off on selected items. Limited time only!</p>
            </div>
            <RouterLink :to="{ name: 'products.index' }" class="btn bg-white text-primary-700 hover:bg-primary-50 font-semibold shadow-xl whitespace-nowrap">
              Shop the Sale
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-12 md:py-16 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
          <div>
            <h2 class="section-title">New Arrivals</h2>
            <p class="text-neutral-500 mt-1">Fresh drops this week</p>
          </div>
          <RouterLink :to="{ name: 'products.index' }" class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 transition-colors">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </RouterLink>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="product in newProducts.slice(0, 8)" :key="product.id"
            @click="goToProduct(product.slug)"
            class="product-card cursor-pointer">
            <div class="aspect-square bg-neutral-50 relative overflow-hidden">
              <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-12 h-12 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div class="absolute top-3 left-3 bg-primary-500 text-white text-xs font-bold px-2.5 py-1 rounded-lg">
                NEW
              </div>
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold text-neutral-800 line-clamp-2 mb-2 group-hover:text-primary-700 transition-colors">{{ product.name }}</h3>
              <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold text-primary-700">{{ formatPrice(product.discount_price || product.price) }}</span>
                <span v-if="product.discount_price" class="text-sm text-neutral-400 line-through">{{ formatPrice(product.price) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-12 md:py-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-accent-500 to-accent-600 rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">
          <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-1/4 w-48 h-48 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 w-48 h-48 bg-white rounded-full blur-3xl"></div>
          </div>
          <div class="relative z-10">
            <h2 class="text-2xl md:text-3xl font-bold font-display text-white mb-3">Stay Updated</h2>
            <p class="text-accent-100/80 mb-6 max-w-md mx-auto">Subscribe to get notified about new products, exclusive offers, and more.</p>
            <div class="flex max-w-md mx-auto">
              <input type="email" placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-l-xl border-0 focus:outline-none focus:ring-2 focus:ring-white/50" />
              <button class="btn bg-neutral-900 text-white hover:bg-neutral-800 rounded-l-none rounded-r-xl px-6">
                Subscribe
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
