<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import OrderService from '@/services/OrderService'
import { formatPrice } from '@/utils/currency'
import { getImageUrl } from '@/utils/image'

const route = useRoute()

const order = ref(null)
const loading = ref(true)
const errorMessage = ref('')

const shippingAddress = computed(() => {
  const address = order.value?.shipping_address
  if (!address) return ''
  if (typeof address === 'string') return address
  return Object.values(address).filter(Boolean).join(', ')
})

const statusClasses = computed(() => {
  const status = order.value?.status
  const map = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-blue-100 text-blue-800',
    processing: 'bg-blue-100 text-blue-800',
    packed: 'bg-indigo-100 text-indigo-800',
    shipped: 'bg-indigo-100 text-indigo-800',
    delivered: 'bg-green-100 text-green-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    failed: 'bg-red-100 text-red-800',
    returned: 'bg-orange-100 text-orange-800',
    refunded: 'bg-orange-100 text-orange-800',
  }
  return map[status] || 'bg-gray-100 text-gray-800'
})

const formatDate = (iso) => {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(async () => {
  try {
    const res = await OrderService.getById(route.params.id)
    order.value = res.data.data
  } catch (error) {
    const status = error.response?.status
    errorMessage.value =
      error.response?.data?.message ||
      (status === 404 ? 'Order not found.' : 'Failed to load order details.')
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Back link -->
    <RouterLink
      :to="{ name: 'customer.orders' }"
      class="inline-flex items-center gap-1 text-primary-600 hover:underline mb-6"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
      Back to Orders
    </RouterLink>

    <h1 class="text-3xl font-bold mb-8">Order Details</h1>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-6">
      <div class="bg-white rounded-lg shadow-md p-6 animate-pulse space-y-4">
        <div class="h-6 bg-gray-200 rounded w-48"></div>
        <div class="h-4 bg-gray-200 rounded w-32"></div>
        <div class="h-32 bg-gray-200 rounded w-full"></div>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6 animate-pulse space-y-3">
        <div class="h-5 bg-gray-200 rounded w-40"></div>
        <div class="h-4 bg-gray-200 rounded w-full"></div>
        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-6 text-red-700">
      {{ errorMessage }}
    </div>

    <!-- Order Details -->
    <template v-else-if="order">
      <!-- Header card -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-start">
          <div>
            <h2 class="text-xl font-semibold">Order #{{ order.order_number }}</h2>
            <p class="text-gray-500 mt-1">{{ formatDate(order.created_at) }}</p>
            <p v-if="order.coupon_code" class="text-sm text-gray-500 mt-1">
              Coupon: <span class="font-medium">{{ order.coupon_code }}</span>
            </p>
          </div>
          <span :class="`px-3 py-1 rounded-full text-sm font-medium ${statusClasses}`">
            {{ order.status_label || order.status }}
          </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-gray-100">
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wider">Payment Method</p>
            <p class="font-medium mt-1">{{ order.payment_method || 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wider">Payment Status</p>
            <p class="font-medium mt-1" :class="order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'">
              {{ order.payment_status_label || order.payment_status }}
            </p>
          </div>
          <div>
            <p class="text-xs text-gray-500 uppercase tracking-wider">Shipping Status</p>
            <p class="font-medium mt-1 capitalize">{{ (order.shipping_status || 'pending').replace(/_/g, ' ') }}</p>
          </div>
        </div>
      </div>

      <!-- Items -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
          <h3 class="font-semibold">Order Items ({{ order.items?.length || 0 }})</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="item in order.items" :key="item.id">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden shrink-0 flex items-center justify-center">
                    <img
                      v-if="item.product?.thumbnail"
                      :src="getImageUrl(item.product.thumbnail)"
                      :alt="item.product_name"
                      class="w-full h-full object-cover"
                      loading="lazy"
                    />
                    <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ item.product_name }}</p>
                    <p v-if="item.product_sku" class="text-xs text-gray-500">SKU: {{ item.product_sku }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">{{ item.quantity }}</td>
              <td class="px-6 py-4">{{ formatPrice(item.unit_price) }}</td>
              <td class="px-6 py-4 font-semibold">{{ formatPrice(item.total) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Totals -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="font-semibold mb-4">Order Summary</h3>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-gray-500">Subtotal</dt>
            <dd class="font-medium">{{ formatPrice(order.subtotal) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-500">Discount</dt>
            <dd class="font-medium text-green-600">-{{ formatPrice(order.discount) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-500">Tax</dt>
            <dd class="font-medium">{{ formatPrice(order.tax) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-gray-500">Shipping</dt>
            <dd class="font-medium">{{ formatPrice(order.shipping_charge) }}</dd>
          </div>
          <div class="flex justify-between pt-2 border-t border-gray-100 text-base font-bold">
            <dt>Total</dt>
            <dd class="text-primary-600">{{ formatPrice(order.grand_total) }}</dd>
          </div>
          <div class="flex justify-between text-sm">
            <dt class="text-gray-500">Paid</dt>
            <dd class="font-medium">{{ formatPrice(order.paid_amount) }}</dd>
          </div>
          <div class="flex justify-between text-sm">
            <dt class="text-gray-500">Due</dt>
            <dd class="font-medium">{{ formatPrice(order.due_amount) }}</dd>
          </div>
        </dl>
      </div>

      <!-- Shipping Address -->
      <div class="bg-white rounded-lg shadow-md p-6" v-if="shippingAddress">
        <h3 class="font-semibold mb-2">Shipping Address</h3>
        <p class="text-gray-700">{{ shippingAddress }}</p>
      </div>
    </template>
  </div>
</template>
