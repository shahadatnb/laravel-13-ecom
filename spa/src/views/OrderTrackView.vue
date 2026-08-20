<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const email = ref('')
const orderNumber = ref('')
const loading = ref(false)
const searched = ref(false)
const orderData = ref(null)
const errorMessage = ref('')

const statusStep = computed(() => {
  if (!orderData.value?.order) return 0
  const status = orderData.value.order.status
  const steps = ['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'completed']
  const idx = steps.indexOf(status)
  return idx >= 0 ? idx + 1 : (status === 'cancelled' || status === 'returned' || status === 'refunded' || status === 'failed' ? -1 : 0)
})

const isTerminalStatus = computed(() => {
  if (!orderData.value?.order) return false
  return ['cancelled', 'returned', 'refunded', 'failed'].includes(orderData.value.order.status)
})

const statusColor = computed(() => {
  if (!orderData.value?.order) return 'gray'
  const s = orderData.value.order.status
  if (s === 'delivered' || s === 'completed') return 'green'
  if (s === 'cancelled' || s === 'failed') return 'red'
  if (s === 'returned' || s === 'refunded') return 'orange'
  return 'blue'
})

const timeline = computed(() => orderData.value?.timeline || [])

const formatDate = (iso) => {
  if (!iso) return ''
  const d = new Date(iso)
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })
}

const formatCurrency = (amount, currency = 'BDT') => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount || 0)
}

async function handleTrack() {
  if (!email.value.trim() || !orderNumber.value.trim()) {
    errorMessage.value = 'Please enter both your email and order number.'
    return
  }

  loading.value = true
  searched.value = true
  errorMessage.value = ''
  orderData.value = null

  try {
    const res = await axios.get('/api/orders/track', {
      params: {
        email: email.value.trim(),
        order_number: orderNumber.value.trim()
      }
    })
    orderData.value = res.data.data
  } catch (err) {
    if (err.response?.status === 404) {
      errorMessage.value = err.response.data?.message || 'No order found with that email and order number.'
    } else {
      errorMessage.value = 'An error occurred. Please try again later.'
    }
  } finally {
    loading.value = false
  }
}

