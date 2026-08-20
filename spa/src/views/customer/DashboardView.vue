<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Stats Cards -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Total Orders</p>
            <p class="text-3xl font-bold text-primary-600">0</p>
          </div>
          <div class="text-4xl">📦</div>
        </div>
        <RouterLink to="/orders" class="text-primary-600 text-sm mt-4 inline-block hover:underline">
          View Orders →
        </RouterLink>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Wallet Balance</p>
            <p class="text-3xl font-bold text-green-600">0.00</p>
          </div>
          <div class="text-4xl">💰</div>
        </div>
        <RouterLink to="/wallet" class="text-primary-600 text-sm mt-4 inline-block hover:underline">
          View Wallet →
        </RouterLink>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Addresses</p>
            <p class="text-3xl font-bold text-primary-600">0</p>
          </div>
          <div class="text-4xl">📍</div>
        </div>
        <RouterLink to="/addresses" class="text-primary-600 text-sm mt-4 inline-block hover:underline">
          Manage Addresses →
        </RouterLink>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm">Wishlist</p>
            <p class="text-3xl font-bold text-primary-600">0</p>
          </div>
          <div class="text-4xl">❤️</div>
        </div>
        <RouterLink to="/wishlist" class="text-primary-600 text-sm mt-4 inline-block hover:underline">
          View Wishlist →
        </RouterLink>
      </div>

    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <RouterLink to="/products" class="btn btn-secondary text-center py-4">
          🛍️ Shop Now
        </RouterLink>
        <RouterLink to="/profile" class="btn btn-secondary text-center py-4">
          👤 Edit Profile
        </RouterLink>
        <RouterLink to="/addresses" class="btn btn-secondary text-center py-4">
          📍 Add Address
        </RouterLink>
        <RouterLink to="/wallet" class="btn btn-secondary text-center py-4">
          💳 Add Funds
        </RouterLink>
      </div>
    </div>

    <!-- Welcome Message -->
    <div v-if="authStore.user" class="mt-8 bg-primary-50 rounded-lg p-6">
      <h2 class="text-xl font-bold mb-2">Welcome back, {{ authStore.user.name }}!</h2>
      <p class="text-gray-600">
        Thank you for being a valued customer. Explore our latest products and enjoy exclusive deals!
      </p>
    </div>
  </div>
</template>
