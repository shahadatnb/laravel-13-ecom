<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useToast } from 'vue-toastification'
import AuthService from '@/services/AuthService'
import { getImageUrl } from '@/utils/image'

const authStore = useAuthStore()
const toast = useToast()

const loading = ref(false)
const errors = ref({})
const avatarInput = ref(null)
const avatarPreview = ref(null)
const selectedAvatar = ref(null)

const form = ref({
  name: '',
  email: '',
  phone: '',
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
    }
  }
})

const displayAvatar = computed(() => {
  if (avatarPreview.value) {
    return avatarPreview.value
  }
  const avatar = authStore.user?.avatar
  return avatar ? getImageUrl(avatar) : null
})

function triggerAvatarSelect() {
  avatarInput.value?.click()
}

function onAvatarSelected(event) {
  const file = event.target.files?.[0]
  if (!file) {
    return
  }

  if (!file.type.startsWith('image/')) {
    toast.error('Please select a valid image file.')
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    toast.error('Image must be smaller than 2 MB.')
    return
  }

  if (avatarPreview.value) {
    URL.revokeObjectURL(avatarPreview.value)
  }
  selectedAvatar.value = file
  avatarPreview.value = URL.createObjectURL(file)
}

async function handleSubmit() {
  loading.value = true
  errors.value = {}

  try {
    const payload = new FormData()
    payload.append('name', form.value.name)
    payload.append('email', form.value.email)
    payload.append('phone', form.value.phone || '')
    if (selectedAvatar.value) {
      payload.append('avatar', selectedAvatar.value)
    }

    const res = await AuthService.updateProfile(payload)
    toast.success(res.data.message || 'Profile updated successfully!')

    await authStore.fetchUser()

    selectedAvatar.value = null
    if (avatarPreview.value) {
      URL.revokeObjectURL(avatarPreview.value)
      avatarPreview.value = null
    }
    if (avatarInput.value) {
      avatarInput.value.value = ''
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
      toast.error('Please fix the highlighted errors.')
    } else {
      toast.error(error.response?.data?.message || 'Failed to update profile.')
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
      <form class="space-y-6" @submit.prevent="handleSubmit">
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
          <div class="w-20 h-20 bg-gray-200 rounded-full overflow-hidden flex items-center justify-center flex-shrink-0">
            <img
              v-if="displayAvatar"
              :src="displayAvatar"
              alt="Avatar"
              class="w-full h-full object-cover"
            />
            <span v-else class="text-3xl">👤</span>
          </div>
          <div>
            <button type="button" class="btn btn-secondary" @click="triggerAvatarSelect">
              Change Avatar
            </button>
            <p class="text-xs text-gray-500 mt-1">JPG, PNG or WebP. Max 2 MB.</p>
            <p v-if="errors.avatar" class="mt-1 text-sm text-red-600">{{ errors.avatar[0] }}</p>
          </div>
          <input
            ref="avatarInput"
            type="file"
            accept="image/jpeg,image/png,image/webp"
            class="hidden"
            @change="onAvatarSelected"
          />
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
