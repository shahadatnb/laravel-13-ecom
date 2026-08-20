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

function calcDiscount(regular, sale) {
  if (!regular || !sale || regular <= sale) return 0
  return Math.round(((regular - sale) / regular) * 100)
}

function goToProduct(slug) { router.push({ name: 'product.show', params: { slug } }) }
function goToCategory(slug) { router.push({ name: 'category.show', params: { slug } }) }
</script>

<template>
  <div>
    <!-- Hero: Bold deals-focused -->
    <section class="relative bg-gradient-to-br from-red-600 via-red-700 to-orange-700 overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-yellow-400 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-pink-400 rounded-full blur-[100px]"></div>
      </div>
      <!-- Floating badges -->
      <div class="absolute top-8 right-8 md:top-12 md:right-16 bg-yellow-400 text-red-900 font-black text-sm md:text-base px-5 py-2 rounded-full rotate-6 shadow-xl hidden sm:block">
        🔥 HOT DEALS
      </div>
      <div class="absolute bottom-12 left-8 md:bottom-16 md:left-16 bg-white text-red-700 font-black text-xs md:text-sm px-4 py-1.5 rounded-full -rotate-3 shadow-lg hidden sm:block">
        UP TO 50% OFF
      </div>

      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10">
        <div class="max-w-2xl">
          <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm text-white/90 text-sm font-bold px-4 py-1.5 rounded-full mb-6 border border-white/20">
            <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
            MEGA SALE LIVE NOW
          </div>
          <h1 class="text-4xl md:text-6xl font-black font-display text-white leading-tight mb-4">
            Save Big on
            <span class="text-yellow-300">Top Brands</span>
          </h1>
          <p class="text-lg text-white/80 mb-8 max-w-lg">
            Massive discounts on electronics, fashion, and more. Limited stocks available — grab before they're gone!
          </p>
          <div class="flex flex-wrap gap-4">
            <RouterLink :to="{ name: 'products.index' }" class="btn bg-yellow-400 text-red-900 hover:bg-yellow-300 font-black px-8 py-3.5 shadow-xl shadow-yellow-400/30">
              Shop the Sale →
            </RouterLink>
            <RouterLink :to="{ name: 'categories.index' }" class="btn bg-white/15 text-white hover:bg-white/25 backdrop-blur-sm border border-white/25 font-semibold">
              Browse Categories
            </RouterLink>
          </div>
        </div>
      </div>
    </section>

    <!-- Urgency Strip -->
    <section class="bg-red-900 text-white">
      <div class="container mx-auto px-4 py-3 flex items-center justify-center gap-6 text-sm font-semibold overflow-hidden">
        <span class="flex items-center gap-2 whitespace-nowrap">
          <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
          FREE DELIVERY on ৳5,000+
        </span>
        <span class="text-red-400">|</span>
        <span class="flex items-center gap-2 whitespace-nowrap">⚡ Flash deals dropping daily</span>
        <span class="text-red-400">|</span>
        <span class="flex items-center gap-2 whitespace-nowrap">🔄 Easy 7-day returns</span>
      </div>
    </section>

    <!-- Categories with colored cards -->
    <section class="py-12 md:py-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-2xl md:text-3xl font-black font-display text-neutral-900">Shop by Category</h2>
            <p class="text-neutral-500 text-sm mt-1">Find what you need</p>
          </div>
          <RouterLink :to="{ name: 'categories.index' }" class="text-sm font-bold text-red-600 hover:text-red-700">View All →</RouterLink>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
          <div v-for="(cat, i) in categoryStore.categories.slice(0, 6)" :key="cat.id"
            @click="goToCategory(cat.slug)"
            class="group cursor-pointer rounded-2xl p-5 text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1"
            :class="[
              i % 4 === 0 ? 'bg-red-50 hover:bg-red-100' :
              i % 4 === 1 ? 'bg-blue-50 hover:bg-blue-100' :
              i % 4 === 2 ? 'bg-amber-50 hover:bg-amber-100' :
              'bg-emerald-50 hover:bg-emerald-100'
            ]">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mx-auto mb-3 transition-transform group-hover:scale-110"
              :class="[
                i % 4 === 0 ? 'bg-red-200 text-red-700' :
                i % 4 === 1 ? 'bg-blue-200 text-blue-700' :
                i % 4 === 2 ? 'bg-amber-200 text-amber-700' :
                'bg-emerald-200 text-emerald-700'
              ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-neutral-800">{{ cat.name }}</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Deals -->
    <section class="py-12 md:py-16 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-3">
            <div>
              <h2 class="text-2xl md:text-3xl font-black font-display text-neutral-900">Featured Deals</h2>
              <p class="text-neutral-500 text-sm mt-1">Handpicked bargains for you</p>
            </div>
            <span class="bg-red-100 text-red-700 text-xs font-black px-3 py-1 rounded-full animate-pulse">HOT</span>
          </div>
          <RouterLink :to="{ name: 'products.index' }" class="text-sm font-bold text-red-600 hover:text-red-700">View All →</RouterLink>
        </div>

        <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="n in 4" :key="n" class="animate-pulse bg-neutral-100 rounded-2xl aspect-square"></div>
        </div>

        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="product in featuredProducts.slice(0, 8)" :key="product.id"
            @click="goToProduct(product.slug)"
            class="group cursor-pointer bg-white rounded-2xl border border-neutral-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative">
            <!-- Discount badge -->
            <div v-if="product.discount_price"
              class="absolute top-3 left-3 z-10 bg-red-500 text-white text-xs font-black px-2.5 py-1 rounded-lg shadow-md">
              -{{ calcDiscount(product.regular_price || product.price, product.discount_price) }}%
            </div>
            <div class="aspect-square bg-neutral-50 overflow-hidden">
              <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold text-neutral-800 line-clamp-2 mb-2 group-hover:text-red-600 transition-colors">{{ product.name }}</h3>
              <div class="flex items-baseline gap-2">
                <span class="text-lg font-black text-red-600">{{ formatPrice(product.discount_price || product.price) }}</span>
                <span v-if="product.discount_price" class="text-xs text-neutral-400 line-through">{{ formatPrice(product.regular_price || product.price) }}</span>
              </div>
              <div class="flex items-center gap-1 mt-2">
                <div class="flex text-yellow-400 text-xs">★★★★★</div>
                <span class="text-xs text-neutral-400">(4.5)</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Dual Promo Banners -->
    <section class="py-12 md:py-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-4">
          <!-- Banner 1 -->
          <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 md:p-10 relative overflow-hidden group cursor-pointer"
            @click="router.push({ name: 'categories.index' })">
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="relative z-10">
              <span class="inline-block bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">ELECTRONICS</span>
              <h3 class="text-2xl font-black font-display text-white mb-2">Smart Devices</h3>
              <p class="text-blue-100 text-sm mb-4">Up to 40% off on the latest gadgets</p>
              <span class="text-white text-sm font-bold group-hover:underline">Shop Now →</span>
            </div>
          </div>
          <!-- Banner 2 -->
          <div class="bg-gradient-to-br from-pink-500 to-rose-600 rounded-3xl p-8 md:p-10 relative overflow-hidden group cursor-pointer"
            @click="router.push({ name: 'categories.index' })">
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
            <div class="relative z-10">
              <span class="inline-block bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">FASHION</span>
              <h3 class="text-2xl font-black font-display text-white mb-2">Style Sale</h3>
              <p class="text-pink-100 text-sm mb-4">Fresh looks at unbeatable prices</p>
              <span class="text-white text-sm font-bold group-hover:underline">Shop Now →</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- New Arrivals with "NEW" badge -->
    <section class="py-12 md:py-16 bg-white">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-3">
            <h2 class="text-2xl md:text-3xl font-black font-display text-neutral-900">New Arrivals</h2>
            <span class="bg-emerald-100 text-emerald-700 text-xs font-black px-3 py-1 rounded-full">NEW</span>
          </div>
          <RouterLink :to="{ name: 'products.index' }" class="text-sm font-bold text-red-600 hover:text-red-700">View All →</RouterLink>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div v-for="product in newProducts.slice(0, 8)" :key="product.id"
            @click="goToProduct(product.slug)"
            class="group cursor-pointer bg-white rounded-2xl border border-neutral-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="aspect-square bg-neutral-50 overflow-hidden relative">
              <img v-if="product.thumbnail" :src="product.thumbnail" :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-neutral-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              </div>
              <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider">
                New
              </div>
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold text-neutral-800 line-clamp-2 mb-2 group-hover:text-red-600 transition-colors">{{ product.name }}</h3>
              <span class="text-lg font-black text-neutral-900">{{ formatPrice(product.discount_price || product.price) }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Trust Strip -->
    <section class="py-10 bg-neutral-100">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0"><span class="text-lg">🚚</span></div>
            <div><p class="text-sm font-bold text-neutral-800">Free Shipping</p><p class="text-xs text-neutral-500">On ৳5,000+</p></div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0"><span class="text-lg">🔒</span></div>
            <div><p class="text-sm font-bold text-neutral-800">Secure Pay</p><p class="text-xs text-neutral-500">100% protected</p></div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0"><span class="text-lg">↩️</span></div>
            <div><p class="text-sm font-bold text-neutral-800">Easy Returns</p><p class="text-xs text-neutral-500">7-day policy</p></div>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0"><span class="text-lg">💬</span></div>
            <div><p class="text-sm font-bold text-neutral-800">24/7 Support</p><p class="text-xs text-neutral-500">Always here</p></div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
