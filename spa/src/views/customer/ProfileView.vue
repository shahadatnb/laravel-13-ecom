<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'

const authStore = useAuthStore()
const toast = useToast()

const loading = ref(false)
const errors = ref({})

const form = ref({
  name: '',
  email: '',
  phone: '',
  avatar: null
})

onMounted(async () => {
  if (!authStore.user) {
    await authStore.fetchUser()
  }
  if (authStore.user) {
    form.value = {
      name: authStore.user.name || '',
      email: authStore.user.email || '',
      phone: authStore.user.phone || '',
      avatar: null
    }
  }
})

async function handleSubmit() {
  loading.value = true
  errors.value = {}

  try {
    // TODO: Implement update profile API call
    toast.success('Profile updated successfully!')
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      toast.error('Failed to update profile')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">My Profile</h1>

    <div class="bg-white rounded-lg shadow-md p-8 max-w-2xl">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
          <input v-model="form.name" type="text" required class="input" />
          <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input v-model="form.email" type="email" required class="input" />
          <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
          <input v-model="form.phone" type="tel" class="input" />
          <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
        </div>

        <div class="flex items-center gap-4">
          <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
            <span class="text-3xl">👤</span>
          </div>
          <button type="button" class="btn btn-secondary">Change Avatar</button>
        </div>

        <div class="flex gap-4">
          <button type="submit" :disabled="loading" class="btn btn-primary">
            {{ loading ? 'Saving...' : 'Save Changes' }}
          </button>
          <button type="button" class="btn btn-danger">Change Password</button>
        </div>
      </form>
    </div>
  </div>
</template>