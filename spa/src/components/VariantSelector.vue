<template>
  <div v-if="hasVariants" class="space-y-4">
    <div v-for="attrKey in variantAttributeKeys" :key="attrKey" class="variant-attr-group">
      <label class="block text-sm font-semibold text-gray-700 mb-2.5">
        {{ attrKey }}
        <span v-if="selectedAttributes[attrKey]" class="text-primary-600 font-normal ml-1.5">
          : {{ selectedAttributes[attrKey] }}
        </span>
      </label>

      <!-- Color swatches for color attributes -->
      <div v-if="isColorAttribute(attrKey)" class="flex flex-wrap gap-3">
        <button
          v-for="option in (variantAttributeOptions[attrKey] || [])"
          :key="option"
          @click="selectAttribute(attrKey, option)"
          :disabled="!isAttributeAvailable(attrKey, option)"
          class="group relative flex flex-col items-center gap-1.5 transition-all duration-150"
          :title="option"
        >
          <span
            class="block w-9 h-9 rounded-full border-2 transition-all duration-200"
            :class="[
              selectedAttributes[attrKey] === option
                ? 'border-primary-600 ring-2 ring-primary-200 scale-110 shadow-md'
                : isAttributeAvailable(attrKey, option)
                  ? 'border-gray-300 hover:border-gray-500 hover:scale-105 hover:shadow-sm'
                  : 'border-gray-200 opacity-30 cursor-not-allowed'
            ]"
            :style="{ backgroundColor: getColorHex(option) }"
          >
            <!-- Checkmark on selected -->
            <svg
              v-if="selectedAttributes[attrKey] === option"
              class="w-full h-full p-2"
              :class="isLightColor(getColorHex(option)) ? 'text-gray-800' : 'text-white'"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
          </span>
          <!-- Crossed-out for unavailable -->
          <span
            v-if="!isAttributeAvailable(attrKey, option) && selectedAttributes[attrKey] !== option"
            class="absolute inset-0 flex items-center justify-center"
          >
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </span>
          <span
            class="text-xs font-medium transition-colors duration-150"
            :class="selectedAttributes[attrKey] === option
              ? 'text-primary-700'
              : isAttributeAvailable(attrKey, option)
                ? 'text-gray-600 group-hover:text-gray-900'
                : 'text-gray-300'"
          >
            {{ option }}
          </span>
        </button>
      </div>

      <!-- Standard text buttons for non-color attributes -->
      <div v-else class="flex flex-wrap gap-2">
        <button
          v-for="option in (variantAttributeOptions[attrKey] || [])"
          :key="option"
          @click="selectAttribute(attrKey, option)"
          :disabled="!isAttributeAvailable(attrKey, option)"
          class="px-4 py-2 rounded-xl border-2 text-sm font-medium transition-all duration-150"
          :class="selectedAttributes[attrKey] === option
            ? 'border-primary-600 bg-primary-50 text-primary-700 shadow-sm'
            : isAttributeAvailable(attrKey, option)
              ? 'border-gray-200 bg-white text-gray-700 hover:border-gray-400 hover:shadow-sm'
              : 'border-gray-100 bg-gray-50 text-gray-300 cursor-not-allowed line-through opacity-50'"
        >
          {{ option }}
        </button>
      </div>
    </div>

    <!-- Selected variant info -->
    <div
      v-if="selectedVariant"
      class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800"
    >
      <div class="flex items-center gap-2 font-medium">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        Selected: {{ selectedVariant.name }}
      </div>
      <div class="mt-1 text-green-700 text-xs space-x-4">
        <span v-if="selectedVariant.sku">SKU: {{ selectedVariant.sku }}</span>
        <span>Stock: {{ selectedVariant.stock ?? 0 }}</span>
      </div>
    </div>
    <div
      v-else-if="Object.keys(selectedAttributes).length > 0 && Object.keys(selectedAttributes).length < variantAttributeKeys.length"
      class="mt-2 text-sm text-amber-600 flex items-center gap-1.5"
    >
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      Please select all options to see available variant.
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  variants: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits(['update:modelValue', 'update:selectedAttributes', 'update:variantImage'])

// ── Variant Selection State ──
const selectedAttributes = ref({})
const selectedVariant = ref(null)

// Extract unique attribute keys from all variants (e.g., "Size", "Color")
const variantAttributeKeys = computed(() => {
  if (!props.variants || props.variants.length === 0) return []
  const keys = new Set()
  props.variants.forEach(v => {
    if (v.attributes && typeof v.attributes === 'object') {
      Object.keys(v.attributes).forEach(k => keys.add(k))
    }
  })
  return Array.from(keys)
})

// Extract unique values for each attribute key (e.g., Size → ["S", "M", "L"])
const variantAttributeOptions = computed(() => {
  if (!props.variants) return {}
  const options = {}
  props.variants.forEach(v => {
    if (v.attributes && typeof v.attributes === 'object') {
      Object.keys(v.attributes).forEach(k => {
        if (!options[k]) options[k] = new Set()
        options[k].add(v.attributes[k])
      })
    }
  })
  const result = {}
  Object.keys(options).forEach(k => { result[k] = Array.from(options[k]) })
  return result
})

// Computed: does the product have variants?
const hasVariants = computed(() => {
  return props.variants && props.variants.length > 0
})

