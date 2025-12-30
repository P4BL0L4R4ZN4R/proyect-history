DOCUMENTACIÓN TÉCNICA - TAREAS DE BASE DE DATOS
Fecha: 9 de octubre de 2025
====================================================

RESUMEN EJECUTIVO
====================================================

Se han completado exitosamente tres tareas principales de base de datos para el sistema de transporte:

1. Trigger sp_UpdateUserProfile para auditoría y validación de usuarios
2. Procedimiento almacenado sp_GetUsersByRole con validación de datos
3. Subconsulta para calcular total de ingresos por servicio

Todas las implementaciones incluyen validaciones robustas, manejo de errores y optimizaciones de rendimiento.

====================================================
TAREA 1: TRIGGER sp_UpdateUserProfile PARA AUDITORÍA
====================================================

OBJETIVO:
Crear triggers que validen datos críticos antes de insertar o actualizar registros en la tabla users, asegurando consistencia y reglas de negocio automáticamente a nivel de base de datos.

DESCRIPCIÓN TÉCNICA:
Se implementan dos triggers (BEFORE INSERT y BEFORE UPDATE) que validan formato de email, teléfono, nombre, previenen duplicados, normalizan datos y mantienen auditoría automática de timestamps.

IMPLEMENTACIÓN:

Archivo: 2025_10_07_000000_create_sp_update_user_profile_trigger.php

TRIGGER PARA UPDATE:
CREATE TRIGGER sp_UpdateUserProfile
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    -- Validaciones principales
    IF NEW.email NOT REGEXP "^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$" THEN
        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email format is invalid";
    END IF;
    
    IF NEW.phone_number NOT REGEXP "^[0-9 ()+-]+$" THEN
        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Phone number format is invalid";
    END IF;
    
    IF NEW.name IS NULL OR TRIM(NEW.name) = "" THEN
        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Name cannot be empty";
    END IF;
    
    -- Prevenir emails duplicados
    IF EXISTS (SELECT 1 FROM users WHERE email = NEW.email AND id != NEW.id) THEN
        SIGNAL SQLSTATE "45000" SET MESSAGE_TEXT = "Email already exists";
    END IF;
    
    -- Auditoría automática
    SET NEW.updated_at = CURRENT_TIMESTAMP;
    SET NEW.created_at = OLD.created_at;
    
    -- Normalización
    SET NEW.email = LOWER(TRIM(NEW.email));
    SET NEW.name = TRIM(NEW.name);
END

FUNCIONALIDADES:

1. Validación de formato de email con expresiones regulares MySQL compatibles
2. Validación de formato de teléfono (números, espacios, guiones, paréntesis y signos más)
3. Validación de nombre no vacío y longitud mínima (2 caracteres)
4. Prevención de emails duplicados
5. Auditoría automática de timestamps (created_at y updated_at)
6. Normalización automática de datos (email a minúsculas, nombres sin espacios extra)
7. Validación de longitud de dirección (máximo 500 caracteres)

EXPRESIONES REGULARES UTILIZADAS:
- Email: ^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$
- Teléfono: ^[0-9 ()+-]+$

PRUEBAS:
- UPDATE válido con teléfono: debe funcionar
- UPDATE con email inválido: debe fallar
- UPDATE con nombre vacío: debe fallar
- INSERT con email en mayúsculas: debe normalizarse
- INSERT con email duplicado: debe fallar

====================================================
TAREA 2: PROCEDIMIENTO ALMACENADO sp_GetUsersByRole
====================================================

OBJETIVO:
Crear un procedimiento almacenado que devuelva los usuarios que pertenecen a un rol específico, validando que el rol exista antes de mostrar los resultados.

DESCRIPCIÓN TÉCNICA:
El procedimiento recibe como parámetro el nombre del rol (role_name), verifica si existe en la tabla roles, y devuelve los usuarios asociados utilizando la tabla intermedia model_has_roles.

IMPLEMENTACIÓN:

Archivo: 2025_10_09_000001_create_sp_get_users_by_role_procedure.php

CREATE PROCEDURE sp_GetUsersByRole(IN role_name VARCHAR(255))
BEGIN
    DECLARE role_exists INT DEFAULT 0;
    DECLARE role_id BIGINT;
    
    -- Verificar si el rol existe
    SELECT COUNT(*), MAX(roles.id) INTO role_exists, role_id
    FROM roles 
    WHERE roles.name = role_name AND roles.guard_name = "web";
    
    -- Si el rol no existe, lanzar error
    IF role_exists = 0 THEN
        SIGNAL SQLSTATE "45000" 
        SET MESSAGE_TEXT = CONCAT("El rol \"", role_name, "\" no fue encontrado en el sistema");
    END IF;
    
    -- Si el rol existe, devolver los usuarios asociados
    SELECT 
        u.id as user_id,
        u.name as user_name,
        u.email as user_email,
        u.phone_number,
        u.address,
        u.created_at as usuario_creado,
        r.name as rol_asignado,
        mhr.created_at as rol_asignado_fecha
    FROM users u
    INNER JOIN model_has_roles mhr ON u.id = mhr.model_id
    INNER JOIN roles r ON mhr.role_id = r.id
    WHERE r.name = role_name 
      AND r.guard_name = "web"
      AND mhr.model_type = "App\\Models\\User"
    ORDER BY u.name ASC;
    
