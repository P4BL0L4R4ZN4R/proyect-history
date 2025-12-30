<template>
  <div class="landing-page">
    <NavBar />
    
    <main>
      <section id="inicio" class="hero-section">
        <div class="hero-content">
          <h1>Bienvenido a <span class="brand-name">
          <span class="rut">RUT</span><span class="vans">VANS</span>
        </span></h1>
          <p class="subtitle">La solución inteligente para el transporte de personal</p>
          <button class="cta-button" @click="scrollToContact">Contáctanos</button>
        </div>
      </section>

      <section id="acerca" class="about-section">
        <h2>Acerca de <span class="rut">RUT</span><span class="vans">VANS</span></h2>
        <div class="about-content">
            <p class="intro-text">
            Somos una plataforma inteligente diseñada para transformar el transporte de personal. Integramos tecnología, eficiencia y experiencia para llevar a tu equipo donde lo necesita, de forma segura y puntual.
            </p>
            <div class="features">
                <div class="feature">
                    <i class="fa fa-map"></i>  
                    <h3>Optimización de rutas</h3>
                    <p>Algoritmos avanzados para determinar trayectos más rápidos y rentables.</p>
                </div>
                <div class="feature">
                    <i class="fa-solid fa-eye"></i>
                    <h3>Seguimiento en tiempo real</h3>
                    <p>Monitorea tus unidades y mantén el control desde cualquier dispositivo.</p>
                </div>
                <div class="feature">
                    <i class="fa-solid fa-file-lines"></i>
                    <h3>Reportes detallados</h3>
                    <p>Datos útiles y visuales para optimizar el desempeño de tu flota.</p>
                </div>
            </div>
        </div>
     </section>


<section id="contacto" class="contact-section">
  <h2>Conecta tu flota con <span class="rut">RUT</span><span class="vans">VANS</span></h2>

  <p class="contact-intro">
    Si representas un servicio de transporte interurbano y deseas implementar nuestra solución tecnológica,
    completa el siguiente formulario. Nos pondremos en contacto contigo para comenzar tu transformación digital.
  </p>

  <div class="contact-content">
    <form class="contact-form">
      <div class="form-group">
        <input type="text" placeholder="Nombre completo" required>
      </div>
      <div class="form-group">
        <input type="email" placeholder="Correo electrónico empresarial" required>
      </div>
      <div class="form-group">
        <input type="text" placeholder="Nombre de la empresa o línea de transporte" required>
      </div>
      <div class="form-group">
        <textarea placeholder="¿Qué tipo de solución necesitas? Ej. seguimiento, rutas, reportes…" rows="5" required></textarea>
      </div>
      <button type="submit" class="submit-button">Solicitar contacto</button>
    </form>

    <div class="contact-info">
      <h3>¿Prefieres escribirnos directamente?</h3>
      <p><strong>Email:</strong> info@rutvans.com</p>
      <p><strong>Teléfono:</strong> +52 55 1234 5678</p>
      <p><strong>Ubicación:</strong> Ciudad de México, México</p>
      <p>También puedes agendar una llamada para una demostración personalizada de RUTVANS.</p>
    </div>
  </div>
</section>


      <section id="ayuda" class="help-section">
        <h2>¿Necesitas ayuda?</h2>
        <div class="help-content">
          <p>Nuestro equipo de soporte está disponible para ayudarte con cualquier pregunta o problema que puedas tener.</p>
          <button class="help-button">Centro de ayuda</button>
          <button class="demo-button" @click="visitApp">Visitar RUTVANS</button>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'

export default {
  name: 'LandingView',
  components: {
    NavBar,
    Footer
  },
  methods: {
    scrollToContact() {
      const element = document.getElementById('contacto');
      element.scrollIntoView({ behavior: 'smooth' });
    },
    visitApp() {
      // Aquí iría la lógica para redirigir a la aplicación principal
      alert('Redirigiendo a la aplicación RUTVANS...');
    }
  }
}
</script>

