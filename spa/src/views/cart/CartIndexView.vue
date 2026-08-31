<script setup>
import { RouterLink } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'
import { formatPrice } from '@/utils/currency'

const cartStore = useCartStore()
const toast = useToast()

function updateQuantity(productId, quantity) {
  cartStore.updateQuantity(productId, quantity)
}

function removeItem(productId) {
  cartStore.removeItem(productId)
  toast.success('Item removed from cart')
}

function clearCart() {
  cartStore.clearCart()
  toast.success('Cart cleared')
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

    <div v-if="cartStore.items.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Cart Items -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md">
          <div v-for="item in cartStore.items" :key="item.id" class="p-6 border-b last:border-b-0">
            <div class="flex items-center gap-6">
              <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center" aria-hidden="true">
                <span class="text-3xl">📷</span>
              </div>
              <div class="flex-1">
                <h3 class="font-semibold text-lg">{{ item.name }}</h3>
                <p class="text-primary-600 font-bold text-xl mt-1">{{ formatPrice(item.price) }}</p>
              </div>
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <button
                    @click="updateQuantity(item.id, item.quantity - 1)"
                    class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-gray-300"
                    :disabled="item.quantity <= 1"
                    aria-label="Decrease quantity"
                  >
                    -
                  </button>
                  <span class="w-8 text-center font-semibold tabular-nums">{{ item.quantity }}</span>
                  <button
                    @click="updateQuantity(item.id, item.quantity + 1)"
                    class="w-8 h-8 rounded-lg bg-gray-200 hover:bg-gray-300"
                    aria-label="Increase quantity"
                  >
                    +
                  </button>
                </div>
                <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-700" aria-label="Remove item">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
              <div class="text-right">
                <p class="font-bold text-lg">{{ formatPrice(item.price * item.quantity) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-4 flex justify-between">
          <RouterLink to="/products" class="btn btn-secondary">
            Continue Shopping
          </RouterLink>
          <button @click="clearCart" class="btn btn-danger">
            Clear Cart
          </button>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
          <h2 class="text-xl font-bold mb-4">Order Summary</h2>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Subtotal</span>
              <span class="font-semibold">{{ formatPrice(cartStore.subtotal) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Shipping</span>
              <span class="font-semibold">Free</span>
            </div>
            <div class="border-t pt-3">
              <div class="flex justify-between text-lg font-bold">
                <span>Total</span>
                <span>{{ formatPrice(cartStore.subtotal) }}</span>
              </div>
            </div>
          </div>
          <RouterLink to="/checkout" class="btn btn-primary w-full mt-6 py-3 block text-center">
            Proceed to Checkout
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Empty Cart -->
    <div v-else class="text-center py-12 bg-white rounded-lg shadow-md">
      <div class="text-6xl mb-4">🛒</div>
      <h2 class="text-2xl font-bold mb-4">Your cart is empty</h2>
      <p class="text-gray-600 mb-6">Add some products to get started!</p>
      <RouterLink to="/products" class="btn btn-primary">
        Browse Products
      </RouterLink>
    </div>
  </div>
</template>