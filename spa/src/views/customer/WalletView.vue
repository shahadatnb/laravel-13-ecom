<script setup>
import { ref } from 'vue'
import { formatPrice } from '@/utils/currency'

const wallet = ref({
  balance: 250,
  total_credits: 500,
  total_debits: 250
})

const transactions = ref([
  { id: 1, type: 'credit', amount: 100, description: 'Order refund', date: '2024-01-15' },
  { id: 2, type: 'debit', amount: 50, description: 'Order #ORD-001', date: '2024-01-10' }
])
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Wallet</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
        <h3 class="text-sm opacity-80">Current Balance</h3>
        <p class="text-4xl font-bold mt-2">{{ formatPrice(wallet.balance) }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm">Total Credits</h3>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ formatPrice(wallet.total_credits) }}</p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm">Total Debits</h3>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ formatPrice(wallet.total_debits) }}</p>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <h2 class="text-xl font-bold p-6 border-b">Transaction History</h2>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="tx in transactions" :key="tx.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ tx.date }}</td>
            <td class="px-6 py-4">{{ tx.description }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="`px-2 py-1 text-xs rounded-full ${tx.type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`">
                {{ tx.type }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap font-semibold" :class="tx.type === 'credit' ? 'text-green-600' : 'text-red-600'">
              {{ tx.type === 'credit' ? '+' : '-' }}{{ formatPrice(tx.amount) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>