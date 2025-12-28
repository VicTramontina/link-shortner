<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { logout as logoutApi } from '../services/api'
import { IconSearch, IconPlus, IconBell, IconChevronDown } from '../assets/icons'

const props = defineProps({
  searchQuery: String,
})

const emit = defineEmits(['search', 'add'])

const router = useRouter()
const showUserMenu = ref(false)
const user = computed(() => JSON.parse(localStorage.getItem('user') || '{}'))

const handleSearch = (e) => {
  emit('search', e.target.value)
}

const handleLogout = async () => {
  try {
    await logoutApi()
  } catch (e) {
    // Ignore errors
  }
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  router.push('/login')
}
</script>

<template>
  <header class="bg-white border-b border-gray-100">
    <!-- Desktop Header -->
    <div class="hidden sm:flex items-center justify-between py-4 px-4 sm:px-6">
      <!-- Logo -->
      <router-link to="/" class="text-xl sm:text-2xl font-bold text-gray-800 flex-shrink-0">
        DDSV
      </router-link>

      <!-- Search Bar -->
      <div class="flex-1 max-w-xl mx-4 lg:mx-8 flex items-center gap-2 sm:gap-3">
        <IconSearch class="text-gray-400 flex-shrink-0" />
        <input
          type="text"
          :value="searchQuery"
          @input="handleSearch"
          placeholder="Buscar ou colar URL"
          class="flex-1 min-w-0 px-3 sm:px-4 py-2 sm:py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
        />
        <!-- Add Button -->
        <button
          @click="emit('add')"
          class="text-gray-500 hover:text-gray-700 transition flex-shrink-0"
        >
          <IconPlus />
        </button>
      </div>

      <!-- User Menu -->
      <div class="relative flex-shrink-0">
        <button
          @click="showUserMenu = !showUserMenu"
          class="flex items-center gap-2"
        >
          <div class="w-9 h-9 sm:w-10 sm:h-10 bg-primary-500 text-white rounded-full flex items-center justify-center font-semibold text-sm sm:text-base">
            {{ user.name?.charAt(0).toUpperCase() || 'U' }}
          </div>
          <IconChevronDown class="text-gray-400 hidden sm:block" />
        </button>

        <!-- Dropdown Menu -->
        <div
          v-if="showUserMenu"
          class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50"
        >
          <div class="px-4 py-2 border-b border-gray-100">
            <p class="font-semibold text-gray-800">{{ user.name }}</p>
            <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
          </div>
          <button
            @click="handleLogout"
            class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50 transition"
          >
            Sair
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Header -->
    <div class="sm:hidden">
      <!-- Top row: Logo and User -->
      <div class="flex items-center justify-between py-3 px-4">
        <router-link to="/" class="text-xl font-bold text-gray-800">
          DDSV
        </router-link>

        <div class="relative">
          <button
            @click="showUserMenu = !showUserMenu"
            class="flex items-center"
          >
            <div class="w-9 h-9 bg-primary-500 text-white rounded-full flex items-center justify-center font-semibold text-sm">
              {{ user.name?.charAt(0).toUpperCase() || 'U' }}
            </div>
          </button>

          <!-- Dropdown Menu -->
          <div
            v-if="showUserMenu"
            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50"
          >
            <div class="px-4 py-2 border-b border-gray-100">
              <p class="font-semibold text-gray-800">{{ user.name }}</p>
              <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
            </div>
            <button
              @click="handleLogout"
              class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50 transition"
            >
              Sair
            </button>
          </div>
        </div>
      </div>

      <!-- Bottom row: Search -->
      <div class="flex items-center gap-2 px-4 pb-3">
        <IconSearch class="text-gray-400 flex-shrink-0 w-5 h-5" />
        <input
          type="text"
          :value="searchQuery"
          @input="handleSearch"
          placeholder="Buscar ou colar URL"
          class="flex-1 min-w-0 px-3 py-2 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm"
        />
        <button
          @click="emit('add')"
          class="text-gray-500 hover:text-gray-700 transition flex-shrink-0 p-1"
        >
          <IconPlus />
        </button>
      </div>
    </div>
  </header>

  <!-- Backdrop for user menu -->
  <div
    v-if="showUserMenu"
    class="fixed inset-0 z-40"
    @click="showUserMenu = false"
  ></div>
</template>
