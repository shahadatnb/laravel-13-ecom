<template>
  <div v-if="swatches.length > 0" class="flex flex-wrap gap-1.5" @click.stop @mousedown.stop>
    <button
      v-for="(swatch, i) in swatches"
      :key="i"
      @click="selectSwatch(swatch)"
      class="relative w-6 h-6 rounded-full border-2 transition-all duration-150 flex-shrink-0"
      :class="selectedValue === swatch.value
        ? 'border-primary-600 ring-2 ring-primary-200 scale-110 shadow-sm'
        : 'border-gray-300 hover:border-gray-500 hover:scale-105'"
      :style="{ backgroundColor: swatch.hex }"
      :title="swatch.value"
    >
      <!-- Checkmark on selected -->
      <svg
        v-if="selectedValue === swatch.value"
        class="w-full h-full p-1"
        :class="isLightColor(swatch.hex) ? 'text-gray-800' : 'text-white'"
        fill="none" stroke="currentColor" viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  variants: { type: Array, default: () => [] },
})

const emit = defineEmits(['select'])

const selectedValue = ref(null)

// ── Color Swatch Helpers (same as VariantSelector.vue) ──
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

const _colorCache = {}

function getColorHex(colorName) {
  if (!colorName) return '#CBD5E1'
  const n = colorName.toLowerCase().trim()
  if (_colorCache[n]) return _colorCache[n]
  if (colorHexMap[n]) { _colorCache[n] = colorHexMap[n]; return _colorCache[n] }
  if (/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(n)) { _colorCache[n] = n; return n }
  try {
    const ctx = document.createElement('canvas').getContext('2d')
    ctx.fillStyle = n
    const r = ctx.fillStyle
    _colorCache[n] = r !== n ? r : '#CBD5E1'
  } catch { _colorCache[n] = '#CBD5E1' }
  return _colorCache[n]
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

// ── Extract first attribute values as color swatches ──
const swatches = computed(() => {
  if (!props.variants || props.variants.length === 0) return []

  // Find the first attribute key across all variants
  let firstKey = null
  for (const v of props.variants) {
    if (v.attributes && typeof v.attributes === 'object') {
      const keys = Object.keys(v.attributes)
      if (keys.length > 0) { firstKey = keys[0]; break }
    }
  }
  if (!firstKey) return []

  // Collect unique values for this key
  const values = new Set()
  props.variants.forEach(v => {
    if (v.attributes && v.attributes[firstKey]) {
      values.add(v.attributes[firstKey])
    }
  })

  return Array.from(values).map(val => ({
    key: firstKey,
    value: val,
    hex: getColorHex(val),
  }))
})

function selectSwatch(swatch) {
  if (selectedValue.value === swatch.value) {
    selectedValue.value = null
    emit('select', null)
  } else {
    selectedValue.value = swatch.value
    // Find the first variant matching this attribute value
    const match = props.variants.find(v =>
      v.attributes && v.attributes[swatch.key] === swatch.value
    )
    emit('select', match || swatch.value)
  }
}

// Expose reset for parent cleanup
function reset() {
  selectedValue.value = null
}

defineExpose({ reset })
</script>
