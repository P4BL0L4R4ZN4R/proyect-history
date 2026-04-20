# 🔗 PanelLab - Sistema de Administración de Múltiples Bases de Datos

![Badge Laravel](https://img.shields.io/badge/Laravel-9.x-red)
![Badge PHP](https://img.shields.io/badge/PHP-8.1-blue)
![Badge MySQL](https://img.shields.io/badge/DB-MySQL%20(Multi--Conexión)-orange)
![Badge Estado](https://img.shields.io/badge/Estado-Estable-brightgreen)

**Autor:** Pablo Lara Aznar

## 📋 Descripción General

Sistema avanzado de administración diseñado para entornos donde se requiere **gestionar múltiples bases de datos independientes desde una sola interfaz**. Originalmente desarrollado para **INADWARESOFT**, permite conectar, visualizar y manipular datos de distintas fuentes sin necesidad de modificar el código fuente.

**Característica Clave:** El sistema utiliza **Conexiones Dinámicas de Base de Datos en Laravel**, permitiendo cambiar el target de las consultas SQL en tiempo de ejecución según el cliente o licencia seleccionada.

## ⚙️ Funcionalidades Principales

### 🗄️ Módulo de Multi-Conexión (El Núcleo)
- Configuración dinámica de conexiones `mysql_multiple` en controllers.
- Lógica en modelos para apuntar a diferentes bases de datos según el contexto de licencia.
- Migraciones unificadas que se ejecutan selectivamente sobre la base de datos activa.

### 🔐 Módulo de Autenticación
- Sistema de Login/Registro estándar (Laravel UI).
- **Sin roles:** Acceso único para administradores del sistema.

### 🖨️ Módulo de Reportes
- Generación de reportes en **PDF** (usando `barryvdh/laravel-dompdf`).
- Filtros por rango de fechas y tipo de dato.
- Exportación de datos tabulares a Excel/CSV.

### 🔑 Módulo de Gestión de Licencias
- Panel para **Activar / Desactivar** el acceso a bases de datos específicas.
- Control de estado: `Activo`, `Suspendido`.
- Validación de vigencia antes de permitir consultas a la base de datos remota.

## 🛠️ Stack Tecnológico y Librerías Clave

| Tecnología | Uso Específico en este Proyecto |
| :--- | :--- |
| **Laravel 9** | Framework base. |
| **MySQL (Multi-Conexión)** | Uso avanzado de `DB::connection('cliente_x')`. |
| **Laravel DOMPDF** | Generación de facturas y reportes en PDF. |
| **Maatwebsite Excel** | Exportación de datos a Excel. |
| **Laravel UI** | Autenticación básica sin roles. |
