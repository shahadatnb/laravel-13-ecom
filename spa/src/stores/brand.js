import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import BrandService from '@/services/BrandService'

export const useBrandStore = defineStore('brand', () => {
  const brands = ref([])
  const loading = ref(false)
  const error = ref(null)

  const activeBrands = computed(() => brands.value)

  async function fetchBrands() {
    loading.value = true
    error.value = null
    try {
      const response = await BrandService.getAll()
      brands.value = response.data.data || []
      return response.data.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  function clearError() {
    error.value = null
  }

  return {
    brands,
    loading,
    error,
    activeBrands,
    fetchBrands,
    clearError
  }
})
