<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { useCategoryStore } from '@/stores/category'
import { useSiteStore } from '@/stores/site'
import { useCartStore } from '@/stores/cart'
import { useToast } from 'vue-toastification'
import { getImageUrl } from '@/utils/image'
import { getThemeText } from '@/utils/themeTexts'
import { formatPrice, initCurrencySettings } from '@/utils/currency'

const productStore = useProductStore()
const categoryStore = useCategoryStore()
const siteStore = useSiteStore()
const cartStore = useCartStore()
const toast = useToast()

const loading = ref(true)
const currentSlide = ref(0)

const heroSlides = computed(() => {
  const slides = siteStore.slides
  if (slides.length > 0) {
    return slides.map(s => ({
      title: s.title,
      subtitle: s.subtitle,
      cta: s.cta_text || 'Shop Now',
      link: s.cta_link || '/products',
      image: s.image_emoji || '🎉',
      badge_text: s.badge_text || 'Limited Time Offer'
    }))
  }
  return [{
    title: getThemeText('hero_title', 'Welcome to Our Store'),
    subtitle: getThemeText('hero_subtitle', 'Discover amazing products at great prices'),
    cta: 'Shop Now',
    link: '/products',
    image: '🎉',
    badge_text: 'Welcome'
  }]
})

const featuredProducts = computed(() => productStore.featuredProducts || [])
const newArrivals = computed(() => productStore.newArrivals || [])

const parentCategories = computed(() =>
  categoryStore.categories.filter(cat => !cat.parent_id).slice(0, 6)
)

const heroProduct = computed(() => featuredProducts.value[0] || null)

const tickerItems = computed(() => {
  const threshold = siteStore.getSetting('free_shipping_threshold', 5000)
  const base = [
    getThemeText('cash_on_delivery', 'ক্যাশ অন ডেলিভারি'),
    `ফ্রি ডেলিভারি ${formatPrice(threshold)}+`,
    getThemeText('easy_returns', '৭ দিনের রিটার্ন'),
    getThemeText('original_product', 'অরিজিনাল প্রোডাক্ট'),
    getThemeText('support_247', '২৪/৭ সাপোর্ট'),
  ]
  const phone = siteStore.getSetting('contact_phone', '+880 1234 567890')
  return [...base, `হটলাইন ${phone}`]
})

function discountPercent(product) {
  if (!product.sale_price || !product.regular_price) return 0
  const regular = parseFloat(product.regular_price)
  const sale = parseFloat(product.sale_price)
  if (regular <= 0 || sale <= 0) return 0
  return Math.round(((regular - sale) / regular) * 100)
}

function addToCart(product) {
  cartStore.addItem(product)
  toast.success(`${product.name} added to cart!`)
}

function pad(n) {
  return String(n).padStart(2, '0')
}

let slideInterval = null

onMounted(async () => {
  loading.value = true
  try {
    if (!siteStore.settings.site_name) {
      await siteStore.fetchSiteData()
    }
    await Promise.all([
      productStore.fetchFeatured(),
      productStore.fetchNewArrivals(),
      categoryStore.fetchCategories(),
    ])
    if (siteStore.settings) {
      initCurrencySettings(siteStore.settings)
    }
    slideInterval = setInterval(() => {
      currentSlide.value = (currentSlide.value + 1) % heroSlides.value.length
    }, 6000)
  } finally {
    loading.value = false
  }
})

onUnmounted(() => {
  if (slideInterval) clearInterval(slideInterval)
})
</script>

