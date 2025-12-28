<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { register } from '../services/api'
import BaseButton from '../components/BaseButton.vue'

const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const error = ref('')
const errors = ref({})
const loading = ref(false)

const handleSubmit = async () => {
  error.value = ''
  errors.value = {}
  loading.value = true

  try {
    const response = await register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    localStorage.setItem('token', response.data.token)
    localStorage.setItem('user', JSON.stringify(response.data.user))
    router.push('/')
  } catch (err) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors
    } else {
      error.value = err.response?.data?.message || 'Registration failed'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-custom flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl shadow-xl p-5 sm:p-8 w-full max-w-md">
      <div class="text-center mb-5 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">DDSV</h1>
        <p class="text-gray-500 mt-1 sm:mt-2 text-sm sm:text-base">Crie sua conta</p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-3 sm:space-y-5">
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-3 sm:px-4 py-2 sm:py-3 rounded text-sm">
          {{ error }}
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Nome</label>
          <input
            v-model="name"
            type="text"
            required
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            placeholder="Digite seu nome"
          />
          <p v-if="errors.name" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.name[0] }}</p>
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
          <p v-if="errors.email" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.email[0] }}</p>
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
          <p v-if="errors.password" class="text-red-500 text-xs sm:text-sm mt-1">{{ errors.password[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Confirmar Senha</label>
          <input
            v-model="passwordConfirmation"
            type="password"
            required
            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition text-sm sm:text-base"
            placeholder="Confirme sua senha"
          />
        </div>

        <BaseButton type="submit" size="lg" block :loading="loading">
          <template #loading>Criando conta...</template>
          Cadastrar
        </BaseButton>
      </form>

      <p class="text-center mt-4 sm:mt-6 text-gray-600 text-sm sm:text-base">
        Já tem uma conta?
        <router-link to="/login" class="text-primary-500 font-semibold hover:underline">
          Entrar
        </router-link>
      </p>
    </div>
  </div>
</template>