function resetSearch() {
  searched.value = false
  orderData.value = null
  errorMessage.value = ''
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
      <!-- Hero section -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Track Your Order</h1>
        <p class="mt-2 text-gray-600">
          Enter your email address and order number to check your order status and delivery updates.
        </p>
      </div>

      <!-- Search form -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-8">
        <form @submit.prevent="handleTrack" class="space-y-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="track-email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <svg class="w-4 h-4 inline -mt-0.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Email Address
              </label>
              <input
                id="track-email"
                v-model="email"
                type="email"
                autocomplete="email"
                name="email"
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
              />
            </div>
            <div>
              <label for="track-order" class="block text-sm font-semibold text-gray-700 mb-1.5">
                <svg class="w-4 h-4 inline -mt-0.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Order Number
              </label>
              <input
                id="track-order"
                v-model="orderNumber"
                type="text"
                autocomplete="off"
                name="order_number"
                placeholder="e.g. ORD-001"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
              />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <button
              type="submit"
              :disabled="loading"
              class="px-8 py-2.5 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 disabled:opacity-60 disabled:cursor-not-allowed transition-all flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
              {{ loading ? 'Searching...' : 'Track Order' }}
            </button>
            <button
              v-if="searched"
              type="button"
              @click="resetSearch"
              class="px-6 py-2.5 text-gray-600 font-medium rounded-lg hover:bg-gray-100 transition"
            >
              New Search
            </button>
          </div>
        </form>
      </div>

      <!-- Error message -->
      <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-xl p-5 mb-8">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <p class="text-red-700">{{ errorMessage }}</p>
        </div>
      </div>

      <!-- Order details -->
      <div v-if="orderData?.order" class="space-y-6">
        <!-- Status banner -->
        <div
          class="rounded-xl p-5 border"
          :class="{
            'bg-green-50 border-green-200': statusColor === 'green',
            'bg-red-50 border-red-200': statusColor === 'red',
            'bg-orange-50 border-orange-200': statusColor === 'orange',
            'bg-blue-50 border-blue-200': statusColor === 'blue',
            'bg-gray-50 border-gray-200': statusColor === 'gray',
          }"
        >
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <p class="text-sm text-gray-600">Order</p>
              <p class="text-xl font-bold text-gray-900">{{ orderData.order.order_number }}</p>
            </div>
            <div class="text-right">
              <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                :class="{
                  'bg-green-100 text-green-800': statusColor === 'green',
                  'bg-red-100 text-red-800': statusColor === 'red',
                  'bg-orange-100 text-orange-800': statusColor === 'orange',
                  'bg-blue-100 text-blue-800': statusColor === 'blue',
                  'bg-gray-100 text-gray-800': statusColor === 'gray',
                }"
              >
                {{ orderData.order.status_label }}
              </span>
              <p class="text-sm text-gray-500 mt-1">{{ formatDate(orderData.order.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Order summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Order Summary</h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Subtotal</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(orderData.order.subtotal, orderData.order.currency) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Shipping</p>
              <p class="text-lg font-semibold text-gray-900">{{ formatCurrency(orderData.order.shipping_charge, orderData.order.currency) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Discount</p>
              <p class="text-lg font-semibold text-green-600">-{{ formatCurrency(orderData.order.discount, orderData.order.currency) }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Total</p>
              <p class="text-lg font-bold text-primary-600">{{ formatCurrency(orderData.order.grand_total, orderData.order.currency) }}</p>
            </div>
          </div>
        </div>

        <!-- Payment info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h3 class="font-semibold text-gray-900 mb-4">Payment</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Method</p>
              <p class="font-medium text-gray-900">{{ orderData.order.payment_method || 'N/A' }}</p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Status</p>
              <p class="font-medium" :class="orderData.order.payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600'">
                {{ orderData.order.payment_status_label }}
              </p>
            </div>
            <div>
              <p class="text-xs text-gray-500 uppercase tracking-wider">Paid</p>
              <p class="font-medium text-gray-900">{{ formatCurrency(orderData.order.paid_amount, orderData.order.currency) }}</p>
            </div>
          </div>
        </div>

        <!-- Shipping address -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" v-if="orderData.order.shipping_address">
          <h3 class="font-semibold text-gray-900 mb-4">Shipping Address</h3>
          <p class="text-gray-700 whitespace-pre-line">{{ typeof orderData.order.shipping_address === 'object' ? Object.values(orderData.order.shipping_address).filter(Boolean).join(', ') : orderData.order.shipping_address }}</p>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">Order Items ({{ orderData.order.items?.length || 0 }})</h3>
          </div>
          <div class="divide-y divide-gray-100">
            <div v-for="(item, i) in orderData.order.items" :key="i" class="px-6 py-4 flex items-center gap-4">
              <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden shrink-0 flex items-center justify-center">
                <img
                  v-if="item.product?.thumbnail"
                  :src="`/storage/${item.product.thumbnail}`"
                  :alt="item.name"
                  class="w-full h-full object-cover"
                  loading="lazy"
                />
                <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-900 truncate">{{ item.name }}</p>
                <p class="text-sm text-gray-500">{{ item.sku ? 'SKU: ' + item.sku : '' }} &times; {{ item.quantity }}</p>
              </div>
              <div class="text-right shrink-0">
                <p class="font-semibold text-gray-900">{{ formatCurrency(item.subtotal || item.price * item.quantity, orderData.order.currency) }}</p>
                <p class="text-xs text-gray-500">{{ formatCurrency(item.price, orderData.order.currency) }} each</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" v-if="timeline.length > 0">
          <h3 class="font-semibold text-gray-900 mb-5">Order Timeline</h3>
          <div class="relative">
            <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-gray-200" aria-hidden="true"></div>
            <ul class="space-y-5">
              <li v-for="(event, i) in timeline" :key="i" class="relative pl-11">
                <div
                  class="absolute left-2.5 w-3 h-3 rounded-full border-2 top-1.5"
                  :class="{
                    'bg-green-500 border-green-500': event.status === 'delivered' || event.status === 'completed',
                    'bg-blue-500 border-blue-500': event.status === 'confirmed' || event.status === 'processing',
                    'bg-yellow-500 border-yellow-500': event.status === 'packed' || event.status === 'shipped',
                    'bg-red-500 border-red-500': event.status === 'cancelled' || event.status === 'failed',
                    'bg-orange-500 border-orange-500': event.status === 'returned' || event.status === 'refunded',
                    'bg-gray-400 border-gray-400': !['delivered','completed','confirmed','processing','packed','shipped','cancelled','failed','returned','refunded'].includes(event.status),
                  }"
                  aria-hidden="true"
                ></div>
                <div>
                  <p class="font-medium text-gray-900 capitalize">{{ event.status.replace(/_/g, ' ') }}</p>
                  <p v-if="event.notes" class="text-sm text-gray-600">{{ event.notes }}</p>
                  <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(event.date) }}</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Help text when not searched -->
      <div v-if="!searched" class="text-center text-gray-400 py-8">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <p>Enter your email and order number above to track your order.</p>
        <p class="text-sm mt-1">Your order number can be found in your order confirmation email.</p>
      </div>
    </div>
  </div>
</template>
