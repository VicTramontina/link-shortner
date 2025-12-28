<script setup>
import { ref, onMounted, provide } from 'vue'
import AppHeader from './AppHeader.vue'
import NavTabs from './NavTabs.vue'
import StatsCard from './StatsCard.vue'
import { getStats } from '../services/api'

const props = defineProps({
  showSort: {
    type: Boolean,
    default: false,
  },
  sortBy: String,
  sortOrder: String,
})

const emit = defineEmits(['search', 'add', 'sort'])

const searchQuery = ref('')
const stats = ref({
  total_links: 0,
  total_views: 0,
  total_clicks: 0,
  avg_ctr: '0%',
})

const fetchStats = async () => {
  try {
    const response = await getStats()
    stats.value = response.data
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const handleSearch = (query) => {
  searchQuery.value = query
  emit('search', query)
}

// Provide refreshStats to child components
provide('refreshStats', fetchStats)

// Expose for parent access
defineExpose({ fetchStats })

onMounted(() => {
  fetchStats()
})
</script>

<template>
  <div class="min-h-screen bg-gradient-main">
    <!-- Decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-20 left-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-secondary-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Main content -->
    <div class="relative z-10 max-w-6xl mx-auto px-2 sm:px-4 py-4 sm:py-6">
      <!-- Main Card -->
      <div class="bg-gray-50 rounded-2xl sm:rounded-3xl shadow-xl overflow-hidden">
        <!-- Header -->
        <AppHeader
          :searchQuery="searchQuery"
          @search="handleSearch"
          @add="emit('add')"
        />

        <!-- Stats Section -->
        <div class="py-4 px-4 sm:px-6 md:mx-4 lg:mx-10 border-b border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800">ESTATÍSTICAS</h2>
            <router-link to="/stats" class="text-gray-500 text-sm">
              Ver Tudo
            </router-link>
          </div>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-6">
            <StatsCard icon="link" :value="stats.total_links" label="Links" />
            <StatsCard icon="eye" :value="stats.total_views" label="Visualizações" />
            <StatsCard icon="click" :value="stats.total_clicks" label="Cliques" />
            <StatsCard icon="ctr" :value="stats.avg_ctr" label="CTR Médio" />
          </div>
        </div>

        <!-- Navigation Tabs -->
        <NavTabs
          :showSort="showSort"
          :sortBy="sortBy"
          :sortOrder="sortOrder"
          @sort="emit('sort', $event)"
        />

        <!-- Page Content -->
        <div class="px-3 sm:px-6 pb-4 sm:pb-6">
          <slot></slot>
        </div>
      </div>
    </div>
  </div>
</template>
