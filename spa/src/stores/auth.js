import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import AuthService from '@/services/AuthService'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials) {
    try {
      const response = await AuthService.login(credentials)
      token.value = response.data.data.token
      localStorage.setItem('token', response.data.data.token)
      await fetchUser()
      return response
    } catch (error) {
      throw error
    }
  }

  async function register(data) {
    try {
      const response = await AuthService.register(data)
      token.value = response.data.data.token
      localStorage.setItem('token', response.data.data.token)
      await fetchUser()
      return response
    } catch (error) {
      throw error
    }
  }

  async function fetchUser() {
    try {
      const response = await AuthService.getProfile()
      user.value = response.data.data
    } catch (error) {
      logout()
      throw error
    }
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser
  }
})