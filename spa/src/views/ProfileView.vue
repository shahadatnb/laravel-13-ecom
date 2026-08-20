<template>
  <div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your account information and preferences.</p>
      </div>

      <div v-if="profileStore.loading" class="px-6 py-12 text-center text-gray-500">
        Loading...
      </div>

      <div v-else-if="profileStore.error" class="px-6 py-8">
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
          {{ profileStore.error }}
        </div>
      </div>

      <form v-else @submit.prevent="handleSubmit" class="px-6 py-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input id="name" v-model="form.name" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input id="email" v-model="form.email" type="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" v-model="form.phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
          </div>

          <div>
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
            <input id="date_of_birth" v-model="form.date_of_birth" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
          </div>

          <div>
            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
            <select id="gender" v-model="form.gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
              <option value="">Select gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div>
            <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
            <input id="timezone" v-model="form.timezone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
          </div>
        </div>

        <div>
          <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
          <textarea id="address" v-model="form.address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        <div>
          <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
          <textarea id="bio" v-model="form.bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        </div>

        <div>
          <label for="avatar" class="block text-sm font-medium text-gray-700">Avatar URL</label>
          <input id="avatar" v-model="form.avatar" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
          <input id="password" v-model="form.password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
          <p class="mt-1 text-sm text-gray-500">Leave blank to keep your current password.</p>
        </div>

        <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
          {{ successMessage }}
        </div>

        <div class="flex justify-end">
          <button type="submit" :disabled="profileStore.loading" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <span v-if="profileStore.loading">Saving...</span>
            <span v-else>Save Changes</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useProfileStore } from '../stores/profile'

const profileStore = useProfileStore()
const successMessage = ref('')

const form = reactive({
  name: '',
  email: '',
  phone: '',
  address: '',
  avatar: '',
  bio: '',
  date_of_birth: '',
  gender: '',
  timezone: 'UTC',
  locale: 'en',
  password: '',
})

onMounted(async () => {
  await profileStore.fetchProfile()

  if (profileStore.user) {
    form.name = profileStore.user.name || ''
    form.email = profileStore.user.email || ''
    form.phone = profileStore.user.phone || ''
    form.address = profileStore.user.address || ''
    form.avatar = profileStore.user.avatar || ''
    form.bio = profileStore.user.bio || ''
    form.date_of_birth = profileStore.user.date_of_birth || ''
    form.gender = profileStore.user.gender || ''
    form.timezone = profileStore.user.timezone || 'UTC'
    form.locale = profileStore.user.locale || 'en'
  }
})

async function handleSubmit() {
  successMessage.value = ''

  const payload = { ...form }
  if (!payload.password) {
    delete payload.password
  }

  await profileStore.updateProfile(payload)
  successMessage.value = 'Profile updated successfully.'
}
</script>