// ── Color Swatch Helpers ──
const colorHexMap = {
  red: '#EF4444', darkred: '#8B0000', crimson: '#DC143C', firebrick: '#B22222',
  blue: '#3B82F6', navy: '#1E3A5F', royalblue: '#4169E1', skyblue: '#87CEEB', steelblue: '#4682B4', dodgerblue: '#1E90FF',
  green: '#22C55E', darkgreen: '#006400', forestgreen: '#228B22', lime: '#00FF00', olive: '#808000', seagreen: '#2E8B57', teal: '#008080',
  yellow: '#EAB308', gold: '#FFD700', orange: '#F97316', darkorange: '#FF8C00', coral: '#FF7F50', tomato: '#FF6347',
  purple: '#A855F7', indigo: '#4F46E5', violet: '#8B5CF6', plum: '#DDA0DD', orchid: '#DA70D6',
  pink: '#EC4899', hotpink: '#FF69B4', deeppink: '#FF1493', lightpink: '#FFB6C1',
  brown: '#92400E', saddlebrown: '#8B4513', sienna: '#A0522D', chocolate: '#D2691E',
  black: '#111111', white: '#FFFFFF', gray: '#6B7280', grey: '#6B7280', silver: '#C0C0C0',
  cyan: '#06B6D4', turquoise: '#40E0D0', aqua: '#00FFFF',
  beige: '#F5F5DC', ivory: '#FFFFF0', lavender: '#E6E6FA', mint: '#98FB98',
  maroon: '#800000', magenta: '#FF00FF', khaki: '#F0E68C', salmon: '#FA8072',
}

function isColorAttribute(attrKey) {
  const key = attrKey.toLowerCase()
  return key === 'color' || key === 'colour' || key === 'colors' || key === 'colours' || key.includes('color') || key.includes('colour')
}

const _colorCache = {}

function getColorHex(colorName) {
  if (!colorName) return '#CBD5E1'
  const normalized = colorName.toLowerCase().trim()
  if (_colorCache[normalized]) return _colorCache[normalized]
  if (colorHexMap[normalized]) {
    _colorCache[normalized] = colorHexMap[normalized]
    return _colorCache[normalized]
  }
  if (/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(normalized)) {
    _colorCache[normalized] = normalized
    return normalized
  }
  try {
    const ctx = document.createElement('canvas').getContext('2d')
    ctx.fillStyle = normalized
    const resolved = ctx.fillStyle
    _colorCache[normalized] = resolved !== normalized ? resolved : '#CBD5E1'
  } catch {
    _colorCache[normalized] = '#CBD5E1'
  }
  return _colorCache[normalized]
}

function isLightColor(hex) {
  if (!hex) return false
  let c = hex.replace('#', '')
  if (c.length === 3) c = c[0] + c[0] + c[1] + c[1] + c[2] + c[2]
  if (c.length < 6) return false
  const r = parseInt(c.substring(0, 2), 16)
  const g = parseInt(c.substring(2, 4), 16)
  const b = parseInt(c.substring(4, 6), 16)
  return (0.299 * r + 0.587 * g + 0.114 * b) > 186
}

// ── Selection Logic ──

// Check if attribute value is selectable given current selections
function isAttributeAvailable(attrKey, attrValue) {
  if (!props.variants || props.variants.length === 0) return true
  const current = { ...selectedAttributes.value, [attrKey]: attrValue }
  return props.variants.some(v => {
    if (!v.attributes) return false
    return Object.keys(current).every(k => v.attributes[k] === current[k])
  })
}

// When user picks an attribute value
function selectAttribute(attrKey, attrValue) {
  if (selectedAttributes.value[attrKey] === attrValue) {
    const newAttrs = { ...selectedAttributes.value }
    delete newAttrs[attrKey]
    selectedAttributes.value = newAttrs
  } else {
    selectedAttributes.value = { ...selectedAttributes.value, [attrKey]: attrValue }
  }
  selectedVariant.value = findMatchingVariant()
}

// Find the variant that matches all currently selected attributes
function findMatchingVariant() {
  if (!props.variants) return null
  const attrs = selectedAttributes.value
  const keys = Object.keys(attrs)
  if (keys.length === 0) return null
  if (keys.length < variantAttributeKeys.value.length) return null

  return props.variants.find(v => {
    if (!v.attributes) return false
    return keys.every(k => v.attributes[k] === attrs[k])
  }) || null
}

// Find the first variant matching ALL currently selected attributes (partial match)
function findVariantByPartialAttributes() {
  if (!props.variants) return null
  const attrs = selectedAttributes.value
  const keys = Object.keys(attrs)
  if (keys.length === 0) return null
  return props.variants.find(v => {
    if (!v.attributes) return false
    return keys.every(k => v.attributes[k] === attrs[k])
  }) || null
}

/**
 * Computed: best-matching variant's first image (or null).
 * Uses partial-attribute matching so parents can switch the main product image
 * as soon as ANY attribute (e.g. Color) is selected, without waiting for all.
 */
const variantImage = computed(() => {
  // If a full variant is selected, use its first image
  if (selectedVariant.value && selectedVariant.value.images && selectedVariant.value.images.length > 0) {
    return selectedVariant.value.images[0]
  }
  // Otherwise try partial match on the current attributes
  const partial = findVariantByPartialAttributes()
  if (partial && partial.images && partial.images.length > 0) {
    return partial.images[0]
  }
  return null
})

// Emit variantImage whenever selectedAttributes or selectedVariant changes
watch(variantImage, (img) => {
  emit('update:variantImage', img)
})

// Reset selections when variants prop changes
watch(() => props.variants, () => {
  selectedAttributes.value = {}
  selectedVariant.value = null
})

// Emit changes to parent
watch(selectedVariant, (val) => {
  emit('update:modelValue', val)
})

watch(selectedAttributes, () => {
  emit('update:selectedAttributes', { ...selectedAttributes.value })
}, { deep: true })
</script>
