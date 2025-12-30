```vue
<template>
  <footer class="app-footer">
    <div class="footer-content">
      <div class="footer-section">
        <h3>RUTVANS</h3>
        <p>Tu solución de transporte confiable</p>
      </div>
      <div class="footer-section">
        <h3>Contacto</h3>
        <p><i class="fas fa-envelope"></i> info@rutvans.com</p>
        <p><i class="fas fa-phone"></i> +52 55 1234 5678</p>
      </div>
      <div class="footer-section">
        <h3>Redes Sociales</h3>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-whatsapp"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 RUTVANS. Todos los derechos reservados.</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'MainFooter',
};
</script>

<style scoped>
.app-footer {
  background: linear-gradient(to right, #2a2929, #0d0d0d);
  color: var(--text-light);
  padding: 2.5rem 0 0;
}

.footer-content {
  display: flex;
  justify-content: space-around;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 2rem;
}

.footer-section {
  flex: 1;
  margin: 0 1.5rem;
}

.footer-section h3 {
  font-size: 1.3rem;
  margin-bottom: 1.2rem;
  color: var(--text-light);
}

.footer-section p {
  margin: 0.8rem 0;
  display: flex;
  align-items: center;
  gap: 8px;
}

.social-icons {
  display: flex;
  gap: 15px;
}

.social-icons a {
  color: var(--text-light);
  font-size: 1.5rem;
  transition: all 0.3s ease-in-out;
}

.social-icons a:hover {
  color: var(--hover-color);
  transform: translateY(-3px);
}

.footer-bottom {
  text-align: center;
  padding: 1.2rem;
  margin-top: 2.5rem;
  background-color: rgba(0, 0, 0, 0.3);
}

.footer-bottom p {
  margin: 0;
  font-size: 0.95rem;
}

@media (max-width: 768px) {
  .footer-content {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }
  .footer-section {
    margin: 0.5rem 0;
  }
}
</style>
```