# 🖼️ Guía de Uso del StorageHelper - RutVans

## 📋 Problema Solucionado
✅ **Error resuelto**: `Call to undefined function storageExists()`
✅ **Compatibilidad**: Funciona en local y producción
✅ **Mantenimiento**: Un solo código para ambos entornos

## 🎯 Uso Correcto en Vistas Blade

### ❌ No usar (causa errores):
```blade
@if ($photo && @storageExists($photo))
    <img src="@storageImg($photo)">
@endif
```

### ✅ Usar (funciona siempre):
```blade
@if ($photo && \App\Helpers\StorageHelper::fileExists($photo))
    <img src="{{ \App\Helpers\StorageHelper::getPublicUrl($photo) }}">
@endif
```

## 🔧 Métodos Disponibles

### `StorageHelper::getPublicUrl($path)`
```php
// Genera URL correcta según el entorno
$url = \App\Helpers\StorageHelper::getPublicUrl($coordinador->photo);
// Local: http://localhost:8000/storage/coordinators/1/juan/coordinator_photo.jpg
// Producción: https://rutvans.com/storage/coordinators/1/juan/coordinator_photo.jpg
```

### `StorageHelper::fileExists($path)`
```php
// Verifica si un archivo existe
$exists = \App\Helpers\StorageHelper::fileExists($coordinador->photo);
// Retorna: true o false
```

### `StorageHelper::storeFileAs($file, $directory, $filename)`
```php
// Almacena archivo con nombre específico
$path = \App\Helpers\StorageHelper::storeFileAs($request->file('photo'), 'coordinators/1', 'photo.jpg');
```

### `StorageHelper::deletePublicFile($path)`
```php
// Elimina archivo del storage
\App\Helpers\StorageHelper::deletePublicFile($coordinador->photo);
```

## 🎨 Patrones de Uso Comunes

### Mostrar Imagen con Fallback
```blade
@if ($user->photo && \App\Helpers\StorageHelper::fileExists($user->photo))
    <img src="{{ \App\Helpers\StorageHelper::getPublicUrl($user->photo) }}" 
         alt="Foto de {{ $user->name }}" 
         class="img-thumbnail">
@else
    <div class="bg-secondary d-flex align-items-center justify-content-center">
        <i class="fas fa-user"></i>
    </div>
@endif
```

### Para Datos de Botones (data attributes)
```blade
<button data-photo="{{ $driver->photo && \App\Helpers\StorageHelper::fileExists($driver->photo) ? \App\Helpers\StorageHelper::getPublicUrl($driver->photo) : '' }}">
    Editar
</button>
```

### Para Imágenes Complejas con Múltiples Condiciones
```blade
@php
    $profilePhoto = null;
    if ($coordinate && $coordinate->photo && \App\Helpers\StorageHelper::fileExists($coordinate->photo)) {
        $profilePhoto = \App\Helpers\StorageHelper::getPublicUrl($coordinate->photo);
    } elseif ($user->photo && \App\Helpers\StorageHelper::fileExists($user->photo)) {
        $profilePhoto = \App\Helpers\StorageHelper::getPublicUrl($user->photo);
    } else {
        $profilePhoto = asset('images/default-avatar.png');
    }
@endphp
<img src="{{ $profilePhoto }}" alt="Perfil">
```

## 🔄 En Controladores

### Almacenar Archivos
```php
use App\Helpers\StorageHelper;

// Crear directorio
StorageHelper::makeDirectory('coordinators/' . $user->id);

// Almacenar archivo
$path = StorageHelper::storeFileAs($request->file('photo'), 'coordinators/' . $user->id, 'photo.jpg');

// Guardar ruta en base de datos
$coordinator->photo = $path;
$coordinator->save();
```

### Eliminar Archivos
```php
// Eliminar archivo anterior
if ($coordinator->photo) {
    StorageHelper::deletePublicFile($coordinator->photo);
}
```

## 🌍 Configuración por Entorno

### Local (.env)
```env
APP_ENV=local
FILESYSTEM_DISK=public
# No definir STORAGE_PATH
```

### Producción (.env)
```env
APP_ENV=production
FILESYSTEM_DISK=public
STORAGE_PATH=/home/u350475089/domains/rutvans.com/public_html/storage
```

## ⚡ Ventajas de Esta Implementación

1. **✅ Sin Errores**: No más `Call to undefined function`
2. **✅ Automático**: Detecta el entorno automáticamente  
3. **✅ Consistente**: Mismo código en todas las vistas
4. **✅ Mantenible**: Centralizado en una clase helper
5. **✅ Flexible**: Fácil de extender y modificar

## 🐛 Solución de Problemas

### Error: "Class 'App\Helpers\StorageHelper' not found"
```bash
composer dump-autoload
```

### Error: Imágenes no se muestran
1. Verificar que `public/storage` existe (local)
2. Crear enlace simbólico en producción
3. Revisar permisos (755)

### Error: Archivos no se suben
1. Verificar permisos de escritura
2. Comprobar configuración PHP (upload_max_filesize)
3. Revisar variable STORAGE_PATH

## 📁 Archivos Actualizados

✅ `app/Helpers/StorageHelper.php` - Helper principal
✅ `app/Http/Controllers/CoordinateController.php` - Uso del helper
✅ `resources/views/rutvans/sites/asignar/index.blade.php` - Vista principal
✅ `resources/views/units/index.blade.php` - Vista de unidades  
✅ `resources/views/empleados/cashiers/index.blade.php` - Vista de cajeros
✅ `resources/views/empleados/drivers/index.blade.php` - Vista de conductores
✅ `resources/views/profile/partials/edit-profile.blade.php` - Perfil
✅ `resources/views/dashboards/coordinate.blade.php` - Dashboard coordinador