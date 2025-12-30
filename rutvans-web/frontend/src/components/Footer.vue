<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-section">
        <img src="../assets/LogoRutvans.png" alt="RUTVANS Logo" class="footer-logo">
        <p>La solución inteligente para el transporte de personal.</p>
      </div>
      <div class="footer-section">
        <h3>Enlaces rápidos</h3>
        <ul>
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#acerca">Acerca de</a></li>
          <li><a href="#contacto">Contáctanos</a></li>
          <li><a href="#ayuda">Ayuda</a></li>
        </ul>
      </div>
      <div class="footer-section">
        <h3>Contacto</h3>
        <ul>
          <li>info@rutvans.com</li>
          <li>+1 234 567 890</li>
          <li>Calle Ficticia 123, Ciudad, País</li>
        </ul>
      </div>
      <div class="footer-section">
        <h3>Síguenos</h3>
        <div class="social-icons">
          <a href="#" class="social-icon">FB</a>
          <a href="#" class="social-icon">TW</a>
          <a href="#" class="social-icon">IG</a>
          <a href="#" class="social-icon">LI</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2023 RUTVANS. Todos los derechos reservados.</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer'
}
</script>

<style scoped>
.footer {
  background-color: #131313;
  color: #f5f5f5;
  padding: 60px 20px 0;
  font-family: Arial, sans-serif;
}

.footer-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 40px;
  max-width: 1200px;
  margin: 0 auto;
  padding-bottom: 40px;
  border-bottom: 1px solid #2a2a2a;
}

.footer-section {
  flex: 1;
  min-width: 220px;
}

.footer-logo {
  height: 45px;
  margin-bottom: 15px;
}

.footer-section p {
  font-size: 0.95rem;
  line-height: 1.6;
  color: #ccc;
}

.footer-section h3 {
  color: #e67e22;
  margin-bottom: 20px;
  font-size: 1.1rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.footer-section ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-section ul li {
  margin-bottom: 10px;
  font-size: 0.95rem;
}

.footer-section ul li a {
  color: #ccc;
  text-decoration: none;
  transition: color 0.3s;
}

.footer-section ul li a:hover {
  color: #e67e22;
  background-color: transparent !important;
}

.social-icons {
  display: flex;
  gap: 15px;
  margin-top: 10px;
}

.social-icon {
  display: inline-flex;
  width: 38px;
  height: 38px;
  background-color: #333;
  color: #f0f0f0;
  border-radius: 50%;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  text-decoration: none;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

.social-icon:hover {
  background-color: #e67e22;
  transform: scale(1.05);
}

.footer-bottom {
  background-color: #111;
  padding: 20px 0;
  text-align: center;
}

.footer-bottom p {
  margin: 0;
  color: #999;
  font-size: 0.85rem;
}

@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .footer-section {
    max-width: 100%;
  }

  .social-icons {
    justify-content: center;
  }
}

</style>