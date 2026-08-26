import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

const CART_STORAGE_KEY = 'shopping_cart'

function loadCartFromStorage() {
  try {
    const saved = localStorage.getItem(CART_STORAGE_KEY)
    return saved ? JSON.parse(saved) : []
  } catch {
    return []
  }
}

function saveCartToStorage(items) {
  try {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(items))
  } catch {
    // Storage full or unavailable — silently ignore
  }
}

export const useCartStore = defineStore('cart', () => {
  const items = ref(loadCartFromStorage())

  const itemCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const subtotal = computed(() => {
    return items.value.reduce((total, item) => total + ((item.price || 0) * item.quantity), 0)
  })

  function addItem(product, quantity = 1) {
    // Resolve price from various field names (price, sale_price, regular_price)
    const price = product.price || product.sale_price || product.regular_price || 0
    // Compare both product ID and variant ID for duplicate detection
    const existingItem = items.value.find(
      item => item.id === product.id && (item.variant_id ?? null) === (product.variant_id ?? null)
    )
    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      items.value.push({
        id: product.id,
        variant_id: product.variant_id || null,
        variant_name: product.variant_name || null,
        variant_sku: product.variant_sku || null,
        name: product.name,
        price: price,
        image: product.image,
        sku: product.sku || null,
        quantity: quantity
      })
    }
    saveCartToStorage(items.value)
  }

  function removeItem(productId) {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
    }
    saveCartToStorage(items.value)
  }

  function updateQuantity(productId, quantity) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      item.quantity = Math.max(1, quantity)
    }
    saveCartToStorage(items.value)
  }

  function clearCart() {
    items.value = []
    saveCartToStorage(items.value)
  }

  return {
    items,
    itemCount,
    subtotal,
    addItem,
    removeItem,
    updateQuantity,
    clearCart
  }
})