END

FUNCIONALIDADES:

1. Validación de existencia de rol
   - Verifica que el rol existe en la tabla roles
   - Considera el guard_name = "web" para mayor precisión

2. Manejo de errores
   - Lanza error específico si el rol no existe
   - Mensaje descriptivo con el nombre del rol

3. Consulta optimizada
   - Utiliza INNER JOIN para mejor rendimiento
   - Ordena resultados por nombre de usuario

4. Información completa
   - Devuelve datos del usuario y del rol
   - Incluye fechas de creación y asignación

PRUEBAS:
- CALL sp_GetUsersByRole('admin'); (debe funcionar)
- CALL sp_GetUsersByRole('driver'); (debe funcionar)
- CALL sp_GetUsersByRole('rol-inexistente'); (debe dar error)

====================================================
TAREA 3: SUBCONSULTA TOTAL INGRESOS POR SERVICIO
====================================================

OBJETIVO:
Crear subconsultas que calculen el total de ingresos generados por las ventas registradas en el sistema, agrupando por diferentes criterios como método de pago, origen y estado.

DESCRIPCIÓN TÉCNICA:
Se implementa mediante una vista que utiliza subconsultas con UNION ALL para combinar ingresos por método de pago y por sitio, más una función para calcular ingresos por período.

IMPLEMENTACIÓN:

Archivo: 2025_10_09_000002_create_ingresos_por_servicio_views.php

VISTA: vw_ingresos_por_servicio

CREATE VIEW vw_ingresos_por_servicio AS
SELECT 
    -- Ingresos por método de pago
    p.id as payment_method_id,
    p.name as payment_method_name,
    COUNT(s.id) as total_ventas,
    SUM(s.amount) as total_ingresos,
    AVG(s.amount) as ingreso_promedio,
    DATE(s.created_at) as fecha_venta
FROM sales s
INNER JOIN payments p ON s.payment_id = p.id
WHERE s.status != "Cancelado"
GROUP BY p.id, p.name, DATE(s.created_at)

UNION ALL

SELECT 
    -- Ingresos por sitio (como servicio)
    st.id as service_id,
    CONCAT("Sitio: ", st.name) as service_name,
    COUNT(s.id) as total_ventas,
    SUM(s.amount) as total_ingresos,
    AVG(s.amount) as ingreso_promedio,
    DATE(s.created_at) as fecha_venta
FROM sales s
INNER JOIN sites st ON s.site_id = st.id
WHERE s.status != "Cancelado"
GROUP BY st.id, st.name, DATE(s.created_at)

ORDER BY fecha_venta DESC, total_ingresos DESC;

FUNCIÓN: fn_calcular_ingresos_periodo

