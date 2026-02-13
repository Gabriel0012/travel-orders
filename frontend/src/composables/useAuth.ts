import { ref } from 'vue'
import { post, get } from '@/services/api'
import { useAuthStore } from '@/stores/authStore'

export function useAuth() {
  const loading = ref(false)
  const error = ref<Error | null>(null)
  const authStore = useAuthStore()

  async function login(credentials: { email: string; password: string }) {
    loading.value = true
    error.value = null
    try {
      const res = await post('/login', credentials)
      // server returns { access_token }
      if (res && res.access_token) {
        authStore.setToken(res.access_token)
      }

      // fetch user profile
      const user = await get('/me')
      if (user) authStore.setUser(user)

      return user
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      throw error.value
    } finally {
      loading.value = false
    }
  }

  async function me() {
    try {
      const user = await get('/me')
      if (user) authStore.setUser(user)
      return user
    } catch (err) {
      throw err
    }
  }

  async function logout() {
    try {
      await post('/logout')
    } finally {
      authStore.logout()
    }
  }

  return {
    loading,
    error,
    login,
    me,
    logout,
    isAuthenticated: authStore.isAuthenticated,
    user: authStore.user
  }
}
