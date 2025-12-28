<script setup>
import { ref, onMounted } from 'vue'
import MainLayout from '../components/MainLayout.vue'
import LinkItem from '../components/LinkItem.vue'
import ConfirmDialog from '../components/ConfirmDialog.vue'
import BaseButton from '../components/BaseButton.vue'
import { getTrashedLinks, restoreLink, forceDeleteLink } from '../services/api'
import { IconTrash } from '../assets/icons'

const layoutRef = ref(null)
const links = ref([])
const loading = ref(false)
const searchQuery = ref('')

// Modals
const showDeleteConfirm = ref(false)
const deletingLink = ref(null)

// Fetch trashed links
const fetchLinks = async () => {
  loading.value = true
  try {
    const response = await getTrashedLinks()
    links.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch trashed links:', error)
  } finally {
    loading.value = false
  }
}

// Search handler
const handleSearch = (query) => {
  searchQuery.value = query
}

// Filtered links
const filteredLinks = () => {
  if (!searchQuery.value) return links.value
  const query = searchQuery.value.toLowerCase()
  return links.value.filter(link =>
    link.title?.toLowerCase().includes(query) ||
    link.original_url?.toLowerCase().includes(query) ||
    link.slug?.toLowerCase().includes(query)
  )
}

// Restore link
const handleRestore = async (link) => {
  try {
    await restoreLink(link.id)
    fetchLinks()
    layoutRef.value?.fetchStats()
  } catch (error) {
    console.error('Failed to restore link:', error)
  }
}

// Open force delete confirm
const confirmForceDelete = (link) => {
  deletingLink.value = link
  showDeleteConfirm.value = true
}

// Force delete link
const handleForceDelete = async () => {
  if (!deletingLink.value) return
  try {
    await forceDeleteLink(deletingLink.value.id)
    showDeleteConfirm.value = false
    deletingLink.value = null
    fetchLinks()
    layoutRef.value?.fetchStats()
  } catch (error) {
    console.error('Failed to force delete link:', error)
  }
}

onMounted(() => {
  fetchLinks()
})
</script>

<template>
  <MainLayout ref="layoutRef" @search="handleSearch">
    <!-- Loading State -->
    <div v-if="loading" class="py-12 text-center text-gray-500">
      Carregando...
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredLinks().length === 0" class="py-12 text-center">
      <IconTrash class="w-16 h-16 mx-auto text-gray-300 mb-4" />
      <p class="text-gray-500">A lixeira está vazia</p>
      <router-link
        to="/"
        class="mt-4 inline-block px-4 py-2.5 bg-primary-500 text-white rounded-lg font-semibold hover:bg-primary-600 transition"
      >
        Voltar para Home
      </router-link>
    </div>

    <!-- Links -->
    <div v-else class="space-y-3">
      <LinkItem
        v-for="link in filteredLinks()"
        :key="link.id"
        :link="link"
        :showRestore="true"
        @restore="handleRestore"
        @forceDelete="confirmForceDelete"
      />
    </div>
  </MainLayout>

  <!-- Force Delete Confirmation -->
  <ConfirmDialog
    :show="showDeleteConfirm"
    title="Deletar Permanentemente"
    :message="`Tem certeza que deseja deletar '${deletingLink?.title || 'este link'}' permanentemente? Esta ação não pode ser desfeita.`"
    confirmText="Deletar Permanentemente"
    :danger="true"
    @confirm="handleForceDelete"
    @cancel="showDeleteConfirm = false"
  />
</template>
