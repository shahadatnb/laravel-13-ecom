<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import AddressService from '@/services/AddressService'

const toast = useToast()

const addresses = ref([])
const loading = ref(true)
const showForm = ref(false)
const editingId = ref(null)
const saving = ref(false)
const deletingId = ref(null)
const errors = ref({})

const emptyForm = () => ({
  address_type: 'home',
  recipient_name: '',
  phone: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'Bangladesh',
  is_default: false,
  notes: '',
})

const form = ref(emptyForm())

onMounted(() => loadAddresses())

async function loadAddresses() {
  loading.value = true
  try {
    const res = await AddressService.getAll()
    addresses.value = res.data.data
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to load addresses.')
  } finally {
    loading.value = false
  }
}

function startAdd() {
  editingId.value = null
  form.value = emptyForm()
  form.value.is_default = addresses.value.length === 0
  errors.value = {}
  showForm.value = true
}

function startEdit(address) {
  editingId.value = address.id
  form.value = {
    address_type: address.address_type,
    recipient_name: address.recipient_name,
    phone: address.phone,
    address_line_1: address.address_line_1,
    address_line_2: address.address_line_2 || '',
    city: address.city,
    state: address.state || '',
    postal_code: address.postal_code || '',
    country: address.country,
    is_default: !!address.is_default,
    notes: address.notes || '',
  }
  errors.value = {}
  showForm.value = true
}

function cancelForm() {
  showForm.value = false
  editingId.value = null
  errors.value = {}
}

async function handleSubmit() {
  saving.value = true
  errors.value = {}

  try {
    const payload = { ...form.value }

    if (editingId.value) {
      const res = await AddressService.update(editingId.value, payload)
      toast.success(res.data.message || 'Address updated successfully.')
    } else {
      const res = await AddressService.create(payload)
      toast.success(res.data.message || 'Address added successfully.')
    }

    cancelForm()
    await loadAddresses()
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
      toast.error('Please fix the highlighted errors.')
    } else {
      toast.error(error.response?.data?.message || 'Something went wrong.')
    }
  } finally {
    saving.value = false
  }
}

async function handleDelete(address) {
  if (!window.confirm('Are you sure you want to delete this address?')) {
    return
  }

  deletingId.value = address.id
  try {
    const res = await AddressService.remove(address.id)
    toast.success(res.data.message || 'Address deleted successfully.')
    await loadAddresses()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to delete address.')
  } finally {
    deletingId.value = null
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold">My Addresses</h1>
      <button v-if="!showForm" @click="startAdd" class="btn btn-primary">Add New Address</button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div v-for="n in 2" :key="n" class="bg-white rounded-lg shadow-md p-6 animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-1/4 mb-3"></div>
        <div class="h-5 bg-gray-200 rounded w-1/3 mb-2"></div>
        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
      </div>
    </div>

    <!-- Address Cards -->
    <div v-else-if="!showForm && addresses.length" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div
        v-for="address in addresses"
        :key="address.id"
        class="bg-white rounded-lg shadow-md p-6 relative"
      >
        <span
          v-if="address.is_default"
          class="absolute top-4 right-4 px-2 py-1 text-xs bg-green-100 text-green-800 rounded"
        >
          Default
        </span>

        <span class="inline-block px-2 py-1 text-xs bg-primary-100 text-primary-700 rounded">
          {{ address.address_type_label }}
        </span>

        <h3 class="font-semibold text-lg mt-2">{{ address.recipient_name }}</h3>
        <p class="text-gray-600 mt-2">{{ address.full_address }}</p>
        <p class="text-gray-500 text-sm mt-1">{{ address.phone }}</p>

        <div class="mt-4 flex gap-2">
          <button @click="startEdit(address)" class="btn btn-secondary text-sm">Edit</button>
          <button
            v-if="!address.is_default"
            @click="handleDelete(address)"
            :disabled="deletingId === address.id"
            class="btn btn-danger text-sm"
          >
            {{ deletingId === address.id ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!showForm" class="bg-white rounded-lg shadow-md p-10 text-center">
      <div class="text-6xl mb-4">📍</div>
      <h2 class="text-xl font-bold mb-2">No addresses yet</h2>
      <p class="text-gray-600 mb-6">Add a delivery address to make checkout faster.</p>
      <button @click="startAdd" class="btn btn-primary">Add New Address</button>
    </div>

    <!-- Add / Edit Form -->
    <div v-else class="bg-white rounded-lg shadow-md p-6">
      <h2 class="text-xl font-bold mb-4">
        {{ editingId ? 'Edit Address' : 'Add New Address' }}
      </h2>

      <form class="space-y-4" @submit.prevent="handleSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Address Type</label>
            <select v-model="form.address_type" class="input">
              <option value="home">Home</option>
              <option value="work">Work</option>
              <option value="other">Other</option>
            </select>
            <p v-if="errors.address_type" class="mt-1 text-sm text-red-600">
              {{ errors.address_type[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Recipient Name</label>
            <input v-model="form.recipient_name" type="text" class="input" placeholder="e.g., John Doe" />
            <p v-if="errors.recipient_name" class="mt-1 text-sm text-red-600">
              {{ errors.recipient_name[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input v-model="form.phone" type="tel" class="input" placeholder="017XXXXXXXX" />
            <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
            <input v-model="form.city" type="text" class="input" />
            <p v-if="errors.city" class="mt-1 text-sm text-red-600">{{ errors.city[0] }}</p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
            <input
              v-model="form.address_line_1"
              type="text"
              class="input"
              placeholder="House, road, area"
            />
            <p v-if="errors.address_line_1" class="mt-1 text-sm text-red-600">
              {{ errors.address_line_1[0] }}
            </p>
          </div>

          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Address Line 2 <span class="text-gray-400">(optional)</span>
            </label>
            <input v-model="form.address_line_2" type="text" class="input" />
            <p v-if="errors.address_line_2" class="mt-1 text-sm text-red-600">
              {{ errors.address_line_2[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              State <span class="text-gray-400">(optional)</span>
            </label>
            <input v-model="form.state" type="text" class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Postal Code <span class="text-gray-400">(optional)</span>
            </label>
            <input v-model="form.postal_code" type="text" class="input" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
            <input v-model="form.country" type="text" class="input" />
            <p v-if="errors.country" class="mt-1 text-sm text-red-600">{{ errors.country[0] }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Notes <span class="text-gray-400">(optional)</span>
            </label>
            <input v-model="form.notes" type="text" class="input" placeholder="Landmark, extra info" />
          </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input v-model="form.is_default" type="checkbox" class="rounded border-gray-300" />
          Set as default address
        </label>
        <p v-if="errors.is_default" class="text-sm text-red-600">{{ errors.is_default[0] }}</p>

        <div class="flex gap-4 pt-2">
          <button type="submit" :disabled="saving" class="btn btn-primary">
            {{ saving ? 'Saving...' : editingId ? 'Update Address' : 'Save Address' }}
          </button>
          <button type="button" @click="cancelForm" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</template>
