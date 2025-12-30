<div class="container" style="max-width: 700px; margin: 60px auto; font-family: Arial, sans-serif;">

  <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
    <i class="fas fa-exclamation-triangle" style="font-size: 36px; color: #b22222;"></i>
    <h1 style="color: #b22222; font-weight: 700; margin: 0; font-size: 1.8rem;">Eliminar Cuenta</h1>
  </div>

  <p style="color: #222; font-size: 1rem; margin-bottom: 25px;">
    Esta acción eliminará tu cuenta de forma <strong>permanente</strong> y <strong>no podrá ser revertida</strong>.
    Por favor, ingresa tu contraseña para confirmar.
  </p>

  <form id="eliminarCuentaForm" novalidate style="width: 100%;">
    @csrf
    <label for="passwordinput" style="display: block; font-weight: 600; margin-bottom: 6px; color: #111;">Contraseña</label>
    <div style="position: relative; margin-bottom: 20px;">
      <input
        id="passwordinput"
        name="password"
        type="password"
        autocomplete="current-password"
        placeholder="Ingresa tu contraseña"
        required
        style="
          width: 100%;
          padding: 10px 40px 10px 12px;
          font-size: 1rem;
          border: 1.5px solid #ccc;
          border-radius: 5px;
          box-sizing: border-box;
          transition: border-color 0.2s;
        "
        onfocus="this.style.borderColor='#b22222'"
        onblur="this.style.borderColor='#ccc'"
      />
      <i
        id="togglePassword"
        class="fas fa-eye"
        style="
          position: absolute;
          right: 10px;
          top: 50%;
          transform: translateY(-50%);
          cursor: pointer;
          color: #555;
          font-size: 1.1rem;
        "
        onclick="togglePasswordVisibility()"
        title="Mostrar/Ocultar contraseña"
      ></i>
    </div>
    <div id="passwordError" style="color: #b22222; font-size: 0.9rem; margin-bottom: 20px; display: none;">
      Por favor ingresa tu contraseña
    </div>

    <button
      type="submit"
      style="
        width: 100%;
        background-color: #b22222;
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 12px 0;
        cursor: pointer;
      "
      onmouseover="this.style.backgroundColor='#7f1717'"
      onmouseout="this.style.backgroundColor='#b22222'"
    >
      <i class="fas fa-trash-alt" style="margin-right: 8px;"></i>
      Eliminar mi cuenta
    </button>
  </form>
</div>

<script>
  function togglePasswordVisibility() {
    const input = document.getElementById("passwordinput");
    const icon = document.getElementById("togglePassword");

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eliminarCuentaForm');
    const passwordInput = document.getElementById('passwordinput');
    const passwordError = document.getElementById('passwordError');

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const password = passwordInput.value.trim();

      if (!password) {
        passwordError.style.display = 'block';
        passwordInput.style.borderColor = '#b22222';
        Swal.fire('Error', 'Por favor ingresa tu contraseña', 'error');
        return;
      } else {
        passwordError.style.display = 'none';
        passwordInput.style.borderColor = '#ccc';
      }

      fetch('{{ route("profile.verify-password") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ password })
      })
      .then(response => {
        if (!response.ok) {
          return response.json().then(err => { throw err; });
        }
        return response.json();
      })
      .then(data => {
        if (data.valid) {
          Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción eliminará tu cuenta permanentemente!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#b22222',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminarla',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed || result.value === true) {
              fetch('{{ route("profile.eliminar") }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json'
                },
                body: JSON.stringify({ password })
              })
              .then(response => {
                if (!response.ok) {
                  return response.json().then(err => { throw err; });
                }
                return response.json();
              })
              .then(data => {
                Swal.fire('Cuenta eliminada', data.message || 'Tu cuenta ha sido eliminada.', 'success')
                .then(() => {
                  window.location.href = '{{ route("login") }}';
                });
              })
              .catch(error => {
                Swal.fire('Error', error.message || 'No se pudo eliminar la cuenta', 'error');
              });
            }
          });
        }
      })
      .catch(error => {
        Swal.fire('Error', error.message || 'Contraseña incorrecta', 'error');
      });
    });
  });
</script>
