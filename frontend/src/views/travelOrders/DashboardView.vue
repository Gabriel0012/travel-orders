<template>
  <div class="dashboard-container">
    <header class="dashboard-header">
      <h1 style="color: var(--color-heading)">Pedidos de Viagem</h1>
      <div class="header-actions">
        <button class="btn-create" @click="openCreateModal" :disabled="isLoading">
          + Novo Pedido
        </button>
        <button class="btn-logout" @click="handleLogout" :disabled="isLoading">
          {{ isLoading ? 'Logout...' : 'Logout' }}
        </button>
      </div>
    </header>

    <div v-if="error" class="error-banner" :class="error.type">
      <span>{{ error.message }}</span>
      <button class="btn-close" @click="clearError">×</button>
    </div>

    <main class="dashboard-content">
      <div class="orders-table">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Destino</th>
              <th>Data de Saída</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="!hasOrders">
              <td colspan="5" class="empty-message">Nenhum pedido de viagem encontrado</td>
            </tr>
            <tr v-for="order in orders" :key="order.id">
              <td>{{ order.id }}</td>
              <td>{{ order.destination }}</td>
              <td>{{ formatDate(order.departure_date) }}</td>
              <td><span class="status" :class="order.status">{{ order.status }}</span></td>
              <td>
                <button class="btn-action" @click="openStatusModal(order)" :disabled="isLoading">
                  Alterar Status
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </main>

    <!-- Create Order Modal -->
    <CreateOrderModal 
      v-if="showCreateModal"
      @close="closeCreateModal"
      @save="handleCreateOrder"
    />

    <!-- Update Status Modal -->
    <UpdateStatusModal 
      v-if="showStatusModal"
      :order="selectedOrder"
      @close="closeStatusModal"
      @save="handleUpdateStatus"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'
import CreateOrderModal from './modals/CreateOrderModal.vue'
import UpdateStatusModal from './modals/UpdateStatusModal.vue'

// Types
type OrderStatus = 'pending' | 'approved' | 'rejected' | 'completed'

interface TravelOrder {
  id: number
  destination: string
  departure_date: string
  status: OrderStatus
}

interface CreateOrderData {
  destination: string
  departure_date: string
}

interface DashboardError {
  message: string
  type: 'error' | 'warning' | 'info'
}

// Router & Store
const router = useRouter()
const authStore = useAuthStore()

// State
const orders = ref<TravelOrder[]>([
  {
    id: 1,
    destination: 'São Paulo',
    departure_date: '2026-02-15',
    status: 'pending'
  },
  {
    id: 2,
    destination: 'Rio de Janeiro',
    departure_date: '2026-02-20',
    status: 'approved'
  }
])

const showCreateModal = ref<boolean>(false)
const showStatusModal = ref<boolean>(false)
const selectedOrder = ref<TravelOrder | null>(null)
const isLoading = ref<boolean>(false)
const error = ref<DashboardError | null>(null)

// Computed
const hasOrders = computed<boolean>(() => orders.value.length > 0)

const ordersByStatus = computed<Record<OrderStatus, TravelOrder[]>>(() => {
  const grouped: Record<OrderStatus, TravelOrder[]> = {
    pending: [],
    approved: [],
    rejected: [],
    completed: []
  }

  orders.value.forEach(order => {
    grouped[order.status].push(order)
  })

  return grouped
})

