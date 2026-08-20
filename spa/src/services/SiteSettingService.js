import api from './api'

export default {
  /**
   * Get all public site settings and hero slides
   */
  getAll() {
    return api.get('/site-settings')
  }
}
