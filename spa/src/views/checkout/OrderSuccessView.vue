<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import OrderService from '@/services/OrderService'
import { formatPrice } from '@/utils/currency'

const route = useRoute()
const authStore = useAuthStore()

const loading = ref(true)
const order = ref(null)
const error = ref(null)

onMounted(async () => {
  // First, try to read order data from history state (passed after checkout via router state)
  const stateOrder = window.history.state?.orderData
  if (stateOrder) {
    order.value = stateOrder
    loading.value = false
    return
  }
  
  // Fall back to API call for authenticated users
  if (authStore.isAuthenticated) {
    await fetchOrder()
  } else {
    // Guest without state data — show a simpler confirmation
    loading.value = false
    order.value = { id: route.params.id }
  }
})

async function fetchOrder() {
  loading.value = true
  error.value = null
  try {
    const orderId = route.params.id
    const response = await OrderService.getById(orderId)
    order.value = response.data.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load order details.'
  } finally {
    loading.value = false
  }
}

const statusColor = computed(() => {
  const s = order.value?.status
  if (s === 'pending') return 'bg-yellow-100 text-yellow-800'
  if (s === 'processing') return 'bg-blue-100 text-blue-800'
  if (s === 'completed' || s === 'delivered') return 'bg-green-100 text-green-800'
  if (s === 'cancelled') return 'bg-red-100 text-red-800'
  return 'bg-gray-100 text-gray-800'
})

const estimatedDelivery = computed(() => {
  if (!order.value?.created_at) return null
  const created = new Date(order.value.created_at)
  // Estimate 5-7 business days from order date
  const min = new Date(created)
  min.setDate(min.getDate() + 5)
  const max = new Date(created)
  max.setDate(max.getDate() + 7)
  return {
    min: min.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
    max: max.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
  }
})


