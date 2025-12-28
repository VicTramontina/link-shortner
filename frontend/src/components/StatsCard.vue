<script setup>
import { IconLink, IconEye, IconClick, IconCtr } from '../assets/icons'

defineProps({
  icon: String,
  value: [String, Number],
  label: String,
})
</script>

<template>
  <div class="flex items-center gap-2 sm:gap-3">
    <div class="text-gray-500 flex-shrink-0">
      <IconLink v-if="icon === 'link'" class="w-5 h-5 sm:w-6 sm:h-6" />
      <IconEye v-else-if="icon === 'eye'" class="w-5 h-5 sm:w-6 sm:h-6" />
      <IconClick v-else-if="icon === 'click'" class="w-5 h-5 sm:w-6 sm:h-6" />
      <IconCtr v-else-if="icon === 'ctr'" class="w-5 h-5 sm:w-6 sm:h-6" />
    </div>
    <div class="min-w-0">
      <p class="text-lg sm:text-2xl font-bold text-gray-800 truncate">{{ value }}</p>
      <p class="text-xs sm:text-sm text-gray-500 truncate">{{ label }}</p>
    </div>
  </div>
</template>
