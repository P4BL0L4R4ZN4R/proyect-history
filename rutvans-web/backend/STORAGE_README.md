# 📁 Configuración de Storage - RutVans

Este documento explica cómo configurar el sistema de almacenamiento para que funcione tanto en **desarrollo local** como en **producción (hosting)**.

## 🔧 Problema Resuelto

- **Local**: Los archivos se almacenan en `storage/app/public` y se acceden vía `public/storage`
- **Producción**: Los archivos se almacenan en `/home/u350475089/domains/rutvans.com/public_html/storage`
- **Unificación**: El sistema ahora funciona automáticamente en ambos entornos

## 🛠️ Configuración Implementada

### 1. Helper Personalizado (`StorageHelper`)
- Ubicación: `app/Helpers/StorageHelper.php`
- Funciones:
  - `getPublicUrl($path)` - Genera URLs correctas según el entorno
  - `storePublicFile($file, $directory)` - Almacena archivos
  - `deletePublicFile($path)` - Elimina archivos
  - `fileExists($path)` - Verifica existencia de archivos

### 2. Configuración Adaptativa (`filesystems.php`)
```php
'public' => [
    'driver' => 'local',
    'root' => env('STORAGE_PATH', storage_path('app/public')),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### 3. Variables de Entorno

#### Local (`.env`)
```env
FILESYSTEM_DISK=public
# No definir STORAGE_PATH (usa la ruta por defecto)
```

#### Producción (`.env.production`)
```env
FILESYSTEM_DISK=public
STORAGE_PATH=/home/u350475089/domains/rutvans.com/public_html/storage
```

### 4. Directivas Blade Personalizadas
- `@storageImg($path)` - Muestra imagen del storage
- `@storageExists($path)` - Verifica si existe archivo

## 🚀 Despliegue

### Desarrollo Local
```bash
# Windows PowerShell
.\deploy.ps1

# Linux/Mac
bash deploy.sh
```

### Producción
```bash
# Windows PowerShell  
.\deploy.ps1 production

# Linux/Mac
bash deploy.sh production
```

## 📋 Checklist de Despliegue

### Local ✅
- [x] Ejecutar `php artisan storage:link`
- [x] Verificar que `public/storage` existe
- [x] Probar subida de archivos

### Producción 📦
- [x] Copiar `.env.production` a `.env`
- [x] Ejecutar script de despliegue  
- [ ] Crear enlace simbólico manual en hosting:
  ```bash
  ln -s /home/u350475089/domains/rutvans.com/public_html/storage /home/u350475089/domains/rutvans.com/public_html/storage
  ```
- [ ] Verificar permisos de escritura (755)
- [ ] Probar subida de archivos

## 🔧 Uso en Controladores

### Antes
```php
$path = $file->storeAs($folder, $filename, 'public');
$url = asset('storage/' . $path);
```

### Después
```php
$path = StorageHelper::storeFileAs($file, $folder, $filename);
$url = StorageHelper::getPublicUrl($path);
```

## 🎨 Uso en Vistas

### Antes
```blade
<img src="{{ asset('storage/' . $photo) }}">
```

### Después
```blade
<img src="@storageImg($photo)">
```

## 📁 Estructura de Directorios

```
storage/app/public/          (local)
public_html/storage/         (producción)
├── coordinators/
├── drivers/
├── units/
├── companies/
├── users/
└── temp/
```

## ⚡ Beneficios

1. **Compatibilidad Universal**: Funciona en local y producción sin cambios
2. **Mantenimiento Simplificado**: Un solo código para ambos entornos
3. **URLs Correctas**: Genera automáticamente las URLs apropiadas
4. **Gestión Centralizada**: Todas las operaciones de storage en un lugar
5. **Despliegue Automatizado**: Scripts listos para usar

## 🐛 Solución de Problemas

### Problema: Imágenes no se muestran
- Verificar que el enlace simbólico existe
- Comprobar permisos de directorio (755)
- Revisar configuración de `STORAGE_PATH`

### Problema: Error al subir archivos
- Verificar permisos de escritura
- Comprobar que el directorio de destino existe
- Revisar configuración de `php.ini` (upload_max_filesize, post_max_size)

### Problema: URLs incorrectas
- Verificar `APP_URL` en `.env`
- Comprobar que `StorageHelper::getPublicUrl()` se está usando

## 📞 Soporte

Para problemas específicos, revisar:
1. Logs de Laravel (`storage/logs/`)
2. Logs del servidor web
3. Configuración de permisos del hosting