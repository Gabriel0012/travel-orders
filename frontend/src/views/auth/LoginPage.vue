<template>
  <div class="login-container">
    <div class="login-card">
      <h1>Login</h1>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email">Email:</label>
          <input 
            id="email" 
            v-model="email" 
            type="email" 
            placeholder="seu@email.com"
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
            required
          />
        </div>
        <button type="submit" class="btn-submit">Entrar</button>
      </form>
      <p class="register-link">
        Não tem conta? <router-link to="/auth/register">Cadastre-se</router-link>
      </p>
    </div>
  </div>
  
</template>

<script setup lang="ts">
import { useAuth } from '@/composables'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const password = ref('')

const { login, loading, error } = useAuth()

const handleLogin = async () => {
  try {
    await login({ email: email.value, password: password.value })
    router.push('/dashboard')
  } catch (err) {
    // error is already exposed from composable; nothing else needed here
    console.error('Login failed', err)
  }
}
</script>

<style scoped>
.login-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #0073B7 0%, #667eea  100%);
}

.login-card {
  background: white;
  padding: 40px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
  width: 100%;
  max-width: 400px;
}

.login-card h1 {
  text-align: center;
  margin-bottom: 30px;
  color: #333;
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

.btn-submit:hover {
  transform: translateY(-2px);
}

.register-link {
  text-align: center;
  margin-top: 20px;
  color: #666;
}

.register-link a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.register-link a:hover {
  text-decoration: underline;
}
</style>