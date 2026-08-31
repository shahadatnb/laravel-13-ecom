/**
 * Theme Texts helper
 * Access dynamic marketing copy from site settings (theme_text_* keys)
 */
import { useSiteStore } from '@/stores/site'

/**
 * Get a theme text by key suffix
 * @param {string} key - The suffix after 'theme_text_' (e.g. 'hero_title')
 * @param {string} fallback - Default value if not found
 * @returns {string}
 */
export function getThemeText(key, fallback = '') {
  const siteStore = useSiteStore()
  return siteStore.getSetting(`theme_text_${key}`, fallback)
}

/**
 * Get all theme texts as an object
 */
export function getAllThemeTexts() {
  const siteStore = useSiteStore()
  const settings = siteStore.settings || {}
  const result = {}
  for (const [key, value] of Object.entries(settings)) {
    if (key.startsWith('theme_text_')) {
      result[key.replace('theme_text_', '')] = value
    }
  }
  return result
}
