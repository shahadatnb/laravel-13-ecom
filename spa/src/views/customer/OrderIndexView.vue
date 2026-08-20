<script setup>
import { ref } from 'vue'
import { formatPrice } from '@/utils/currency'

const orders = ref([
  { id: 1, order_number: 'ORD-001', date: '2024-01-15', total: 150, status: 'delivered' },
  { id: 2, order_number: 'ORD-002', date: '2024-01-10', total: 200, status: 'processing' }
])
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Orders</h1>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div v-for="order in orders" :key="order.id" class="border-b last:border-b-0 p-6">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-lg">Order #{{ order.order_number }}</h3>
            <p class="text-sm text-gray-500">{{ order.date }}</p>
          </div>
          <span :class="`px-3 py-1 rounded-full text-sm ${order.status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}`">
            {{ order.status }}
          </span>
        </div>
        <div class="flex justify-between items-center">
          <p class="font-bold">Total: {{ formatPrice(order.total) }}</p>
          <button class="btn btn-secondary text-sm">View Details</button>
        </div>
      </div>
    </div>
  </div>
</template>