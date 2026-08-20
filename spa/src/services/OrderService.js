import api from './api'

export default {
  /**
   * Get all orders for the current customer.
   */
  getAll(params = {}) {
    return api.get('/orders', { params })
  },

  /**
   * Get a single order by ID.
   */
  getById(id) {
    return api.get(`/orders/${id}`)
  },

  /**
   * Create a new order.
   *
   * @param {object} orderData
   * @param {Array}  orderData.items        - Array of cart items
   * @param {string} [orderData.coupon_code]
   * @param {number} [orderData.discount]
   * @param {number} [orderData.tax]
   * @param {number} [orderData.shipping_charge]
   * @param {string} [orderData.currency]
   * @param {string} [orderData.payment_method]
   * @param {object} [orderData.shipping_address]
   * @param {object} [orderData.billing_address]
   * @param {string} [orderData.notes]
   */
  create(orderData) {
    // If user is authenticated, use the protected endpoint
    // Otherwise, use the public guest endpoint
    const token = localStorage.getItem('token')
    const endpoint = token ? '/orders' : '/guest-orders'
    return api.post(endpoint, orderData)
  },

  /**
   * Cancel an order.
   */
  cancel(id) {
    return api.post(`/orders/${id}/cancel`)
  },

  /**
   * Get order tracking timeline.
   */
  getTracking(id) {
    return api.get(`/orders/${id}/tracking`)
  }
}
