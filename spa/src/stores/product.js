import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import ProductService from '@/services/ProductService'

export const useProductStore = defineStore('product', () => {
  const products = ref([])
  const featuredProducts = ref([])
  const newArrivals = ref([])
  const currentProduct = ref(null)
  const relatedProducts = ref([])
  const loading = ref(false)
  const error = ref(null)

  const allProducts = computed(() => products.value)

  async function fetchProducts(params = {}) {
    loading.value = true
    error.value = null
    try {
      const response = await ProductService.getAll(params)
      products.value = response.data.data.data || []
      return response.data.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchFeatured() {
    loading.value = true
    try {
      const response = await ProductService.getFeatured()
      featuredProducts.value = response.data.data || []
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function fetchNewArrivals() {
    loading.value = true
    try {
      const response = await ProductService.getNewArrivals()
      newArrivals.value = response.data.data || []
    } catch (err) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  }

  async function fetchProductById(id) {
    loading.value = true
    error.value = null
    try {
      const response = await ProductService.getById(id)
      currentProduct.value = response.data.data
      relatedProducts.value = response.data.related_products || []
      return response.data.data
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      loading.value = false
    }
  }

  async function searchProducts(query) {
    loading.value = true
    error.value = null
    try {
      const response = await ProductService.search(query)
      products.value = response.data.data || []
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
    products,
    featuredProducts,
    newArrivals,
    currentProduct,
    relatedProducts,
    loading,
    error,
    allProducts,
    fetchProducts,
    fetchFeatured,
    fetchNewArrivals,
    fetchProductById,
    searchProducts,
    clearError
  }
})