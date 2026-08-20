import api from './api'

export default {
  /**
   * Get all wishlist items for the authenticated customer
   */
  getAll() {
    return api.get('/wishlist')
  },

  /**
   * Add a product (or variant) to the wishlist
   */
  add(productId, variantId = null) {
    return api.post('/wishlist', {
      product_id: productId,
      product_variant_id: variantId,
    })
  },

  /**
   * Remove an item from the wishlist
   */
  remove(wishlistItemId) {
    return api.delete(`/wishlist/${wishlistItemId}`)
  },

  /**
   * Check if specific products are in the user's wishlist
   */
  check(productIds) {
    return api.post('/wishlist/check', { product_ids: productIds })
  },
}
