<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login } from '../services/api'
import BaseButton from '../components/BaseButton.vue'

const router = useRouter()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleSubmit = async () => {
  error.value = ''
  loading.value = true

  try {
    const response = await login({ email: email.value, password: password.value })
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-custom flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-xl p-5 sm:p-8 w-full max-w-md">
      <div class="text-center mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">DDSV</h1>
        <p class="text-gray-500 mt-1 sm:mt-2 text-sm sm:text-base">Encurtador de Links</p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-4 sm:space-y-6">
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded text-sm">
          {{ error }}
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">E-mail</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            placeholder="Digite seu e-mail"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Senha</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            placeholder="Digite sua senha"
          />
        </div>

        <BaseButton type="submit" size="lg" block :loading="loading">
          <template #loading>Entrando...</template>
          Entrar
        </BaseButton>
      </form>

      <p class="text-center mt-4 sm:mt-6 text-gray-600 text-sm sm:text-base">
        Não tem uma conta?
        <router-link to="/register" class="text-primary-500 font-semibold hover:underline">
          Cadastre-se
        </router-link>
      </p>
    </div>
  </div>
</template>
