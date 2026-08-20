import api from './api'

export default {
  /**
   * Get all published pages (for navigation)
   */
  getAll() {
    return api.get('/pages')
  },

  /**
   * Get a single page by slug
   */
  getBySlug(slug) {
    return api.get(`/pages/${slug}`)
  }
}
