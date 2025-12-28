<script setup>
import { ref } from 'vue'
import { IconChart, IconCopy, IconCheck, IconEdit, IconTrash, IconRestore } from '../assets/icons'

const props = defineProps({
  link: Object,
  showRestore: Boolean,
})

const emit = defineEmits(['copy', 'edit', 'delete', 'restore', 'forceDelete'])

const copied = ref(false)

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(props.link.short_url)
    copied.value = true
    emit('copy', props.link)
    setTimeout(() => (copied.value = false), 2000)
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}
</script>

<template>
  <div class="bg-white rounded-lg hover:shadow-sm transition">
    <!-- Desktop Layout -->
    <div class="hidden sm:flex items-center justify-between py-4 px-4">
      <div class="flex-1 min-w-0">
        <h3 class="font-medium text-gray-800 truncate">
          {{ link.title || 'Link sem título' }}
        </h3>
        <a
          :href="link.short_url"
          target="_blank"
          class="text-sm text-secondary-500 hover:underline truncate block"
        >
          {{ link.short_url }}
        </a>
      </div>

      <!-- Access Count -->
      <div class="flex items-center gap-2 mx-4">
        <span class="text-gray-600 font-medium">{{ link.access_count }}</span>
        <IconChart class="text-gray-400" />
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-1 sm:gap-2">
        <template v-if="!showRestore">
          <button
            @click="copyToClipboard"
            class="p-2 text-gray-400 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition"
            :title="copied ? 'Copiado!' : 'Copiar URL'"
          >
            <IconCopy v-if="!copied" />
            <IconCheck v-else class="text-green-500" />
          </button>
          <button
            @click="emit('edit', link)"
            class="p-2 text-gray-400 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition"
            title="Editar"
          >
            <IconEdit />
          </button>
          <button
            @click="emit('delete', link)"
            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
            title="Excluir"
          >
            <IconTrash />
          </button>
        </template>
        <template v-else>
          <button
            @click="emit('restore', link)"
            class="p-2 text-gray-400 hover:text-green-500 hover:bg-green-50 rounded-lg transition"
            title="Restaurar"
          >
            <IconRestore />
          </button>
          <button
            @click="emit('forceDelete', link)"
            class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
            title="Excluir permanentemente"
          >
            <IconTrash />
          </button>
        </template>
      </div>
    </div>

    <!-- Mobile Layout -->
    <div class="sm:hidden p-3">
      <div class="flex items-start justify-between gap-2">
        <div class="flex-1 min-w-0">
          <h3 class="font-medium text-gray-800 truncate text-sm">
            {{ link.title || 'Link sem título' }}
          </h3>
          <a
            :href="link.short_url"
            target="_blank"
            class="text-xs text-secondary-500 hover:underline truncate block mt-0.5"
          >
            {{ link.short_url }}
          </a>
        </div>
        <!-- Access Count Mobile -->
        <div class="flex items-center gap-1 text-sm flex-shrink-0">
          <span class="text-gray-600 font-medium">{{ link.access_count }}</span>
          <IconChart class="text-gray-400 w-4 h-4" />
        </div>
      </div>

      <!-- Mobile Actions -->
      <div class="flex items-center justify-end gap-1 mt-2 pt-2 border-t border-gray-100">
        <template v-if="!showRestore">
          <button
            @click="copyToClipboard"
            class="p-1.5 text-gray-400 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition"
            :title="copied ? 'Copiado!' : 'Copiar URL'"
          >
            <IconCopy v-if="!copied" class="w-4 h-4" />
            <IconCheck v-else class="text-green-500 w-4 h-4" />
          </button>
          <button
            @click="emit('edit', link)"
            class="p-1.5 text-gray-400 hover:text-primary-500 hover:bg-primary-50 rounded-lg transition"
            title="Editar"
          >
            <IconEdit class="w-4 h-4" />
          </button>
          <button
            @click="emit('delete', link)"
            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
            title="Excluir"
          >
            <IconTrash class="w-4 h-4" />
          </button>
        </template>
        <template v-else>
          <button
            @click="emit('restore', link)"
            class="p-1.5 text-gray-400 hover:text-green-500 hover:bg-green-50 rounded-lg transition"
            title="Restaurar"
          >
            <IconRestore class="w-4 h-4" />
          </button>
          <button
            @click="emit('forceDelete', link)"
            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition"
            title="Excluir permanentemente"
          >
            <IconTrash class="w-4 h-4" />
          </button>
        </template>
      </div>
    </div>
  </div>
</template>
