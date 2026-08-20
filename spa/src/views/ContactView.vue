<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useSiteStore } from '@/stores/site'

const siteStore = useSiteStore()

onMounted(() => {
  if (!siteStore.settings.site_name) {
    siteStore.fetchSiteData()
  }
})

const toast = useToast()

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const loading = ref(false)

function handleSubmit() {
  loading.value = true
  setTimeout(() => {
    toast.success('Message sent successfully! We will get back to you soon.')
    form.value = { name: '', email: '', subject: '', message: '' }
    loading.value = false
  }, 1000)
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Contact Us</h1>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Contact Form -->
      <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-xl font-bold mb-4">Send us a Message</h2>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
            <input v-model="form.name" type="text" required class="input" placeholder="Your name" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input v-model="form.email" type="email" required class="input" placeholder="your@email.com" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
            <input v-model="form.subject" type="text" required class="input" placeholder="Subject" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
            <textarea v-model="form.message" rows="5" required class="input" placeholder="Your message..."></textarea>
          </div>
          <button type="submit" :disabled="loading" class="btn btn-primary w-full">
            {{ loading ? 'Sending...' : 'Send Message' }}
          </button>
        </form>
      </div>

      <!-- Contact Info -->        <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-md p-8">
          <h2 class="text-xl font-bold mb-4">Contact Information</h2>
          <div class="space-y-4">
            <div class="flex items-start gap-4">
              <span class="text-2xl">📍</span>
              <div>
                <h3 class="font-semibold">Address</h3>
                <p class="text-gray-600">{{ siteStore.getSetting('contact_address', '123 Business Street<br/>Dhaka, Bangladesh') }}</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <span class="text-2xl">📞</span>
              <div>
                <h3 class="font-semibold">Phone</h3>
                <p class="text-gray-600">{{ siteStore.getSetting('contact_phone', '+880 1234 567890') }}</p>
              </div>
            </div>
            <div class="flex items-start gap-4">
              <span class="text-2xl">✉️</span>
              <div>
                <h3 class="font-semibold">Email</h3>
                <p class="text-gray-600">{{ siteStore.getSetting('contact_email', 'info@ecommerce.com') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>