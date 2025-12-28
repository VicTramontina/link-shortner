<script setup>
import { ref, watch, computed } from 'vue'
import { IconClose } from '../assets/icons'
import BaseButton from './BaseButton.vue'

const props = defineProps({
  show: Boolean,
  link: Object,
})

const emit = defineEmits(['close', 'submit'])

const form = ref({
  original_url: '',
  slug: '',
  title: '',
})

const errors = ref({})
const loading = ref(false)

const isEditing = computed(() => !!props.link?.id)

watch(() => props.link, (newLink) => {
  if (newLink) {
    form.value = {
      original_url: newLink.original_url || '',
      slug: newLink.slug || '',
      title: newLink.title || '',
    }
  } else {
    form.value = { original_url: '', slug: '', title: '' }
  }
  errors.value = {}
}, { immediate: true })

const handleSubmit = async () => {
  errors.value = {}
  loading.value = true

  try {
    emit('submit', {
      id: props.link?.id,
      data: form.value,
    })
  } finally {
    loading.value = false
  }
}

const close = () => {
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/50"
        @click="close"
      ></div>

      <!-- Modal -->
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4 sm:mb-6">
          <h2 class="text-lg sm:text-xl font-bold text-gray-800">
            {{ isEditing ? 'Editar Link' : 'Criar Novo Link' }}
          </h2>
          <button
            @click="close"
            class="text-gray-400 hover:text-gray-600 transition p-1"
          >
            <IconClose class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4 sm:space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
              URL <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.original_url"
              type="url"
              required
              placeholder="https://example.com/your-long-url"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            />
            <p v-if="errors.original_url" class="text-red-500 text-xs sm:text-sm mt-1">
              {{ errors.original_url[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
              Slug personalizado (opcional)
            </label>
            <input
              v-model="form.slug"
              type="text"
              placeholder="meu-link"
              minlength="6"
              maxlength="8"
              pattern="[a-zA-Z0-9]+"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            />
            <p class="text-gray-500 text-xs sm:text-sm mt-1">
              6-8 caracteres alfanuméricos. Deixe vazio para gerar automaticamente.
            </p>
            <p v-if="errors.slug" class="text-red-500 text-xs sm:text-sm mt-1">
              {{ errors.slug[0] }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">
              Título (opcional)
            </label>
            <input
              v-model="form.title"
              type="text"
              placeholder="Título do meu link"
              class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            />
          </div>

          <div class="flex gap-2 sm:gap-3 pt-3 sm:pt-4">
            <BaseButton variant="secondary" size="md" class="flex-1 sm:hidden" @click="close">
              Cancelar
            </BaseButton>
            <BaseButton variant="secondary" size="lg" class="flex-1 hidden sm:block" @click="close">
              Cancelar
            </BaseButton>
            <BaseButton type="submit" size="md" class="flex-1 sm:hidden" :loading="loading">
              <template #loading>Salvando...</template>
              {{ isEditing ? 'Atualizar' : 'Criar' }}
            </BaseButton>
            <BaseButton type="submit" size="lg" class="flex-1 hidden sm:block" :loading="loading">
              <template #loading>Salvando...</template>
              {{ isEditing ? 'Atualizar' : 'Criar' }}
            </BaseButton>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
