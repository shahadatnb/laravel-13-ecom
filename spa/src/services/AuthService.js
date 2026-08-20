import api from './api'

export default {
  /**
   * Register a new user
   */
  register(data) {
    return api.post('/auth/register', data)
  },

  /**
   * Login user
   */
  login(credentials) {
    return api.post('/auth/login', credentials)
  },

  /**
   * Logout user
   */
  logout() {
    return api.post('/auth/logout')
  },

  /**
   * Get current user profile
   */
  getProfile() {
    return api.get('/customer')
  },

  /**
   * Update user profile
   */
  updateProfile(data) {
    return api.put('/customer', data)
  },

  /**
   * Check if user is authenticated
   */
  check() {
    const token = localStorage.getItem('token')
    return !!token
  },

  /**
   * Store token
   */
  setToken(token) {
    localStorage.setItem('token', token)
  },

  /**
   * Remove token
   */
  removeToken() {
    localStorage.removeItem('token')
  }
}