</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <!-- Loading -->
      <div v-if="loading" class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 animate-pulse space-y-6">
          <div class="h-8 bg-gray-200 rounded w-1/3 mx-auto"></div>
          <div class="h-4 bg-gray-200 rounded w-2/3 mx-auto"></div>
          <div class="h-20 bg-gray-200 rounded"></div>
          <div class="h-40 bg-gray-200 rounded"></div>
        </div>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="max-w-3xl mx-auto text-center py-20">
        <div class="text-6xl mb-4">😕</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Couldn't Load Order</h2>
        <p class="text-gray-500 mb-6">{{ error }}</p>
        <RouterLink to="/orders" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
          View My Orders
        </RouterLink>
      </div>

      <!-- Success -->
      <div v-else-if="order" class="max-w-3xl mx-auto space-y-6">
        <!-- Success Banner -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
          <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Placed Successfully! 🎉</h1>
          <p class="text-gray-500 text-lg mb-2">
            Thank you for your order, <span class="font-semibold text-gray-700">{{ authStore.user?.name || 'Valued Customer' }}</span>!
          </p>
          <p class="text-gray-400">
            Order <span class="font-mono font-semibold text-gray-700">{{ order.order_number }}</span>
          </p>

          <!-- Delivery Estimate -->
          <div v-if="estimatedDelivery" class="mt-6 inline-flex items-center gap-2 px-5 py-3 bg-blue-50 text-blue-700 rounded-xl text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Estimated delivery: <span class="font-bold">{{ estimatedDelivery.min }} – {{ estimatedDelivery.max }}</span>
          </div>
        </div>

        <!-- Order Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 text-gray-400 text-sm mb-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Status
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold mt-1" :class="statusColor">
              {{ order.status_label || order.status }}
            </span>
          </div>
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 text-gray-400 text-sm mb-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              Payment
            </div>
            <p class="text-sm font-semibold text-gray-900 mt-1 capitalize">{{ order.payment_method || 'N/A' }}</p>
            <p class="text-xs text-gray-500">
              <span class="inline-block w-2 h-2 rounded-full mr-1" :class="order.payment_status === 'paid' ? 'bg-green-500' : 'bg-yellow-500'"></span>
              {{ order.payment_status_label || order.payment_status }}
            </p>
          </div>
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 text-gray-400 text-sm mb-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Shipping
            </div>
            <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ order.shipping_address?.address_line_1 || order.shipping_address?.city || 'N/A' }}</p>
          </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Order Items</h2>
          </div>
          <div class="divide-y divide-gray-50">
            <div v-for="(item, index) in order.items" :key="index" class="px-6 py-4 flex items-center gap-4">
              <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                📦
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm truncate">{{ item.product_name }}</p>
                <p v-if="item.product_sku" class="text-xs text-gray-400">SKU: {{ item.product_sku }}</p>
                <p v-if="item.variant_attributes?.name" class="text-xs text-gray-400">Variant: {{ item.variant_attributes.name }}</p>
              </div>
              <div class="text-right flex-shrink-0">
                <p class="text-sm text-gray-500">{{ item.quantity }} × {{ formatPrice(item.unit_price) }}</p>
                <p class="font-semibold text-gray-900">{{ formatPrice(item.total || item.unit_price * item.quantity) }}</p>
              </div>
            </div>
          </div>

          <!-- Totals -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="max-w-xs ml-auto space-y-2">
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-medium">{{ formatPrice(order.subtotal) }}</span>
              </div>
              <div class="flex justify-between text-sm">
                <span class="text-gray-500">Shipping</span>
                <span class="font-medium" :class="order.shipping_charge > 0 ? '' : 'text-green-600'">
                  {{ order.shipping_charge > 0 ? formatPrice(order.shipping_charge) : 'Free' }}
                </span>
              </div>
              <div v-if="order.tax > 0" class="flex justify-between text-sm">
                <span class="text-gray-500">Tax</span>
                <span class="font-medium">{{ formatPrice(order.tax) }}</span>
              </div>
              <div v-if="order.discount > 0" class="flex justify-between text-sm">
                <span class="text-gray-500">Discount</span>
                <span class="font-medium text-green-600">-{{ formatPrice(order.discount) }}</span>
              </div>
              <div class="flex justify-between text-base font-bold border-t border-gray-200 pt-2">
                <span>Total</span>
                <span class="text-primary-600">{{ formatPrice(order.grand_total) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Address -->
        <div v-if="order.shipping_address" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Shipping Address
          </h2>
          <div class="text-sm text-gray-600 space-y-1">
            <p class="font-medium text-gray-900">{{ order.shipping_address.recipient_name || authStore.user?.name }}</p>
            <p>{{ order.shipping_address.address_line_1 }}{{ order.shipping_address.address_line_2 ? ', ' + order.shipping_address.address_line_2 : '' }}</p>
            <p>{{ order.shipping_address.city }}{{ order.shipping_address.state ? ', ' + order.shipping_address.state : '' }} {{ order.shipping_address.postal_code || '' }}</p>
            <p>{{ order.shipping_address.country }}</p>
            <p v-if="order.shipping_address.phone">📞 {{ order.shipping_address.phone }}</p>
          </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 rounded-2xl border border-primary-100 p-6">
          <h2 class="text-lg font-bold text-gray-900 mb-3">📋 What's Next?</h2>
          <ul class="space-y-3 text-sm text-gray-600">
            <li class="flex items-start gap-3">
              <span class="w-6 h-6 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</span>
              <span>You'll receive an <strong>order confirmation email</strong> shortly with all the details.</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="w-6 h-6 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</span>
              <span>We'll start processing your order. You can track its status in <RouterLink to="/orders" class="text-primary-600 font-semibold hover:underline">My Orders</RouterLink>.</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="w-6 h-6 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</span>
              <span>Estimated delivery between <strong>{{ estimatedDelivery?.min || '—' }} – {{ estimatedDelivery?.max || '—' }}</strong>.</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="w-6 h-6 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">4</span>
              <span>If you have any questions, feel free to <RouterLink to="/contact" class="text-primary-600 font-semibold hover:underline">contact us</RouterLink>.</span>
            </li>
          </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pb-8">
          <RouterLink
            to="/orders"
            class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary-200"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            View My Orders
          </RouterLink>
          <RouterLink
            to="/products"
            class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Continue Shopping
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>
