import api from './api'

export default {
  /**
   * Get all categories
   */
  getAll() {
    return api.get('/categories')
  },

  /**
   * Get category by ID
   */
  getById(id) {
    return api.get(`/categories/${id}`)
  },

  /**
   * Get category with products
   */
  getCategoryWithProducts(id) {
    return api.get(`/categories/${id}`)
  }
}