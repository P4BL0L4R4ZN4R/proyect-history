<template>
  <nav class="navbar">
    <div class="navbar-container">
      <div class="logo-container">
        <img src="../assets/LogoRutvans.png" alt="RUTVANS Logo" class="logo" />
        <span class="brand-name">
          <span class="rut">RUT</span><span class="vans">VANS</span>
        </span>
      </div>
      <div class="nav-links">
        <a href="#inicio" class="nav-link">Inicio</a>
        <a href="#acerca" class="nav-link">Acerca de</a>
        <a href="#contacto" class="nav-link">Contáctanos</a>
        <a href="#ayuda" class="nav-link">Ayuda</a>
        <button class="nav-button" @click="visitApp">Visitar RUTVANS</button>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'NavBar',
  mounted() {
    window.addEventListener('scroll', this.highlightLink);
  },
  methods: {
    visitApp() {
      alert('Redirigiendo a la aplicación RUTVANS...');
    },
    highlightLink() {
      const sections = ['inicio', 'acerca', 'contacto', 'ayuda'];
      sections.forEach(id => {
        const section = document.getElementById(id);
        const link = document.querySelector(`a[href="#${id}"]`);
        if (section && link) {
          const top = section.offsetTop - 100;
          const bottom = top + section.offsetHeight;
          const scrollY = window.scrollY;
          if (scrollY >= top && scrollY <= bottom) {
            link.classList.add('active-link');
          } else {
            link.classList.remove('active-link');
          }
        }
      });
    }
  }
}
</script>

<style scoped>
.navbar {
  background-color: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1000;
}

.navbar-container {
  display: flex;
  justify-content: space-between; /* ← separa logo a la izquierda y links a la derecha */
  align-items: center;
  max-width: 1500px;
  margin: 0 auto;
  padding: 15px 20px;
}


.logo-container {
  display: flex;
  align-items: center;
}

.logo {
  height: 40px;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: bold; /* ← fuerza la negrita */
  font-family: Arial, sans-serif; /* o una fuente que soporte bold bien */
  margin-left: 10px;
  display: flex;
  align-items: center;
}


.rut {
  color: #000;
  font-weight: bold; /* ← fuerza la negrita */
}

.vans {
  color: #e67e22;
  font-weight: bold; /* ← fuerza la negrita */
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-link {
  color: #333;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover {
  color: #e67e22;
  background-color: transparent !important;
}

.active-link {
  color: #e67e22;
  font-weight: 600;
}

.nav-button {
  background-color: #e67e22;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.nav-button:hover {
  background-color: #d35400;
}

@media (max-width: 768px) {
  .navbar-container {
    flex-direction: column;
    padding: 10px;
  }

  .nav-links {
    margin-top: 15px;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
  }
}
</style>
