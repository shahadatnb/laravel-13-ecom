<script setup>
import { ref } from 'vue'

const addresses = ref([
  { id: 1, name: 'Home', address: '123 Main St, City, Country', is_default: true },
  { id: 2, name: 'Office', address: '456 Business Rd, City, Country', is_default: false }
])

const showForm = ref(false)
const form = ref({
  name: '',
  address: '',
  city: '',
  phone: ''
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">My Addresses</h1>
      <button @click="showForm = true" class="btn btn-primary">Add New Address</button>
    </div>

    <div v-if="!showForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="address in addresses" :key="address.id" class="bg-white rounded-lg shadow-md p-6 relative">
        <span v-if="address.is_default" class="absolute top-4 right-4 px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Default</span>
        <h3 class="font-semibold text-lg">{{ address.name }}</h3>
        <p class="text-gray-600 mt-2">{{ address.address }}</p>
        <div class="mt-4 flex gap-2">
          <button class="btn btn-secondary text-sm">Edit</button>
          <button v-if="!address.is_default" class="btn btn-danger text-sm">Delete</button>
        </div>
      </div>
    </div>

    <div v-else class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-bold mb-4">Add New Address</h2>
      <form class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Address Name</label>
          <input v-model="form.name" type="text" class="input" placeholder="e.g., Home, Office" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
          <textarea v-model="form.address" rows="3" class="input"></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
          <input v-model="form.city" type="text" class="input" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
          <input v-model="form.phone" type="tel" class="input" />
        </div>
        <div class="flex gap-4">
          <button type="submit" class="btn btn-primary">Save Address</button>
          <button type="button" @click="showForm = false" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>