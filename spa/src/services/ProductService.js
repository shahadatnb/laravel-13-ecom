import api from './api'

export default {
  /**
   * Get all products
   */
  getAll(params = {}) {
    return api.get('/products', { params })
  },

  /**
   * Get product by ID
   */
  getById(id) {
    return api.get(`/products/${id}`)
  },

  /**
   * Search products
   */
  search(query) {
    return api.get('/products/search', { params: { q: query } })
  },

  /**
   * Get products by category
   */
  getByCategory(categoryId, params = {}) {
    return api.get(`/products/category/${categoryId}`, { params })
  },

  /**
   * Get featured products
   */
  getFeatured() {
    return api.get('/products/featured')
  },

  /**
   * Get new arrivals
   */
  getNewArrivals() {
    return api.get('/products/new-arrivals')
  }
}