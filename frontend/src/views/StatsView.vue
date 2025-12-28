<script setup>
import { ref, onMounted } from 'vue'
import MainLayout from '../components/MainLayout.vue'
import { getDetailedStats } from '../services/api'
import { IconEye } from '../assets/icons'

const topLinks = ref([])
const recentAccess = ref([])
const loading = ref(false)

const fetchDetailedStats = async () => {
  loading.value = true
  try {
    const response = await getDetailedStats()
    topLinks.value = response.data.top_links || []
    recentAccess.value = response.data.recent_access || []
  } catch (error) {
    console.error('Failed to fetch detailed stats:', error)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchDetailedStats()
})
</script>

<template>
  <MainLayout>
    <!-- Loading State -->
    <div v-if="loading" class="py-12 text-center text-gray-500">
      Carregando estatísticas...
    </div>

    <div v-else class="space-y-3 sm:space-y-4">
      <!-- Top Links Section -->
      <div class="bg-white rounded-lg p-4 sm:p-6">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Links Mais Acessados</h2>

        <div v-if="topLinks.length === 0" class="text-center py-6 sm:py-8 text-gray-500 text-sm sm:text-base">
          Nenhum acesso registrado ainda
        </div>

        <!-- Desktop Table -->
        <div v-else class="hidden sm:block overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left text-sm text-gray-500 border-b border-gray-100">
                <th class="pb-3 font-medium">#</th>
                <th class="pb-3 font-medium">Título</th>
                <th class="pb-3 font-medium">URL Curta</th>
                <th class="pb-3 font-medium text-right">Acessos</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(link, index) in topLinks"
                :key="link.id"
                class="border-b border-gray-50"
              >
                <td class="py-3 text-gray-500">{{ index + 1 }}</td>
                <td class="py-3">
                  <span class="font-medium text-gray-800">{{ link.title || 'Sem título' }}</span>
                </td>
                <td class="py-3">
                  <a
                    :href="link.short_url"
                    target="_blank"
                    class="text-secondary-500 hover:underline"
                  >
                    {{ link.short_url }}
                  </a>
                </td>
                <td class="py-3 text-right">
                  <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 text-primary-600 rounded-full text-sm">
                    {{ link.access_count }}
                    <IconEye class="w-4 h-4" />
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div v-if="topLinks.length > 0" class="sm:hidden space-y-2">
          <div
            v-for="(link, index) in topLinks"
            :key="link.id"
            class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
          >
            <span class="text-gray-400 font-medium text-sm w-5">{{ index + 1 }}</span>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 text-sm truncate">{{ link.title || 'Sem título' }}</p>
              <a
                :href="link.short_url"
                target="_blank"
                class="text-xs text-secondary-500 hover:underline truncate block"
              >
                {{ link.short_url }}
              </a>
            </div>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-100 text-primary-600 rounded-full text-xs flex-shrink-0">
              {{ link.access_count }}
              <IconEye class="w-3 h-3" />
            </span>
          </div>
        </div>
      </div>

      <!-- Recent Access Section -->
      <div class="bg-white rounded-lg p-4 sm:p-6">
        <h2 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Acessos Recentes</h2>

        <div v-if="recentAccess.length === 0" class="text-center py-6 sm:py-8 text-gray-500 text-sm sm:text-base">
          Nenhum acesso registrado ainda
        </div>

        <!-- Desktop Table -->
        <div v-else class="hidden sm:block overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left text-sm text-gray-500 border-b border-gray-100">
                <th class="pb-3 font-medium">Link</th>
                <th class="pb-3 font-medium">IP</th>
                <th class="pb-3 font-medium">Navegador</th>
                <th class="pb-3 font-medium text-right">Data/Hora</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="access in recentAccess"
                :key="access.id"
                class="border-b border-gray-50"
              >
                <td class="py-3">
                  <span class="font-medium text-gray-800">{{ access.link?.title || access.link?.slug }}</span>
                </td>
                <td class="py-3 text-gray-600 font-mono text-sm">
                  {{ access.ip_address }}
                </td>
                <td class="py-3 text-gray-500 text-sm max-w-xs truncate">
                  {{ access.user_agent }}
                </td>
                <td class="py-3 text-right text-gray-500 text-sm">
                  {{ formatDate(access.accessed_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards -->
        <div v-if="recentAccess.length > 0" class="sm:hidden space-y-2">
          <div
            v-for="access in recentAccess"
            :key="access.id"
            class="p-3 bg-gray-50 rounded-lg"
          >
            <div class="flex items-center justify-between mb-1">
              <p class="font-medium text-gray-800 text-sm truncate">{{ access.link?.title || access.link?.slug }}</p>
              <span class="text-xs text-gray-500 flex-shrink-0 ml-2">{{ formatDate(access.accessed_at) }}</span>
            </div>
            <p class="text-xs text-gray-500 font-mono">{{ access.ip_address }}</p>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>
