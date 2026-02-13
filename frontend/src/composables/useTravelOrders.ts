import { ref } from 'vue'
import api from '@/services/api'

export function useTravelOrders() {
  const loading = ref(false)
  const error = ref<Error | null>(null)

  async function updateStatus(orderId: number | string, status: string) {
    loading.value = true
    error.value = null
    try {
      const res = await api.patch(`/travel-orders/${orderId}/status/${status}`)
      return res.data
    } catch (err) {
      error.value = err instanceof Error ? err : new Error(String(err))
      throw error.value
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    updateStatus
  }
}
