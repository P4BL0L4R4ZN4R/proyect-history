<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    // Nombre explícito de la tabla (aunque Laravel lo podría inferir)
    protected $table = 'rates';

    // Campos que pueden asignarse masivamente
    protected $fillable = [
        'name',
        'percentage',
    ];

    /**
     * Aquí puedes definir relaciones si las llegas a necesitar en el futuro.
     * Ejemplo:
     * public function tarifasAplicadas()
     * {
     *     return $this->hasMany(Servicio::class, 'tipo_tarifa_id');
     * }
     */
}