<template>
  <div class="showroom">
    <!-- ===== OFFER TICKER (signature) ===== -->
    <div class="ticker" role="marquee" aria-label="Store offers">
      <div class="ticker-track">
        <span v-for="(item, i) in [...tickerItems, ...tickerItems]" :key="i" class="ticker-item">
          <span class="ticker-star" aria-hidden="true">✦</span> {{ item }}
        </span>
      </div>
    </div>

    <!-- ===== HERO ===== -->
    <section class="hero">
      <div class="hero-grid">
        <div class="hero-copy">
          <p class="eyebrow">
            № {{ pad(currentSlide + 1) }} — {{ (heroSlides[currentSlide] || {}).badge_text || 'Featured Drop' }}
          </p>
          <h1 class="display" :key="'title-' + currentSlide">
            {{ (heroSlides[currentSlide] || {}).title || 'Welcome' }}
          </h1>
          <p class="lede">{{ (heroSlides[currentSlide] || {}).subtitle }}</p>
          <RouterLink :to="(heroSlides[currentSlide] || {}).link || '/products'" class="cta-block">
            {{ (heroSlides[currentSlide] || {}).cta || 'Shop Now' }} <span aria-hidden="true">→</span>
          </RouterLink>
          <div class="dots" role="tablist" :aria-label="'Slide ' + heroSlides.length + ' of ' + heroSlides.length">
            <button
              v-for="(slide, i) in heroSlides"
              :key="i"
              class="dot"
              :class="{ active: i === currentSlide }"
              :aria-label="'Show slide ' + (i + 1)"
              :aria-selected="i === currentSlide"
              role="tab"
              @click="currentSlide = i"
            ></button>
          </div>
        </div>

        <div class="hero-frame">
          <div v-if="(heroSlides[currentSlide] || {}).feature_image" class="frame-card">
            <img :src="(heroSlides[currentSlide] || {}).feature_image" :alt="(heroSlides[currentSlide] || {}).title"
              class="frame-img" width="600" height="600" />
          </div>
          <div v-else-if="heroProduct" class="frame-card">
            <img
              v-if="heroProduct.thumbnail || heroProduct.images?.length > 0"
              :src="getImageUrl(heroProduct.thumbnail || heroProduct.images[0]?.image)"
              :alt="heroProduct.name"
              class="frame-img"
              width="600"
              height="600"
            />
            <span v-else class="frame-emoji" aria-hidden="true">{{ (heroSlides[currentSlide] || {}).image || '🎉' }}</span>
            <div class="frame-caption">
              <span class="mono">№ {{ pad(currentSlide + 1) }} · {{ heroProduct.name }}</span>
              <span class="mono amber">{{ formatPrice(heroProduct.sale_price || heroProduct.regular_price) }}</span>
            </div>
          </div>
          <div v-else class="frame-card frame-empty">
            <span class="frame-emoji" aria-hidden="true">{{ (heroSlides[currentSlide] || {}).image || '🎉' }}</span>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== CATEGORY INDEX ===== -->
    <section class="band">
      <div class="container">
        <div class="section-head">
          <p class="eyebrow">Index</p>
          <h2 class="display small">{{ getThemeText('shop_by_category', 'Shop by Category') }}</h2>
        </div>
        <div v-if="loading" class="grid-cols-2 md:grid-cols-3 gap-px bg-line">
          <div v-for="i in 6" :key="i" class="cat-row animate-pulse"><div class="h-4 w-24 bg-paper/10"></div></div>
        </div>
        <div v-else class="cat-list">
          <RouterLink
            v-for="(category, i) in parentCategories"
            :key="category.id"
            :to="`/category/${category.slug}`"
            class="cat-row"
          >
            <span class="mono dim">{{ pad(i + 1) }}</span>
            <div v-if="category.thumbnail" class="w-8 h-8 rounded overflow-hidden flex-shrink-0">
              <img :src="getImageUrl(category.thumbnail)" :alt="category.name" class="w-full h-full object-cover" />
            </div>
            <span class="cat-name">{{ category.name }}</span>
            <span class="mono dim count">{{ category.products?.length || 0 }} items</span>
            <span class="cat-arrow" aria-hidden="true">→</span>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- ===== FEATURED — editorial index ===== -->
    <section class="band alt">
      <div class="container">
        <div class="section-head between">
          <div>
            <p class="eyebrow">Selection</p>
            <h2 class="display small">Featured Products</h2>
          </div>
          <RouterLink to="/products" class="text-link mono">View all →</RouterLink>
        </div>

        <div v-if="loading" class="prods">
          <div v-for="i in 4" :key="i" class="prod-row animate-pulse"><div class="h-5 w-40 bg-paper/10"></div></div>
        </div>

        <div v-else-if="featuredProducts.length > 0" class="prods">
          <div v-for="(product, i) in featuredProducts" :key="product.id" class="prod-row">
            <span class="mono dim">{{ pad(i + 1) }}</span>
            <RouterLink :to="`/product/${product.slug}`" class="prod-main">
              <img
                v-if="product.thumbnail || product.images?.length > 0"
                :src="getImageUrl(product.thumbnail || product.images[0]?.image)"
                :alt="product.name"
                class="prod-thumb"
                width="72"
                height="72"
                loading="lazy"
              />
              <span v-else class="prod-thumb prod-thumb-empty" aria-hidden="true">📦</span>
              <span class="prod-info">
                <span class="prod-name">{{ product.name }}</span>
                <span class="mono dim">{{ product.short_description }}</span>
              </span>
            </RouterLink>
            <span v-if="discountPercent(product) > 0" class="badge mono">-{{ discountPercent(product) }}%</span>
            <span class="mono amber price">{{ formatPrice(product.sale_price || product.regular_price) }}</span>
            <button class="add" :aria-label="'Add ' + product.name + ' to cart'" @click="addToCart(product)">+</button>
          </div>
        </div>

        <div v-else class="empty mono dim">No featured products yet.</div>
      </div>
    </section>

    <!-- ===== DEAL BAND ===== -->
    <section class="deal">
      <div class="container deal-inner">
        <div>
          <p class="eyebrow dark">Flash Sale</p>
          <h2 class="display">Deal of the Day</h2>
          <p class="lede dark">Limited-time offers, honest prices.</p>
        </div>
        <RouterLink to="/products" class="deal-cta mono">Shop deals 🔥</RouterLink>
      </div>
    </section>

    <!-- ===== NEW ARRIVALS ===== -->
    <section class="band">
      <div class="container">
        <div class="section-head between">
          <div>
            <p class="eyebrow">New In</p>
            <h2 class="display small">New Arrivals</h2>
          </div>
          <RouterLink to="/products?sort=newest" class="text-link mono">View all →</RouterLink>
        </div>

        <div v-if="loading" class="arrivals">
          <div v-for="i in 4" :key="i" class="arrive animate-pulse"><div class="h-40 bg-paper/10"></div></div>
        </div>

        <div v-else-if="newArrivals.length > 0" class="arrivals">
          <RouterLink v-for="(product, i) in newArrivals" :key="product.id" :to="`/product/${product.slug}`" class="arrive">
            <div class="arrive-img">
              <img
                v-if="product.thumbnail || product.images?.length > 0"
                :src="getImageUrl(product.thumbnail || product.images[0]?.image)"
                :alt="product.name"
                loading="lazy"
                width="400"
                height="400"
              />
              <span v-else aria-hidden="true">📦</span>
            </div>
            <div class="arrive-meta">
              <span class="mono dim">{{ pad(i + 1) }}</span>
              <span class="arrive-name">{{ product.name }}</span>
              <span class="mono amber">{{ formatPrice(product.sale_price || product.regular_price) }}</span>
            </div>
          </RouterLink>
        </div>

        <div v-else class="empty mono dim">New products will appear here.</div>
      </div>
    </section>

    <!-- ===== TRUST ===== -->
    <section class="band alt">
      <div class="container">
        <div class="section-head">
          <p class="eyebrow">Assurance</p>
          <h2 class="display small">Why Shop With Us</h2>
        </div>
        <div v-if="siteStore.settings.trust_features" class="trust-grid">
          <div v-for="(feature, i) in siteStore.settings.trust_features" :key="i" class="trust-item">
            <span class="mono dim trust-no">{{ pad(i + 1) }}</span>
            <span class="trust-icon" aria-hidden="true">{{ feature.icon || '✦' }}</span>
            <h3 class="trust-title">{{ feature.title }}</h3>
            <p class="trust-desc dim">{{ feature.description }}</p>
          </div>
        </div>
        <div v-else class="empty mono dim">Configure trust features in Admin → Settings → Site Settings</div>
      </div>
    </section>

    <!-- ===== NEWSLETTER + BRANDS ===== -->
    <section class="band">
      <div class="container">
        <div class="newsletter">
          <h2 class="display small">Stay in the loop</h2>
          <p class="dim">Offers, new arrivals and restocks — no spam, unsubscribe anytime.</p>
          <form class="news-form" @submit.prevent>
            <label for="showroom-newsletter" class="sr-only">Email address for newsletter</label>
            <input
              id="showroom-newsletter"
              type="email"
              name="email"
              autocomplete="email"
              placeholder="you@example.com"
              class="news-input mono"
            />
            <button type="submit" class="news-btn mono">Subscribe →</button>
          </form>
        </div>
        <div v-if="siteStore.settings.trusted_brands" class="brands">
          <span v-for="(brand, i) in siteStore.settings.trusted_brands" :key="i" class="brand mono dim">
            {{ typeof brand === 'string' ? brand : brand.name }}
          </span>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=IBM+Plex+Mono:wght@400;500&display=swap');