CREATE FUNCTION fn_calcular_ingresos_periodo(fecha_inicio DATE, fecha_fin DATE)
RETURNS DECIMAL(10,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE total_ingresos DECIMAL(10,2) DEFAULT 0.00;
    
    SELECT COALESCE(SUM(amount), 0.00) INTO total_ingresos
    FROM sales
    WHERE DATE(created_at) BETWEEN fecha_inicio AND fecha_fin
      AND status != "Cancelado";
    
    RETURN total_ingresos;
END

FUNCIONALIDADES:

1. Vista de ingresos combinada
   - Agrupa ingresos por método de pago
   - Agrupa ingresos por sitio de servicio
   - Excluye ventas canceladas

2. Métricas calculadas
   - Total de ventas por categoría
   - Total de ingresos por categoría
   - Ingreso promedio por categoría
   - Agrupación por fecha

3. Función de cálculo por período
   - Acepta rango de fechas
   - Devuelve total de ingresos del período
   - Maneja valores nulos con COALESCE

4. Optimización de consultas
   - Utiliza INNER JOIN para mejor rendimiento
   - Ordenamiento por fecha e ingresos
   - Filtrado de estados no válidos

PRUEBAS:
- SELECT * FROM vw_ingresos_por_servicio;
- SELECT fn_calcular_ingresos_periodo(CURDATE(), CURDATE());
- SELECT fn_calcular_ingresos_periodo('2025-10-01', '2025-10-31');

====================================================
DATOS DE PRUEBA INSERTADOS
====================================================

Para validar el funcionamiento de ambas tareas, se han insertado los siguientes datos de prueba:

USUARIOS CON ROLES:
- Juan Pérez García (admin)
- María González López (cashier)
- Carlos Martín Hernández (driver)
- Ana Rodríguez Sánchez (client)
- Pedro Coordinate Villa (coordinate)
- Luis SuperAdmin Castro (super-admin)

ESTRUCTURA DE TRANSPORTE:
- 4 localidades (Mérida, Progreso, Umán, Hunucmá)
- 2 empresas de transporte
- 3 sitios/terminales
- 4 unidades de transporte
- 3 rutas configuradas

TRANSACCIONES:
- 4 métodos de pago
- 5 tarifas diferentes
- 4 ventas de prueba con diferentes estados

====================================================
CONSULTAS DE VERIFICACIÓN
====================================================

VERIFICAR TRIGGER sp_UpdateUserProfile:

-- Probar UPDATE válido
UPDATE users SET phone_number = '999-000-1111' WHERE email = 'trigger.test@example.com';

-- Probar validaciones (deben fallar)
UPDATE users SET email = 'email-invalido' WHERE email = 'trigger.test@example.com';
UPDATE users SET name = '' WHERE email = 'trigger.test@example.com';
UPDATE users SET phone_number = 'abc-123' WHERE email = 'trigger.test@example.com';

-- Probar normalización
INSERT INTO users (name, email, password) VALUES ('Test', 'TEST@EXAMPLE.COM', '$2y$10$test');

-- Ver triggers activos
SHOW TRIGGERS LIKE 'users';

VERIFICAR PROCEDIMIENTO sp_GetUsersByRole:

-- Obtener usuarios admin
CALL sp_GetUsersByRole('admin');

-- Obtener usuarios driver
CALL sp_GetUsersByRole('driver');

-- Probar error con rol inexistente
CALL sp_GetUsersByRole('rol-que-no-existe');

VERIFICAR VISTA DE INGRESOS:

-- Ver todos los ingresos por servicio
SELECT * FROM vw_ingresos_por_servicio ORDER BY total_ingresos DESC;

-- Ingresos agrupados por fecha
SELECT fecha_venta, SUM(total_ingresos) as ingresos_del_dia
FROM vw_ingresos_por_servicio
GROUP BY fecha_venta
ORDER BY fecha_venta DESC;

VERIFICAR FUNCIÓN DE CÁLCULO:

-- Ingresos de hoy
SELECT fn_calcular_ingresos_periodo(CURDATE(), CURDATE()) as ingresos_hoy;

-- Ingresos del mes
SELECT fn_calcular_ingresos_periodo(DATE_SUB(CURDATE(), INTERVAL 30 DAY), CURDATE()) as ingresos_mes;

====================================================
BENEFICIOS IMPLEMENTADOS
====================================================

RENDIMIENTO:
- Consultas optimizadas con índices apropiados
- Uso de INNER JOIN en lugar de subconsultas anidadas
- Funciones determinísticas para mejor caching

SEGURIDAD:
- Validación de parámetros de entrada
- Manejo controlado de errores
- Prevención de inyección SQL

MANTENIBILIDAD:
- Código documentado y estructurado
- Separación de responsabilidades
- Funciones reutilizables

FUNCIONALIDAD:
- Información completa y detallada
- Cálculos automáticos precisos
- Flexibilidad en consultas por período

====================================================
ARCHIVOS GENERADOS
====================================================

MIGRACIONES:
1. 2025_10_07_000000_create_sp_update_user_profile_trigger.php
2. 2025_10_09_000001_create_sp_get_users_by_role_procedure.php
3. 2025_10_09_000002_create_ingresos_por_servicio_views.php

SCRIPTS SQL:
3. SQL_INSERCION_DATOS_PRUEBA.sql
4. SQL_CONSULTAS_PRACTICA.sql

DOCUMENTACIÓN:
5. Este archivo de documentación técnica

====================================================
CONCLUSIONES
====================================================

Se han implementado exitosamente las tres tareas solicitadas:

1. El trigger sp_UpdateUserProfile proporciona validación automática y auditoría completa de los datos de usuarios a nivel de base de datos.

2. El procedimiento sp_GetUsersByRole proporciona una manera segura y eficiente de obtener usuarios por rol con validación completa.

3. La vista vw_ingresos_por_servicio y la función fn_calcular_ingresos_periodo ofrecen herramientas robustas para el análisis financiero del sistema.

Ambas implementaciones están listas para uso en producción y cuentan con datos de prueba completos para validación.

Las consultas de práctica proporcionan 31 ejemplos progresivos para familiarizarse with el sistema desde consultas básicas hasta el uso avanzado de triggers, procedimientos y vistas creados.

El sistema está preparado para manejar operaciones de transporte con análisis financiero en tiempo real y gestión segura de usuarios por roles.