<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useWishlistStore } from '@/stores/wishlist'
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'
import { getImageUrl } from '@/utils/image'
import { formatPrice } from '@/utils/currency'

const wishlistStore = useWishlistStore()
const cartStore = useCartStore()
const toast = useToast()

const loading = ref(true)
const removing = ref(null)
const addingCart = ref(null)

onMounted(async () => {
  await loadWishlist()
})

async function loadWishlist() {
  loading.value = true
  try {
    await wishlistStore.fetchWishlist()
  } finally {
    loading.value = false
  }
}

async function handleRemove(item) {
  removing.value = item.id
  try {
    const success = await wishlistStore.removeItem(item)
    if (success) {
      toast.success('Removed from wishlist.')
    } else {
      toast.error('Failed to remove item.')
    }
  } finally {
    removing.value = null
  }
}

function handleAddToCart(item) {
  addingCart.value = item.id
  try {
    cartStore.addItem({
      id: item.product_id,
      variant_id: item.variant_id || null,
      variant_name: item.variant_name || null,
      variant_sku: item.variant_sku || null,
      name: item.product_name,
      price: item.sale_price || item.regular_price,
      image: item.product_image,
      sku: item.variant_sku || null,
    })
    toast.success('Added to cart!')
  } finally {
    addingCart.value = null
  }
}


</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
        <p v-if="!loading && wishlistStore.itemCount > 0" class="text-gray-500 mt-1">
          {{ wishlistStore.itemCount }} {{ wishlistStore.itemCount === 1 ? 'item' : 'items' }} saved
        </p>
      </div>
      <RouterLink
        to="/products"
        class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white rounded-xl font-medium hover:bg-primary-700 transition-colors shadow-sm"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Browse Products
      </RouterLink>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="n in 4" :key="n" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-pulse">
        <div class="aspect-square bg-gray-200"></div>
        <div class="p-4 space-y-3">
          <div class="h-4 bg-gray-200 rounded w-3/4"></div>
          <div class="h-4 bg-gray-200 rounded w-1/3"></div>
          <div class="h-10 bg-gray-200 rounded w-full mt-4"></div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="wishlistStore.itemCount === 0" class="text-center py-20">
      <div class="text-8xl mb-6">💔</div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">Your wishlist is empty</h2>
      <p class="text-gray-500 mb-8 max-w-md mx-auto">
        Start saving your favorite products! Browse our collection and click the heart icon to add items here.
      </p>
      <RouterLink
        to="/products"
        class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition-colors shadow-lg shadow-primary-200"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        Start Shopping
      </RouterLink>
    </div>

    <!-- Wishlist Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="item in wishlistStore.items"
        :key="item.id"
        class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-200"
      >
        <!-- Product Image -->
        <RouterLink :to="`/product/${item.product_slug}`">
          <div class="aspect-square bg-gray-50 p-6 flex items-center justify-center relative">
            <img
              v-if="item.product_image"
              :src="getImageUrl(item.product_image)"
              :alt="item.product_name"
              width="400"
              height="400"
              loading="lazy"
              class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
            />
            <div v-else class="text-6xl opacity-30">📷</div>

            <!-- Remove Button (overlay) -->
            <button
              @click.prevent="handleRemove(item)"
              :disabled="removing === item.id"
              class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 shadow-sm flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all opacity-0 group-hover:opacity-100"
              aria-label="Remove from wishlist"
            >
              <svg v-if="removing !== item.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
            </button>

            <!-- Stock Badge -->
            <div
              v-if="item.stock !== undefined"
              class="absolute top-3 left-3 text-xs font-medium px-2.5 py-1 rounded-full"
              :class="item.stock > 0
                ? 'bg-green-100 text-green-700'
                : 'bg-red-100 text-red-700'"
            >
              {{ item.stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </div>
          </div>
        </RouterLink>

        <!-- Product Info -->
        <div class="p-4">
          <RouterLink :to="`/product/${item.product_slug}`">
            <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 group-hover:text-primary-600 transition-colors min-h-[2.5rem]">
              {{ item.product_name }}
            </h3>
          </RouterLink>

          <p v-if="item.variant_name" class="text-xs text-gray-400 mb-2">
            Variant: {{ item.variant_name }}
          </p>

          <!-- Price -->
          <div class="flex items-baseline gap-2 mb-3">
            <span class="text-lg font-bold text-primary-600">
              {{ formatPrice(item.sale_price) }}
            </span>
            <span
              v-if="item.regular_price && parseFloat(item.sale_price) < parseFloat(item.regular_price)"
              class="text-sm text-gray-400 line-through"
            >
              {{ formatPrice(item.regular_price) }}
            </span>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <button
              @click="handleAddToCart(item)"
              :disabled="addingCart === item.id || item.stock <= 0"
              class="flex-1 py-2.5 px-3 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-1.5"
              :class="item.stock > 0
                ? 'bg-primary-600 text-white hover:bg-primary-700 active:scale-[0.97] shadow-sm'
                : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
            >
              <svg v-if="addingCart !== item.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
              </svg>
              <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
              {{ item.stock > 0 ? (addingCart === item.id ? 'Adding...' : 'Add to Cart') : 'Out of Stock' }}
            </button>
            <button
              @click="handleRemove(item)"
              :disabled="removing === item.id"
              class="w-10 h-10 rounded-xl border border-gray-200 flex items-center justify-center text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all flex-shrink-0"
              aria-label="Remove"
            >
              <svg v-if="removing !== item.id" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              <svg v-else class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
