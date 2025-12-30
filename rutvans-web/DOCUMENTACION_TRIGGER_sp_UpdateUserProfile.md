# 📋 DOCUMENTACIÓN TÉCNICA - TRIGGER sp_UpdateUserProfile

## 📅 **INFORMACIÓN GENERAL**
- **Fecha:** 7 de octubre de 2025
- **Tarea:** #1 - sp_UpdateUserProfile, auditoría created_at/updated_at
- **Migración:** `2025_10_07_000000_create_sp_update_user_profile_trigger.php`
- **Desarrollador:** Sistema de IA - GitHub Copilot

---

## 🎯 **OBJETIVO**
Implementar triggers de base de datos que validen datos críticos antes de insertar o actualizar registros en la tabla `users`, asegurando consistencia y reglas de negocio automáticamente a nivel de base de datos.

---

## 🏗️ **IMPLEMENTACIÓN**

### **TRIGGERS CREADOS**

#### 1. **`sp_UpdateUserProfile`** (BEFORE UPDATE)
- **Propósito:** Validar y auditar actualizaciones en perfiles de usuario
- **Momento:** Se ejecuta ANTES de cada UPDATE en la tabla `users`

#### 2. **`sp_InsertUserProfile`** (BEFORE INSERT)
- **Propósito:** Validar y auditar inserciones de nuevos usuarios
- **Momento:** Se ejecuta ANTES de cada INSERT en la tabla `users`

---

## ✅ **VALIDACIONES IMPLEMENTADAS**

### **📧 VALIDACIÓN DE EMAIL**
```sql
-- Formato de email válido usando REGEXP
IF NEW.email NOT REGEXP "^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" THEN
    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email format is invalid";
END IF;
```
- **Qué hace:** Valida formato estándar de email
- **Ejemplo válido:** `usuario@ejemplo.com`
- **Ejemplo inválido:** `usuario@`, `@ejemplo.com`, `usuario.ejemplo`

### **📞 VALIDACIÓN DE TELÉFONO**
```sql
-- Solo números, espacios, guiones, paréntesis y signo +
IF NEW.phone_number NOT REGEXP "^[0-9\s\-\(\)\+]+$" THEN
    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Phone number format is invalid";
END IF;
```
- **Qué hace:** Permite formatos comunes de teléfono
- **Ejemplo válido:** `+52 999 123 4567`, `(999) 123-4567`, `9991234567`
- **Ejemplo inválido:** `999-abc-4567`, `teléfono123`

### **👤 VALIDACIÓN DE NOMBRE**
```sql
-- Nombre no puede estar vacío
IF NEW.name IS NULL OR TRIM(NEW.name) = "" THEN
    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name cannot be empty";
END IF;

-- Mínimo 2 caracteres
IF LENGTH(TRIM(NEW.name)) < 2 THEN
    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name must be at least 2 characters long";
END IF;
```
- **Qué hace:** Asegura nombres válidos y no vacíos
- **Ejemplo válido:** `Juan Pérez`, `María`
- **Ejemplo inválido:** ``, `   `, `A`

### **🔄 PREVENCIÓN DE EMAILS DUPLICADOS**
```sql
-- Verifica que no exista otro usuario con el mismo email
IF EXISTS (SELECT 1 FROM users WHERE email = NEW.email AND id != NEW.id) THEN
    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email already exists";
END IF;
```
- **Qué hace:** Previene emails duplicados a nivel de base de datos
- **Excepción:** Permite actualizar el mismo registro sin error

### **🏠 VALIDACIÓN DE DIRECCIÓN**
```sql
-- Máximo 500 caracteres para la dirección
IF NEW.address IS NOT NULL AND LENGTH(NEW.address) > 500 THEN
    SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Address is too long (max 500 characters)";
END IF;
```
- **Qué hace:** Limita la longitud de direcciones
- **Límite:** 500 caracteres máximo

---

## 🔧 **AUDITORÍA AUTOMÁTICA**

### **TIMESTAMPS AUTOMÁTICOS**
```sql
-- En UPDATE: preservar created_at, actualizar updated_at
SET NEW.updated_at = CURRENT_TIMESTAMP;
SET NEW.created_at = OLD.created_at;

-- En INSERT: establecer ambos timestamps
IF NEW.created_at IS NULL THEN
    SET NEW.created_at = CURRENT_TIMESTAMP;
END IF;
IF NEW.updated_at IS NULL THEN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END IF;
```

### **NORMALIZACIÓN DE DATOS**
```sql
-- Email siempre en minúsculas y sin espacios
SET NEW.email = LOWER(TRIM(NEW.email));

-- Nombre sin espacios extra al inicio/final
SET NEW.name = TRIM(NEW.name);
```

---

## 🚀 **BENEFICIOS**

### **🛡️ SEGURIDAD**
- **Validación a nivel de BD:** No depende del código de aplicación
- **Consistencia garantizada:** Imposible insertar datos inválidos
- **Auditoría automática:** Timestamps siempre correctos

### **⚡ RENDIMIENTO**
- **Validación inmediata:** No requiere consultas adicionales desde la app
- **Menos código:** Validaciones centralizadas en la BD
- **Atomicidad:** Falla rápido si hay errores

### **🔧 MANTENIBILIDAD**
- **Reglas centralizadas:** Un solo lugar para cambiar validaciones
- **Código limpio:** Menos validaciones repetitivas en controladores
- **Documentación clara:** Errores descriptivos

---

## 📊 **CASOS DE USO**

### **✅ CASOS EXITOSOS**
```sql
-- INSERT válido
INSERT INTO users (name, email, phone_number) 
VALUES ('Juan Pérez', 'juan@ejemplo.com', '+52 999 123 4567');

-- UPDATE válido
UPDATE users SET name = 'Juan Carlos Pérez' WHERE id = 1;
```

### **❌ CASOS QUE FALLARÁN**
```sql
-- Email inválido
INSERT INTO users (name, email) VALUES ('Juan', 'email-invalido');
-- Error: "Email format is invalid"

-- Nombre vacío
UPDATE users SET name = '' WHERE id = 1;
-- Error: "Name cannot be empty"

-- Email duplicado
INSERT INTO users (name, email) VALUES ('Pedro', 'juan@ejemplo.com');
-- Error: "Email already exists"
```

---

## 🔄 **MIGRACIÓN**

### **APLICAR CAMBIOS**
```bash
php artisan migrate
```

### **REVERTIR CAMBIOS**
```bash
php artisan migrate:rollback
```

---

## 📈 **IMPACTO EN EL SISTEMA**

### **🎯 TABLAS AFECTADAS**
- `users` - Triggers aplicados directamente

### **🔗 EFECTOS EN CASCADA**
- **Mejor calidad de datos** en todas las relaciones con `users`
- **Reducción de errores** en autenticación y perfiles
- **Auditoría completa** de cambios en perfiles

### **⚠️ CONSIDERACIONES**
- **Manejo de errores:** Las aplicaciones deben manejar las excepciones SQL
- **Testing:** Verificar que las validaciones funcionen correctamente
- **Performance:** Los triggers se ejecutan en cada operación (overhead mínimo)

---

## 🧪 **TESTING RECOMENDADO**

1. **Probar inserts válidos e inválidos**
2. **Verificar que timestamps se actualicen correctamente**
3. **Confirmar que emails duplicados se rechacen**
4. **Validar normalización de datos**
5. **Comprobar rollback de la migración**

---

**✅ TAREA COMPLETADA:** Trigger sp_UpdateUserProfile implementado exitosamente con validaciones robustas y auditoría automática.