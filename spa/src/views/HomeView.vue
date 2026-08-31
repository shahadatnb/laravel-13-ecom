<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useSiteStore } from '@/stores/site'
import { useSeoMeta } from '@/composables/useSeoMeta'
import HomeModern from '@/views/home/HomeModern.vue'
import HomeMinimal from '@/views/home/HomeMinimal.vue'
import HomeDeals from '@/views/home/HomeDeals.vue'
import HomeClassic from '@/views/home/HomeClassic.vue'
import HomeShowroom from '@/views/home/HomeShowroom.vue'

const siteStore = useSiteStore()
const { setSeoMeta, setOrganizationJsonLd } = useSeoMeta()

const themes = {
  modern: HomeModern,
  minimal: HomeMinimal,
  deals: HomeDeals,
  classic: HomeClassic,
  showroom: HomeShowroom,
}

const activeTheme = ref('modern')

const themeComponent = computed(() => themes[activeTheme.value] || themes.modern)

onMounted(async () => {
  if (!siteStore.settings.site_name) {
    await siteStore.fetchSiteData()
  }
  // SEO: Home page meta
  setSeoMeta({
    title: '',
    description: siteStore.getSetting('site_description') || 'Welcome to our online store. Shop the best products at great prices.',
    keywords: 'online store, shop, deals, products',
    type: 'website'
  })
  setOrganizationJsonLd()
  const chosen = siteStore.getSetting('active_theme', 'modern')
  if (chosen) activeTheme.value = chosen
})

watch(() => siteStore.settings.active_theme, (value) => {
  if (value) activeTheme.value = value
})
</script>

<template>
  <component :is="themeComponent" />
</template>
