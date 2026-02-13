<template>
  <div class="register-container">
    <div class="register-card">
      <h1>Cadastrar</h1>
      
      <div v-if="error" class="error-message">
        {{ error.message }}
      </div>

      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label for="name">Nome:</label>
          <input 
            id="name" 
            v-model="name" 
            type="text" 
            placeholder="Seu nome completo"
            :disabled="isLoading"
            required
          />
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input 
            id="email" 
            v-model="email" 
            type="email" 
            placeholder="seu@email.com"
            :disabled="isLoading"
            required
          />
        </div>
        <div class="form-group">
          <label for="password">Senha:</label>
          <input 
            id="password" 
            v-model="password" 
            type="password" 
            placeholder="Sua senha"
            :disabled="isLoading"
            required
          />
        </div>
        <div class="form-group">
          <label for="confirmPassword">Confirmar Senha:</label>
          <input 
            id="confirmPassword" 
            v-model="confirmPassword" 
            type="password" 
            placeholder="Confirme sua senha"
            :disabled="isLoading"
            required
          />
        </div>
        <button type="submit" class="btn-submit" :disabled="isLoading">
          {{ isLoading ? 'Cadastrando...' : 'Cadastrar' }}
        </button>
      </form>
      <p class="login-link">
        Já tem conta? <router-link to="/auth/login">Faça login</router-link>
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'

// Types
interface RegisterFormData {
  name: string
  email: string
  password: string
  confirmPassword: string
}

interface RegisterError {
  message: string
  field?: keyof RegisterFormData
}

// Router e Store
const router = useRouter()
const authStore = useAuthStore()

// Form State
const name = ref<string>('')
const email = ref<string>('')
const password = ref<string>('')
const confirmPassword = ref<string>('')
const isLoading = ref<boolean>(false)
const error = ref<RegisterError | null>(null)

// Computed properties
const formData = computed<RegisterFormData>(() => ({
  name: name.value,
  email: email.value,
  password: password.value,
  confirmPassword: confirmPassword.value
}))

// Methods
const validateForm = (data: RegisterFormData): RegisterError | null => {
  if (!data.name.trim()) {
    return { message: 'Nome é obrigatório', field: 'name' }
  }

  if (!data.email.trim()) {
    return { message: 'Email é obrigatório', field: 'email' }
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(data.email)) {
    return { message: 'Email inválido', field: 'email' }
  }

  if (!data.password) {
    return { message: 'Senha é obrigatória', field: 'password' }
  }

  if (data.password.length < 6) {
    return { message: 'Senha deve ter no mínimo 6 caracteres', field: 'password' }
  }

  if (data.password !== data.confirmPassword) {
    return { message: 'As senhas não coincidem!', field: 'confirmPassword' }
  }

  return null
}

const handleRegister = async (): Promise<void> => {
  error.value = null

  // Validar formulário
  const validationError = validateForm(formData.value)
  if (validationError) {
    error.value = validationError
    return
  }

  try {
    isLoading.value = true

    // TODO: Aqui você fará a chamada à API
    console.log('Register:', {
      name: name.value,
      email: email.value,
      password: password.value
    })

    // Exemplo de teste (remover após implementar API)
    authStore.setToken('fake-token')
    authStore.setUser({ id: 1, email: email.value, name: name.value })

    await router.push('/dashboard')
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao registrar'
    error.value = { message: `Erro ao registrar: ${errorMessage}` }
    console.error('Register error:', err)
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.register-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #0073B7 0%, #667eea  100%);
}

.register-card {
  background: white;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px;
}

.register-card h1 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
}

.error-message {
  background-color: #fee;
  border: 1px solid #fcc;
  color: #c33;
  padding: 12px;
  border-radius: 5px;
  margin-bottom: 20px;
  font-size: 14px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  color: #555;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
  box-sizing: border-box;
  transition: border-color 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

.btn-submit {
  width: 100%;
  padding: 12px;
  background: linear-gradient(135deg, #0073B7 0%, #667eea 100%);
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  margin-top: 20px;
  color: #666;
}

.login-link a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>