.showroom {
  --ink: #16130d;
  --ink-2: #211d14;
  --paper: #f1ebdf;
  --amber: #e19b2e;
  --muted: #8d8578;
  --line: rgba(241, 235, 223, 0.14);
  background: var(--ink);
  color: var(--paper);
  font-family: 'Inter', system-ui, sans-serif;
  line-height: 1.55;
}

.container {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 1.25rem;
}

.eyebrow,
.mono {
  font-family: 'IBM Plex Mono', ui-monospace, monospace;
  font-size: 0.72rem;
  letter-spacing: 0.14em;
  text-transform: uppercase;
}

.eyebrow { color: var(--amber); margin: 0 0 0.9rem; }
.eyebrow.dark { color: var(--ink); }
.dim { color: var(--muted); }
.amber { color: var(--amber); }
.display {
  font-family: 'Fraunces', Georgia, serif;
  font-weight: 500;
  font-size: clamp(2.6rem, 6vw, 4.6rem);
  line-height: 1.02;
  letter-spacing: -0.01em;
  margin: 0 0 1.2rem;
}
.display.small { font-size: clamp(1.7rem, 3.2vw, 2.4rem); margin-bottom: 0.5rem; }

/* ── Ticker ─────────────────────────────────────────── */
.ticker {
  background: var(--amber);
  color: var(--ink);
  overflow: hidden;
  border-bottom: 1px solid rgba(0, 0, 0, 0.25);
}
.ticker-track {
  display: flex;
  white-space: nowrap;
  width: max-content;
  animation: ticker-scroll 28s linear infinite;
}
.ticker-item {
  font-family: 'IBM Plex Mono', ui-monospace, monospace;
  font-size: 0.78rem;
  font-weight: 500;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 0.55rem 1.4rem;
}
.ticker-star { margin-right: 0.5rem; }
@keyframes ticker-scroll {
  from { transform: translateX(0); }
  to { transform: translateX(-50%); }
}
@media (prefers-reduced-motion: reduce) {
  .ticker-track { animation: none; flex-wrap: wrap; width: 100%; }
}

