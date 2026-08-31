import api from './api'

export default {
  /**
   * Get all active brands with product counts
   */
  getAll() {
    return api.get('/brands')
  }
}
