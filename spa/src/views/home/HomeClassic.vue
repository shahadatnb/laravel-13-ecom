<script setup>
import { ref, onMounted, onUnmounted, computed, reactive } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { useCategoryStore } from '@/stores/category'
import { useSiteStore } from '@/stores/site'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { getImageUrl } from '@/utils/image'
import { getThemeText } from '@/utils/themeTexts'
import { formatPrice, initCurrencySettings } from '@/utils/currency'
import QuickVariantSelector from '@/components/QuickVariantSelector.vue'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const categoryStore = useCategoryStore()
const siteStore = useSiteStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const authStore = useAuthStore()
const toast = useToast()

const wishlistToggling = ref(null)

const loading = ref(true)
const error = ref(null)

// Hero slides from database (loaded via API)
const heroSlides = computed(() => {
  const slides = siteStore.slides
  if (slides.length > 0) {
    return slides.map(s => ({
      title: s.title,
      subtitle: s.subtitle,
      cta: s.cta_text || 'Shop Now',
      link: s.cta_link || '/products',
      bg: s.bg_gradient || 'from-primary-700 via-primary-800 to-primary-900',
      image: s.image_emoji || '🎉',
      badge_text: s.badge_text || 'Limited Time Offer'
    }))
  }
  // Fallback defaults if no slides configured
  return [
    {
      title: getThemeText('hero_title', 'Welcome to Our Store'),
      subtitle: getThemeText('hero_subtitle', 'Discover amazing products at great prices'),
      cta: 'Shop Now',
      link: '/products',
      bg: 'from-primary-700 via-primary-800 to-primary-900',
      image: '🎉',
      badge_text: 'Welcome'
    }
  ]
})

const currentSlide = ref(0)

// Auto-rotate hero slides
let slideInterval = null
onMounted(() => {
  slideInterval = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % heroSlides.length
  }, 5000)
})

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval)
})

function prevSlide() {
  currentSlide.value = (currentSlide.value - 1 + heroSlides.length) % heroSlides.length
}

function nextSlide() {
  currentSlide.value = (currentSlide.value + 1) % heroSlides.length
}


// Calculate discount percentage
function getDiscountPercent(product) {
  if (!product.sale_price || !product.regular_price) return 0
  const regular = parseFloat(product.regular_price)
  const sale = parseFloat(product.sale_price)
  if (regular <= 0 || sale <= 0) return 0
  return Math.round(((regular - sale) / regular) * 100)
}

// ── Quick Variant Selection state per product ──
const selectedVariants = reactive({})

function getSelectedVariant(product) {
  return selectedVariants[product.id] || null
}

function onQuickVariantSelect(product, result) {
  if (result === null) {
    delete selectedVariants[product.id]
  } else if (typeof result === 'object' && result.id) {
    selectedVariants[product.id] = result
  }
}

function getEffectivePrice(product) {
  const sv = selectedVariants[product.id]
  if (sv && sv.price) return sv.price
  return product.sale_price || product.regular_price
}

// Check if a product has variants (variable product)
function isVariableProduct(product) {
  if (!product) return false
  if (product.product_type === 'variable' || product.type === 'variable') return true
  if (product.has_variants === true) return true
  if (product.variants && product.variants.length > 0) return true
  return false
}

// Add to cart (respects quick-variant selection)
function goToProduct(slug) {
  router.push({ name: 'product.show', params: { slug } })
}

function goToCategory(slug) {
  router.push({ name: 'category.show', params: { slug } })
}

function reloadPage() {
  window.location.reload()
}

function addToCart(product) {
  const sv = selectedVariants[product.id]
  if (sv && sv.price) {
    cartStore.addItem({ ...product, variant_id: sv.id, variant_name: sv.name, variant_sku: sv.sku, price: sv.price })
  } else {
    cartStore.addItem(product)
  }
  toast.success(`${product.name}${sv ? ' (' + sv.name + ')' : ''} added to cart!`)
}

async function toggleWishlist(product, event) {
  if (event) event.preventDefault()
  if (!authStore.isAuthenticated) {
    toast.warning('Please login to add items to your wishlist.')
    return
  }
  wishlistToggling.value = product.id
  try {
    const result = await wishlistStore.toggle(product.id)
    if (result.added) {
      toast.success('Added to wishlist!')
    } else {
      toast.success('Removed from wishlist.')
    }
  } finally {
    wishlistToggling.value = null
  }
}

