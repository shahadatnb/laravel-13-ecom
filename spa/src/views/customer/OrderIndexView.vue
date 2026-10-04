<script setup>
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import OrderService from '@/services/OrderService'
import { formatPrice } from '@/utils/currency'

const orders = ref([])
const loading = ref(true)
const errorMessage = ref('')

const formatDate = (iso) => {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const statusClasses = (status) => {
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
}

onMounted(loadOrders)

async function loadOrders() {
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await OrderService.getAll()
    orders.value = res.data.data || []
  } catch (error) {
    errorMessage.value = 'Failed to load your orders. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Orders</h1>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow-md overflow-hidden">
      <div v-for="n in 3" :key="n" class="border-b last:border-b-0 p-6 animate-pulse">
        <div class="flex justify-between items-center mb-4">
          <div class="space-y-2">
            <div class="h-5 bg-gray-200 rounded w-40"></div>
            <div class="h-4 bg-gray-200 rounded w-28"></div>
          </div>
          <div class="h-6 bg-gray-200 rounded-full w-20"></div>
        </div>
        <div class="flex justify-between items-center">
          <div class="h-5 bg-gray-200 rounded w-32"></div>
          <div class="h-9 bg-gray-200 rounded w-28"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-6 text-red-700">
      {{ errorMessage }}
    </div>

    <!-- Empty State -->
    <div v-else-if="orders.length === 0" class="text-center py-20 bg-white rounded-lg shadow-md">
      <div class="text-8xl mb-6">📦</div>
      <h2 class="text-2xl font-bold text-gray-900 mb-3">No orders yet</h2>
      <p class="text-gray-500 mb-8">When you place an order, it will show up here.</p>
      <RouterLink
        to="/products"
        class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-xl font-semibold hover:bg-primary-700 transition"
      >
        Start Shopping
      </RouterLink>
    </div>

    <!-- Orders List -->
    <div v-else class="bg-white rounded-lg shadow-md overflow-hidden">
      <div v-for="order in orders" :key="order.id" class="border-b last:border-b-0 p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-lg">Order #{{ order.order_number }}</h3>
            <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
          </div>
          <span :class="`px-3 py-1 rounded-full text-sm ${statusClasses(order.status)}`">
            {{ order.status_label || order.status }}
          </span>
        </div>
        <div class="flex justify-between items-center">
          <p class="font-bold">Total: {{ formatPrice(order.grand_total) }}</p>
          <RouterLink
            :to="{ name: 'customer.order.show', params: { id: order.id } }"
            class="btn btn-secondary text-sm"
          >
            View Details
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>
