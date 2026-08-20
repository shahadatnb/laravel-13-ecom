import { defineStore } from 'pinia'
import api from '../services/api'

export const useProfileStore = defineStore('profile', {
  state: () => ({
    user: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchProfile() {
      this.loading = true
      this.error = null

      try {
        const response = await api.get('/api/v1/profile')
        this.user = response.data.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch profile.'
      } finally {
        this.loading = false
      }
    },

    async updateProfile(payload) {
      this.loading = true
      this.error = null

      try {
        const response = await api.put('/api/v1/profile', payload)
        this.user = response.data.data
        return response.data
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update profile.'
        throw error
      } finally {
        this.loading = false
      }
    },
  },
})
