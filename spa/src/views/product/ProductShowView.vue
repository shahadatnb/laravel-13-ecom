<style scoped>
.scrollbar-thin::-webkit-scrollbar {
  height: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

/* Vertical thumbnail scrollbar */
.thumb-scrollbar::-webkit-scrollbar {
  width: 3px;
}
.thumb-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.thumb-scrollbar::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

/* Gallery crossfade transition */
.gallery-fade-enter-active,
.gallery-fade-leave-active {
  transition: opacity 0.2s ease;
}
.gallery-fade-enter-from {
  opacity: 0;
}
.gallery-fade-leave-to {
  opacity: 0;
}

/* Lightbox slide transition */
.lightbox-fade-enter-active,
.lightbox-fade-leave-active {
  transition: opacity 0.3s ease;
}
.lightbox-fade-enter-from,
.lightbox-fade-leave-to {
  opacity: 0;
}

.lightbox-slide-enter-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.lightbox-slide-leave-active {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.lightbox-slide-enter-from.slide-next {
  transform: translateX(50px);
  opacity: 0;
}
.lightbox-slide-enter-from.slide-prev {
  transform: translateX(-50px);
  opacity: 0;
}
.lightbox-slide-leave-to.slide-next {
  transform: translateX(-30px);
  opacity: 0;
}
.lightbox-slide-leave-to.slide-prev {
  transform: translateX(30px);
  opacity: 0;
}

/* Lightbox backdrop blur */
.lightbox-backdrop {
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}
</style>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { useCartStore } from '@/stores/cart'
import { useWishlistStore } from '@/stores/wishlist'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import { getImageUrl } from '@/utils/image'
import { formatPrice } from '@/utils/currency'
import VariantSelector from '@/components/VariantSelector.vue'
import { useSeoMeta } from '@/composables/useSeoMeta'


const route = useRoute()
const router = useRouter()
const productStore = useProductStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const authStore = useAuthStore()
const toast = useToast()
const { setSeoMeta, setProductJsonLd, setBreadcrumbJsonLd, clearSeoMeta } = useSeoMeta()

const quantity = ref(1)
const loading = ref(true)
const selectedImage = ref(null)
const zoomActive = ref(false)
const zoomPosition = ref({ x: 0, y: 0 })
const activeTab = ref('description')

// ── Enhanced Gallery State ──
const lightboxActive = ref(false)
const currentImageIndex = ref(0)
const slideDirection = ref('next') // 'next' or 'prev'
const galleryTransitioning = ref(false)

// Computed: all available images (product images + currently selected variant images)
const galleryImages = computed(() => {
  const product = productStore.currentProduct
  if (!product) return []
  // Use variant images if a variant with images is selected
  const v = selectedVariant.value
  if (v && v.images && v.images.length > 0) return v.images
  if (product.images && product.images.length > 0) return product.images
  // Fallback to the product thumbnail when no gallery images exist
  if (product.thumbnail) return [{ image: product.thumbnail }]
  return []
})

// Update currentImageIndex whenever selectedImage changes
watch(selectedImage, (img) => {
  if (!img || !galleryImages.value.length) {
    currentImageIndex.value = 0
    return
  }
  const idx = galleryImages.value.findIndex(i => i.image === img.image)
  if (idx >= 0) currentImageIndex.value = idx
})

// Template refs for thumbnail containers
const thumbStripVertical = ref(null)
const thumbStripHorizontal = ref(null)

/**
 * Scroll the active thumbnail button into view within its scroll container.
 * Uses direct child indexing by currentImageIndex rather than a CSS class
 * selector, so it won't break if thumbnail styling classes change.
 */
function scrollToActiveThumbnail() {
  const idx = currentImageIndex.value
  const containers = [thumbStripVertical.value, thumbStripHorizontal.value]
  containers.forEach(function(container) {
    if (!container) return
    const activeBtn = container.children[idx]
    if (activeBtn && typeof activeBtn.scrollIntoView === 'function') {
      activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' })
    }
  })
}

watch(currentImageIndex, function() {
  // Don't scroll page thumbnails while lightbox is open
  if (lightboxActive.value) return
  // Defer scroll until after DOM update so Vue has rendered the active thumbnail
  requestAnimationFrame(scrollToActiveThumbnail)
})

function goToImage(index) {
  if (galleryTransitioning.value || index === currentImageIndex.value) return
  slideDirection.value = index > currentImageIndex.value ? 'next' : 'prev'
  galleryTransitioning.value = true
  currentImageIndex.value = index
  selectedImage.value = galleryImages.value[index]
  setTimeout(() => { galleryTransitioning.value = false }, 300)
}

function prevImage() {
  const len = galleryImages.value.length
  if (len === 0) return
  goToImage((currentImageIndex.value - 1 + len) % len)
}

function nextImage() {
  const len = galleryImages.value.length
  if (len === 0) return
  goToImage((currentImageIndex.value + 1) % len)
}

function openLightbox() {
  lightboxActive.value = true
  document.body.style.overflow = 'hidden'
}

function closeLightbox() {
  lightboxActive.value = false
  document.body.style.overflow = ''
}

// Keyboard navigation for lightbox
function handleLightboxKeydown(e) {
  if (!lightboxActive.value) return
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowLeft') prevImage()
  if (e.key === 'ArrowRight') nextImage()
}

// Watch lightbox state for keyboard listener
watch(lightboxActive, (active) => {
  if (active) {
    document.addEventListener('keydown', handleLightboxKeydown)
  } else {
    document.removeEventListener('keydown', handleLightboxKeydown)
  }
})

// Clean up if component unmounts while lightbox is open
onUnmounted(() => {
  document.removeEventListener('keydown', handleLightboxKeydown)
  document.body.style.overflow = ''
  clearSeoMeta()
})

// Can add to cart — requires variant selection for variable products
const canAddToCart = computed(() => {
  if (!inStock.value) return false
  if (hasVariants.value && !selectedVariant.value) return false
  return true
})

// ── Variant Selection (managed by VariantSelector component) ──
const selectedAttributes = ref({})
const selectedVariant = ref(null)

function onVariantSelected(variant) {
  selectedVariant.value = variant
}

function onAttributesChanged(attrs) {
  selectedAttributes.value = attrs
}

// Receive the best-matching variant image from VariantSelector
function onVariantImageUpdate(img) {
  if (img) {
    selectedImage.value = img
  } else if (!selectedVariant.value && productStore.currentProduct?.images?.length) {
    selectedImage.value = productStore.currentProduct.images[0]
  }
}

// Computed: is the product variable-type with variants?
const hasVariants = computed(() => {
  const p = productStore.currentProduct
  if (!p) return false
  if (p.product_type === 'variable' || p.type === 'variable') {
    return p.variants && p.variants.length > 0
  }
  if (p.variants && p.variants.length > 0) return true
  return p.has_variants === true
})

// Watch: reset variant selection when product changes + SEO
watch(
  () => productStore.currentProduct,
  (newProduct) => {
    selectedAttributes.value = {}
    selectedVariant.value = null
    if (newProduct?.images?.length) {
      selectedImage.value = newProduct.images[0]
    }
    // SEO meta tags
    if (newProduct) {
      const imageUrl = newProduct.images?.[0]?.url || newProduct.thumbnail || ''
      setSeoMeta({
        title: newProduct.meta_title || newProduct.name,
        description: newProduct.meta_description || newProduct.short_description || newProduct.description?.replace(/<[^>]*>/g, '').substring(0, 160),
        keywords: newProduct.meta_keywords || newProduct.name,
        image: imageUrl,
        type: 'product'
      })
      setProductJsonLd(newProduct)
      setBreadcrumbJsonLd([
        { name: 'Home', url: '/' },
        { name: newProduct.category?.name || 'Products', url: `/category/${newProduct.category?.slug}` },
        { name: newProduct.name }
      ])
    }
  }
)

// Watch: update displayed image when variant with its own images is selected
// (fallback in case the onVariantImageUpdate from VariantSelector doesn't fire)
watch(selectedVariant, (variant) => {
  if (variant && variant.images && variant.images.length > 0) {
    selectedImage.value = variant.images[0]
  } else if (productStore.currentProduct?.images?.length) {
    selectedImage.value = productStore.currentProduct.images[0]
  }
})

onMounted(async () => {
  await fetchProduct()
})

// Watch for slug changes — e.g. clicking a related product, browser back/forward
watch(
  () => route.params.slug,
  async (newSlug) => {
    if (newSlug) {
      await fetchProduct(newSlug)
    }
  }
)

async function fetchProduct(slug) {
  loading.value = true
  quantity.value = 1
  selectedImage.value = null
  zoomActive.value = false
  activeTab.value = 'description'
  try {
    await productStore.fetchProductById(slug || route.params.slug)
    if (productStore.currentProduct?.images?.length) {
      selectedImage.value = productStore.currentProduct.images[0]
    }
  } finally {
    loading.value = false
  }
}

const discountPercentage = computed(() => {
  const product = productStore.currentProduct
  const v = selectedVariant.value
  const reg = v?.regular_price || product?.regular_price
  const sale = v?.sale_price || product?.sale_price
  if (!reg || !sale) return 0
  const regular = parseFloat(reg)
  const saleP = parseFloat(sale)
  if (regular <= 0 || saleP >= regular) return 0
  return Math.round(((regular - saleP) / regular) * 100)
})

const inStock = computed(() => {
  const v = selectedVariant.value
  if (v) return (v.stock ?? 0) > 0
  return (productStore.currentProduct?.stock ?? 0) > 0
})

const stockStatusText = computed(() => {
  const v = selectedVariant.value
  const stock = v ? (v.stock ?? 0) : (productStore.currentProduct?.stock ?? 0)
  if (stock === 0) return 'Out of Stock'
  if (stock <= 10) return `Only ${stock} left in stock`
  return 'In Stock'
})

function addToCart() {
  if (!canAddToCart.value) {
    if (!inStock.value) {
      toast.error('This product is currently out of stock!')
    } else if (hasVariants.value && !selectedVariant.value) {
      toast.warning('Please select all product options first.')
    }
    return
  }
  if (productStore.currentProduct) {
    // Include selected variant info when adding to cart
    const item = {
      ...productStore.currentProduct,
      variant_id: selectedVariant.value?.id || null,
      variant_name: selectedVariant.value?.name || null,
      variant_sku: selectedVariant.value?.sku || null,
      // Override price with variant price if selected
      price: selectedVariant.value?.price || productStore.currentProduct.sale_price || productStore.currentProduct.regular_price,
      stock: selectedVariant.value?.stock ?? productStore.currentProduct.stock
    }
    cartStore.addItem(item, quantity.value)
    const name = selectedVariant.value?.name || productStore.currentProduct.name
    toast.success(`${quantity.value} x ${name} added to cart!`)
  }
}

function increment() {
  const max = productStore.currentProduct?.maximum_order || 99
  if (quantity.value < max) quantity.value++
}

function decrement() {
  if (quantity.value > 1) quantity.value--
}

function handleMouseMove(event) {
  const rect = event.currentTarget.getBoundingClientRect()
  const x = ((event.clientX - rect.left) / rect.width) * 100
  const y = ((event.clientY - rect.top) / rect.height) * 100
  zoomPosition.value = { x, y }
}

// ── Wishlist ──
const wishlistLoading = ref(false)

const isWishlisted = computed(() => {
  const p = productStore.currentProduct
  if (!p) return false
  return wishlistStore.isWishlisted(p.id, selectedVariant.value?.id || null)
})

async function toggleWishlist() {
  if (!authStore.isAuthenticated) {
    toast.warning('Please login to add items to your wishlist.')
    return
  }
  wishlistLoading.value = true
  try {
    const p = productStore.currentProduct
    const result = await wishlistStore.toggle(p.id, selectedVariant.value?.id || null)
    if (result.added) {
      toast.success('Added to wishlist!')
    } else {
      toast.success('Removed from wishlist.')
    }
  } finally {
    wishlistLoading.value = false
  }
}

</script>

<template>
  <div class="min-h-screen bg-gray-50 overflow-x-hidden">
    <div class="container mx-auto px-4 py-8">
      <!-- Breadcrumb -->
      <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <RouterLink to="/" class="hover:text-primary-600">Home</RouterLink>
        <span>/</span>
        <RouterLink
          v-if="productStore.currentProduct?.category"
          :to="`/category/${productStore.currentProduct.category.slug}`"
          class="hover:text-primary-600"
        >
          {{ productStore.currentProduct.category.name }}
        </RouterLink>
        <span v-if="productStore.currentProduct">/</span>
        <span v-if="productStore.currentProduct" class="text-gray-900 font-medium truncate max-w-[200px]">
          {{ productStore.currentProduct.name }}
        </span>
      </nav>

      <!-- Loading Skeleton -->
      <div v-if="loading" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8 animate-pulse">
          <div class="bg-gray-200 aspect-square rounded-xl"></div>
          <div class="space-y-6">
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            <div class="h-8 bg-gray-200 rounded w-3/4"></div>
            <div class="h-4 bg-gray-200 rounded w-full"></div>
            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
            <div class="h-10 bg-gray-200 rounded w-1/3"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            <div class="h-12 bg-gray-200 rounded w-full"></div>
          </div>
        </div>
      </div>

      <!-- Product Details -->
      <div v-else-if="productStore.currentProduct" class="space-y-8">
        <!-- Main Product Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">                <!-- Image Section -->
            <div class="border-b lg:border-b-0 lg:border-r border-gray-100 bg-white">
              <!-- Desktop: side-by-side layout with vertical thumbnails -->
              <div class="flex flex-col lg:flex-row h-full">
                <!-- Vertical Thumbnail Strip (desktop left) -->
                <div
                  ref="thumbStripVertical"
                  v-if="galleryImages.length > 1"
                  class="hidden lg:flex flex-col gap-2 p-3 border-r border-gray-100 bg-gray-50/50 overflow-y-auto max-h-[600px]"
                  style="min-width: 90px; width: 90px;"
                >
                  <button
                    v-for="(img, index) in galleryImages"
                    :key="'thumb-' + index"
                    @click="goToImage(index)"
                    class="relative w-[66px] h-[66px] rounded-xl overflow-hidden flex-shrink-0 transition-all duration-200 group/thumb border-2"
                    :class="currentImageIndex === index
                      ? 'border-primary-500 ring-2 ring-primary-200 shadow-md'
                      : 'border-gray-200 hover:border-gray-400 hover:shadow-sm'"
                  >
                    <img
                      :src="getImageUrl(img.image)"
                      :alt="'View image ' + (index + 1)"
                      class="w-full h-full object-cover"
                      loading="lazy"
                    />
                    <!-- Number badge -->
                    <span
                      class="absolute bottom-0.5 right-0.5 text-[9px] font-bold text-white bg-black/50 px-1 rounded"
                    >{{ index + 1 }}</span>
                  </button>
                </div>

                <!-- Main Image Area -->
                <div class="flex-1 relative">
                  <!-- Main Image with Zoom & Click-to-lightbox -->
                  <div
                    class="relative aspect-square bg-gray-50 overflow-hidden group cursor-crosshair"
                    @mouseenter="zoomActive = true"
                    @mouseleave="zoomActive = false"
                    @mousemove="handleMouseMove"
                  >
                    <!-- Crossfade container -->
                    <Transition name="gallery-fade" mode="out-in">
                      <img
                        :key="selectedImage?.image || galleryImages[0]?.image"
                        :src="getImageUrl(selectedImage?.image || galleryImages[0]?.image)"
                        :alt="productStore.currentProduct.name"
                        class="w-full h-full object-contain p-8 transition-transform duration-200 cursor-pointer"
                        :class="{ 'scale-150': zoomActive }"
                        :style="zoomActive ? {
                          transformOrigin: `${zoomPosition.x}% ${zoomPosition.y}%`
                        } : {}"
                        @click="openLightbox"
                      />
                    </Transition>

                    <!-- Click to expand hint -->
                    <div
                      class="absolute top-4 right-4 bg-black/50 text-white text-xs px-2.5 py-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1.5 pointer-events-none"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                      </svg>
                      Click to expand
                    </div>

                    <!-- Discount Badge -->
                    <div
                      v-if="discountPercentage > 0"
                      class="absolute top-4 left-4 bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-lg z-10"
                    >
                      -{{ discountPercentage }}%
                    </div>

                    <!-- Prev / Next Arrows on main image (desktop hover) -->
                    <div v-if="galleryImages.length > 1" class="absolute inset-x-0 top-0 bottom-0 flex items-center justify-between px-3 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button
                        @click.stop="prevImage"
                        class="w-10 h-10 rounded-full bg-white/90 shadow-lg flex items-center justify-center hover:bg-white hover:scale-110 transition-all text-gray-700"
                        aria-label="Previous image"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                      </button>
                      <button
                        @click.stop="nextImage"
                        class="w-10 h-10 rounded-full bg-white/90 shadow-lg flex items-center justify-center hover:bg-white hover:scale-110 transition-all text-gray-700"
                        aria-label="Next image"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                      </button>
                    </div>

                    <!-- Image Counter Badge -->
                    <div
                      v-if="galleryImages.length > 1"
                      class="absolute bottom-4 right-4 bg-black/60 text-white text-xs font-medium px-3 py-1 rounded-full backdrop-blur-sm"
                    >
                      {{ currentImageIndex + 1 }} / {{ galleryImages.length }}
                    </div>

                    <!-- Zoom Lens Indicator -->
                    <div
                      v-if="zoomActive"
                      class="absolute inset-0 pointer-events-none"
                    >
                      <div
                        class="absolute w-32 h-32 border-2 border-primary-500 bg-primary-100 bg-opacity-20 rounded-full pointer-events-none"
                        :style="{
                          left: `calc(${zoomPosition.x}% - 64px)`,
                          top: `calc(${zoomPosition.y}% - 64px)`
                        }"
                      ></div>
                    </div>

                    <!-- Zoom Hint -->
                    <div
                      v-if="!zoomActive"
                      class="absolute bottom-4 left-4 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1.5 pointer-events-none"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                      </svg>
                      Hover to zoom
                    </div>
                  </div>

                  <!-- Horizontal Thumbnail Strip (mobile only) -->
                  <div
                    ref="thumbStripHorizontal"
                    v-if="galleryImages.length > 1"
                    class="lg:hidden flex gap-2.5 px-4 pb-4 pt-2 overflow-x-auto flex-nowrap scrollbar-thin"
                  >
                    <button
                      v-for="(img, index) in galleryImages"
                      :key="'mthumb-' + index"
                      @click="goToImage(index)"
                      class="w-16 h-16 min-w-[4rem] rounded-xl overflow-hidden flex-shrink-0 transition-all duration-200 border-2"
                      :class="currentImageIndex === index
                        ? 'border-primary-500 ring-2 ring-primary-200 shadow-md'
                        : 'border-gray-200 hover:border-gray-400'"
                    >
                      <img
                        :src="getImageUrl(img.image)"
                        :alt="'View image ' + (index + 1)"
                        class="w-full h-full object-cover"
                        loading="lazy"
                      />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-6 lg:p-8 flex flex-col">
              <!-- Category & Brand -->
              <div class="flex items-center gap-3 mb-4">
                <RouterLink
                  v-if="productStore.currentProduct.category"
                  :to="`/category/${productStore.currentProduct.category.slug}`"
                  class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary-50 text-primary-700 rounded-full text-xs font-medium hover:bg-primary-100 transition-colors"
                >
                  {{ productStore.currentProduct.category.name }}
                </RouterLink>
                <span v-if="productStore.currentProduct.brand" class="text-xs text-gray-400">|</span>
                <span v-if="productStore.currentProduct.brand" class="text-xs text-gray-500 font-medium">
                  {{ productStore.currentProduct.brand.name }}
                </span>
              </div>

              <!-- Product Name -->
              <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3 leading-tight">
                {{ productStore.currentProduct.name }}
              </h1>

              <!-- Short Description -->
              <p v-if="productStore.currentProduct.short_description" class="text-gray-500 text-sm mb-5 leading-relaxed">
                {{ productStore.currentProduct.short_description }}
              </p>

              <!-- Price Section — uses variant price if a variant is selected -->
              <div class="mb-6">
                <div class="flex items-baseline gap-3">
                  <span class="text-3xl lg:text-4xl font-bold text-primary-600">
                    {{ formatPrice(selectedVariant?.price || productStore.currentProduct.sale_price || productStore.currentProduct.regular_price) }}
                  </span>
                  <span
                    v-if="productStore.currentProduct.regular_price && parseFloat(selectedVariant?.price || productStore.currentProduct.sale_price || 0) < parseFloat(productStore.currentProduct.regular_price)"
                    class="text-xl text-gray-400 line-through"
                  >
                    {{ formatPrice(productStore.currentProduct.regular_price) }}
                  </span>
                  <span
                    v-if="discountPercentage > 0"
                    class="text-sm font-semibold text-green-600 bg-green-50 px-2.5 py-0.5 rounded-full"
                  >
                    Save {{ discountPercentage }}%
                  </span>
                </div>
              </div>

              <!-- Stock Status -->
              <div class="flex items-center gap-2 mb-6">
                <span
                  class="inline-block w-2.5 h-2.5 rounded-full"
                  :class="inStock ? 'bg-green-500' : 'bg-red-500'"
                ></span>
                <span
                  class="text-sm font-medium"
                  :class="inStock ? 'text-green-700' : 'text-red-600'"
                >
                  {{ stockStatusText }}
                </span>
              </div>


              <!-- ===== VARIANT SELECTOR (reusable component) ===== -->
              <VariantSelector
                v-if="hasVariants"
                :variants="productStore.currentProduct?.variants || []"
                @update:modelValue="onVariantSelected"
                @update:selectedAttributes="onAttributesChanged"
                @update:variantImage="onVariantImageUpdate"
              />
              <!-- ===== END VARIANT SELECTOR ===== -->

              <!-- Divider (hide when variants shown because variant section has its own spacing) -->
              <div v-if="!hasVariants" class="border-t border-gray-100 mb-6"></div>

              <!-- Quantity Selector -->
              <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2.5">Quantity</label>
                <div class="flex items-center gap-1">
                  <button
                    @click="decrement"
                    :disabled="quantity <= 1"
                    class="w-11 h-11 rounded-xl border border-gray-300 flex items-center justify-center text-lg font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-400 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </button>
                  <input
                    v-model.number="quantity"
                    type="number"
                    min="1"
                    :max="productStore.currentProduct.maximum_order || 99"
                    class="w-16 h-11 text-center border border-gray-300 rounded-xl font-semibold text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                  />
                  <button
                    @click="increment"
                    :disabled="quantity >= (productStore.currentProduct.maximum_order || 99)"
                    class="w-11 h-11 rounded-xl border border-gray-300 flex items-center justify-center text-lg font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-400 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Wishlist + Add to Cart Row -->
              <div class="flex gap-3 mb-4">
                <!-- Wishlist Heart Button -->
                <button
                  @click="toggleWishlist"
                  :disabled="wishlistLoading"
                  class="w-14 h-14 rounded-xl border-2 flex items-center justify-center flex-shrink-0 transition-all duration-200"
                  :class="isWishlisted
                    ? 'border-red-200 bg-red-50 text-red-500 hover:bg-red-100'
                    : 'border-gray-200 bg-white text-gray-400 hover:border-red-300 hover:text-red-400 hover:bg-red-50'"
                  :title="isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'"
                >
                  <svg v-if="wishlistLoading" class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  <svg v-else class="w-6 h-6" :fill="isWishlisted ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                </button>

                <!-- Add to Cart Button -->
                <button
                  @click="addToCart"
                  :disabled="!canAddToCart"
                  class="flex-1 py-3.5 px-6 rounded-xl font-bold text-lg transition-all duration-200 flex items-center justify-center gap-3"
                  :class="canAddToCart
                    ? 'bg-primary-600 text-white hover:bg-primary-700 active:scale-[0.98] shadow-lg shadow-primary-200'
                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                >
                <template v-if="canAddToCart">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                  </svg>
                  {{ hasVariants && !selectedVariant ? 'Select Options' : 'Add to Cart' }}
                </template>
                <template v-else-if="!inStock">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                  Out of Stock
                </template>
                <template v-else>
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                  </svg>
                  Select Options
                </template>
              </button>
            </div>

              <!-- Product Meta -->
              <div class="mt-6 pt-6 border-t border-gray-100">
                <div class="grid grid-cols-2 gap-y-3 gap-x-6 text-sm">
                  <div class="flex items-center gap-2 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span>SKU:</span>
                    <span class="font-medium text-gray-900">{{ selectedVariant?.sku || productStore.currentProduct.sku || 'N/A' }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Brand:</span>
                    <span class="font-medium text-gray-900">{{ productStore.currentProduct.brand?.name || 'N/A' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs Section (Description / Additional Info) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <!-- Tab Header -->
          <div class="border-b border-gray-100">
            <div class="flex">
              <button
                @click="activeTab = 'description'"
                class="px-6 py-4 text-sm font-semibold transition-colors relative"
                :class="activeTab === 'description' ? 'text-primary-600' : 'text-gray-500 hover:text-gray-700'"
              >
                Description
                <div
                  v-if="activeTab === 'description'"
                  class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600"
                ></div>
              </button>
              <button
                @click="activeTab = 'additional'"
                class="px-6 py-4 text-sm font-semibold transition-colors relative"
                :class="activeTab === 'additional' ? 'text-primary-600' : 'text-gray-500 hover:text-gray-700'"
              >
                Additional Information
                <div
                  v-if="activeTab === 'additional'"
                  class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary-600"
                ></div>
              </button>
            </div>
          </div>

          <!-- Tab Content -->
          <div class="p-6 lg:p-8">
            <div v-if="activeTab === 'description'">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Product Description</h2>
              <div class="prose prose-gray max-w-none">
                <p class="text-gray-600 leading-relaxed">
                  {{ productStore.currentProduct.description }}
                </p>
              </div>
            </div>

            <div v-if="activeTab === 'additional'" class="space-y-4">
              <h2 class="text-xl font-bold text-gray-900 mb-4">Additional Information</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="productStore.currentProduct.weight" class="flex justify-between py-3 px-4 bg-gray-50 rounded-xl">
                  <span class="text-gray-500">Weight</span>
                  <span class="font-medium">{{ productStore.currentProduct.weight }} kg</span>
                </div>
                <div v-if="productStore.currentProduct.length" class="flex justify-between py-3 px-4 bg-gray-50 rounded-xl">
                  <span class="text-gray-500">Dimensions</span>
                  <span class="font-medium">{{ productStore.currentProduct.length }} × {{ productStore.currentProduct.width }} × {{ productStore.currentProduct.height }} cm</span>
                </div>
                <div v-if="productStore.currentProduct.tax_class" class="flex justify-between py-3 px-4 bg-gray-50 rounded-xl">
                  <span class="text-gray-500">Tax Class</span>
                  <span class="font-medium capitalize">{{ productStore.currentProduct.tax_class }}</span>
                </div>
                <div v-if="productStore.currentProduct.shipping_class" class="flex justify-between py-3 px-4 bg-gray-50 rounded-xl">
                  <span class="text-gray-500">Shipping Class</span>
                  <span class="font-medium capitalize">{{ productStore.currentProduct.shipping_class }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Products -->
        <div v-if="productStore.relatedProducts?.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6 lg:p-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Related Products</h2>
            <RouterLink
              :to="`/category/${productStore.currentProduct.category?.slug}`"
              class="text-sm font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1"
            >
              View All
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </RouterLink>
          </div>

          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="product in productStore.relatedProducts"
              :key="product.id"
              class="group bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-lg hover:border-gray-200 transition-all duration-200"
            >
              <RouterLink :to="`/product/${product.slug}`">
                <div class="aspect-square bg-gray-50 p-4 flex items-center justify-center">
                  <img
                    :src="getImageUrl(product.thumbnail || product.images?.[0]?.image)"
                    :alt="product.name"
                    class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300"
                  />
                </div>
              </RouterLink>
              <div class="p-4">
                <p class="text-xs text-gray-400 mb-1">{{ product.brand?.name || product.category?.name }}</p>
                <RouterLink :to="`/product/${product.slug}`">
                  <h3 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                    {{ product.name }}
                  </h3>
                </RouterLink>
                <div class="flex items-center justify-between">
                  <div>
                    <span class="text-lg font-bold text-primary-600">
                      {{ formatPrice(product.sale_price || product.regular_price) }}
                    </span>
                    <span
                      v-if="product.regular_price && product.sale_price && parseFloat(product.sale_price) < parseFloat(product.regular_price)"
                      class="text-xs text-gray-400 line-through ml-1.5"
                    >
                      {{ formatPrice(product.regular_price) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Not Found -->
      <div v-else class="text-center py-20">
        <div class="text-6xl mb-6">😕</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Product Not Found</h2>
        <p class="text-gray-500 mb-8">The product you're looking for doesn't exist or has been removed.</p>
        <div class="flex items-center justify-center gap-4">
          <RouterLink to="/products" class="btn btn-primary px-6 py-3">
            Browse Products
          </RouterLink>
          <RouterLink to="/" class="px-6 py-3 text-gray-600 hover:text-gray-900 font-medium transition-colors">
            Go Home
          </RouterLink>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== LIGHTBOX OVERLAY ===== -->
  <Teleport to="body">
    <Transition name="lightbox-fade">
      <div
        v-if="lightboxActive && galleryImages.length"
        class="fixed inset-0 z-[9999] lightbox-backdrop bg-black/85 flex items-center justify-center"
        @click.self="closeLightbox"
      >
        <!-- Close button -->
        <button
          @click="closeLightbox"
          class="absolute top-5 right-5 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all hover:scale-110 z-10"
          aria-label="Close lightbox"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Image counter top -->
        <div class="absolute top-5 left-5 text-white/80 text-sm font-medium z-10">
          {{ currentImageIndex + 1 }} / {{ galleryImages.length }}
        </div>

        <!-- Prev arrow -->
        <button
          v-if="galleryImages.length > 1"
          @click="prevImage"
          class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all hover:scale-110 hover:bg-white/30 z-10"
          aria-label="Previous image"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <!-- Image container with slide transition -->
        <div class="max-w-5xl max-h-[85vh] w-full h-full flex items-center justify-center p-4">
          <Transition
            name="lightbox-slide"
            mode="out-in"
            @before-enter="(el) => { if (slideDirection === 'next') el.classList.add('slide-next'); else el.classList.add('slide-prev'); }"
            @after-leave="(el) => { el.classList.remove('slide-next', 'slide-prev'); }"
          >
            <img
              :key="galleryImages[currentImageIndex]?.image || currentImageIndex"
              :src="getImageUrl(galleryImages[currentImageIndex]?.image)"
              :alt="'Full size image ' + (currentImageIndex + 1)"
              class="max-w-full max-h-full object-contain select-none"
              draggable="false"
            />
          </Transition>
        </div>

        <!-- Next arrow -->
        <button
          v-if="galleryImages.length > 1"
          @click="nextImage"
          class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all hover:scale-110 hover:bg-white/30 z-10"
          aria-label="Next image"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <!-- Bottom thumbnail strip inside lightbox -->
        <div
          v-if="galleryImages.length > 1"
          class="absolute bottom-5 left-1/2 -translate-x-1/2 flex gap-2 px-4 py-2 rounded-full bg-black/40 backdrop-blur-sm"
        >
          <button
            v-for="(img, idx) in galleryImages"
            :key="'lb-thumb-' + idx"
            @click="goToImage(idx)"
            class="w-10 h-10 rounded-lg overflow-hidden transition-all duration-200 border-2 flex-shrink-0"
            :class="currentImageIndex === idx ? 'border-white scale-110 shadow-lg' : 'border-transparent opacity-60 hover:opacity-100'"
          >
            <img
              :src="getImageUrl(img.image)"
              :alt="'Thumbnail ' + (idx + 1)"
              class="w-full h-full object-cover"
              loading="lazy"
            />
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