function isWishlisted(productId) {
  return wishlistStore.isWishlisted(productId)
}

// Load data
onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    await Promise.all([
      productStore.fetchFeatured(),
      productStore.fetchNewArrivals(),
      categoryStore.fetchCategories(),
      siteStore.fetchSiteData()
    ])

    // Initialize currency settings
    if (siteStore.settings) {
      initCurrencySettings(siteStore.settings)
    }
  } catch (err) {
    error.value = err.message || 'Failed to load data'
    console.error('Homepage load error:', err)
  } finally {
    loading.value = false
  }
})

// Computed: unique parent categories (first 6) with icons
const parentCategories = computed(() => {
  return categoryStore.categories
    .filter(cat => !cat.parent_id)
    .slice(0, 6)
})

const featuredProducts = computed(() => productStore.featuredProducts || [])
const newArrivals = computed(() => productStore.newArrivals || [])

// Category icon mapping
const categoryIcons = {
  'Electronics': '🔌',
  'Clothing': '👕',
  'Home & Garden': '🏡',
  'Sports': '⚽',
  'Photography': '📷',
  'Books': '📚',
  'Toys': '🧸',
  'Beauty': '💄',
  'Automotive': '🚗',
  'Food': '🍕',
  default: '📦'
}

function getCategoryIcon(name) {
  return categoryIcons[name] || categoryIcons.default
}
</script>

