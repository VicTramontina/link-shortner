<script setup>
import BaseButton from './BaseButton.vue'

defineProps({
  show: Boolean,
  title: String,
  message: String,
  confirmText: {
    type: String,
    default: 'Confirmar',
  },
  cancelText: {
    type: String,
    default: 'Cancelar',
  },
  danger: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['confirm', 'cancel'])
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
        @click="emit('cancel')"
      ></div>

      <!-- Modal -->
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">
          {{ title }}
        </h2>
        <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
          {{ message }}
        </p>

        <div class="flex gap-2 sm:gap-3">
          <BaseButton variant="secondary" size="md" class="flex-1 sm:hidden" @click="emit('cancel')">
            {{ cancelText }}
          </BaseButton>
          <BaseButton variant="secondary" size="lg" class="flex-1 hidden sm:block" @click="emit('cancel')">
            {{ cancelText }}
          </BaseButton>
          <BaseButton
            :variant="danger ? 'danger' : 'primary'"
            size="md"
            class="flex-1 sm:hidden"
            @click="emit('confirm')"
          >
            {{ confirmText }}
          </BaseButton>
          <BaseButton
            :variant="danger ? 'danger' : 'primary'"
            size="lg"
            class="flex-1 hidden sm:block"
            @click="emit('confirm')"
          >
            {{ confirmText }}
          </BaseButton>
        </div>
      </div>
    </div>
  </Teleport>
</template>
