<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useAuthStore } from '@/stores/auth'
import { useSiteStore } from '@/stores/site'
import { useToast } from 'vue-toastification'
import OrderService from '@/services/OrderService'
import { formatPrice, initCurrencySettings } from '@/utils/currency'

const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const siteStore = useSiteStore()
const toast = useToast()

const submitting = ref(false)

// Guest checkout state
const guestEmail = ref('')

const form = ref({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  phone: authStore.user?.phone || '',
  address: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'Bangladesh',
  notes: '',
  payment_method: 'cod'
})

const hasItems = computed(() => cartStore.itemCount > 0)
const cartItems = computed(() => cartStore.items)
const subtotal = computed(() => cartStore.subtotal)

// Dynamic shipping & tax from site settings (with fallback defaults)
const taxRate = computed(() => {
  const val = parseFloat(siteStore.getSetting('tax_rate', '5'))
  return isNaN(val) ? 5 : val
})
const freeShippingThreshold = computed(() => {
  const val = parseFloat(siteStore.getSetting('free_shipping_threshold', '50'))
  return isNaN(val) ? 50 : val
})
const standardShippingRate = computed(() => {
  const val = parseFloat(siteStore.getSetting('shipping_rate', '5'))
  return isNaN(val) ? 5 : val
})

const shipping = computed(() => subtotal.value >= freeShippingThreshold.value ? 0 : standardShippingRate.value)
const tax = computed(() => subtotal.value * (taxRate.value / 100))
const total = computed(() => subtotal.value + shipping.value + tax.value)

// Initialize currency settings on mount
onMounted(() => {
  if (siteStore.settings) {
    initCurrencySettings(siteStore.settings)
  }
})

