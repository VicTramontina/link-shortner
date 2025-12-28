import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

// Auth
export const register = (data) => api.post('/register', data)
export const login = (data) => api.post('/login', data)
export const logout = () => api.post('/logout')
export const getUser = () => api.get('/user')

// Links
export const getLinks = (params) => api.get('/links', { params })
export const createLink = (data) => api.post('/links', data)
export const getLink = (id) => api.get(`/links/${id}`)
export const updateLink = (id, data) => api.put(`/links/${id}`, data)
export const deleteLink = (id) => api.delete(`/links/${id}`)
export const getTrashedLinks = () => api.get('/links/trash')
export const restoreLink = (id) => api.post(`/links/${id}/restore`)
export const forceDeleteLink = (id) => api.delete(`/links/${id}/force`)

// Stats
export const getStats = () => api.get('/stats')
export const getDetailedStats = () => api.get('/stats/detailed')

export default api
