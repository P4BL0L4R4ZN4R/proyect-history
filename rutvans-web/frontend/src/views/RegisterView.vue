<template>
  <div class="login-container">
    <div class="login-card">
      <div class="logo-container">
        <img src="../assets/LogoRutvans.png" alt="RUTVANS Logo" class="logo">
        <h1 class="title">Crear Cuenta</h1>
      </div>
      <form @submit.prevent="handleRegister" class="login-form">
        <div class="input-group">
          <label for="name">Nombre de la cuenta</label>
          <div class="input-with-icon">
            <input type="text" id="name" v-model="name" required placeholder="Nombre de usuario" :disabled="loading" @input="clearError" autocomplete="name">
            <i class="bi bi-person-fill input-icon"></i>
          </div>
        </div>
        <div class="input-group">
          <label for="email">Correo Electrónico</label>
          <div class="input-with-icon">
            <input type="email" id="email" v-model="email" required placeholder="usuario@rutvans.com" :disabled="loading" @input="clearError" autocomplete="email">
            <i class="bi bi-envelope-fill input-icon"></i>
          </div>
        </div>
        <div class="input-group">
          <label for="password">Contraseña</label>
          <div class="input-with-icon">
            <input :type="showPassword ? 'text' : 'password'" id="password" v-model="password" required placeholder="Contraseña" :disabled="loading" @input="clearError" autocomplete="new-password">
            <i class="bi bi-lock-fill input-icon"></i>
            <button type="button" class="show-password" @click="togglePasswordVisibility" aria-label="Mostrar contraseña">
              <i :class="showPassword ? 'bi bi-eye-slash-fill' : 'bi bi-eye-fill'"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="login-btn" :disabled="loading" :class="{'loading': loading}">
          <span v-if="!loading">
            <i class="bi bi-person-plus-fill btn-icon"></i> Registrarse
          </span>
          <span v-else>
            <i class="bi bi-hourglass-split"></i> Procesando...
          </span>
        </button>
        <transition name="fade">
          <div v-if="errorMessage" class="error-message">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ errorMessage }}
          </div>
        </transition>
      </form>
      <p class="login-link">
        ¿Ya tienes una cuenta? 
        <router-link to="/login" class="login-text">Inicia sesión.</router-link>
      </p>
    </div>
    <div class="footer">
      <p>© 2025 RUTVANS. Todos los derechos reservados.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'RegisterView',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      errorMessage: '',
      loading: false,
      showPassword: false
    };
  },
  methods: {
    clearError() {
      this.errorMessage = '';
    },
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword;
    },
    async handleRegister() {
      if (!this.name || !this.email || !this.password) {
        this.errorMessage = 'Por favor completa todos los campos';
        return;
      }

      this.loading = true;
      this.errorMessage = '';

      try {
        const response = await axios.post("http://localhost:8000/api/auth/register", {
          name: this.name,
          email: this.email,
          password: this.password
        });

        if (response.data.success) {
          localStorage.setItem("user", JSON.stringify(response.data.user));
          localStorage.setItem("token", response.data.token);
          this.$router.push("/");
        } else {
          this.errorMessage = response.data.message || "Error al registrar la cuenta";
        }
      } catch (error) {
        if (error.response) {
          switch (error.response.status) {
            case 400:
              this.errorMessage = "El correo ya está registrado o los datos son inválidos";
              break;
            case 500:
              this.errorMessage = "Error en el servidor. Por favor intenta más tarde";
              break;
            default:
              this.errorMessage = "Error al conectar con el servidor";
          }
        } else {
          this.errorMessage = "No se pudo conectar con el servidor";
        }
        console.error("Register error:", error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
:root {
  --primary-color: #e67e22;
  --primary-hover: #d35400;
  --error-color: #e74c3c;
  --text-color: #333;
  --light-text: #777;
  --border-color: #ddd;
  --bg-color: #f5f5f5;
  --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.login-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: var(--bg-color);
  padding: 2rem;
}

.login-card {
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
  padding: 3rem;
  border-radius: 12px;
  box-shadow: var(--card-shadow);
  width: 100%;
  max-width: 450px;
  position: relative;
  overflow: hidden;
}

.logo-container {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo {
  height: 80px;
  margin-bottom: 1.5rem;
}

.title {
  font-size: 1.8rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.8rem;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.input-with-icon {
  position: relative;
}

.input-group label {
  font-weight: 600;
  color: var(--text-color);
  font-size: 0.95rem;
}

.input-group input {
  padding: 14px 15px 14px 40px;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s;
  background-color: #f9f9f9;
  width: 100%;
  box-sizing: border-box;
}

.input-group input:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.1);
  background-color: white;
}

.input-icon {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--light-text);
  font-size: 1rem;
}

.show-password {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--light-text);
  cursor: pointer;
  font-size: 1rem;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.show-password:hover {
  color: var(--primary-color);
}

.login-btn {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 0.5rem;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.login-btn:hover:not(:disabled) {
  background-color: var(--primary-hover);
  transform: translateY(-2px);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-icon {
  font-size: 1.2rem;
}

.error-message {
  color: var(--error-color);
  text-align: center;
  margin-top: 1rem;
  font-weight: 500;
  padding: 12px;
  border-radius: 8px;
  background-color: rgba(231, 76, 60, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter, .fade-leave-to {
  opacity: 0;
}

.login-link {
  text-align: center;
  margin-top: 1.5rem;
  color: var(--light-text);
  font-size: 0.95rem;
}

.login-text {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s;
}

.login-text:hover {
  color: var(--primary-hover);
  text-decoration: underline;

}

.footer {
  margin-top: 2.5rem;
  text-align: center;
  color: var(--light-text);
  font-size: 0.85rem;
}

@media (max-width: 576px) {
  .login-card {
    padding: 2rem 1.5rem;
  }
  .login-container {
    padding: 1rem;
  }
  .title {
    font-size: 1.5rem;
  }
}
</style>