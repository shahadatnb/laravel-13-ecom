import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import CategoryService from '@/services/CategoryService'

export const useCategoryStore = defineStore('category', () => {
  const categories = ref([])
  const currentCategory = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const activeCategories = computed(() => {
    return categories.value.filter(cat => cat.status === 'active')
  })

  async function fetchCategories() {
    loading.value = true
    error.value = null
    try {
      const response = await CategoryService.getAll()
      categories.value = response.data.data || []
      return response.data.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchCategoryById(id) {
    loading.value = true
    error.value = null
    try {
      const response = await CategoryService.getById(id)
      currentCategory.value = response.data.data
      return response.data.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchCategoryWithProducts(id) {
    loading.value = true
    error.value = null
    try {
      const response = await CategoryService.getCategoryWithProducts(id)
      currentCategory.value = response.data.data
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
    categories,
    currentCategory,
    loading,
    error,
    activeCategories,
    fetchCategories,
    fetchCategoryById,
    fetchCategoryWithProducts,
    clearError
  }
})