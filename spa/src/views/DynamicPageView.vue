<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import PageService from '@/services/PageService'
import { isEditorJsData, editorJsToHtml } from '@/utils/editorParser'

const route = useRoute()
const router = useRouter()

const page = ref(null)
const loading = ref(true)
const error = ref(null)

// Compute rendered HTML from either Editor.js JSON or legacy HTML
const renderedContent = computed(() => {
  const content = page.value?.content
  if (!content) return ''
  if (isEditorJsData(content)) {
    return editorJsToHtml(content)
  }
  return content // legacy HTML
})

async function loadPage(slug) {
  loading.value = true
  error.value = null
  page.value = null
  try {
    const response = await PageService.getBySlug(slug)
    page.value = response.data.data
    document.title = page.value.meta_title
      ? `${page.value.meta_title} - E-Commerce`
      : `${page.value.title} - E-Commerce`
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Page not found.'
    } else {
      error.value = 'Failed to load page.'
    }
    console.error('Page load error:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const slug = route.params.slug
  if (slug) loadPage(slug)
})

watch(() => route.params.slug, (newSlug) => {
  if (newSlug) loadPage(newSlug)
})
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16">
      <div class="text-4xl mb-4 animate-pulse">📄</div>
      <p class="text-gray-400">Loading page...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-16">
      <div class="text-6xl mb-4">🔍</div>
      <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ error }}</h2>
      <p class="text-gray-500 mb-6">The page you're looking for doesn't exist.</p>
      <RouterLink to="/" class="btn btn-primary">Go Home</RouterLink>
    </div>

    <!-- Page Content -->
    <div v-else-if="page" class="max-w-7xl mx-auto">
      <h1 class="text-3xl md:text-4xl font-bold mb-8">{{ page.title }}</h1>
      <div class="bg-white rounded-2xl shadow-md p-8 md:p-12">
        <div
          v-if="renderedContent"
          class="prose prose-lg max-w-none"
          v-html="renderedContent"
        ></div>
        <div v-else class="text-center py-8 text-gray-400">
          <p>No content yet.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.prose h2 {
  @apply text-2xl font-bold text-gray-900 mt-8 mb-4;
}
.prose h3 {
  @apply text-xl font-semibold text-gray-800 mt-6 mb-3;
}
.prose p {
  @apply text-gray-600 mb-4 leading-relaxed;
}
.prose ul, .prose ol {
  @apply text-gray-600 mb-4 pl-6 space-y-2;
}
.prose ul { @apply list-disc; }
.prose ol { @apply list-decimal; }
.prose li { @apply leading-relaxed; }
.prose strong { @apply font-semibold text-gray-800; }
</style>
