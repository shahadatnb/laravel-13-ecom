<script setup>
import { onMounted, ref } from 'vue'
import { formatPrice } from '@/utils/currency'
import WalletService from '@/services/WalletService'

const loading = ref(true)
const wallet = ref(null)
const transactions = ref([])
const pagination = ref(null)
const errorMessage = ref('')

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

onMounted(() => loadWallet())

async function loadWallet(page = 1) {
  loading.value = true
  errorMessage.value = ''
  try {
    const res = await WalletService.getAll({ page })
    const data = res.data.data
    wallet.value = data
    transactions.value = data.transactions?.data || []
    pagination.value = {
      current_page: data.transactions?.current_page || 1,
      last_page: data.transactions?.last_page || 1,
      total: data.transactions?.total || 0,
    }
  } catch (error) {
    errorMessage.value = 'Failed to load wallet. Please try again.'
  } finally {
    loading.value = false
  }
}

function changePage(page) {
  if (page < 1 || page > (pagination.value?.last_page || 1)) return
  loadWallet(page)
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Wallet</h1>

    <!-- Error State -->
    <div v-if="errorMessage && !loading" class="bg-red-50 border border-red-200 rounded-lg p-6 text-red-700 mb-8">
      {{ errorMessage }}
    </div>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
        <h3 class="text-sm opacity-80">Current Balance</h3>
        <p class="text-4xl font-bold mt-2">
          <span v-if="loading" class="inline-block bg-white/30 rounded w-32 h-9 animate-pulse"></span>
          <template v-else>{{ formatPrice(wallet?.balance || 0) }}</template>
        </p>
        <p v-if="!loading && wallet && wallet.locked_balance > 0" class="text-sm opacity-80 mt-2">
          Available: {{ formatPrice(wallet.available_balance) }}
        </p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm">Total Credits</h3>
        <p class="text-3xl font-bold text-green-600 mt-2">
          <span v-if="loading" class="inline-block bg-gray-200 rounded w-28 h-8 animate-pulse"></span>
          <template v-else>{{ formatPrice(wallet?.total_credits || 0) }}</template>
        </p>
      </div>
      <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-gray-500 text-sm">Total Debits</h3>
        <p class="text-3xl font-bold text-red-600 mt-2">
          <span v-if="loading" class="inline-block bg-gray-200 rounded w-28 h-8 animate-pulse"></span>
          <template v-else>{{ formatPrice(wallet?.total_debits || 0) }}</template>
        </p>
      </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <div class="flex items-center justify-between p-6 border-b">
        <h2 class="text-xl font-bold">Transaction History</h2>
        <span v-if="!loading && pagination" class="text-sm text-gray-500">
          {{ pagination.total }} {{ pagination.total === 1 ? 'transaction' : 'transactions' }}
        </span>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="divide-y divide-gray-100">
        <div v-for="n in 4" :key="n" class="px-6 py-4 animate-pulse flex items-center gap-4">
          <div class="h-4 bg-gray-200 rounded w-32"></div>
          <div class="h-4 bg-gray-200 rounded flex-1"></div>
          <div class="h-4 bg-gray-200 rounded w-16"></div>
          <div class="h-4 bg-gray-200 rounded w-20"></div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else-if="transactions.length === 0" class="p-12 text-center text-gray-400">
        <div class="text-6xl mb-4">💳</div>
        <p>No transactions yet.</p>
      </div>

      <!-- Table -->
      <template v-else>
        <div class="overflow-x-auto">
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(tx.created_at) }}</td>
                <td class="px-6 py-4">{{ tx.description || '—' }}</td>
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

        <!-- Pagination -->
        <div
          v-if="pagination && pagination.last_page > 1"
          class="flex items-center justify-between px-6 py-4 border-t border-gray-100"
        >
          <button
            class="px-4 py-2 text-sm border rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50"
            :disabled="pagination.current_page <= 1"
            @click="changePage(pagination.current_page - 1)"
          >
            Previous
          </button>
          <span class="text-sm text-gray-500">
            Page {{ pagination.current_page }} of {{ pagination.last_page }}
          </span>
          <button
            class="px-4 py-2 text-sm border rounded-lg disabled:opacity-40 disabled:cursor-not-allowed hover:bg-gray-50"
            :disabled="pagination.current_page >= pagination.last_page"
            @click="changePage(pagination.current_page + 1)"
          >
            Next
          </button>
        </div>
      </template>
    </div>
  </div>
</template>
