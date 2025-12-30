<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Aquí puedes añadir métodos personalizados o relaciones adicionales
    // Ejemplo:
    protected $fillable = [
        'name',
        'guard_name',
        // Agrega más campos si necesitas
    ];
}