// Methods
const formatDate = (date: string): string => {
  try {
    return new Date(date).toLocaleDateString('pt-BR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
  } catch {
    return 'Data inválida'
  }
}

const openCreateModal = (): void => {
  showCreateModal.value = true
  error.value = null
}

const closeCreateModal = (): void => {
  showCreateModal.value = false
}

const handleCreateOrder = (orderData: CreateOrderData): void => {
  try {
    if (!orderData.destination || !orderData.departure_date) {
      throw new Error('Dados incompletos para criar pedido')
    }

    const newOrder: TravelOrder = {
      id: Math.max(...orders.value.map(o => o.id), 0) + 1,
      ...orderData,
      status: 'pending'
    }

    orders.value.push(newOrder)
    closeCreateModal()
    error.value = null
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao criar pedido'
    error.value = {
      message: errorMessage,
      type: 'error'
    }
    console.error('Error creating order:', err)
  }
}

const openStatusModal = (order: TravelOrder): void => {
  selectedOrder.value = order
  showStatusModal.value = true
  error.value = null
}

const closeStatusModal = (): void => {
  showStatusModal.value = false
  selectedOrder.value = null
}

const handleUpdateStatus = (newStatus: OrderStatus): void => {
  try {
    if (!selectedOrder.value) {
      throw new Error('Nenhum pedido selecionado')
    }

    const order = orders.value.find(o => o.id === selectedOrder.value!.id)
    if (!order) {
      throw new Error('Pedido não encontrado')
    }

    order.status = newStatus
    closeStatusModal()
    error.value = null
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao atualizar status'
    error.value = {
      message: errorMessage,
      type: 'error'
    }
    console.error('Error updating status:', err)
  }
}

const handleLogout = async (): Promise<void> => {
  try {
    isLoading.value = true
    authStore.logout()
    await router.push('/auth/login')
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Erro ao fazer logout'
    error.value = {
      message: errorMessage,
      type: 'error'
    }
    console.error('Logout error:', err)
  } finally {
    isLoading.value = false
  }
}

const clearError = (): void => {
  error.value = null
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background: var(--color-background-soft);
}

.dashboard-header {
  background: var(--color-background);
  padding: 20px 30px;
  box-shadow: 0 2px 4px var(--box-shadow-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dashboard-header h1 {
  margin: 0;
  color: #333;
  font-size: 28px;
}

.header-actions {
  display: flex;
  gap: 10px;
}

.error-banner {
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
}

.error-banner.error {
  background: #fee;
  border-bottom: 1px solid #fcc;
  color: #c33;
}

.error-banner.warning {
  background: #fff3cd;
  border-bottom: 1px solid #ffeaa7;
  color: #856404;
}

.error-banner.info {
  background: #d1ecf1;
  border-bottom: 1px solid #bee5eb;
  color: #0c5460;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  opacity: 0.7;
  transition: opacity 0.2s;
  padding: 0;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close:hover {
  opacity: 1;
}

.btn-create {
  padding: 10px 20px;
  background: var(--bg-primary-button);
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: transform 0.2s;
}

.btn-create:hover:not(:disabled) {
  transform: translateY(-2px);
}

.btn-create:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-logout {
  padding: 10px 20px;
  background: var(--color-background-soft);
  color: var(--color-text);
  border: 1px solid var(--color-border);
  border-radius: 5px;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.btn-logout:hover:not(:disabled) {
  background: var(--color-background-mute);
}

.btn-logout:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.dashboard-content {
  padding: 30px;
}

.orders-table {
  background: var(--color-background);
  border-radius: 8px;
  box-shadow: 0 2px 8px var(--box-shadow-color);
  overflow: hidden;
}

.orders-table table {
  width: 100%;
  border-collapse: collapse;
}

.orders-table th {
  background: var(--color-background-mute);
  padding: 15px;
  text-align: left;
  font-weight: 600;
  color: var(--color-text);
  border-bottom: 1px solid var(--color-border);
}

.orders-table td {
  padding: 15px;
  border-bottom: 1px solid var(--color-border);
}

.orders-table tr:hover {
  background: var(--color-background-mute);
}

.empty-message {
  text-align: center;
  color: #999;
  padding: 40px 15px !important;
}

.status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
}

.status.pending {
  background: #fff3cd;
  color: #856404;
}

.status.approved {
  background: #d4edda;
  color: #155724;
}

.status.rejected {
  background: #f8d7da;
  color: #721c24;
}

.status.completed {
  background: #e2e3e5;
  color: #383d41;
}

.btn-action {
  padding: 6px 12px;
  background: var(--bg-primary-button);
  color: var(--color-heading);
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  transition: background 0.2s;
}

.btn-action:hover:not(:disabled) {
  background: var(--bg-primary-button-hover);
}

.btn-action:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>
