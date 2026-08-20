<script setup>
import { ref } from 'vue'
import { formatPrice } from '@/utils/currency'

const order = ref({
  id: 1,
  order_number: 'ORD-001',
  date: '2024-01-15',
  status: 'delivered',
  total: 150,
  items: [
    { id: 1, name: 'Product 1', quantity: 2, price: 50 },
    { id: 2, name: 'Product 2', quantity: 1, price: 50 }
  ],
  shipping_address: '123 Main St, City, Country'
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Order Details</h1>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="flex justify-between mb-4">
        <div>
          <h2 class="text-xl font-semibold">Order #{{ order.order_number }}</h2>
          <p class="text-gray-500">{{ order.date }}</p>
        </div>
        <span :class="`px-3 py-1 rounded-full ${order.status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}`">
          {{ order.status }}
        </span>
      </div>

      <h3 class="font-semibold mb-2">Order Items</h3>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Product</th>
            <th class="px-4 py-2 text-left">Quantity</th>
            <th class="px-4 py-2 text-left">Price</th>
            <th class="px-4 py-2 text-left">Total</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in order.items" :key="item.id">
            <td class="px-4 py-2">{{ item.name }}</td>
            <td class="px-4 py-2">{{ item.quantity }}</td>
            <td class="px-4 py-2">{{ formatPrice(item.price) }}</td>
            <td class="px-4 py-2">{{ formatPrice(item.quantity * item.price) }}</td>
          </tr>
        </tbody>
      </table>

      <div class="mt-4 border-t pt-4">
        <div class="flex justify-between font-bold text-lg">
          <span>Total</span>
          <span>{{ formatPrice(order.total) }}</span>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
      <h3 class="font-semibold mb-2">Shipping Address</h3>
      <p>{{ order.shipping_address }}</p>
    </div>
  </div>
</template>