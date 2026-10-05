import api from './api'

export default {
  /**
   * Get all addresses for the authenticated customer
   */
  getAll() {
    return api.get('/addresses')
  },

  /**
   * Create a new address
   */
  create(data) {
    return api.post('/addresses', data)
  },

  /**
   * Update an existing address
   */
  update(id, data) {
    return api.put(`/addresses/${id}`, data)
  },

  /**
   * Delete an address
   */
  remove(id) {
    return api.delete(`/addresses/${id}`)
  },
}
