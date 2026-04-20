# 🐾 PetCare POS - Sistema de Punto de Venta para Clínica Veterinaria

![Badge en Desarrollo](https://img.shields.io/badge/Estado-MVP%20Funcional-yellow)
![Badge Laravel](https://img.shields.io/badge/Backend-Laravel%2010-red)
![Badge PHP](https://img.shields.io/badge/PHP-8.2-blue)
![Badge MySQL](https://img.shields.io/badge/DB-MySQL-orange)

**Autor:** Pablo Lara Aznar

## 📋 Descripción General

Sistema de administración y punto de venta diseñado específicamente para clínicas veterinarias pequeñas. El proyecto surgió como una solución a la medida para gestionar inventario de medicamentos, registro de mascotas/pacientes y control de citas, unificando la operación diaria en una sola plataforma web.

Este repositorio contiene el núcleo del **Backend (API REST)** y el **Panel Administrativo Web**.

## ✨ Funcionalidades Implementadas (MVP)

### 🧑‍⚕️ Módulo de Pacientes (Futuro desarrollo)
- Registro de mascotas con datos específicos: Especie, Raza, Edad, Peso.
- Asociación de mascota con su dueño (Cliente).
- Historial básico de visitas.

### 📦 Módulo de Inventario
- CRUD completo de productos (medicamentos, alimentos, accesorios).
- Control de stock inicial.
- Registro de movimientos de entrada/salida (Ajustes de inventario).

### 🛒 Módulo de Punto de Venta (POS)
- Carrito de compras funcional con JavaScript.
- Cálculo automático de totales e impuestos.
- Proceso de "Checkout" que descuenta automáticamente el stock del inventario.

### 🔐 Módulo de Autenticación
- Sistema de roles básico: Administrador y Veterinario/Vendedor.
- Login seguro con Laravel Breeze/UI.

## 🛠️ Stack Tecnológico

| Área | Tecnología | Justificación |
| :--- | :--- | :--- |
| **Backend** | Laravel 11 / PHP 8.2 | Framework robusto para lógica de negocio compleja (inventario, ventas). |
| **Base de Datos** | MySQL | Fiabilidad en transacciones de venta (ACID) para evitar descuadres de stock. |
| **Frontend Web** | Blade / Bootstrap 5 / JavaScript | Interfaz rápida y responsiva para uso en tablets en recepción. |
| **APIs** | RESTful API |
| **Herramientas** | Composer, Postman, Git | |