/* ── Hero ───────────────────────────────────────────── */
.hero { padding: clamp(2.5rem, 6vw, 5rem) 0 clamp(2rem, 5vw, 4rem); }
.hero-grid {
  max-width: 1120px;
  margin: 0 auto;
  padding: 0 1.25rem;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 3rem;
  align-items: center;
}
.hero-copy .display { font-style: italic; }
.lede { font-size: 1.12rem; color: var(--muted); max-width: 34rem; margin: 0 0 1.8rem; }
.lede.dark { color: rgba(22, 19, 13, 0.75); }
.cta-block {
  display: inline-flex;
  align-items: center;
  gap: 0.9rem;
  background: var(--amber);
  color: var(--ink);
  font-weight: 600;
  font-size: 0.95rem;
  padding: 0.95rem 1.6rem;
  border-radius: 2px;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.cta-block:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(225, 155, 46, 0.25); }
.dots { display: flex; gap: 0.6rem; margin-top: 2.2rem; }
.dot {
  width: 2.2rem;
  height: 3px;
  border: 0;
  background: var(--line);
  cursor: pointer;
  padding: 0;
  transition: background 0.2s ease;
}
.dot.active { background: var(--amber); }

.hero-frame { display: flex; justify-content: center; }
.frame-card {
  width: 100%;
  max-width: 430px;
  background: var(--ink-2);
  border: 1px solid var(--line);
  border-radius: 3px;
  overflow: hidden;
}
.frame-img { width: 100%; aspect-ratio: 1; object-fit: contain; padding: 1rem; background: #f6f1e6; }
.frame-emoji { display: flex; align-items: center; justify-content: center; font-size: 7rem; aspect-ratio: 1; }
.frame-empty { min-height: 320px; }
.frame-caption {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.8rem 1rem;
  border-top: 1px solid var(--line);
  font-size: 0.7rem;
}

/* ── Bands & sections ───────────────────────────────── */
.band { padding: clamp(2.5rem, 6vw, 5rem) 0; border-top: 1px solid var(--line); }
.band.alt { background: var(--ink-2); }
.section-head { margin-bottom: 2rem; }
.section-head.between { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
.text-link { color: var(--amber); text-decoration: none; font-size: 0.78rem; }
.text-link:hover { text-decoration: underline; }
.bg-line { background: var(--line); }

/* ── Category index ─────────────────────────────────── */
.cat-list { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; background: var(--line); border: 1px solid var(--line); }
.cat-row {
  display: grid;
  grid-template-columns: 2.2rem 1fr auto 1.4rem;
  align-items: center;
  gap: 0.8rem;
  background: var(--ink);
  padding: 1.1rem 1rem;
  text-decoration: none;
  color: var(--paper);
  transition: background 0.18s ease;
}
.cat-row:hover { background: var(--ink-2); }
.cat-name { font-family: 'Fraunces', Georgia, serif; font-size: 1.15rem; }
.cat-arrow { color: var(--amber); transition: transform 0.18s ease; }
.cat-row:hover .cat-arrow { transform: translateX(4px); }

/* ── Featured editorial index ───────────────────────── */
.prods { border-top: 1px solid var(--line); }
.prod-row {
  display: grid;
  grid-template-columns: 2.2rem minmax(0, 1fr) auto auto 2.4rem;
  align-items: center;
  gap: 1rem;
  padding: 1.1rem 0.4rem;
  border-bottom: 1px solid var(--line);
}
.prod-main { display: flex; align-items: center; gap: 1rem; text-decoration: none; color: var(--paper); min-width: 0; }
.prod-thumb { width: 64px; height: 64px; object-fit: contain; background: #f6f1e6; border-radius: 3px; flex-shrink: 0; }
.prod-thumb-empty { display: flex; align-items: center; justify-content: center; font-size: 1.8rem; background: var(--ink-2); }
.prod-info { display: flex; flex-direction: column; min-width: 0; }
.prod-name { font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.prod-info .mono { font-size: 0.68rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.badge { background: var(--amber); color: var(--ink); font-size: 0.68rem; font-weight: 500; padding: 0.2rem 0.5rem; border-radius: 2px; }
.price { font-size: 0.85rem; white-space: nowrap; }
.add {
  width: 2.3rem;
  height: 2.3rem;
  border: 1px solid var(--line);
  background: transparent;
  color: var(--amber);
  font-size: 1.1rem;
  border-radius: 3px;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
}
.add:hover { background: var(--amber); color: var(--ink); }

/* ── Deal band ──────────────────────────────────────── */
.deal { background: var(--amber); color: var(--ink); }
.deal-inner { display: flex; align-items: center; justify-content: space-between; gap: 2rem; flex-wrap: wrap; padding: clamp(2.5rem, 6vw, 4rem) 1.25rem; }
.deal .display { margin-bottom: 0.3rem; }
.deal-cta {
  font-size: 0.85rem;
  font-weight: 500;
  border: 2px solid var(--ink);
  color: var(--ink);
  text-decoration: none;
  padding: 0.9rem 1.6rem;
  border-radius: 2px;
  transition: background 0.18s ease, color 0.18s ease;
}
.deal-cta:hover { background: var(--ink); color: var(--amber); }

/* ── New arrivals ───────────────────────────────────── */
.arrivals { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
.arrive { text-decoration: none; color: var(--paper); }
.arrive-img {
  aspect-ratio: 1;
  background: var(--ink-2);
  border: 1px solid var(--line);
  border-radius: 3px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  margin-bottom: 0.8rem;
}
.arrive-img img { width: 100%; height: 100%; object-fit: contain; background: #f6f1e6; transition: transform 0.3s ease; }
.arrive:hover .arrive-img img { transform: scale(1.05); }
.arrive-meta { display: flex; flex-direction: column; gap: 0.3rem; }
.arrive-name { font-weight: 600; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* ── Trust ──────────────────────────────────────────── */
.trust-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; }
.trust-item { border: 1px solid var(--line); padding: 1.4rem; border-radius: 3px; }
.trust-no { font-size: 0.68rem; }
.trust-icon { display: block; font-size: 1.8rem; margin: 0.8rem 0 0.6rem; }
.trust-title { font-family: 'Fraunces', Georgia, serif; font-size: 1.15rem; margin: 0 0 0.35rem; }
.trust-desc { font-size: 0.88rem; margin: 0; }

/* ── Newsletter + brands ────────────────────────────── */
.newsletter { max-width: 34rem; margin: 0 auto; text-align: center; }
.newsletter .dim { margin-bottom: 1.5rem; }
.news-form { display: flex; gap: 0.6rem; }
.news-input {
  flex: 1;
  background: var(--ink-2);
  border: 1px solid var(--line);
  color: var(--paper);
  padding: 0.8rem 1rem;
  border-radius: 2px;
  font-size: 0.8rem;
}
.news-input:focus { outline: none; border-color: var(--amber); }
.news-btn {
  background: var(--amber);
  color: var(--ink);
  border: 0;
  font-size: 0.78rem;
  font-weight: 500;
  padding: 0 1.4rem;
  border-radius: 2px;
  cursor: pointer;
}
.brands { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem 2.5rem; margin-top: 3rem; opacity: 0.55; }

.empty { padding: 2rem 0; }

/* ── Responsive ─────────────────────────────────────── */
@media (max-width: 860px) {
  .hero-grid { grid-template-columns: 1fr; }
  .cat-list { grid-template-columns: 1fr; }
  .prod-row { grid-template-columns: 1.6rem minmax(0, 1fr) auto; }
  .prod-row .badge { display: none; }
  .arrivals { grid-template-columns: repeat(2, 1fr); }
  .trust-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .arrivals { grid-template-columns: 1fr; }
  .trust-grid { grid-template-columns: 1fr; }
}
</style>
