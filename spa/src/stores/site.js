import { defineStore } from 'pinia'
import { ref } from 'vue'
import SiteSettingService from '@/services/SiteSettingService'

export const useSiteStore = defineStore('site', () => {
  const slides = ref([])         // hero slides
  const settings = ref({})       // key-value settings map
  const loading = ref(false)
  const error = ref(null)

  // Reuse the in-flight request so multiple callers (layout + home page)
  // await the same promise instead of firing duplicate fetches.
  let inflight = null
  async function fetchSiteData() {
    if (inflight) return inflight
    loading.value = true
    error.value = null
    inflight = (async () => {
      try {
        const response = await SiteSettingService.getAll()
        const data = response.data.data || {}
        slides.value = data.slides || []
        settings.value = data.settings || {}
      } catch (err) {
        error.value = err.message
        console.error('Failed to load site settings:', err)
      } finally {
        loading.value = false
        inflight = null
      }
    })()
    return inflight
  }

  // Convenience getters
  function getSetting(key, defaultValue = null) {
    return settings.value[key] !== undefined ? settings.value[key] : defaultValue
  }

  return {
    slides,
    settings,
    loading,
    error,
    fetchSiteData,
    getSetting,
  }
})
