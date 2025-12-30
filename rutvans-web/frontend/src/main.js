
import './assets/main.css'
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap-icons/font/bootstrap-icons.css'



import { createApp } from 'vue'
import { createPinia } from 'pinia'
import axios from 'axios' // Importar axios

import App from './App.vue'
import router from './router'


const app = createApp(App)

// Configuración global de axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api' // Reemplaza con tu URL base
// O si usas variables de entorno:
// axios.defaults.baseURL = process.env.VUE_APP_API_URL

// Interceptor para agregar el token a las peticiones
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config
}, error => {
  return Promise.reject(error)
})

// Interceptor para manejar respuestas no autorizadas
axios.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response && error.response.status === 401) {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
  return Promise.reject(error)
})

// Hacer axios disponible globalmente (opcional)
app.config.globalProperties.$axios = axios

app.use(createPinia())
app.use(router)

app.mount('#app')