<style scoped>
.landing-page {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

main {
  flex: 1;
}

/* Hero Section */
.hero-section {
  background-image:
  linear-gradient(rgba(0, 0, 0, 0.019), rgba(0, 0, 0, 0.7)), 
   url('@/assets/vanDriver.jpg'); /* Ajusta la ruta si usas Vue, Vite, o Webpack */
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  height: 100vh; /* Altura de pantalla completa opcional */
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: white; /* Asegura legibilidad sobre la imagen */
}


.hero-content {
  max-width: 800px;
  padding: 0 20px;
}

.hero-content h1 {
  font-size: 3.5rem;
  margin-bottom: 1rem;
}

.subtitle {
  font-size: 1.5rem;
  margin-bottom: 2rem;
}

.cta-button {
  background-color: #e67e22;
  color: white;
  border: none;
  padding: 15px 30px;
  font-size: 1.2rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.cta-button:hover {
  background-color: #d35400;
}

/* About Section */
.about-section {
  padding: 100px 20px;
   background-color: #f9f9f9;
  text-align: center;
}

.about-section h2 {
  font-size: 2.8rem;
  margin-bottom: 2rem;
  color: #333;
}

.rut {
  color: #000;
  font-weight: bold;
}

.vans {
  color: #e67e22;
  font-weight: bold;
}

.intro-text {
  font-size: 1.25rem;
  margin-bottom: 3rem;
  color: #555;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
  line-height: 1.7;
}

.features {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 30px;
}

.feature {
  flex: 1;
  min-width: 260px;
  max-width: 300px;
  background: white;
  padding: 35px 25px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
  transition: transform 0.3s ease;
}

.feature:hover {
  transform: translateY(-8px);
}

.feature i {
  font-size: 2.5rem;
  color: #e67e22;
  margin-bottom: 15px;
}

.feature h3 {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 0.75rem;
}

.feature p {
  color: #666;
  font-size: 1rem;
  line-height: 1.5;
}

/* Contact Section */
.contact-section {
  padding: 100px 20px;
  background-color: white;
  text-align: center;
}

.contact-section h2 {
  font-size: 2.8rem;
  margin-bottom: 1.5rem;
  color: #333;
}

.rut {
  color: #000;
  font-weight: bold;
}

.vans {
  color: #e67e22;
  font-weight: bold;
}

.contact-intro {
  font-size: 1.15rem;
  color: #555;
  max-width: 800px;
  margin: 0 auto 3rem auto;
  line-height: 1.7;
}

.contact-content {
  max-width: 1000px;
  margin: 0 auto;
  display: flex;
  gap: 50px;
  flex-wrap: wrap;
  text-align: left;
}

.contact-form {
  flex: 1;
  min-width: 300px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 15px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 1rem;
  background-color: #fefefe;
}

.submit-button {
  background-color: #e67e22;
  color: white;
  border: none;
  padding: 14px 26px;
  font-size: 1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.submit-button:hover {
  background-color: #d35400;
}

.contact-info {
  flex: 1;
  min-width: 300px;
  background-color: #f3f3f3;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.contact-info h3 {
  color: #e67e22;
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.contact-info p {
  margin-bottom: 1rem;
  line-height: 1.6;
  font-size: 1rem;
}


/* Help Section */
.help-section {
  padding: 80px 20px;
  background-color: #333;
  color: white;
  text-align: center;
}

.help-section h2 {
  font-size: 2.5rem;
  margin-bottom: 2rem;
}

.help-content {
  max-width: 800px;
  margin: 0 auto;
}

.help-content p {
  font-size: 1.2rem;
  margin-bottom: 3rem;
  line-height: 1.6;
}

.help-button {
  background-color: transparent;
  color: white;
  border: 2px solid white;
  padding: 15px 30px;
  font-size: 1.1rem;
  border-radius: 5px;
  cursor: pointer;
  margin-right: 15px;
  transition: all 0.3s;
}

.help-button:hover {
  background-color: white;
  color: #333;
}

.demo-button {
  background-color: #e67e22;
  color: white;
  border: none;
  padding: 15px 30px;
  font-size: 1.1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.rut {
  color: #000;
  font-weight: bold; /* ← fuerza la negrita */
}

.vans {
  color: #e67e22;
  font-weight: bold; /* ← fuerza la negrita */
}

.demo-button:hover {
  background-color: #d35400;
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.5rem;
  }
  
  .subtitle {
    font-size: 1.2rem;
  }
  
  .contact-content {
    flex-direction: column;
  }
}
</style>