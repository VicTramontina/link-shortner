<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { IconHome, IconChart, IconTrash, IconSort } from '../assets/icons'

const props = defineProps({
  showSort: {
    type: Boolean,
    default: false,
  },
  sortBy: {
    type: String,
    default: 'created_at',
  },
  sortOrder: {
    type: String,
    default: 'desc',
  },
})

const emit = defineEmits(['sort'])

const route = useRoute()
const showSortDropdown = ref(false)

const tabs = [
  { path: '/', icon: 'home', label: 'Home' },
  { path: '/stats', icon: 'chart', label: 'Estatísticas' },
  { path: '/trash', icon: 'trash', label: 'Lixeira' },
]

const sortOptions = [
  { field: 'title', label: 'Título' },
  { field: 'access_count', label: 'Acessos' },
  { field: 'created_at', label: 'Data' },
]

const currentSortLabel = computed(() => {
  const option = sortOptions.find(o => o.field === props.sortBy)
  return option ? option.label : 'Data'
})

const handleSort = (field) => {
  const newOrder = props.sortBy === field && props.sortOrder === 'desc' ? 'asc' : 'desc'
  emit('sort', { field, order: newOrder })
}

const isActive = (path) => {
  return route.path === path
}
</script>

<template>
  <div class="px-4 sm:px-6 md:px-10 py-3 sm:py-4">
    <div class="flex items-center justify-between">
      <!-- Tabs -->
      <div class="flex gap-1 sm:gap-2">
        <router-link
          v-for="tab in tabs"
          :key="tab.path"
          :to="tab.path"
          class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-2 rounded-lg transition-all duration-300"
          :class="isActive(tab.path)
            ? 'text-primary-500 bg-primary-50'
            : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'"
        >
          <IconHome v-if="tab.icon === 'home'" class="w-5 h-5" />
          <IconChart v-else-if="tab.icon === 'chart'" class="w-5 h-5" />
          <IconTrash v-else-if="tab.icon === 'trash'" class="w-5 h-5" />

          <span
            class="font-medium overflow-hidden whitespace-nowrap transition-all duration-300 text-sm sm:text-base hidden xs:inline sm:inline"
            :class="isActive(tab.path) ? 'max-w-[100px] opacity-100' : 'max-w-0 opacity-0'"
          >
            {{ tab.label }}
          </span>
        </router-link>
      </div>

      <!-- Sort Dropdown -->
      <div v-if="showSort" class="relative">
        <button
          @click="showSortDropdown = !showSortDropdown"
          class="flex items-center gap-2 px-2 sm:px-3 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-lg transition"
        >
          <IconSort class="w-5 h-5" />
        </button>

        <!-- Dropdown Menu -->
        <div
          v-if="showSortDropdown"
          class="absolute right-0 mt-2 w-36 sm:w-40 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-100"
        >
          <button
            v-for="option in sortOptions"
            :key="option.field"
            @click="handleSort(option.field)"
            class="w-full text-left px-3 sm:px-4 py-2 text-sm hover:bg-gray-50 transition flex items-center justify-between"
            :class="sortBy === option.field ? 'text-primary-500 bg-primary-50' : 'text-gray-700'"
          >
            {{ option.label }}
            <span v-if="sortBy === option.field" class="text-xs">
              {{ sortOrder === 'asc' ? '↑' : '↓' }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Backdrop para fechar dropdown -->
  <div
    v-if="showSortDropdown"
    class="fixed inset-0 z-40"
    @click="showSortDropdown = false"
  ></div>
</template>
