import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import WishlistService from '@/services/WishlistService'

export const useWishlistStore = defineStore('wishlist', () => {
  const items = ref([])
  const loading = ref(false)
  const wishlistIds = ref(new Set())

  const itemCount = computed(() => items.value.length)

  /**
   * Check if a specific product (or variant) is wishlisted
   */
  function isWishlisted(productId, variantId = null) {
    const key = variantId ? `${productId}-${variantId}` : `${productId}`
    return wishlistIds.value.has(key)
  }

  /**
   * Fetch all wishlist items from the API
   */
  async function fetchWishlist() {
    loading.value = true
    try {
      const response = await WishlistService.getAll()
      items.value = response.data.data || []

      // Build lookup set for O(1) checks
      const set = new Set()
      items.value.forEach(item => {
        const key = item.variant_id ? `${item.product_id}-${item.variant_id}` : `${item.product_id}`
        set.add(key)
      })
      wishlistIds.value = set
    } catch {
      items.value = []
    } finally {
      loading.value = false
    }
  }

  /**
   * Toggle an item in/out of the wishlist.
   * Returns { added: boolean } so the caller can show appropriate feedback.
   */
  async function toggle(productId, variantId = null) {
    const key = variantId ? `${productId}-${variantId}` : `${productId}`

    if (wishlistIds.value.has(key)) {
      // Find the local item to get its ID for removal
      const localItem = items.value.find(item =>
        item.product_id === productId &&
        (item.variant_id ?? null) === (variantId ?? null)
      )
      if (localItem) {
        try {
          await WishlistService.remove(localItem.id)
          items.value = items.value.filter(item => item.id !== localItem.id)
          wishlistIds.value.delete(key)
          return { added: false }
        } catch {
          return { added: true }
        }
      }
    }

    // Add to wishlist
    try {
      const response = await WishlistService.add(productId, variantId)
      const newItem = response.data.data
      if (newItem) {
        items.value.unshift({
          id: newItem.id,
          product_id: newItem.product_id,
          variant_id: newItem.variant_id,
        })
      }
      wishlistIds.value.add(key)
      return { added: true }
    } catch {
      return { added: false }
    }
  }

  /**
   * Remove a specific item by its wishlist ID
   */
  async function removeItem(wishlistItem) {
    try {
      await WishlistService.remove(wishlistItem.id)
      const key = wishlistItem.variant_id
        ? `${wishlistItem.product_id}-${wishlistItem.variant_id}`
        : `${wishlistItem.product_id}`
      items.value = items.value.filter(item => item.id !== wishlistItem.id)
      wishlistIds.value.delete(key)
      return true
    } catch {
      return false
    }
  }

  return {
    items,
    loading,
    itemCount,
    isWishlisted,
    fetchWishlist,
    toggle,
    removeItem,
  }
})
