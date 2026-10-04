import api from './api'

export default {
  /**
   * Get summary stats for the customer dashboard
   * (total orders, wallet balance, addresses, wishlist).
   */
  getStats() {
    return api.get('/dashboard/stats')
  }
}
