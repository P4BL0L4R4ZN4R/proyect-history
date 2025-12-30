import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'

// Configurar axios base URL
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const roles = ref([])
  const permissions = ref([])
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!user.value)
  const hasRole = (role) => computed(() => roles.value.includes(role)).value
  const can = (permission) => computed(() => permissions.value.includes(permission)).value

  // Actions
  const setAuth = (authData) => {
    user.value = authData.user
    roles.value = authData.roles || []
    permissions.value = authData.permissions || []
    
    // Guardar en localStorage para persistencia
    localStorage.setItem('user', JSON.stringify(authData.user))
    
    console.log('✅ Auth actualizado - Usuario:', authData.user?.name)
    console.log('✅ Roles:', authData.roles)
    console.log('✅ Permisos:', authData.permissions)
  }

  const clearAuth = () => {
    user.value = null
    roles.value = []
    permissions.value = []
    
    // Limpiar localStorage
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    
    // Limpiar headers de axios
    delete apiClient.defaults.headers.common['Authorization']
    
    console.log('🔒 Auth limpiado - Sesión cerrada')
  }

  const fetchUserRoles = async () => {
    try {
      isLoading.value = true
      const token = localStorage.getItem('token')
      
      if (!token) {
        console.log('❌ No hay token - limpiando auth')
        clearAuth()
        return null
      }

      console.log('🔄 Cargando roles del usuario...')
      
      // Configurar el token en los headers
      apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`
      
      const response = await apiClient.get('/user/roles')
      
      console.log('✅ Respuesta de roles:', response.data)

      setAuth(response.data)
      return response.data
    } catch (error) {
      console.error('❌ Error fetching user roles:', error)
      if (error.response) {
        console.error('📊 Error response:', error.response.data)
        console.error('🔢 Status:', error.response.status)
      }
      clearAuth()
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const login = async (credentials) => {
    try {
      isLoading.value = true
      console.log('🔐 Iniciando proceso de login...')
      
      // 1. Hacer login
      const response = await apiClient.post('/auth/login', credentials)
      const { user: userData, token, success } = response.data

      if (!success) {
        throw new Error('Login fallido')
      }

      console.log('✅ Login exitoso - Guardando datos...')
      
      // 2. Guardar en localStorage
      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(userData))
      
      // 3. Configurar headers para futuras requests
      apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`
      
      // 4. Cargar roles y permisos
      await fetchUserRoles()
      
      console.log('🎉 Login completado exitosamente')
      return response.data
    } catch (error) {
      console.error('❌ Error en login:', error)
      clearAuth()
      throw error
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    console.log('🚪 Cerrando sesión...')
    clearAuth()
  }

  const checkPermission = async (permission) => {
    try {
      const token = localStorage.getItem('token')
      if (!token) return false

      const response = await apiClient.post('/user/can', {
        permission: permission
      })

      return response.data.can
    } catch (error) {
      console.error('Error checking permission:', error)
      return false
    }
  }

  const checkRole = async (role) => {
    try {
      const token = localStorage.getItem('token')
      if (!token) return false

      const response = await apiClient.post('/user/has-role', {
        role: role
      })

      return response.data.has_role
    } catch (error) {
      console.error('Error checking role:', error)
      return false
    }
  }

  // Inicializar desde localStorage si existe
  const initializeFromStorage = () => {
    const storedUser = localStorage.getItem('user')
    const token = localStorage.getItem('token')
    
    if (storedUser && token) {
      try {
        console.log('🔄 Inicializando auth desde localStorage...')
        user.value = JSON.parse(storedUser)
        // Configurar el token en los headers
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${token}`
        // Cargar roles actualizados
        fetchUserRoles()
      } catch (error) {
        console.error('Error initializing from storage:', error)
        clearAuth()
      }
    } else {
      console.log('📭 No hay datos de auth en localStorage')
    }
  }

  // Verificar estado actual de la autenticación
  const getAuthStatus = () => {
    return {
      isAuthenticated: isAuthenticated.value,
      user: user.value,
      roles: roles.value,
      permissions: permissions.value,
      hasToken: !!localStorage.getItem('token')
    }
  }

  return {
    // State
    user,
    roles,
    permissions,
    isLoading,
    
    // Getters
    isAuthenticated,
    hasRole,
    can,
    
    // Actions
    setAuth,
    clearAuth,
    fetchUserRoles,
    login,
    logout,
    checkPermission,
    checkRole,
    initializeFromStorage,
    getAuthStatus
  }
})