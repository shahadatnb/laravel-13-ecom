import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useSiteStore } from '@/stores/site'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    // Restore scroll position when navigating back/forward
    if (savedPosition) {
      return savedPosition
    }
    // Scroll to hash anchor if present
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    // Scroll to top on every normal navigation
    return { top: 0, behavior: 'smooth' }
  },
  routes: [
    // Public routes
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      meta: { title: 'Home' }
    },
    {
      path: '/contact',
      name: 'contact',
      component: () => import('@/views/ContactView.vue'),
      meta: { title: 'Contact Us' }
    },

    // Auth routes
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/LoginView.vue'),
      meta: { title: 'Login', guestOnly: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/RegisterView.vue'),
      meta: { title: 'Register', guestOnly: true }
    },

    // Category routes
    {
      path: '/categories',
      name: 'categories.index',
      component: () => import('@/views/category/CategoryIndexView.vue'),
      meta: { title: 'Categories' }
    },
    {
      path: '/category/:slug',
      name: 'category.show',
      component: () => import('@/views/category/CategoryShowView.vue'),
      meta: { title: 'Category' }
    },

    // Product routes
    {
      path: '/products',
      name: 'products.index',
      component: () => import('@/views/product/ProductIndexView.vue'),
      meta: { title: 'Products' }
    },
    {
      path: '/product/:slug',
      name: 'product.show',
      component: () => import('@/views/product/ProductShowView.vue'),
      meta: { title: 'Product Detail' }
    },
    {
      path: '/search',
      name: 'products.search',
      component: () => import('@/views/product/ProductSearchView.vue'),
      meta: { title: 'Search Products' }
    },

    // Cart routes
    {
      path: '/cart',
      name: 'cart.index',
      component: () => import('@/views/cart/CartIndexView.vue'),
      meta: { title: 'Shopping Cart' }
    },

    // Checkout routes
    {
      path: '/checkout',
      name: 'checkout.index',
      component: () => import('@/views/checkout/CheckoutIndexView.vue'),
      meta: { title: 'Checkout' }
    },
    {
      path: '/order-success/:id',
      name: 'checkout.success',
      component: () => import('@/views/checkout/OrderSuccessView.vue'),
      meta: { title: 'Order Confirmed' }
    },

    // Customer routes (requires auth)
    {
      path: '/dashboard',
      name: 'customer.dashboard',
      component: () => import('@/views/customer/DashboardView.vue'),
      meta: { title: 'Dashboard', requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'customer.profile',
      component: () => import('@/views/customer/ProfileView.vue'),
      meta: { title: 'My Profile', requiresAuth: true }
    },
    {
      path: '/orders',
      name: 'customer.orders',
      component: () => import('@/views/customer/OrderIndexView.vue'),
      meta: { title: 'My Orders', requiresAuth: true }
    },
    {
      path: '/orders/:id',
      name: 'customer.order.show',
      component: () => import('@/views/customer/OrderShowView.vue'),
      meta: { title: 'Order Details', requiresAuth: true }
    },
    {
      path: '/wallet',
      name: 'customer.wallet',
      component: () => import('@/views/customer/WalletView.vue'),
      meta: { title: 'My Wallet', requiresAuth: true }
    },
    {
      path: '/addresses',
      name: 'customer.addresses',
      component: () => import('@/views/customer/AddressIndexView.vue'),
      meta: { title: 'My Addresses', requiresAuth: true }
    },
    {
      path: '/wishlist',
      name: 'customer.wishlist',
      component: () => import('@/views/customer/WishlistView.vue'),
      meta: { title: 'My Wishlist', requiresAuth: true }
    },

    // Order tracking (public)
    {
      path: '/order/track',
      name: 'order.track',
      component: () => import('@/views/OrderTrackView.vue'),
      meta: { title: 'Track Order' }
    },

    // Dynamic pages
    {
      path: '/page/:slug',
      name: 'page.show',
      component: () => import('@/views/DynamicPageView.vue'),
      meta: { title: 'Page' }
    },

    // 404
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFoundView.vue'),
      meta: { title: 'Page Not Found' }
    }
  ]
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const siteStore = useSiteStore()
  const siteName = siteStore.getSetting('site_name') || 'E-Commerce'
  document.title = to.meta.title ? `${to.meta.title} - ${siteName}` : siteName

  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login', query: { redirect: to.fullPath } })
  }
  else if (to.meta.guestOnly && authStore.isAuthenticated) {
    next({ name: 'customer.dashboard' })
  }
  else {
    next()
  }
})

export default router
