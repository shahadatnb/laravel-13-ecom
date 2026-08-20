<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useCartStore } from '@/stores/cart'
import { useCategoryStore } from '@/stores/category'
import { useSiteStore } from '@/stores/site'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const categoryStore = useCategoryStore()
const siteStore = useSiteStore()

onMounted(() => {
  if (!siteStore.settings.site_name) {
    siteStore.fetchSiteData()
  }
})

const mobileMenuOpen = ref(false)
const searchQuery = ref('')

async function handleSearch() {
  if (searchQuery.value.trim()) {
    await router.push({ name: 'products.search', query: { q: searchQuery.value } })
  }
}

async function handleLogout() {
  await authStore.logout()
  window.location.href = '/'
}
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- Top Announcement Bar -->
    <div class="bg-neutral-900 text-neutral-300 text-sm">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-2 flex justify-between items-center">
        <div class="flex items-center space-x-4">
          <span class="inline-flex items-center gap-1.5 hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            {{ siteStore.getSetting('contact_phone', '+880 1234 567890') }}
          </span>
          <span class="hidden sm:inline-flex items-center gap-1.5 hover:text-white transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ siteStore.getSetting('contact_email', 'info@ecommerce.com') }}
          </span>
        </div>
        <div class="flex items-center space-x-3 text-xs">
          <span class="hidden md:inline-flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            Free shipping on orders over ৳5000
          </span>
        </div>
      </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white/80 backdrop-blur-lg border-b border-neutral-100 sticky top-0 z-50">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 lg:h-20">
          <!-- Mobile Menu Button -->
          <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-xl hover:bg-neutral-100 transition-colors">
            <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>

          <!-- Logo -->
          <RouterLink :to="{ name: 'home' }" class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/20">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <span class="text-xl font-bold font-display text-neutral-900 hidden sm:block">
              {{ siteStore.getSetting('site_name', 'Store') }}
            </span>
          </RouterLink>

          <!-- Desktop Navigation -->
          <nav class="hidden lg:flex items-center gap-1">
            <RouterLink :to="{ name: 'home' }" class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
              :class="route.name === 'home' ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900'">
              Home
            </RouterLink>
            <RouterLink :to="{ name: 'categories.index' }" class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
              :class="route.name?.startsWith('categories') ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900'">
              Categories
            </RouterLink>
            <RouterLink :to="{ name: 'products.index' }" class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
              :class="route.name?.startsWith('products') ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900'">
              Products
            </RouterLink>
            <RouterLink :to="{ name: 'contact' }" class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
              :class="route.name === 'contact' ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900'">
              Contact
            </RouterLink>
          </nav>

          <!-- Search Bar (Desktop) -->
          <div class="hidden md:flex flex-1 max-w-md mx-8">
            <form @submit.prevent="handleSearch" class="w-full relative">
              <input v-model="searchQuery" type="text" placeholder="Search products..."
                class="w-full pl-10 pr-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500 transition-all" />
              <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>
          </div>

          <!-- Right Actions -->
          <div class="flex items-center gap-2">
            <!-- Search Toggle (Mobile) -->
            <button @click="router.push({ name: 'products.search' })" class="md:hidden p-2 rounded-xl hover:bg-neutral-100 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>

            <!-- Wishlist -->
            <RouterLink v-if="authStore.isAuthenticated" :to="{ name: 'customer.wishlist' }" class="p-2 rounded-xl hover:bg-neutral-100 transition-colors relative">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            </RouterLink>

            <!-- Cart -->
            <RouterLink :to="{ name: 'cart.index' }" class="p-2 rounded-xl hover:bg-neutral-100 transition-colors relative">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
              <span v-if="cartStore.totalItems > 0"
                class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-accent-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm">
                {{ cartStore.totalItems }}
              </span>
            </RouterLink>

            <!-- User Menu -->
            <div v-if="authStore.isAuthenticated" class="relative">
              <RouterLink :to="{ name: 'customer.dashboard' }" class="p-2 rounded-xl hover:bg-neutral-100 transition-colors flex items-center gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              </RouterLink>
            </div>
            <div v-else class="flex items-center gap-2">
              <RouterLink :to="{ name: 'login' }" class="btn btn-secondary text-sm !px-3 !py-1.5 hidden sm:inline-flex">
                Sign In
              </RouterLink>
              <RouterLink :to="{ name: 'register' }" class="btn btn-primary text-sm !px-3 !py-1.5 hidden sm:inline-flex">
                Join Now
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Menu -->
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div v-if="mobileMenuOpen" class="lg:hidden border-t border-neutral-100 bg-white">
          <div class="container mx-auto px-4 py-4 space-y-1">
            <form @submit.prevent="handleSearch" class="mb-3">
              <input v-model="searchQuery" type="text" placeholder="Search products..." class="input text-sm" />
            </form>
            <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'home' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">Home</RouterLink>
            <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'categories.index' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">Categories</RouterLink>
            <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'products.index' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">Products</RouterLink>
            <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'contact' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">Contact</RouterLink>
            <hr class="my-2 border-neutral-100">
            <template v-if="authStore.isAuthenticated">
              <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'customer.dashboard' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">Dashboard</RouterLink>
              <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'customer.orders' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">My Orders</RouterLink>
              <button @click="handleLogout" class="block w-full text-left px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50">Sign Out</button>
            </template>
            <template v-else>
              <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'login' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-primary-600 hover:bg-primary-50">Sign In</RouterLink>
              <RouterLink @click="mobileMenuOpen = false" :to="{ name: 'register' }" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-neutral-700 hover:bg-neutral-50">Create Account</RouterLink>
            </template>
          </div>
        </div>
      </Transition>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-neutral-900 text-neutral-400 mt-16">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="md:col-span-1">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
              </div>
              <span class="text-lg font-bold font-display text-white">{{ siteStore.getSetting('site_name', 'Store') }}</span>
            </div>
            <p class="text-sm leading-relaxed">{{ siteStore.getSetting('site_description', 'Your one-stop shop for everything you need.') }}</p>
          </div>
          <div>
            <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Shop</h4>
            <ul class="space-y-2.5">
              <li><RouterLink :to="{ name: 'products.index' }" class="text-sm hover:text-white transition-colors">All Products</RouterLink></li>
              <li><RouterLink :to="{ name: 'categories.index' }" class="text-sm hover:text-white transition-colors">Categories</RouterLink></li>
              <li><RouterLink :to="{ name: 'products.search' }" class="text-sm hover:text-white transition-colors">Search</RouterLink></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Account</h4>
            <ul class="space-y-2.5">
              <li><RouterLink :to="{ name: 'customer.dashboard' }" class="text-sm hover:text-white transition-colors">Dashboard</RouterLink></li>
              <li><RouterLink :to="{ name: 'customer.orders' }" class="text-sm hover:text-white transition-colors">My Orders</RouterLink></li>
              <li><RouterLink :to="{ name: 'cart.index' }" class="text-sm hover:text-white transition-colors">Cart</RouterLink></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Support</h4>
            <ul class="space-y-2.5">
              <li><RouterLink :to="{ name: 'contact' }" class="text-sm hover:text-white transition-colors">Contact Us</RouterLink></li>
              <li><RouterLink :to="{ name: 'order.track' }" class="text-sm hover:text-white transition-colors">Track Order</RouterLink></li>
            </ul>
          </div>
        </div>
        <hr class="border-neutral-800 my-8">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <p class="text-xs">&copy; {{ new Date().getFullYear() }} {{ siteStore.getSetting('site_name', 'Store') }}. All rights reserved.</p>
          <div class="flex items-center gap-4">
            <span class="text-xs">Secure payments powered by trusted gateways</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
