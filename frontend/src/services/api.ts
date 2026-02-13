import axios, { type AxiosInstance, AxiosError, type AxiosRequestConfig } from 'axios'

const API_BASE_URL = (import.meta.env.VITE_API_BASE_URL as string) || ''

const api: AxiosInstance = axios.create({
	baseURL: API_BASE_URL,
	timeout: 10000,
	headers: {
		'Content-Type': 'application/json'
	}
})

// Interceptors: attach auth token and handle auth errors
import router from '@/router'
import { useAuthStore } from '@/stores/authStore'

// Request interceptor: add Authorization header when token is available
api.interceptors.request.use((config) => {
	try {
		const authStore = useAuthStore()
		const token = authStore?.token?.value || localStorage.getItem('token')
		if (token && config && config.headers) {
			config.headers['Authorization'] = `Bearer ${token}`
		}
	} catch (e) {
		// If Pinia isn't ready, fallback to localStorage
		const token = localStorage.getItem('token')
		if (token && config && config.headers) {
			config.headers['Authorization'] = `Bearer ${token}`
		}
	}
	return config
}, (error) => Promise.reject(error))

// Response interceptor: handle 401 -> logout and redirect to login
api.interceptors.response.use(
	(response) => response,
	(error) => {
		const status = error?.response?.status
		if (status === 401) {
			try {
				const authStore = useAuthStore()
				authStore.logout()
			} catch (e) {
				// fallback: clear local token
				localStorage.removeItem('token')
			}

			// redirect to login
			try {
				router.push('/auth/login')
			} catch (e) {
				// as a last resort, use location
				window.location.href = '/auth/login'
			}
		}

		return Promise.reject(error)
	}
)

function parseAxiosError(err: unknown): Error {
	if (axios.isAxiosError(err)) {
		const axiosErr = err as AxiosError<any>
		const serverMessage = axiosErr.response?.data?.message || axiosErr.message
		return new Error(String(serverMessage))
	}

	return err instanceof Error ? err : new Error(String(err))
}

export async function get<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
	try {
		const res = await api.get<T>(url, config)
		return res.data
	} catch (err) {
		throw parseAxiosError(err)
	}
}

export async function post<T = any, D = any>(url: string, data?: D, config?: AxiosRequestConfig): Promise<T> {
	try {
		const res = await api.post<T>(url, data, config)
		return res.data
	} catch (err) {
		throw parseAxiosError(err)
	}
}

export async function put<T = any, D = any>(url: string, data?: D, config?: AxiosRequestConfig): Promise<T> {
	try {
		const res = await api.put<T>(url, data, config)
		return res.data
	} catch (err) {
		throw parseAxiosError(err)
	}
}

export async function del<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
	try {
		const res = await api.delete<T>(url, config)
		return res.data
	} catch (err) {
		throw parseAxiosError(err)
	}
}

export default api

