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
  const loadingMore = ref(false)
  const error = ref(null)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const totalProducts = ref(0)
  const perPage = ref(12)
  const lastParams = ref({})

  const allProducts = computed(() => products.value)
  const hasMore = computed(() => currentPage.value < lastPage.value)

  async function fetchProducts(params = {}, options = {}) {
    const { append = false } = options
    if (append) {
      loadingMore.value = true
    } else {
      loading.value = true
    }
    error.value = null
    try {
      const page = append ? currentPage.value + 1 : 1
      const response = await ProductService.getAll({
        ...params,
        page,
        per_page: perPage.value,
      })
      const paginator = response.data.data || {}
      const items = paginator.data || []
      if (append) {
        products.value.push(...items)
      } else {
        products.value = items
        lastParams.value = { ...params }
      }
      currentPage.value = paginator.current_page || page
      lastPage.value = paginator.last_page || page
      totalProducts.value = paginator.total || products.value.length
      if (paginator.per_page) {
        perPage.value = paginator.per_page
      }
      return paginator
    } catch (err) {
      error.value = err.message
      throw err
    } finally {
      if (append) {
        loadingMore.value = false
      } else {
        loading.value = false
      }
    }
  }

  async function loadMoreProducts() {
    if (loading.value || loadingMore.value || !hasMore.value) {
      return null
    }
    return fetchProducts(lastParams.value, { append: true })
  }

  function resetProducts() {
    products.value = []
    currentPage.value = 1
    lastPage.value = 1
    totalProducts.value = 0
    lastParams.value = {}
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
    loadingMore,
    error,
    currentPage,
    lastPage,
    totalProducts,
    perPage,
    allProducts,
    hasMore,
    fetchProducts,
    loadMoreProducts,
    resetProducts,
    fetchFeatured,
    fetchNewArrivals,
    fetchProductById,
    searchProducts,
    clearError
  }
})