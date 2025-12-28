<script setup>
import { ref, computed, onMounted } from 'vue'
import MainLayout from '../components/MainLayout.vue'
import LinkItem from '../components/LinkItem.vue'
import LinkForm from '../components/LinkForm.vue'
import ConfirmDialog from '../components/ConfirmDialog.vue'
import BaseButton from '../components/BaseButton.vue'
import { getLinks, createLink, updateLink, deleteLink } from '../services/api'
import { IconLink } from '../assets/icons'

const layoutRef = ref(null)
const links = ref([])
const loading = ref(false)
const searchQuery = ref('')
const sortBy = ref('created_at')
const sortOrder = ref('desc')
const currentPage = ref(1)
const totalPages = ref(1)

// Modals
const showLinkForm = ref(false)
const editingLink = ref(null)
const showDeleteConfirm = ref(false)
const deletingLink = ref(null)

// Filtered and sorted links
const filteredLinks = computed(() => links.value)

// Fetch links from API
const fetchLinks = async () => {
  loading.value = true
  try {
    const response = await getLinks({
      search: searchQuery.value,
      sort_by: sortBy.value,
      sort_order: sortOrder.value,
      page: currentPage.value,
    })
    links.value = response.data.data
    totalPages.value = response.data.last_page || 1
  } catch (error) {
    console.error('Failed to fetch links:', error)
  } finally {
    loading.value = false
  }
}

// Search handler with debounce
let searchTimeout = null
const handleSearch = (query) => {
  searchQuery.value = query
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchLinks()
  }, 300)
}

// Sort handler
const handleSort = ({ field, order }) => {
  sortBy.value = field
  sortOrder.value = order
  fetchLinks()
}

// Open create modal
const openCreateModal = () => {
  editingLink.value = null
  showLinkForm.value = true
}

// Open edit modal
const openEditModal = (link) => {
  editingLink.value = { ...link }
  showLinkForm.value = true
}

// Handle form submit
const handleFormSubmit = async ({ id, data }) => {
  try {
    if (id) {
      await updateLink(id, data)
    } else {
      await createLink(data)
    }
    showLinkForm.value = false
    fetchLinks()
    layoutRef.value?.fetchStats()
  } catch (error) {
    console.error('Failed to save link:', error)
  }
}

// Open delete confirm
const confirmDelete = (link) => {
  deletingLink.value = link
  showDeleteConfirm.value = true
}

// Handle delete
const handleDelete = async () => {
  if (!deletingLink.value) return
  try {
    await deleteLink(deletingLink.value.id)
    showDeleteConfirm.value = false
    deletingLink.value = null
    fetchLinks()
    layoutRef.value?.fetchStats()
  } catch (error) {
    console.error('Failed to delete link:', error)
  }
}

// Pagination
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    fetchLinks()
  }
}

onMounted(() => {
  fetchLinks()
})
</script>

<template>
  <MainLayout
    ref="layoutRef"
    :showSort="true"
    :sortBy="sortBy"
    :sortOrder="sortOrder"
    @search="handleSearch"
    @add="openCreateModal"
    @sort="handleSort"
  >
    <!-- Loading State -->
    <div v-if="loading" class="py-12 text-center text-gray-500">
      Carregando...
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredLinks.length === 0" class="py-12 text-center">
      <IconLink class="w-16 h-16 mx-auto text-gray-300 mb-4" />
      <p class="text-gray-500">Nenhum link encontrado</p>
      <BaseButton class="mt-4" @click="openCreateModal">
        Criar primeiro link
      </BaseButton>
    </div>

    <!-- Links -->
    <div v-else class="space-y-3">
      <LinkItem
        v-for="link in filteredLinks"
        :key="link.id"
        :link="link"
        @edit="openEditModal"
        @delete="confirmDelete"
      />
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="flex items-center justify-center gap-2 mt-6">
      <BaseButton
        variant="secondary"
        size="sm"
        :disabled="currentPage === 1"
        @click="goToPage(currentPage - 1)"
      >
        Anterior
      </BaseButton>
      <span class="px-4 py-1 text-gray-600">
        Página {{ currentPage }} de {{ totalPages }}
      </span>
      <BaseButton
        variant="secondary"
        size="sm"
        :disabled="currentPage === totalPages"
        @click="goToPage(currentPage + 1)"
      >
        Próxima
      </BaseButton>
    </div>
  </MainLayout>

  <!-- Link Form Modal -->
  <LinkForm
    :show="showLinkForm"
    :link="editingLink"
    @close="showLinkForm = false"
    @submit="handleFormSubmit"
  />

  <!-- Delete Confirmation -->
  <ConfirmDialog
    :show="showDeleteConfirm"
    title="Deletar Link"
    :message="`Tem certeza que deseja mover '${deletingLink?.title || 'este link'}' para a lixeira?`"
    confirmText="Deletar"
    :danger="true"
    @confirm="handleDelete"
    @cancel="showDeleteConfirm = false"
  />
</template>
