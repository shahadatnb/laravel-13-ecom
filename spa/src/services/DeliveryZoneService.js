import api from './api'

export default {
  /**
   * Get all active delivery zones with their districts
   */
  getZones() {
    return api.get('/delivery-zones')
  },

  /**
   * Get flat list of all active districts with zone info
   */
  getDistricts() {
    return api.get('/delivery-zones/districts')
  },

  /**
   * Calculate delivery charge for a given district
   * @param {string} district - District name
   * @param {number} orderAmount - Order subtotal for free delivery check
   */
  calculateCharge(district, orderAmount = 0) {
    return api.get('/delivery-zones/calculate', {
      params: { district, order_amount: orderAmount }
    })
  }
}