async function placeOrder() {
  if (!hasItems.value) { toast.error('Your cart is empty!'); return }
  if (!form.value.name || !form.value.phone || !form.value.address || !form.value.city) {
    toast.warning('Please fill in all required shipping fields.')
    return
  }
  // Guest checkout email validation
  if (!authStore.isAuthenticated) {
    if (!guestEmail.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(guestEmail.value)) {
      toast.warning('Please enter a valid email address for guest checkout, or sign in to your account.')
      return
    }
  }

  submitting.value = true
  try {
    const items = cartItems.value.map(item => ({
      product_id: item.id,
      product_variant_id: item.variant_id || null,
      product_name: item.name,
      product_sku: item.variant_sku || item.sku || null,
      unit_price: item.price,
      quantity: item.quantity,
      variant_attributes: item.variant_name ? { name: item.variant_name } : null
    }))

    const orderData = {
      items,
      guest_email: authStore.isAuthenticated ? undefined : guestEmail.value,
      shipping_charge: shipping.value,
      tax: tax.value,
      payment_method: form.value.payment_method,
      currency: 'USD',
      shipping_address: {
        recipient_name: form.value.name,
        phone: form.value.phone,
        address_line_1: form.value.address,
        city: form.value.city,
        state: form.value.state,
        postal_code: form.value.postal_code,
        country: form.value.country
      },
      notes: form.value.notes,
    }

    const response = await OrderService.create(orderData)
    cartStore.clearCart()
    toast.success('Order placed successfully!')
    const orderId = response.data.data?.id
    if (orderId) {
      // Pass order data via route state so guests can see confirmation without an API call
      router.push({
        name: 'checkout.success',
        params: { id: orderId },
        state: { orderData: response.data.data }
      })
    } else {
      router.push({ name: 'customer.orders' })
    }
  } catch (error) {
    const message = error.response?.data?.message || error.message || 'Failed to place order.'
    toast.error(message)
    if (error.response?.data?.errors) {
      Object.values(error.response.data.errors).forEach(errs => {
        toast.warning(errs[0])
      })
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

      <div v-if="!hasItems" class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="text-6xl mb-4">🛒</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
        <p class="text-gray-500 mb-6">Add some products before checking out.</p>
        <RouterLink to="/products" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
          Browse Products
        </RouterLink>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Shipping -->
        <div class="lg:col-span-2 space-y-6">
          <!-- ===== AUTH / GUEST SECTION ===== -->
          <div v-if="!authStore.isAuthenticated" class="mb-8 p-5 bg-gradient-to-br from-primary-50 to-blue-50 border border-primary-100 rounded-2xl">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h3 class="font-bold text-gray-900">Guest Checkout</h3>
                <p class="text-sm text-gray-500">You're checking out as a guest.</p>
              </div>
            </div>

            <div class="space-y-3">
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Email Address <span class="text-red-500">*</span>
              </label>
              <div class="flex flex-col sm:flex-row gap-3">
                <input
                  v-model="guestEmail"
                  type="email"
                  autocomplete="email"
                  name="guest_email"
                  class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all"
                  placeholder="your@email.com"
                  required
                />
                <RouterLink
                  :to="{ name: 'login', query: { redirect: $route.fullPath } }"
                  class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-primary-300 transition-all whitespace-nowrap"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                  </svg>
                  Sign In Instead
                </RouterLink>
              </div>
              <p class="text-xs text-gray-400">
                Your email will be used to send order confirmation and updates. 
                Already have an account? <RouterLink :to="{ name: 'login', query: { redirect: $route.fullPath } }" class="text-primary-600 font-medium hover:underline">Sign in</RouterLink> for a faster checkout.
              </p>
            </div>
          </div>

          <!-- Logged-in user info banner -->
          <div v-else class="mb-8 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-center gap-4">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div>
              <p class="font-semibold text-gray-900">Signed in as <span class="text-primary-600">{{ authStore.user?.name || authStore.user?.email }}</span></p>
              <p class="text-sm text-gray-500">Your order will be linked to your account. <RouterLink to="/profile" class="text-primary-600 hover:underline">Edit profile</RouterLink></p>
            </div>
          </div>

          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Shipping Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                <input v-model="form.name" type="text" autocomplete="name" name="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" placeholder="John Doe" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                <input v-model="form.email" type="email" autocomplete="email" name="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" placeholder="john@example.com" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone <span class="text-red-500">*</span></label>
                <input v-model="form.phone" type="tel" autocomplete="tel" name="phone" inputmode="tel" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" placeholder="+880 1XXX XXXXXXX" />
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address <span class="text-red-500">*</span></label>
                <textarea v-model="form.address" rows="2" autocomplete="street-address" name="address" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all resize-none" placeholder="Street address, building, apartment"></textarea>
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">City <span class="text-red-500">*</span></label>
                <input v-model="form.city" type="text" autocomplete="address-level2" name="city" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" placeholder="Dhaka" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">State / District</label>
                <input v-model="form.state" type="text" autocomplete="address-level1" name="state" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" placeholder="Dhaka" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Postal Code</label>
                <input v-model="form.postal_code" type="text" autocomplete="postal-code" name="postal_code" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" placeholder="1207" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Country</label>
                <input v-model="form.country" type="text" autocomplete="country-name" name="country" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all" />
              </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-100">
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Order Notes (optional)</label>
              <textarea v-model="form.notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all resize-none" placeholder="Any special instructions for your order..."></textarea>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
              <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
              Payment Method
            </h2>
            <div class="space-y-3">
              <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-primary-500 transition-colors">
                <input type="radio" v-model="form.payment_method" value="cod" class="w-4 h-4 text-primary-600" />
                <div class="flex-1">
                  <div class="font-semibold text-gray-900">Cash on Delivery</div>
                  <div class="text-sm text-gray-500">Pay when you receive your order</div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 sticky top-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h2>
            
            <div class="space-y-4 mb-6">
              <div v-for="item in cartItems" :key="item.id" class="flex gap-4 pb-4 border-b border-gray-100 last:border-0">
                <img :src="item.images?.[0] || '/images/placeholder.png'" :alt="item.name" class="w-16 h-16 object-cover rounded-lg" />
                <div class="flex-1 min-w-0">
                  <div class="font-medium text-gray-900 truncate">{{ item.name }}</div>
                  <div class="text-sm text-gray-500">Qty: {{ item.quantity }}</div>
                  <div class="text-sm font-semibold text-gray-900">{{ formatPrice(item.price) }} each</div>
                </div>
                <div class="text-right font-semibold text-gray-900">{{ formatPrice(item.price * item.quantity) }}</div>
              </div>
            </div>

            <div class="space-y-3 pb-6 border-b border-gray-100">
              <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span class="font-semibold">{{ formatPrice(subtotal) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Shipping</span>
                <span class="font-semibold">{{ shipping.value === 0 ? 'Free' : formatPrice(shipping) }}</span>
              </div>
              <div class="flex justify-between text-gray-600">
                <span>Tax ({{ taxRate.value }}%)</span>
                <span class="font-semibold">{{ formatPrice(tax) }}</span>
              </div>
            </div>

            <div class="pt-6">
              <div class="flex justify-between text-lg font-bold text-gray-900 mb-6">
                <span>Total</span>
                <span>{{ formatPrice(total) }}</span>
              </div>
              
              <button 
                @click="placeOrder" 
                :disabled="submitting"
                class="w-full py-3.5 px-6 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2"
              >
                <span v-if="submitting">Processing...</span>
                <span v-else>Place Order</span>
              </button>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
              <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Secure checkout</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
