import api from './api'

export default {
  /**
   * Get wallet summary with paginated transaction history.
   *
   * @param {object}  [params]
   * @param {number}  [params.page]     - Page number for transactions
   * @param {number}  [params.per_page] - Transactions per page (max 50)
   */
  getAll(params = {}) {
    return api.get('/wallet', { params })
  }
}
