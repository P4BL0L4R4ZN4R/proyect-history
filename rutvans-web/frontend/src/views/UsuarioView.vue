<template>
  <div id="usuario">
    <h1 class="titulo">Consultar Usuarios</h1>
    <button class="boton" @click="fetch">Consultar</button>
    <div v-if="usuarios.length" class="contenedor-tabla">
      <table class="tabla-usuarios">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre de Usuario</th>
            <th>Rol</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.idUsuario">
            <td>{{ usuario.idUsuario }}</td>
            <td>{{ usuario.nombreUsuario }}</td>
            <td>{{ usuario.rol ? usuario.rol.nombreRol : "Sin rol" }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p v-else class="mensaje-vacio">No hay datos para mostrar.</p>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "UsuarioView",
  data() {
    return {
      usuarios: [],
    };
  },
  methods: {
    fetch() {
      axios
        .get("http://127.0.0.1:8000/api/ver-usuarios")
        .then((res) => {
          this.usuarios = res.data.usuarios;
        })
        .catch((err) => {
          console.error(err);
        });
    },
  },
};
</script>

<style scoped>
#usuario {
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #2c3e50;
  display: flex;
  flex-direction: column;
  padding: 20px;
  background-color: #f8f9fa;
  min-height: 100vh;
  width: 100%; 
  margin: 0 auto;
}




.titulo {
  font-size: 2rem;
  font-weight: 600;
  color: #e67e22;
  margin-bottom: 20px;
}

.boton {
  background-color: #f39c12;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-bottom: 20px;
  display: block;
  margin: 0 auto 20px auto;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
  width: 20vw;
}

.boton:hover {
  background-color: #e67e22;
}

.contenedor-tabla {
  width: 100%;
  max-width: 1000px;
  overflow-x: auto;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  border-radius: 10px;
  background-color: white;
  padding: 10px;
  display: block;
  margin: 0 auto 20px auto;
}

.tabla-usuarios {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  border-radius: 10px;
  overflow: hidden;
  
}

.tabla-usuarios th {
  background-color: #f39c12;
  color: white;
  font-weight: 700;
  padding: 12px 15px;
  text-align: center;
}

.tabla-usuarios td {
  padding: 12px 15px;
  border-bottom: 1px solid #ddd;
  text-align: center;
  color: #2c3e50;
}

.tabla-usuarios tbody tr {
  background-color: #ffebcc;
  transition: background-color 0.3s ease;
}

.tabla-usuarios tbody tr:hover {
  background-color: #ffe0b3;
}

.mensaje-vacio {
  font-size: 1.2rem;
  color: #7f8c8d;
  margin-top: 20px;
}

</style>
