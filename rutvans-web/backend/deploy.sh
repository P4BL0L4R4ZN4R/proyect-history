#!/bin/bash

# Script de despliegue para RutVans
# Este script configura el entorno según el tipo de despliegue (local o producción)

echo "🚀 Iniciando despliegue de RutVans..."

# Verificar si estamos en local o producción
if [ "$1" == "production" ]; then
    echo "📦 Configurando entorno de PRODUCCIÓN..."
    
    # Copiar archivo .env de producción
    if [ -f ".env.production" ]; then
        cp .env.production .env
        echo "✅ Archivo .env de producción copiado"
    else
        echo "❌ Error: Archivo .env.production no encontrado"
        exit 1
    fi
    
    # Configurar storage para producción
    echo "📁 Configurando storage para producción..."
    php artisan storage:link-custom --force
    
else
    echo "🏠 Configurando entorno LOCAL..."
    
    # Asegurar que el .env local esté configurado
    if [ ! -f ".env" ]; then
        cp .env.example .env
        echo "⚠️ Archivo .env creado desde .env.example"
        echo "🔑 No olvides configurar APP_KEY ejecutando: php artisan key:generate"
    fi
    
    # Crear enlace de storage local
    php artisan storage:link
fi

# Comandos comunes para ambos entornos
echo "🔄 Ejecutando comandos comunes..."

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generar caché de configuración (solo en producción)
if [ "$1" == "production" ]; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    echo "✅ Caché generado para producción"
fi

# Optimizar autoloader
composer dump-autoload --optimize

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force

echo "✨ ¡Despliegue completado exitosamente!"
echo ""

if [ "$1" == "production" ]; then
    echo "📋 Recordatorios para producción:"
    echo "1. Crear manualmente el enlace simbólico en public_html:"
    echo "   ln -s /home/u350475089/domains/rutvans.com/public_html/storage /home/u350475089/domains/rutvans.com/public_html/storage"
    echo "2. Verificar permisos de escritura en el directorio storage"
    echo "3. Configurar correctamente la base de datos"
else
    echo "📋 Entorno local listo para desarrollo"
fi