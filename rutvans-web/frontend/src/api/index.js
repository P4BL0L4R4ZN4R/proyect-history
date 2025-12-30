import axios from 'axios';
import router from '../router'; // Asegúrate de importar tu instancia de router

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api/cashier', 
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

// Interceptor para agregar el token a las peticiones
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor para manejar respuestas
api.interceptors.response.use(
  response => response,
  error => {
    if (error.response) {
      // Manejo específico de errores 401 (No autorizado)
      if (error.response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        router.push('/login');
      }
      console.error('Error de API:', error.response.data);
    } else {
      console.error('Error de conexión:', error.message);
    }
    return Promise.reject(error);
  }
);

export default api;