<template>
  <div>
    <!-- ===== HERO SECTION ===== -->
    <section class="relative overflow-hidden">
      <!-- Hero Slides -->
      <div
        v-for="(slide, index) in heroSlides"
        :key="index"
        class="relative transition-all duration-700 ease-in-out"
        :class="index === currentSlide ? 'block' : 'hidden'"
      >
        <div
          class="relative flex items-center min-h-[450px] md:min-h-[550px]"
          :class="slide.bg_image ? '' : `bg-gradient-to-r ${slide.bg}`"
        >
          <!-- Background Image -->
          <div v-if="slide.bg_image" class="absolute inset-0 bg-cover bg-center" :style="{ backgroundImage: `url(${slide.bg_image})` }">
            <div class="absolute inset-0 bg-black/40"></div>
          </div>
          <!-- Decorative circles (only when no bg image) -->
          <div v-if="!slide.bg_image" class="absolute top-10 left-10 w-64 h-64 bg-white/5 rounded-full blur-3xl" aria-hidden="true"></div>
          <div v-if="!slide.bg_image" class="absolute bottom-10 right-10 w-80 h-80 bg-white/5 rounded-full blur-3xl" aria-hidden="true"></div>
          <div v-if="!slide.bg_image" class="absolute top-1/2 right-1/4 w-40 h-40 bg-white/5 rounded-full blur-2xl" aria-hidden="true"></div>

          <div class="container mx-auto px-4 relative z-10">
            <div :key="'hero-' + index + '-' + currentSlide" class="grid md:grid-cols-2 gap-8 items-center">
              <div class="text-white">
                <!-- Badge with gradient text -->
                <div v-if="slide.badge_text" class="hero-animate hero-delay-1 inline-flex items-center gap-2 px-5 py-1.5 rounded-full text-sm font-medium mb-6 bg-white/10 backdrop-blur-md border border-white/20 shadow-lg">
                  <span class="w-2 h-2 rounded-full bg-gradient-to-r from-primary-300 to-primary-400 animate-pulse"></span>
                  <span class="bg-gradient-to-r from-primary-200 via-white to-primary-300 bg-clip-text text-transparent font-semibold">
                    {{ slide.badge_text }}
                  </span>
                </div>

                <!-- Title -->
                <h1 class="hero-animate hero-delay-2 text-4xl md:text-6xl font-bold mb-4 leading-tight">
                  {{ slide.title }}
                </h1>

                <!-- Subtitle -->
                <p class="hero-animate hero-delay-3 text-xl md:text-2xl text-white/80 mb-8">
                  {{ slide.subtitle }}
                </p>

                <!-- CTA Button -->
                <div class="hero-animate hero-delay-4">
                  <RouterLink
                    :to="slide.link"
                    class="group inline-flex items-center gap-2 px-8 py-4 bg-primary-500 text-white font-bold rounded-xl hover:bg-primary-400 transition-all duration-300 shadow-lg shadow-primary-900/30 hover:shadow-xl hover:scale-105 hover:-translate-y-0.5"
                  >
                    {{ slide.cta }}
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                  </RouterLink>
                </div>
              </div>
              <div class="hidden md:flex justify-center hero-animate hero-delay-5">
                <img v-if="slide.feature_image" :src="slide.feature_image" :alt="slide.title"
                  class="max-w-full h-auto rounded-2xl shadow-2xl" style="max-height:350px;object-fit:contain;" />
                <span v-else class="text-[200px] leading-none animate-bounce-slow">{{ slide.image }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Hero Navigation Arrows -->
      <button
        @click="prevSlide"
        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm hover:bg-white/40 text-white rounded-full flex items-center justify-center transition-all z-20"
        aria-label="Previous slide"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <button
        @click="nextSlide"
        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/20 backdrop-blur-sm hover:bg-white/40 text-white rounded-full flex items-center justify-center transition-all z-20"
        aria-label="Next slide"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>

      <!-- Slide Indicators -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-3 z-20">
        <button
          v-for="(slide, index) in heroSlides"
          :key="index"
          @click="currentSlide = index"
          class="transition-all duration-300 rounded-full"
          :class="index === currentSlide ? 'w-8 h-3 bg-white' : 'w-3 h-3 bg-white/40 hover:bg-white/70'"
          :aria-label="'Go to slide ' + (index + 1)"
        ></button>
      </div>
    </section>

    <!-- ===== CATEGORIES SECTION ===== -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <span class="text-primary-500 font-semibold text-sm uppercase tracking-wider">Categories</span>
          <h2 class="text-3xl md:text-4xl font-bold mt-2">{{ getThemeText('shop_by_category', 'Shop by Category') }}</h2>
          <p class="text-gray-500 mt-3 max-w-xl mx-auto">Browse our wide range of product categories</p>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-8 text-center animate-pulse">
            <div class="w-16 h-16 bg-gray-200 rounded-2xl mx-auto mb-4"></div>
            <div class="h-4 bg-gray-200 rounded w-20 mx-auto"></div>
          </div>
        </div>

        <!-- Category Grid -->
        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <RouterLink
            v-for="category in parentCategories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="group bg-white rounded-2xl p-6 text-center hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-gray-100"
          >
            <div class="w-16 h-16 bg-primary-50 group-hover:bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4 overflow-hidden transition-colors">
              <img v-if="category.thumbnail" :src="getImageUrl(category.thumbnail)" :alt="category.name" class="w-full h-full object-cover rounded-2xl" />
              <span v-else class="text-3xl">{{ getCategoryIcon(category.name) }}</span>
            </div>
            <h3 class="font-semibold text-gray-800 group-hover:text-primary-600 transition-colors">{{ category.name }}</h3>
            <p class="text-sm text-gray-400 mt-1">{{ category.products?.length || 0 }} items</p>
          </RouterLink>
        </div>

        <div class="text-center mt-8">
          <RouterLink
            to="/categories"
            class="inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors group"
          >
            View All Categories
            <span class="group-hover:translate-x-1 transition-transform" aria-hidden="true">→</span>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- ===== FEATURED PRODUCTS ===== -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
          <div>
            <span class="text-primary-500 font-semibold text-sm uppercase tracking-wider">Featured</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2">Featured Products</h2>
            <p class="text-gray-500 mt-2">Hand-picked products just for you</p>
          </div>
          <RouterLink
            to="/products"
            class="mt-4 md:mt-0 inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors group"
          >
            View All
            <span class="group-hover:translate-x-1 transition-transform" aria-hidden="true">→</span>
          </RouterLink>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-gray-100 overflow-hidden animate-pulse">
            <div class="aspect-square bg-gray-200"></div>
            <div class="p-4 space-y-3">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              <div class="h-5 bg-gray-200 rounded w-1/4"></div>
            </div>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-16">
          <div class="text-6xl mb-4">😕</div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Oops! Something went wrong</h3>
          <p class="text-gray-500 mb-6">{{ error }}</p>
          <button @click="reloadPage" class="btn btn-primary">Try Again</button>
        </div>

        <!-- Products Grid -->
        <div v-else-if="featuredProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="product in featuredProducts"
            :key="product.id"
            class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
          >
            <RouterLink :to="`/product/${product.slug}`">
              <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
                <!-- Wishlist Heart Icon -->
                <button
                  @click.prevent="toggleWishlist(product, $event)"
                  :disabled="wishlistToggling === product.id"
                  class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 shadow-sm"
                  :class="isWishlisted(product.id)
                    ? 'bg-red-50 text-red-500 hover:bg-red-100'
                    : 'bg-white/90 text-gray-400 hover:text-red-500 hover:bg-red-50'"
                  :title="isWishlisted(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                >
                  <svg v-if="wishlistToggling === product.id" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <svg v-else class="w-4.5 h-4.5" :fill="isWishlisted(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>

                <!-- Discount badge -->
                <div
                  v-if="getDiscountPercent(product) > 0"
                  class="absolute top-3 left-3 z-10 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg"
                >
                  -{{ getDiscountPercent(product) }}%
                </div>
                <!-- Product Image -->
                <img
                  v-if="product.thumbnail || product.images?.length > 0"
                  :src="getImageUrl(product.thumbnail || product.images?.[0]?.image)"
                  :alt="product.name"
                  width="400"
                  height="400"
                  loading="lazy"
                  class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-500"
                />
                <span v-else class="text-6xl group-hover:scale-110 transition-transform duration-500" aria-hidden="true">📦</span>
                <!-- Quick add overlay -->
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                  <template v-if="isVariableProduct(product)">
                    <RouterLink
                      :to="`/product/${product.slug}`"
                      class="inline-block bg-white text-gray-900 px-5 py-2.5 rounded-xl font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0 shadow-lg hover:bg-primary-600 hover:text-white"
                    >
                      Select Options
                    </RouterLink>
                  </template>
                  <button
                    v-else
                    @click.prevent="addToCart(product)"
                    class="bg-white text-gray-900 px-5 py-2.5 rounded-xl font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0 shadow-lg hover:bg-primary-600 hover:text-white inline-flex items-center"
                  >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Add to Cart
                  </button>
                </div>
              </div>
            </RouterLink>
            <div class="p-4">
              <RouterLink :to="`/product/${product.slug}`">
                <h3 class="font-semibold text-gray-800 mb-1 line-clamp-1 group-hover:text-primary-600 transition-colors">
                  {{ product.name }}
                </h3>
              </RouterLink>
              <p class="text-sm text-gray-400 mb-3 line-clamp-1">{{ product.short_description }}</p>
              <div class="space-y-2">
                <!-- Quick variant swatches (variable products only) -->
                <QuickVariantSelector
                  v-if="isVariableProduct(product) && product.variants"
                  :variants="product.variants"
                  @select="(r) => onQuickVariantSelect(product, r)"
                />
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-gray-900">
                      {{ formatPrice(getEffectivePrice(product)) }}
                    </span>
                    <span
                      v-if="product.sale_price && product.regular_price"
                      class="text-sm text-gray-400 line-through"
                    >
                      {{ formatPrice(product.regular_price) }}
                    </span>
                  </div>
                  <template v-if="isVariableProduct(product)">
                    <button
                      @click="goToProduct(product.slug)"
                      class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-xl flex items-center justify-center transition-all"
                      title="View product options"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </template>
                  <button
                    v-else
                    @click="addToCart(product)"
                    class="w-9 h-9 bg-primary-50 hover:bg-primary-600 text-primary-600 hover:text-white rounded-xl flex items-center justify-center transition-all"
                  >
                    <span class="text-lg">+</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16 bg-gray-50 rounded-2xl">
          <div class="text-6xl mb-4">📦</div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">No featured products yet</h3>
          <p class="text-gray-500">Check back soon for our featured collection.</p>
        </div>
      </div>
    </section>

    <!-- ===== DEAL OF THE DAY BANNER ===== -->
    <section class="py-16 bg-gradient-to-r from-orange-500 via-red-500 to-pink-500">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
          <div class="text-white">
            <div class="inline-block px-4 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-medium mb-4">
              ⚡ Flash Sale
            </div>
            <h2 class="text-3xl md:text-5xl font-bold mb-4">Deal of the Day</h2>
            <p class="text-xl text-white/80 mb-6">Limited time offers you can't miss!</p>
            <div class="flex gap-4">
              <RouterLink
                to="/products"
                class="inline-flex items-center gap-2 px-8 py-3 bg-white text-gray-900 font-semibold rounded-xl hover:bg-gray-100 transition-all"
              >
                Shop Deals
                <span>🔥</span>
              </RouterLink>
            </div>
          </div>
          <div class="grid grid-cols-4 gap-3">
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 text-center text-white">
              <div class="text-3xl font-bold">12</div>
              <div class="text-sm text-white/70">Hours</div>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 text-center text-white">
              <div class="text-3xl font-bold">45</div>
              <div class="text-sm text-white/70">Mins</div>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 text-center text-white">
              <div class="text-3xl font-bold">30</div>
              <div class="text-sm text-white/70">Secs</div>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-xl p-4 text-center text-white">
              <div class="text-3xl font-bold">💥</div>
              <div class="text-sm text-white/70">Go!</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== NEW ARRIVALS ===== -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
          <div>
            <span class="text-primary-500 font-semibold text-sm uppercase tracking-wider">New</span>
            <h2 class="text-3xl md:text-4xl font-bold mt-2">New Arrivals</h2>
            <p class="text-gray-500 mt-2">The latest products added to our store</p>
          </div>
          <RouterLink
            to="/products?sort=newest"
            class="mt-4 md:mt-0 inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors group"
          >
            View All
            <span class="group-hover:translate-x-1 transition-transform" aria-hidden="true">→</span>
          </RouterLink>
        </div>

        <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-gray-100 overflow-hidden animate-pulse">
            <div class="aspect-square bg-gray-200"></div>
            <div class="p-4 space-y-3">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              <div class="h-5 bg-gray-200 rounded w-1/4"></div>
            </div>
          </div>
        </div>

        <div v-else-if="newArrivals.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="product in newArrivals"
            :key="product.id"
            class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
          >
            <RouterLink :to="`/product/${product.slug}`">
              <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
                <!-- Wishlist Heart Icon -->
                <button
                  @click.prevent="toggleWishlist(product, $event)"
                  :disabled="wishlistToggling === product.id"
                  class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 shadow-sm"
                  :class="isWishlisted(product.id)
                    ? 'bg-red-50 text-red-500 hover:bg-red-100'
                    : 'bg-white/90 text-gray-400 hover:text-red-500 hover:bg-red-50'"
                  :title="isWishlisted(product.id) ? 'Remove from wishlist' : 'Add to wishlist'"
                >
                  <svg v-if="wishlistToggling === product.id" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <svg v-else class="w-4.5 h-4.5" :fill="isWishlisted(product.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>
                <div class="absolute top-3 left-3 z-10 bg-emerald-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                  ✨ New
                </div>
                <!-- Product Image -->                  <img
                    v-if="product.thumbnail || product.images?.length > 0"
                    :src="getImageUrl(product.thumbnail || product.images?.[0]?.image)"
                    :alt="product.name"
                    width="400"
                    height="400"
                    loading="lazy"
                    class="w-full h-full object-contain p-4 group-hover:scale-110 transition-transform duration-500"
                  />
                  <span v-else class="text-6xl group-hover:scale-110 transition-transform duration-500" aria-hidden="true">📦</span>
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all duration-300 flex items-center justify-center">
                  <template v-if="isVariableProduct(product)">
                    <RouterLink
                      :to="`/product/${product.slug}`"
                      class="inline-block bg-white text-gray-900 px-5 py-2.5 rounded-xl font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0 shadow-lg hover:bg-primary-600 hover:text-white"
                    >
                      Select Options
                    </RouterLink>
                  </template>
                  <button
                    v-else
                    @click.prevent="addToCart(product)"
                    class="bg-white text-gray-900 px-5 py-2.5 rounded-xl font-semibold opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-4 group-hover:translate-y-0 shadow-lg hover:bg-primary-600 hover:text-white inline-flex items-center"
                  >
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Add to Cart
                  </button>
                </div>
              </div>
            </RouterLink>
            <div class="p-4">
              <RouterLink :to="`/product/${product.slug}`">
                <h3 class="font-semibold text-gray-800 mb-1 line-clamp-1 group-hover:text-primary-600 transition-colors">
                  {{ product.name }}
                </h3>
              </RouterLink>
              <p class="text-sm text-gray-400 mb-3 line-clamp-1">{{ product.short_description }}</p>                <div class="space-y-2">
                <!-- Quick variant swatches (variable products only) -->
                <QuickVariantSelector
                  v-if="isVariableProduct(product) && product.variants"
                  :variants="product.variants"
                  @select="(r) => onQuickVariantSelect(product, r)"
                />
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-gray-900">
                      {{ formatPrice(getEffectivePrice(product)) }}
                    </span>
                    <span
                      v-if="product.sale_price && product.regular_price"
                      class="text-sm text-gray-400 line-through"
                    >
                      {{ formatPrice(product.regular_price) }}
                    </span>
                  </div>
                  <template v-if="isVariableProduct(product)">
                    <button
                      @click="goToProduct(product.slug)"
                      class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-xl flex items-center justify-center transition-all"
                      title="View product options"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </template>
                  <button
                    v-else
                    @click="addToCart(product)"
                    class="w-9 h-9 bg-primary-50 hover:bg-primary-600 text-primary-600 hover:text-white rounded-xl flex items-center justify-center transition-all"
                  >
                    <span class="text-lg">+</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-16 bg-white rounded-2xl">
          <div class="text-6xl mb-4">🚀</div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">No new arrivals yet</h3>
          <p class="text-gray-500">New products will appear here as they're added.</p>
        </div>
      </div>
    </section>

    <!-- ===== TRUST FEATURES (from DB) ===== -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold">Why Shop With Us</h2>
          <p class="text-gray-500 mt-2">We provide the best shopping experience</p>
        </div>
        <div v-if="siteStore.settings.trust_features" class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div
            v-for="(feature, index) in siteStore.settings.trust_features"
            :key="index"
            class="text-center group"
          >
            <div class="w-16 h-16 bg-primary-50 group-hover:bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl transition-colors">
              {{ feature.icon || '⭐' }}
            </div>
            <h3 class="font-semibold text-lg mb-1">{{ feature.title }}</h3>
            <p class="text-gray-500 text-sm">{{ feature.description }}</p>
          </div>
        </div>
        <!-- Fallback if no features configured -->
        <div v-else class="text-center text-gray-400 py-8">
          <p>Configure trust features in Admin → Settings → Site Settings</p>
        </div>
      </div>
    </section>

    <!-- ===== NEWSLETTER ===== -->
    <section class="py-16 bg-gradient-to-r from-primary-600 to-primary-800">
      <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center text-white">
          <div class="text-5xl mb-4">📬</div>
          <h2 class="text-3xl md:text-4xl font-bold mb-4">Subscribe to Our Newsletter</h2>
          <p class="text-primary-100 mb-8 text-lg">Get exclusive deals, new arrivals, and special offers straight to your inbox.</p>            <form @submit.prevent class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
            <label for="newsletter-email" class="sr-only">Email address for newsletter</label>
            <input
              id="newsletter-email"
              type="email"
              autocomplete="email"
              name="email"
              placeholder="Enter your email…"
              class="flex-1 px-5 py-3.5 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white/50"
            />
            <button
              type="submit"
              class="px-8 py-3.5 bg-white text-primary-700 font-semibold rounded-xl hover:bg-gray-100 transition-all shadow-lg hover:shadow-xl"
            >
              Subscribe
            </button>
          </form>
          <p class="text-primary-200 text-sm mt-4">No spam, unsubscribe anytime.</p>
        </div>
      </div>
    </section>

    <!-- ===== BRANDS SECTION (from DB) ===== -->
    <section class="py-12 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="text-center mb-8">
          <h3 class="text-lg font-semibold text-gray-600 uppercase tracking-wider">Trusted Brands</h3>
        </div>
        <div v-if="siteStore.settings.trusted_brands" class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-50">
          <div
            v-for="(brand, index) in siteStore.settings.trusted_brands"
            :key="index"
            class="text-2xl font-bold text-gray-400"
          >
            {{ typeof brand === 'string' ? brand : brand.name }}
          </div>
        </div>
        <div v-else class="text-center text-gray-400">
          <p>Configure brands in Admin → Settings → Site Settings</p>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}
.animate-bounce-slow {
  animation: bounce-slow 3s ease-in-out infinite;
}

/* Hero Entrance Animations */
@keyframes hero-fade-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.hero-animate {
  animation: hero-fade-up 0.6s cubic-bezier(0.21, 1.02, 0.73, 1) both;
}

.hero-delay-1 { animation-delay: 0.1s; }
.hero-delay-2 { animation-delay: 0.25s; }
.hero-delay-3 { animation-delay: 0.4s; }
.hero-delay-4 { animation-delay: 0.55s; }
.hero-delay-5 { animation-delay: 0.7s; }
</style>
