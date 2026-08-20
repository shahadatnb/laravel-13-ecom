<script setup>
import { ref, onMounted } from 'vue'
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
    console.error(err)
  } finally {
    loading.value = false
  }
})

function formatPrice(price) {
  return new Intl.NumberFormat('en-BD', {
    style: 'currency', currency: 'BDT', minimumFractionDigits: 0,
  }).format(price || 0)
}

function goToProduct(slug) { router.push({ name: 'product.show', params: { slug } }) }
function goToCategory(slug) { router.push({ name: 'category.show', params: { slug } }) }
</script>

<template>
  <div>
    <!-- Hero: Ultra-clean minimal -->
    <section class="relative bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
        <div class="max-w-3xl">
          <p class="text-sm font-semibold tracking-[0.2em] uppercase text-primary-600 mb-6">Welcome to</p>
          <h1 class="text-5xl md:text-7xl font-bold font-display text-neutral-900 leading-[1.05] mb-6">
            {{ siteStore.getSetting('site_name', 'Store') }}
          </h1>
          <p class="text-xl text-neutral-500 leading-relaxed mb-10 max-w-xl">
            Curated products for modern living. Simple, beautiful, and made to last.
          </p>
          <div class="flex flex-wrap gap-4">
            <RouterLink :to="{ name: 'products.index' }" class="btn bg-neutral-900 text-white hover:bg-neutral-800 px-8 py-3.5 text-sm font-semibold tracking-wide">
              Explore Collection
            </RouterLink>
            <RouterLink :to="{ name: 'categories.index' }" class="btn border-2 border-neutral-200 text-neutral-700 hover:border-neutral-400 px-8 py-3.5 text-sm font-semibold tracking-wide">
              Categories
            </RouterLink>
          </div>
        </div>
      </div>
      <!-- Thin accent line -->
      <div class="h-px bg-gradient-to-r from-transparent via-primary-300 to-transparent"></div>
    </section>

    <!-- Categories: Elegant horizontal scroll -->
    <section class="py-16 md:py-24 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-sm font-semibold tracking-[0.2em] uppercase text-primary-600 mb-3">Browse</p>
        <h2 class="text-3xl md:text-4xl font-bold font-display text-neutral-900 mb-12">Categories</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
          <div v-for="cat in categoryStore.categories.slice(0, 6)" :key="cat.id"
            @click="goToCategory(cat.slug)"
            class="group cursor-pointer border border-neutral-100 rounded-2xl p-6 text-center hover:border-primary-300 hover:shadow-md transition-all duration-300">
            <div class="w-12 h-12 rounded-full bg-neutral-50 group-hover:bg-primary-50 flex items-center justify-center mx-auto mb-3 transition-colors">
              <svg class="w-5 h-5 text-neutral-400 group-hover:text-primary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <h3 class="text-xs font-semibold uppercase tracking-wider text-neutral-700 group-hover:text-primary-700 transition-colors">{{ cat.name }}</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- Divider -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8"><div class="h-px bg-neutral-100"></div></div>

    <!-- Featured Products: Clean grid with lots of whitespace -->
    <section class="py-16 md:py-24">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
          <div>
            <p class="text-sm font-semibold tracking-[0.2em] uppercase text-primary-600 mb-3">Selected</p>
            <h2 class="text-3xl md:text-4xl font-bold font-display text-neutral-900">Featured</h2>
          </div>
          <RouterLink :to="{ name: 'products.index' }" class="text-sm font-semibold text-neutral-900 hover:text-primary-600 transition-colors border-b-2 border-neutral-200 hover:border-primary-600 pb-0.5">
            View All →
          </RouterLink>
        </div>

        <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          <div v-for="n in 4" :key="n" class="animate-pulse">
            <div class="bg-neutral-100 rounded-2xl aspect-[3/4] mb-4"></div>
            <div class="h-3 bg-neutral-100 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-neutral-100 rounded w-1/3"></div>
          </div>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          <div v-for="product in featuredProducts.slice(0, 8)" :key="product.id"
            @click="goToProduct(product.slug)" class="group cursor-pointer">
            <div class="aspect-[3/4] bg-neutral-50 rounded-2xl overflow-hidden mb-4 relative">
              <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div v-if="product.discount_price" class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold tracking-wider uppercase px-2 py-1 rounded-md">
                Sale
              </div>
            </div>
            <h3 class="text-sm font-medium text-neutral-700 group-hover:text-primary-700 transition-colors line-clamp-1 mb-1">{{ product.name }}</h3>
            <div class="flex items-baseline gap-2">
              <span class="text-base font-bold text-neutral-900">{{ formatPrice(product.discount_price || product.price) }}</span>
              <span v-if="product.discount_price" class="text-xs text-neutral-400 line-through">{{ formatPrice(product.price) }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statement Banner -->
    <section class="py-16 md:py-24 bg-neutral-950">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-sm font-semibold tracking-[0.3em] uppercase text-primary-400 mb-4">Quality Promise</p>
        <h2 class="text-3xl md:text-5xl font-bold font-display text-white leading-tight max-w-2xl mx-auto mb-6">
          Every product is handpicked, tested, and guaranteed.
        </h2>
        <p class="text-neutral-400 max-w-lg mx-auto mb-10">
          We stand behind everything we sell. Free returns within 7 days, no questions asked.
        </p>
        <RouterLink :to="{ name: 'products.index' }" class="btn bg-white text-neutral-900 hover:bg-neutral-100 px-8 py-3.5 text-sm font-semibold tracking-wide">
          Shop the Collection
        </RouterLink>
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-16 md:py-24 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-12">
          <div>
            <p class="text-sm font-semibold tracking-[0.2em] uppercase text-primary-600 mb-3">Just In</p>
            <h2 class="text-3xl md:text-4xl font-bold font-display text-neutral-900">New Arrivals</h2>
          </div>
          <RouterLink :to="{ name: 'products.index' }" class="text-sm font-semibold text-neutral-900 hover:text-primary-600 transition-colors border-b-2 border-neutral-200 hover:border-primary-600 pb-0.5">
            View All →
          </RouterLink>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
          <div v-for="product in newProducts.slice(0, 8)" :key="product.id"
            @click="goToProduct(product.slug)" class="group cursor-pointer">
            <div class="aspect-[3/4] bg-neutral-50 rounded-2xl overflow-hidden mb-4 relative">
              <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
            </div>
            <h3 class="text-sm font-medium text-neutral-700 group-hover:text-primary-700 transition-colors line-clamp-1 mb-1">{{ product.name }}</h3>
            <span class="text-base font-bold text-neutral-900">{{ formatPrice(product.discount_price || product.price) }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-16 md:py-24 bg-neutral-50">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-xl mx-auto">
        <p class="text-sm font-semibold tracking-[0.2em] uppercase text-primary-600 mb-3">Stay Connected</p>
        <h2 class="text-2xl md:text-3xl font-bold font-display text-neutral-900 mb-4">Join our newsletter</h2>
        <p class="text-neutral-500 mb-8">New arrivals, exclusive offers, and inspiration — delivered to your inbox.</p>
        <div class="flex gap-2">
          <input type="email" placeholder="Your email address" class="flex-1 px-4 py-3 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 bg-white" />
          <button class="btn bg-neutral-900 text-white hover:bg-neutral-800 px-6 py-3 text-sm font-semibold rounded-xl">Subscribe</button>
        </div>
      </div>
    </section>
  </div>
</template>
