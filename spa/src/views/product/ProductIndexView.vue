<script setup>
import { ref, onMounted, reactive } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { useCategoryStore } from '@/stores/category'
import { useBrandStore } from '@/stores/brand'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { getImageUrl } from '@/utils/image'
import { formatPrice } from '@/utils/currency'
import QuickVariantSelector from '@/components/QuickVariantSelector.vue'
import { useSeoMeta } from '@/composables/useSeoMeta'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const categoryStore = useCategoryStore()
const brandStore = useBrandStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const authStore = useAuthStore()
const toast = useToast()
const { setSeoMeta, clearSeoMeta } = useSeoMeta()

// Set SEO meta on mount
onMounted(() => {
  setSeoMeta({
    title: 'Products',
    description: 'Browse our collection of products. Find the best deals and shop online.',
    keywords: 'products, shop, online, deals',
    type: 'website'
  })
})

const loading = ref(true)
const selectedCategory = ref(route.query.category || '')
const selectedBrand = ref(route.query.brand || '')
const sortBy = ref('latest')
const wishlistToggling = ref(null)

// ── Quick Variant Selection state ──
const selectedVariants = reactive({})

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

function goToProduct(slug) {
  router.push({ name: 'product.show', params: { slug } })
}

onMounted(async () => {
  await Promise.all([fetchProducts(), categoryStore.fetchCategories(), brandStore.fetchBrands()])
})

async function fetchProducts() {
  loading.value = true
  try {
    const params = {}
    if (selectedCategory.value) {
      params.category = selectedCategory.value
    }
    if (selectedBrand.value) {
      params.brand = selectedBrand.value
    }
    if (sortBy.value) {
      params.sort = sortBy.value
    }
    await productStore.fetchProducts(params)
  } finally {
    loading.value = false
  }
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

// Check if a product has variants (variable product)
function isVariableProduct(product) {
  if (!product) return false
  if (product.product_type === 'variable' || product.type === 'variable') return true
  if (product.has_variants === true) return true
  if (product.variants && product.variants.length > 0) return true
  return false
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">All Products</h1>

    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Sidebar Filters -->
      <aside class="lg:w-64">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="font-semibold text-lg mb-4">Categories</h3>
          <ul class="space-y-2">
            <li>
              <button
                @click="selectedCategory = ''; fetchProducts()"
                :class="['w-full text-left py-2 px-4 rounded', !selectedCategory ? 'bg-primary-100 text-primary-700' : 'hover:bg-gray-100']"
              >
                All Products
              </button>
            </li>
            <li v-for="category in categoryStore.activeCategories" :key="category.id">
              <button
                @click="selectedCategory = category.slug; fetchProducts()"
                :class="['w-full text-left py-2 px-4 rounded', selectedCategory === category.slug ? 'bg-primary-100 text-primary-700' : 'hover:bg-gray-100']"
              >
                {{ category.name }}
              </button>
            </li>
          </ul>
        </div>

        <!-- Brand Filter -->
        <div v-if="brandStore.activeBrands.length" class="bg-white rounded-lg shadow-md p-6 mt-4">
          <h3 class="font-semibold text-lg mb-4">Brands</h3>
          <ul class="space-y-2">
            <li>
              <button
                @click="selectedBrand = ''; fetchProducts()"
                :class="['w-full text-left py-2 px-4 rounded', !selectedBrand ? 'bg-primary-100 text-primary-700' : 'hover:bg-gray-100']"
              >
                All Brands
              </button>
            </li>
            <li v-for="brand in brandStore.activeBrands" :key="brand.id">
              <button
                @click="selectedBrand = brand.slug; fetchProducts()"
                :class="['w-full text-left py-2 px-4 rounded flex items-center justify-between', selectedBrand === brand.slug ? 'bg-primary-100 text-primary-700' : 'hover:bg-gray-100']"
              >
                <span class="flex items-center gap-2">
                  <img v-if="brand.logo" :src="getImageUrl(brand.logo)" :alt="brand.name" class="w-5 h-5 rounded object-cover" />
                  {{ brand.name }}
                </span>
                <span class="text-xs text-gray-400">{{ brand.product_count }}</span>
              </button>
            </li>
          </ul>
        </div>
      </aside>

      <!-- Products Grid -->
      <div class="flex-1">
        <!-- Sort Options -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6 flex justify-between items-center">
          <span class="text-gray-600">
            {{ productStore.products.length }} products found
          </span>
          <label for="sort-products" class="sr-only">Sort products</label>
          <select id="sort-products" v-model="sortBy" @change="fetchProducts" class="input w-auto">
            <option value="latest">Latest</option>
            <option value="price_low">Price: Low to High</option>
            <option value="price_high">Price: High to Low</option>
            <option value="name">Name: A-Z</option>
          </select>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="text-4xl">⏳</div>
          <p class="text-gray-600 mt-4">Loading products...</p>
        </div>

        <!-- Products -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="product in productStore.products"
            :key="product.id"
            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <RouterLink :to="`/product/${product.slug}`">
              <div class="aspect-square bg-gray-200 flex items-center justify-center relative">
                <!-- Wishlist Heart -->
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
                <!-- Product Image -->
                <img
                  v-if="product.thumbnail || product.images?.length > 0"
                  :src="getImageUrl(product.thumbnail || product.images[0]?.image)"
                  :alt="product.name"
                  width="400"
                  height="400"
                  loading="lazy"
                  class="w-full h-full object-contain p-4"
                />
                <span v-else class="text-6xl" aria-hidden="true">📷</span>
              </div>
            </RouterLink>
            <div class="p-4">
              <RouterLink :to="`/product/${product.slug}`">
                <h3 class="font-semibold text-lg mb-2 hover:text-primary-600">
                  {{ product.name }}
                </h3>
              </RouterLink>
              <p class="text-gray-600 text-sm mb-2 line-clamp-2">
                {{ product.short_description }}
              </p>
              <div class="space-y-2">
                <!-- Quick variant swatches (variable products only) -->
                <QuickVariantSelector
                  v-if="isVariableProduct(product) && product.variants"
                  :variants="product.variants"
                  @select="(r) => onQuickVariantSelect(product, r)"
                />
                <div class="flex items-center justify-between">
                  <span class="text-xl font-bold text-primary-600">
                    {{ formatPrice(getEffectivePrice(product)) }}
                  </span>
                  <template v-if="isVariableProduct(product)">
                    <button @click="goToProduct(product.slug)" class="btn btn-primary btn-sm !px-2.5" title="View Options">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                  </template>
                  <button v-else @click="addToCart(product)" class="btn btn-primary btn-sm !px-2.5" title="Add to Cart">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="!loading && productStore.products.length === 0" class="text-center py-12">
          <div class="text-4xl">📦</div>
          <p class="text-gray-600 mt-4">No products found.</p>
        </div>
      </div>
    </div>
  </div>
</template>