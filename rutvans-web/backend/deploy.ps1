# Script de despliegue para RutVans (Windows PowerShell)
# Uso: .\deploy.ps1 [production]

param(
    [string]$Environment = "local"
)

Write-Host "🚀 Iniciando despliegue de RutVans..." -ForegroundColor Green

if ($Environment -eq "production") {
    Write-Host "📦 Configurando entorno de PRODUCCIÓN..." -ForegroundColor Yellow
    
    # Copiar archivo .env de producción
    if (Test-Path ".env.production") {
        Copy-Item ".env.production" ".env" -Force
        Write-Host "✅ Archivo .env de producción copiado" -ForegroundColor Green
    } else {
        Write-Host "❌ Error: Archivo .env.production no encontrado" -ForegroundColor Red
        exit 1
    }
    
    # Configurar storage para producción
    Write-Host "📁 Configurando storage para producción..." -ForegroundColor Cyan
    php artisan storage:link-custom --force
    
} else {
    Write-Host "🏠 Configurando entorno LOCAL..." -ForegroundColor Cyan
    
    # Asegurar que el .env local esté configurado
    if (-not (Test-Path ".env")) {
        Copy-Item ".env.example" ".env"
        Write-Host "⚠️ Archivo .env creado desde .env.example" -ForegroundColor Yellow
        Write-Host "🔑 No olvides configurar APP_KEY ejecutando: php artisan key:generate" -ForegroundColor Yellow
    }
    
    # Crear enlace de storage local
    php artisan storage:link
}

# Comandos comunes para ambos entornos
Write-Host "🔄 Ejecutando comandos comunes..." -ForegroundColor Cyan

# Limpiar caché
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Generar caché de configuración (solo en producción)
if ($Environment -eq "production") {
    php artisan config:cache
    php artisan route:cache  
    php artisan view:cache
    Write-Host "✅ Caché generado para producción" -ForegroundColor Green
}

# Optimizar autoloader
composer dump-autoload --optimize

# Ejecutar migraciones
Write-Host "🗄️ Ejecutando migraciones..." -ForegroundColor Cyan
php artisan migrate --force

Write-Host "✨ ¡Despliegue completado exitosamente!" -ForegroundColor Green
Write-Host ""

if ($Environment -eq "production") {
    Write-Host "📋 Recordatorios para producción:" -ForegroundColor Yellow
    Write-Host "1. Crear manualmente el enlace simbólico en public_html:" -ForegroundColor White
    Write-Host "   ln -s /home/u350475089/domains/rutvans.com/public_html/storage /home/u350475089/domains/rutvans.com/public_html/storage" -ForegroundColor Gray
    Write-Host "2. Verificar permisos de escritura en el directorio storage" -ForegroundColor White
    Write-Host "3. Configurar correctamente la base de datos" -ForegroundColor White
} else {
    Write-Host "📋 Entorno local listo para desarrollo" -ForegroundColor Green
}