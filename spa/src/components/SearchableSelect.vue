<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: [String, Number, null], default: '' },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Search...' },
  label: { type: String, default: '' },
  required: { type: Boolean, default: false },
  searchKey: { type: String, default: 'name' },
  valueKey: { type: String, default: 'name' },
  subKey: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const searchQuery = ref('')
const searchInput = ref(null)
const dropdownRef = ref(null)

const selectedLabel = computed(() => {
  if (!props.modelValue) return ''
  const found = props.options.find(o => o[props.valueKey] === props.modelValue)
  return found ? (props.subKey ? `${found[props.searchKey]} (${found[props.subKey]})` : found[props.searchKey]) : props.modelValue
})

const filteredOptions = computed(() => {
  const q = searchQuery.value.toLowerCase().trim()
  if (!q) return props.options
  return props.options.filter(o => {
    const name = (o[props.searchKey] || '').toLowerCase()
    const sub = props.subKey ? (o[props.subKey] || '').toLowerCase() : ''
    return name.includes(q) || sub.includes(q)
  })
})

function toggle() {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    searchQuery.value = ''
    setTimeout(() => searchInput.value?.focus(), 50)
  }
}

function select(option) {
  emit('update:modelValue', option[props.valueKey])
  isOpen.value = false
  searchQuery.value = ''
}

function clear() {
  emit('update:modelValue', '')
  isOpen.value = false
  searchQuery.value = ''
}

function handleClickOutside(e) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isOpen.value = false
  }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <div ref="dropdownRef" class="relative">
    <label v-if="label" class="block text-sm font-semibold text-gray-700 mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Trigger -->
    <div
      @click="toggle"
      class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-white cursor-pointer flex items-center justify-between focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
    >
      <span :class="modelValue ? 'text-gray-900' : 'text-gray-400'">
        {{ modelValue ? selectedLabel : placeholder }}
      </span>
      <div class="flex items-center gap-1">
        <button v-if="modelValue" @click.stop="clear" class="text-gray-400 hover:text-gray-600 p-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
      </div>
    </div>

    <!-- Dropdown -->
    <div v-if="isOpen" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-hidden">
      <!-- Search input -->
      <div class="p-2 border-b border-gray-100">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-primary-500 focus:border-primary-500 outline-none"
            placeholder="Type to search..."
          />
        </div>
      </div>

      <!-- Options list -->
      <div class="overflow-y-auto max-h-48">
        <div v-if="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center">
          No results found
        </div>
        <div
          v-for="option in filteredOptions"
          :key="option[valueKey]"
          @click="select(option)"
          class="px-4 py-2.5 text-sm cursor-pointer hover:bg-primary-50 transition-colors flex items-center justify-between"
          :class="{ 'bg-primary-50 text-primary-700 font-semibold': option[valueKey] === modelValue }"
        >
          <span class="text-gray-900">{{ option[searchKey] }}</span>
          <span v-if="subKey && option[subKey]" class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ option[subKey] }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
